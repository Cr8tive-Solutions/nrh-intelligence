<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $roles = Role::where('guard_name', 'customer_user')->orderBy('id')->pluck('name');
        $permissions = Permission::where('guard_name', 'customer_user')->orderBy('name')->pluck('name');

        return view('client.settings.users.index', compact('users', 'roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customer_users', 'email')],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'customer_user')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = CustomerUser::create([
            'customer_id' => $this->customerId(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(32)),
            'role' => $validated['role'] === 'Admin' || $validated['role'] === 'Owner' ? 'admin' : 'user',
            'status' => $validated['status'],
        ]);

        $user->syncRoles([$validated['role']]);

        activity('access')
            ->performedOn($user)
            ->withProperties([
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'role' => $validated['role'],
                ],
            ])
            ->event('user.created')
            ->log("Created user {$user->name}");

        return redirect()
            ->route('client.settings.users')
            ->with('status', 'User created. A password reset link will be emailed shortly.');
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
