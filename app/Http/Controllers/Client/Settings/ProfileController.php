<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = CustomerUser::findOrFail(session('client_user_id'));

        return view('client.settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $userId = session('client_user_id');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('customer_users')->ignore($userId)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = CustomerUser::findOrFail($userId);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->name = $validated['name'];
        $user->save();

        session([
            'client_user_name' => $user->name,
            'client_user_avatar' => $user->avatar,
        ]);

        // The email address is the login identity and where 2FA codes are sent,
        // so it is never changed on the strength of a session alone. Changing it
        // requires the current password, and the new address must prove control
        // by clicking a signed, time-limited confirmation link. The old address
        // is notified so the real owner can react to an unauthorised attempt.
        if ($validated['email'] !== $user->email) {
            $request->validate(['current_password' => ['required']]);

            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $this->sendEmailChangeConfirmation($user, $validated['email']);

            return back()->with('success', 'Profile updated. A confirmation link has been sent to '.$validated['email'].' — the new address takes effect once you confirm it.');
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    protected function sendEmailChangeConfirmation(CustomerUser $user, string $newEmail): void
    {
        $url = URL::temporarySignedRoute(
            'client.settings.profile.email.confirm',
            now()->addHour(),
            ['user' => $user->id, 'email' => $newEmail],
        );

        Mail::raw(
            "A request was made to change the email address on your NRH Intelligence account to this address.\n\n"
            ."Confirm the change by opening the link below within one hour:\n\n{$url}\n\n"
            .'If you did not request this, you can ignore this email.',
            fn ($message) => $message->to($newEmail)->subject('Confirm your new NRH Intelligence email'),
        );

        // Notify the current (old) address so the owner is alerted to the change.
        Mail::raw(
            "A request was made to change the email address on your NRH Intelligence account to {$newEmail}.\n\n"
            ."If this was you, no action is needed — confirm the change from the link sent to the new address.\n\n"
            .'If this was NOT you, change your password immediately and contact your administrator.',
            fn ($message) => $message->to($user->email)->subject('Security alert: email change requested'),
        );
    }

    public function confirmEmail(Request $request)
    {
        // The `signed` middleware has already validated the signature and expiry.
        $user = CustomerUser::findOrFail((int) $request->query('user'));
        $newEmail = (string) $request->query('email');

        // Re-check uniqueness at confirmation time, in case the address was
        // taken between request and confirmation.
        $taken = CustomerUser::where('email', $newEmail)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($taken) {
            return redirect()->route('client.login')
                ->withErrors(['email' => 'That email address is no longer available.']);
        }

        $user->email = $newEmail;
        // Rotate the remember_token so any session riding the old identity is
        // forced to re-authenticate.
        $user->setRememberToken(Str::random(60));
        $user->save();

        if (session('client_user_id') === $user->id) {
            session(['client_user_name' => $user->name]);
            $request->session()->regenerate();
        }

        return redirect()->route('client.login')
            ->with('success', 'Your email address has been updated. Please sign in with your new email.');
    }

    public function removeAvatar()
    {
        $user = CustomerUser::findOrFail(session('client_user_id'));

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
            session(['client_user_avatar' => null]);
        }

        return back()->with('success', 'Profile photo removed.');
    }
}
