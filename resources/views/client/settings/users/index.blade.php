<x-client.layouts.app pageTitle="Users">

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                Team <em style="font-style:italic;color:var(--emerald-700);">Users</em>
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Team members with portal access</p>
        </div>
        <button x-data @click="$dispatch('open-create-user')" class="btn-primary">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add User
        </button>
    </div>

    <div class="nrh-card">
        <div style="overflow-x:auto;">
            <table class="nrh-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th style="width:120px;">Role</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:160px;">Created</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
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
                                <span class="pill pill-pending"><span class="dot"></span>{{ ucfirst($user->role) }}</span>
                            </td>
                            <td>
                                <span class="pill {{ $user->status === 'active' ? 'pill-clear' : 'pill-pending' }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $user->created_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <button class="btn-ghost" style="padding:5px 12px;font-size:12px;">Edit</button>
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
        x-show="open"
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);" @click="open = false"></div>
        <div style="position:relative;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);width:100%;max-width:420px;padding:24px;"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:var(--font-display);font-size:18px;font-weight:500;color:var(--ink-900);margin:0;">Add New User</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;color:var(--ink-400);padding:4px;" onmouseover="this.style.color='var(--ink-900)'" onmouseout="this.style.color='var(--ink-400)'">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @php $inp = "width:100%;padding:10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);box-sizing:border-box;"; @endphp
            <form style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Full Name</label>
                    <input type="text" placeholder="Ahmad bin Ali" style="{{ $inp }}" onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'"/>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Email Address</label>
                    <input type="email" placeholder="ahmad@company.com" style="{{ $inp }}" onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'"/>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Role</label>
                    <select style="{{ $inp }}" onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                        <option>User</option>
                        <option>Admin</option>
                    </select>
                </div>
                <p style="font-size:11px;color:var(--ink-400);margin:0;">A temporary password will be sent to the user's email.</p>
                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="button" @click="open = false" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Create User</button>
                </div>
            </form>
        </div>
    </div>

</x-client.layouts.app>
