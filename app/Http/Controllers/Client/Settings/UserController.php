<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use App\Models\CustomerUserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $customerId = $this->customerId();

        $users = CustomerUser::with('roles')
            ->where('customer_id', $customerId)
            ->orderBy('name')
            ->get();

        $pendingInvitedIds = CustomerUserInvitation::whereIn('customer_user_id', $users->pluck('id'))
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->pluck('customer_user_id')
            ->all();

        $roles = Role::where('guard_name', 'customer_user')->orderBy('id')->pluck('name');
        $permissions = Permission::where('guard_name', 'customer_user')->orderBy('name')->pluck('name');

        return view('client.settings.users.index', compact('users', 'roles', 'permissions', 'pendingInvitedIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customer_users', 'email')],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'customer_user')],
        ]);

        $invitation = DB::transaction(function () use ($validated) {
            $user = CustomerUser::create([
                'customer_id' => $this->customerId(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(32)),
                'role' => in_array($validated['role'], ['Admin', 'Owner']) ? 'admin' : 'user',
                'status' => 'inactive',
            ]);

            $user->syncRoles([$validated['role']]);

            return CustomerUserInvitation::create([
                'customer_user_id' => $user->id,
                'token' => bin2hex(random_bytes(32)),
                'expires_at' => now()->addDays(14),
                'sent_count' => 1,
                'last_sent_at' => now(),
            ]);
        });

        $this->sendInvitationEmail($invitation);

        activity('access')
            ->performedOn($invitation->customerUser)
            ->withProperties([
                'attributes' => [
                    'name' => $invitation->customerUser->name,
                    'email' => $invitation->customerUser->email,
                    'role' => $validated['role'],
                    'invitation_expires_at' => $invitation->expires_at->toIso8601String(),
                ],
            ])
            ->event('user.invited')
            ->log("Invited user {$invitation->customerUser->name}");

        return redirect()
            ->route('client.settings.users')
            ->with('status', "Invitation sent to {$invitation->customerUser->email}.");
    }

    protected function sendInvitationEmail(CustomerUserInvitation $invitation): void
    {
        $user = $invitation->customerUser;
        $invitedBy = Auth::guard('customer_user')->user();
        $companyName = $invitedBy?->customer?->name ?? 'NRH Intelligence';
        $url = route('client.invitation.show', $invitation->token);
        $expiresIn = $invitation->expires_at->diffForHumans(null, true);

        $body = "Hi {$user->name},\n\n"
            ."{$invitedBy?->name} has invited you to join {$companyName} on NRH Intelligence.\n\n"
            ."Click the link below to set your password and activate your account:\n\n"
            ."{$url}\n\n"
            ."This invitation expires in {$expiresIn}. If you weren't expecting this email, you can ignore it safely.";

        Mail::raw($body, function ($message) use ($user, $companyName) {
            $message->to($user->email)
                ->subject("You're invited to {$companyName} on NRH Intelligence");
        });
    }

    public function edit(CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);

        $user->load('roles', 'permissions');

        $allPermissions = Permission::where('guard_name', 'customer_user')
            ->orderBy('name')
            ->pluck('name');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $user->roles->first()?->name,
            'role_permissions' => $user->getPermissionsViaRoles()->pluck('name')->all(),
            'direct_permissions' => $user->permissions->pluck('name')->all(),
            'all_permissions' => $allPermissions,
        ]);
    }

    public function update(Request $request, CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customer_users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'customer_user')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'customer_user')],
        ]);

        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $user->roles->first()?->name,
            'permissions' => $user->permissions->pluck('name')->all(),
        ];

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => in_array($validated['role'], ['Admin', 'Owner']) ? 'admin' : 'user',
            'status' => $validated['status'],
        ])->save();

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        $after = [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
        ];

        activity('access')
            ->performedOn($user)
            ->withProperties(['old' => $before, 'attributes' => $after])
            ->event('user.updated')
            ->log("Updated user {$user->name}");

        return redirect()
            ->route('client.settings.users')
            ->with('status', 'User updated.');
    }

    public function resend(CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);

        if ($user->status === 'active') {
            return redirect()
                ->route('client.settings.users')
                ->with('error', 'This user is already active.');
        }

        $invitation = CustomerUserInvitation::where('customer_user_id', $user->id)
            ->whereNull('accepted_at')
            ->latest('id')
            ->first();

        if ($invitation && $invitation->last_sent_at && $invitation->last_sent_at->diffInSeconds(now()) < 60) {
            return redirect()
                ->route('client.settings.users')
                ->with('error', 'Please wait a minute before resending.');
        }

        $invitation = DB::transaction(function () use ($invitation, $user) {
            if ($invitation) {
                $invitation->update([
                    'expires_at' => now()->addDays(14),
                    'sent_count' => $invitation->sent_count + 1,
                    'last_sent_at' => now(),
                ]);

                return $invitation;
            }

            return CustomerUserInvitation::create([
                'customer_user_id' => $user->id,
                'token' => bin2hex(random_bytes(32)),
                'expires_at' => now()->addDays(14),
                'sent_count' => 1,
                'last_sent_at' => now(),
            ]);
        });

        $this->sendInvitationEmail($invitation->fresh('customerUser.customer'));

        activity('access')
            ->performedOn($user)
            ->withProperties(['sent_count' => $invitation->sent_count])
            ->event('user.invitation_resent')
            ->log("Resent invitation to {$user->email}");

        return redirect()
            ->route('client.settings.users')
            ->with('status', "Invitation resent to {$user->email}.");
    }

    public function destroy(CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);

        if ($user->id === Auth::guard('customer_user')->id()) {
            abort(403, 'You cannot remove your own account.');
        }

        $snapshot = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
        ];

        $user->delete();

        activity('access')
            ->withProperties(['old' => $snapshot])
            ->event('user.deleted')
            ->log("Deleted user {$snapshot['name']}");

        return redirect()
            ->route('client.settings.users')
            ->with('status', 'User removed.');
    }

    protected function customerId(): int
    {
        return (int) Auth::guard('customer_user')->user()?->customer_id;
    }

    protected function authorizeSameCustomer(CustomerUser $user): void
    {
        if ($user->customer_id !== $this->customerId()) {
            abort(404);
        }
    }
}
