<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        session([
            'client_user_name' => $user->name,
            'client_user_avatar' => $user->avatar,
        ]);

        return back()->with('success', 'Profile updated successfully.');
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
