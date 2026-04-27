<x-client.layouts.app pageTitle="Users">

    <div class="page-head">
        <div>
            <h1>Team <em>Users</em></h1>
            <div class="sub">Team members with portal access</div>
        </div>
        <button x-data @click="$dispatch('open-create-user')" class="btn btn-primary">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Invite User
        </button>
    </div>

    @if (session('status'))
        <div style="margin-bottom:16px;padding:10px 14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.25);border-left:3px solid var(--emerald-600);border-radius:var(--radius);font-size:13px;color:var(--emerald-700);">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div style="margin-bottom:16px;padding:10px 14px;background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);border-left:3px solid #c4453a;border-radius:var(--radius);font-size:13px;color:#c4453a;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th style="width:140px;">Role</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:160px;">Created</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        @php
                            $userRole = $user->roles->first()?->name ?? '—';
                            $hasPendingInvite = in_array($user->id, $pendingInvitedIds, true);
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:11px;font-weight:600;font-family:var(--font-mono);box-shadow:inset 0 0 0 1px rgba(212,175,55,0.4);flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">{{ $user->name }}</p>
                                        <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="pill {{ $userRole === 'Owner' ? 'pill-clear' : 'pill-pending' }}"><span class="dot"></span>{{ $userRole }}</span>
                            </td>
                            <td>
                                @if ($user->status === 'active')
                                    <span class="pill pill-clear"><span class="dot"></span>Active</span>
                                @elseif ($hasPendingInvite)
                                    <span class="pill pill-progress" style="font-family:var(--font-ui);"><span class="dot"></span>Invitation pending</span>
                                @else
                                    <span class="pill pill-pending"><span class="dot"></span>Inactive</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $user->created_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:6px;">
                                    @if ($user->status !== 'active')
                                        <form method="POST" action="{{ route('client.settings.users.resend-invitation', $user->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost" style="padding:5px 12px;font-size:12px;">Resend invite</button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-ghost" style="padding:5px 12px;font-size:12px;"
                                        x-data @click="$dispatch('open-edit-user', { id: {{ $user->id }} })">
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create user modal --}}
    <div
        x-data="{ open: false }"
        @open-create-user.window="open = true"
        x-show="open" x-cloak
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;"
        x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    >
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);" @click="open = false"></div>
        <div style="position:relative;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);width:100%;max-width:420px;padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:var(--font-display);font-size:18px;font-weight:500;color:var(--ink-900);margin:0;">Invite a Team Member</h3>
                <button type="button" @click="open = false" aria-label="Close" style="background:none;border:none;cursor:pointer;color:var(--ink-400);padding:4px;">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('client.settings.users.store') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <div class="field">
                    <label for="create-name">Full Name</label>
                    <input id="create-name" name="name" type="text" placeholder="Ahmad bin Ali" value="{{ old('name') }}" required/>
                    @error('name')<p style="font-size:11px;color:var(--danger);margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label for="create-email">Email Address</label>
                    <input id="create-email" name="email" type="email" placeholder="ahmad@company.com" value="{{ old('email') }}" required/>
                    @error('email')<p style="font-size:11px;color:var(--danger);margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label for="create-role">Role</label>
                    <select id="create-role" name="role" required>
                        @foreach ($roles as $roleName)
                            <option value="{{ $roleName }}" @selected(old('role', 'Member') === $roleName)>{{ $roleName }}</option>
                        @endforeach
                    </select>
                </div>
                <p style="font-size:11px;color:var(--ink-400);margin:0;">An activation link will be emailed to this address. The user sets their password and the account becomes active on first login. Invitations expire after 14 days.</p>
                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="button" @click="open = false" class="btn btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit user modal --}}
    <div
        x-data="editUserModal()"
        @open-edit-user.window="load($event.detail.id)"
        x-show="open" x-cloak
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;"
        x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    >
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);" @click="open = false"></div>
        <div style="position:relative;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:var(--font-display);font-size:18px;font-weight:500;color:var(--ink-900);margin:0;">Edit User</h3>
                <button type="button" @click="open = false" aria-label="Close" style="background:none;border:none;cursor:pointer;color:var(--ink-400);padding:4px;">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="loading">
                <p style="font-size:13px;color:var(--ink-400);">Loading…</p>
            </template>

            <template x-if="!loading && user">
                <form :action="`{{ url('settings/users') }}/${user.id}`" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf
                    @method('PUT')
                    <div class="field">
                        <label for="edit-name">Full Name</label>
                        <input id="edit-name" name="name" type="text" x-model="user.name" required/>
                    </div>
                    <div class="field">
                        <label for="edit-email">Email Address</label>
                        <input id="edit-email" name="email" type="email" x-model="user.email" required/>
                    </div>
                    <div class="field">
                        <label for="edit-role">Role</label>
                        <select id="edit-role" name="role" x-model="user.role" required>
                            @foreach ($roles as $roleName)
                                <option value="{{ $roleName }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit-status">Status</label>
                        <select id="edit-status" name="status" x-model="user.status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Per-user permission overrides --}}
                    <div style="border-top:1px solid var(--line);padding-top:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <span style="font-size:12px;font-weight:600;color:var(--ink-900);">Permission overrides</span>
                            <span style="font-size:10px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-400);">UBAC</span>
                        </div>
                        <p style="font-size:11px;color:var(--ink-500);margin:0 0 10px;">Direct grants on top of the role's permissions.</p>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <template x-for="perm in user.all_permissions" :key="perm">
                                <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ink-700);cursor:pointer;">
                                    <input type="checkbox" name="permissions[]" :value="perm"
                                        :checked="user.direct_permissions.includes(perm)"
                                        :disabled="user.role_permissions.includes(perm)"
                                        style="cursor:pointer;"/>
                                    <span x-text="perm" style="font-family:var(--font-mono);"></span>
                                    <span x-show="user.role_permissions.includes(perm)" style="font-size:10px;color:var(--ink-400);" x-text="`· via ${user.role}`"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;padding-top:4px;">
                        <button type="button" @click="confirmDelete()" class="btn btn-ghost" style="color:var(--danger);">Delete</button>
                        <div style="flex:1;"></div>
                        <button type="button" @click="open = false" class="btn btn-ghost">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- Hidden delete form (rendered when confirmDelete fires) --}}
    <form id="delete-user-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        function editUserModal() {
            return {
                open: false,
                loading: false,
                user: null,
                async load(id) {
                    this.open = true;
                    this.loading = true;
                    this.user = null;
                    try {
                        const res = await fetch(`{{ url('settings/users') }}/${id}/edit`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (! res.ok) { throw new Error('Failed to load user'); }
                        this.user = await res.json();
                    } catch (e) {
                        this.open = false;
                        alert(e.message);
                    } finally {
                        this.loading = false;
                    }
                },
                confirmDelete() {
                    if (! this.user || ! confirm(`Remove ${this.user.name}?`)) { return; }
                    const form = document.getElementById('delete-user-form');
                    form.action = `{{ url('settings/users') }}/${this.user.id}`;
                    form.submit();
                },
            };
        }
    </script>
    @endpush

</x-client.layouts.app>
