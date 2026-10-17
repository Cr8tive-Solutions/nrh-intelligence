<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CustomerRegistration;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * The invitee's side of invite-to-register onboarding. NRH staff send a link
 * from the admin portal; the invitee enters their company and contact details
 * and chooses a password here. Nothing can sign in until staff approve it in
 * the admin portal, which creates the customer and the Owner login.
 */
class RegistrationController extends Controller
{
    public function show(string $token)
    {
        $registration = CustomerRegistration::findByToken($token);

        if (! $registration || ! $registration->isOpen()) {
            return $this->closed($registration);
        }

        return view('client.registration.form', [
            'registration' => $registration,
            'token' => $token,
            'countries' => Country::orderBy('name')->pluck('name'),
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $registration = CustomerRegistration::findByToken($token);

        if (! $registration || ! $registration->isOpen()) {
            return $this->closed($registration);
        }

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'registration_no' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:2000'],
            'country' => ['required', 'string', 'max:100'],
            'industry' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_designation' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'declaration' => ['accepted'],
        ], [
            'declaration.accepted' => 'Please confirm the details are accurate.',
        ]);

        // Logins are unique by email across every customer.
        if (CustomerUser::where('email', $registration->email)->exists()) {
            return back()->withInput()->withErrors([
                'email' => 'An account already exists for '.$registration->email.'. Please sign in, or contact NRH.',
            ]);
        }

        DB::transaction(function () use ($registration, $data) {
            $registration->fill(collect($data)->except(['password', 'declaration'])->all() + [
                'password_hash' => Hash::make($data['password']),
                'status' => 'submitted',
                'submitted_at' => now(),
            ])->save();

            // Tell the staff member who sent the invite. Everyone with
            // customer.manage also sees the pending count in the sidebar.
            if ($registration->invited_by_admin_id) {
                DB::table('admin_notifications')->insertOrIgnore([
                    'admin_id' => $registration->invited_by_admin_id,
                    'type' => 'registration_submitted',
                    'title' => 'Registration awaiting review',
                    'body' => $data['company_name'].' ('.$registration->email.') has registered and is waiting for approval.',
                    'link' => '/registrations?status=submitted',
                    'reference' => 'registration:'.$registration->id,
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()->route('client.registration.show', $token);
    }

    /** Explain why the link can't be used: submitted, decided, expired or unknown. */
    private function closed(?CustomerRegistration $registration)
    {
        $state = match (true) {
            $registration === null => 'not_found',
            $registration->status === 'invited' => 'expired',
            default => $registration->status, // submitted | approved | rejected | cancelled
        };

        return view('client.registration.closed', [
            'state' => $state,
            'registration' => $registration,
        ]);
    }
}
