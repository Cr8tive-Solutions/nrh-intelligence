<x-client.layouts.app pageTitle="Security">

    <div class="page-head">
        <div>
            <h1><em>Security</em> Settings</h1>
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

        {{-- Two-factor authentication (authenticator app) --}}
        <div class="card" style="margin-top:20px;">
            <div class="card-head">
                <h3>Two-Factor Authentication</h3>
            </div>
            <div class="form-body" style="display:flex;flex-direction:column;gap:16px;">

                @if (session('error'))
                    <div class="alert" style="background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);color:#c4453a;">
                        <p style="margin:0;">{{ session('error') }}</p>
                    </div>
                @endif
                @if (session('status'))
                    <div class="alert" style="background:var(--card);border:1px solid var(--line);color:var(--ink-700);">
                        <p style="margin:0;">{{ session('status') }}</p>
                    </div>
                @endif

                @if ($twoFactorEnabled)
                    <p style="font-size:13px;color:var(--ink-700);margin:0;">
                        An authenticator app is <strong>enabled</strong> on your account. You'll enter a code from your app each time you sign in.
                    </p>
                    <form method="POST" action="{{ route('client.settings.security.2fa.disable') }}" style="display:flex;flex-direction:column;gap:12px;">
                        @csrf
                        <div class="field">
                            <label>Current Password <span style="color:var(--ink-500);font-weight:400;">(required to disable)</span></label>
                            <input type="password" name="current_password" autocomplete="current-password"/>
                            @error('current_password') <p class="hint">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn">Disable authenticator app</button>
                        </div>
                    </form>
                @elseif ($setupSecret)
                    <p style="font-size:13px;color:var(--ink-700);margin:0;">
                        Scan this QR code in Google Authenticator (or any TOTP app), then enter the 6-digit code it shows to finish.
                    </p>
                    <div style="width:200px;height:200px;background:#fff;padding:8px;border:1px solid var(--line);border-radius:var(--radius);">
                        {!! $qrSvg !!}
                    </div>
                    <p style="font-size:12px;color:var(--ink-500);margin:0;">
                        Can't scan? Enter this key manually: <code style="user-select:all;">{{ $setupSecret }}</code>
                    </p>
                    <form method="POST" action="{{ route('client.settings.security.2fa.confirm') }}" style="display:flex;flex-direction:column;gap:12px;">
                        @csrf
                        <div class="field">
                            <label>Verification Code</label>
                            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="123456"/>
                            @error('code') <p class="hint">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">Confirm &amp; enable</button>
                        </div>
                    </form>
                @else
                    <p style="font-size:13px;color:var(--ink-700);margin:0;">
                        Add an authenticator app for stronger sign-in security. If you don't, we'll email you a one-time code instead.
                    </p>
                    <form method="POST" action="{{ route('client.settings.security.2fa.enable') }}">
                        @csrf
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">Set up authenticator app</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

</x-client.layouts.app>
