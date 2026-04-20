<x-client.layouts.app pageTitle="Security">

    <div class="page-head">
        <div>
            <h1>
                <em style="font-style:italic;color:var(--emerald-700);">Security</em> Settings
            </h1>
            <div class="sub">Manage your password and access</div>
        </div>
    </div>

    <div style="max-width:480px;">
        <div class="card">
            <div class="card-head">
                <h3>Change Password</h3>
            </div>
            <div class="form-body" x-data="{ show: { current: false, new: false, confirm: false } }">

                @if (session('success'))
                    <div class="alert alert-success">
                        <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('client.settings.security') }}" style="display:flex;flex-direction:column;gap:18px;">
                    @csrf

                    @foreach ([
                        ['current_password', 'Current Password', 'current'],
                        ['password',         'New Password',     'new'],
                        ['password_confirmation', 'Confirm New Password', 'confirm'],
                    ] as [$name, $label, $ref])
                        <div class="field">
                            <label>{{ $label }}</label>
                            <div class="pw-wrap">
                                <input
                                    :type="show.{{ $ref }} ? 'text' : 'password'"
                                    name="{{ $name }}"
                                    placeholder="••••••••"
                                    {{ $errors->has($name) ? 'style=border-color:#c4453a;' : '' }}
                                />
                                <button type="button" @click="show.{{ $ref }} = !show.{{ $ref }}" class="toggle">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </button>
                            </div>
                            @error($name)
                                <p class="hint">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-client.layouts.app>
