<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
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
        ]);

        $user = CustomerUser::findOrFail($userId);
        $user->update($validated);

        session(['client_user_name' => $user->name]);

        return back()->with('success', 'Profile updated successfully.');
    }
}
