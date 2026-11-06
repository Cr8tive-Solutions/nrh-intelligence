<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use App\Models\CustomerUserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

        // Only these may be re-invited; a deactivated user is reactivated instead.
        $reinvitableIds = $users->reject(fn (CustomerUser $user) => $user->hasBeenActivated())
            ->pluck('id')
            ->all();

        $roles = Role::where('guard_name', 'customer_user')->orderBy('id')->pluck('name');
        $permissions = Permission::where('guard_name', 'customer_user')->orderBy('name')->pluck('name');

        return view('client.settings.users.index', compact('users', 'roles', 'permissions', 'pendingInvitedIds', 'reinvitableIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customer_users', 'email')],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'customer_user')],
        ]);

        $this->assertCanAssignRole($validated['role']);

        $rawToken = CustomerUserInvitation::newRawToken();

        $invitation = DB::transaction(function () use ($validated, $rawToken) {
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
                'token' => CustomerUserInvitation::hashToken($rawToken),
                'expires_at' => now()->addDays(14),
                'sent_count' => 1,
                'last_sent_at' => now(),
            ]);
        });

        $this->sendInvitationEmail($invitation, $rawToken);

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

    /**
     * Email the invitation link. The raw token is passed in because only its
     * hash is stored on the invitation.
     */
    protected function sendInvitationEmail(CustomerUserInvitation $invitation, string $rawToken): void
    {
        $user = $invitation->customerUser;
        $invitedBy = Auth::guard('customer_user')->user();
        $companyName = $invitedBy?->customer?->name ?? 'NRH Intelligence';
        $url = CustomerUserInvitation::acceptUrl($rawToken);
        $expiresIn = $invitation->expires_at->diffForHumans(null, true);

        $data = [
            'name' => $user->name,
            'inviterName' => $invitedBy?->name,
            'companyName' => $companyName,
            'url' => $url,
            'expiresIn' => $expiresIn,
        ];

        Log::info('invitation_email.sending', [
            'invitation_id' => $invitation->id,
            'to' => $user->email,
            'company' => $companyName,
            'sent_count' => $invitation->sent_count,
        ]);

        try {
            Mail::send(
                ['html' => 'emails.invitation', 'text' => 'emails.invitation-text'],
                $data,
                function ($message) use ($user, $companyName) {
                    $message->to($user->email)
                        ->subject("You're invited to {$companyName} on KYRO");
                },
            );
            Log::info('invitation_email.sent', ['to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('invitation_email.failed', [
                'to' => $user->email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function edit(CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);

        $user->load('roles', 'permissions');

        $allPermissions = Permission::where('guard_name', 'customer_user')
            ->orderBy('name')
            ->pluck('name');

        return response()->json([
            // `hashid` is what the modal builds its update/delete URLs from.
            // CustomerUser is hashid-bound, so a raw integer id 404s on the
            // route binding — see the `hid()` note in CLAUDE.md.
            'hashid' => hid($user->id),
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
        $this->assertCanManage($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customer_users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'customer_user')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'customer_user')],
        ]);

        // Keeping a user's existing role is not assigning it (assertCanManage
        // already covers rank), so it is only checked when it changes.
        if ($validated['role'] !== $user->roles->first()?->name) {
            $this->assertCanAssignRole($validated['role']);
        }
        $this->assertCanAssignPermissions($validated['permissions'] ?? []);

        // A manager-set email would let them take the account over through a
        // password reset, so once a user has been activated only the user can
        // change their email (Profile, with a confirmation link). A pending
        // invitee's email may still be corrected here.
        $changesEmail = strcasecmp($validated['email'], $user->email) !== 0;

        if ($changesEmail && $user->hasBeenActivated()) {
            throw ValidationException::withMessages([
                'email' => 'This user has already activated their account. They must change their email themselves from their profile.',
            ]);
        }

        $currentRole = $user->roles->first()?->name;
        $currentStatus = $user->status;
        $currentPermissions = $user->permissions->pluck('name')->all();

        // A manager may not change their own role, status, or permissions — that
        // is the escalation / self-lockout path. Name and email they may edit.
        if ($this->isSelf($user)) {
            $changesRole = $validated['role'] !== $currentRole;
            $changesStatus = $validated['status'] !== $user->status;
            $changesPermissions = array_diff($validated['permissions'] ?? [], $currentPermissions) !== []
                || array_diff($currentPermissions, $validated['permissions'] ?? []) !== [];

            if ($changesRole || $changesStatus || $changesPermissions) {
                abort(403, 'You cannot change your own role, status, or permissions.');
            }
        }

        // Keep at least one active Owner when demoting or deactivating one.
        if ($validated['role'] !== 'Owner' || $validated['status'] !== 'active') {
            $this->assertNotLastActiveOwner($user);
        }

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

        // A link already sent to the old (possibly mistyped) address must not
        // stay usable once the email is corrected; the manager resends.
        if ($changesEmail) {
            CustomerUserInvitation::where('customer_user_id', $user->id)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->update(['expires_at' => now()]);
        }

        // Deactivating a user invalidates their long-lived remember-me cookie
        // immediately; active sessions are torn down by ClientAuth on their
        // next request.
        if ($validated['status'] === 'inactive' && $currentStatus === 'active') {
            $user->setRememberToken(Str::random(60));
            $user->save();
        }

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
            ->with('status', $changesEmail
                ? 'User updated. Their previous invitation link no longer works — resend the invitation to the new address.'
                : 'User updated.');
    }

    public function resend(CustomerUser $user)
    {
        $this->authorizeSameCustomer($user);
        $this->assertCanManage($user);

        if ($user->status === 'active') {
            return redirect()
                ->route('client.settings.users')
                ->with('error', 'This user is already active.');
        }

        // An invitation sets a password and activates the account, so it is
        // only for users who never accepted one. A deactivated user is brought
        // back through the status control, which has its own checks.
        if ($user->hasBeenActivated()) {
            return redirect()
                ->route('client.settings.users')
                ->with('error', 'This user has already activated their account. Reactivate them by changing their status instead.');
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

        // Only the token's hash is stored, so a resend always mints a new token
        // (which also retires the previously emailed link).
        $rawToken = CustomerUserInvitation::newRawToken();

        $invitation = DB::transaction(function () use ($invitation, $user, $rawToken) {
            if ($invitation) {
                $invitation->update([
                    'token' => CustomerUserInvitation::hashToken($rawToken),
                    'expires_at' => now()->addDays(14),
                    'sent_count' => $invitation->sent_count + 1,
                    'last_sent_at' => now(),
                ]);

                return $invitation;
            }

            return CustomerUserInvitation::create([
                'customer_user_id' => $user->id,
                'token' => CustomerUserInvitation::hashToken($rawToken),
                'expires_at' => now()->addDays(14),
                'sent_count' => 1,
                'last_sent_at' => now(),
            ]);
        });

        $this->sendInvitationEmail($invitation->fresh('customerUser.customer'), $rawToken);

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
        $this->assertCanManage($user);

        if ($user->id === Auth::guard('customer_user')->id()) {
            abort(403, 'You cannot remove your own account.');
        }

        $this->assertNotLastActiveOwner($user);

        // Deleting a user cascades to every screening request they submitted
        // (FK ON DELETE CASCADE), taking candidates and report-version rows with
        // it. Refuse the delete when they own request history — deactivate
        // instead — so that data is never silently destroyed from the UI.
        if ($user->screeningRequests()->exists()) {
            abort(403, 'This user has submitted screening requests. Deactivate the account instead of deleting it to preserve that history.');
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

    /**
     * Role privilege ranking. A manager may never assign a role, or act on a
     * user, that outranks their own — so an Admin cannot mint or edit an Owner,
     * and only an Owner can grant the Owner role.
     */
    protected const ROLE_RANK = [
        'Viewer' => 1,
        'HR' => 2,
        'Accounts' => 2,
        'Admin' => 3,
        'Owner' => 4,
    ];

    protected function actingUser(): CustomerUser
    {
        return Auth::guard('customer_user')->user();
    }

    protected function roleRank(?string $role): int
    {
        return self::ROLE_RANK[$role] ?? 0;
    }

    protected function actingRank(): int
    {
        return $this->roleRank($this->actingUser()->roles->first()?->name);
    }

    /**
     * Guard against assigning a role above the acting manager's own rank, or
     * one that carries any permission the manager does not hold — rank alone
     * would let an Admin hand out Accounts' manage-billing.
     */
    protected function assertCanAssignRole(string $roleName): void
    {
        if ($this->roleRank($roleName) > $this->actingRank()) {
            abort(403, 'You cannot assign a role higher than your own.');
        }

        $rolePermissions = Role::findByName($roleName, 'customer_user')->permissions->pluck('name')->all();
        $own = $this->actingUser()->getAllPermissions()->pluck('name')->all();

        if (array_diff($rolePermissions, $own) !== []) {
            abort(403, 'You cannot assign a role with permissions you do not hold yourself.');
        }
    }

    /**
     * A manager may only grant permissions they themselves hold, so direct
     * permissions cannot be used to escalate beyond the manager's own access.
     *
     * @param  array<int, string>  $permissions
     */
    protected function assertCanAssignPermissions(array $permissions): void
    {
        $own = $this->actingUser()->getAllPermissions()->pluck('name')->all();

        if (array_diff($permissions, $own) !== []) {
            abort(403, 'You cannot grant a permission you do not hold yourself.');
        }
    }

    /**
     * Guard against acting on a user who outranks the acting manager (e.g. an
     * Admin editing or deleting an Owner).
     */
    protected function assertCanManage(CustomerUser $target): void
    {
        if ($this->roleRank($target->roles->first()?->name) > $this->actingRank()) {
            abort(403, 'You cannot modify a user with a higher role than your own.');
        }
    }

    protected function isSelf(CustomerUser $target): bool
    {
        return $target->id === $this->actingUser()->id;
    }

    /**
     * Prevent a company from losing its last active Owner. Called before any
     * change that would demote, deactivate, or delete the given Owner.
     */
    protected function assertNotLastActiveOwner(CustomerUser $target): void
    {
        if ($this->roleRank($target->roles->first()?->name) !== self::ROLE_RANK['Owner']) {
            return;
        }

        $otherActiveOwners = CustomerUser::where('customer_id', $this->customerId())
            ->where('id', '!=', $target->id)
            ->where('status', 'active')
            ->whereHas('roles', fn ($q) => $q->where('name', 'Owner')->where('guard_name', 'customer_user'))
            ->exists();

        if (! $otherActiveOwners) {
            abort(403, 'This is the company\'s last active Owner and cannot be removed or demoted.');
        }
    }
}
