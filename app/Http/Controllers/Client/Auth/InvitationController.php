<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ClientAuth;
use App\Models\CustomerUser;
use App\Models\CustomerUserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = CustomerUserInvitation::findByRawToken($token);

        if (! $invitation) {
            return view('client.invitation.invalid', ['reason' => 'not_found']);
        }

        if ($invitation->isAccepted()) {
            return redirect()
                ->route('client.login')
                ->with('info', 'This invitation has already been used. Please sign in.');
        }

        if ($invitation->customerUser->hasBeenActivated()) {
            return redirect()
                ->route('client.login')
                ->with('info', 'This invitation is no longer valid. Please sign in.');
        }

        if ($invitation->isExpired()) {
            return view('client.invitation.invalid', [
                'reason' => 'expired',
                'companyName' => $invitation->customerUser?->customer?->name,
            ]);
        }

        return view('client.invitation.accept', [
            'invitation' => $invitation,
            'user' => $invitation->customerUser,
            'customer' => $invitation->customerUser->customer,
        ]);
    }

    public function accept(Request $request, string $token)
    {
        $invitation = CustomerUserInvitation::findByRawToken($token);

        // An invitation only ever activates a brand-new account. Once a user
        // has been active, a leftover link must not let whoever holds it set a
        // new password — a deactivated user is reactivated by their manager.
        if (! $invitation || ! $invitation->isPending() || $invitation->customerUser->hasBeenActivated()) {
            return redirect()
                ->route('client.login')
                ->with('error', 'This invitation link is no longer valid.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($invitation, $data) {
            $user = $invitation->customerUser;
            $user->password = Hash::make($data['password']);
            $user->status = 'active';
            $user->save();

            // Admin-portal invites arrive with no Spatie role assigned (they only set the legacy
            // role enum). The very first admin-portal-invited user per customer becomes Owner.
            // Any later admin-portal-invited user activates with no role and must be configured
            // by their customer's Owner/Admin via Settings → Users.
            if ($user->roles()->count() === 0) {
                $customerHasOwner = CustomerUser::where('customer_id', $user->customer_id)
                    ->where('id', '!=', $user->id)
                    ->whereHas('roles', fn ($q) => $q->where('name', 'Owner')->where('guard_name', 'customer_user'))
                    ->exists();

                if (! $customerHasOwner) {
                    $user->assignRole('Owner');
                }
            }

            $invitation->accepted_at = now();
            $invitation->save();
        });

        $user = $invitation->customerUser->fresh(['customer']);

        $this->loginAndPopulateSession($request, $user);

        activity('auth')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip(), 'user_agent' => $request->userAgent()])
            ->event('invitation.accepted')
            ->log('Invitation accepted; account activated');

        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Welcome to KYRO — your account is now active.');
    }

    protected function loginAndPopulateSession(Request $request, CustomerUser $user): void
    {
        Auth::guard('customer_user')->login($user);

        session([
            'client_user_id' => $user->id,
            'client_customer_id' => $user->customer_id,
            'client_user_name' => $user->name,
            'client_user_avatar' => $user->avatar,
            'client_company' => $user->customer->name,
            'client_last_login' => now()->inDisplayTz()->format('d M Y, H:i'),
            ClientAuth::LOGIN_AT_SESSION_KEY => now()->timestamp,
        ]);
    }
}
