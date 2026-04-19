<x-client.layouts.app pageTitle="Security">

    @php
        $inputStyle = "width:100%;padding:10px 42px 10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:14px;color:var(--ink-900);outline:none;font-family:var(--font-ui);transition:border-color 120ms,box-shadow 120ms;";
        $labelStyle = "display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;";
    @endphp

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                <em style="font-style:italic;color:var(--emerald-700);">Security</em> Settings
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Manage your password and access</p>
        </div>
    </div>

    <div style="max-width:480px;">
        <div class="nrh-card">
            <div class="card-head">
                <h3>Change Password</h3>
            </div>
            <div style="padding:24px;" x-data="{ show: { current: false, new: false, confirm: false } }">

                @if (session('success'))
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);margin-bottom:20px;">
                        <svg style="width:15px;height:15px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <p style="font-size:13px;color:var(--emerald-800);font-weight:500;margin:0;">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('client.settings.security') }}" style="display:flex;flex-direction:column;gap:18px;">
                    @csrf

                    @foreach ([
                        ['current_password', 'Current Password', 'current'],
                        ['password',         'New Password',     'new'],
                        ['password_confirmation', 'Confirm New Password', 'confirm'],
                    ] as [$name, $label, $ref])
                        <div>
                            <label style="{{ $labelStyle }}">{{ $label }}</label>
                            <div style="position:relative;">
                                <input
                                    :type="show.{{ $ref }} ? 'text' : 'password'"
                                    name="{{ $name }}"
                                    placeholder="••••••••"
                                    style="{{ $inputStyle }} {{ $errors->has($name) ? 'border-color:#c4453a;' : '' }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                                    onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"
                                />
                                <button type="button" @click="show.{{ $ref }} = !show.{{ $ref }}"
                                    style="position:absolute;right:0;top:0;bottom:0;padding:0 12px;background:none;border:none;cursor:pointer;color:var(--ink-400);"
                                    onmouseover="this.style.color='var(--ink-700)'" onmouseout="this.style.color='var(--ink-400)'">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </button>
                            </div>
                            @error($name)
                                <p style="font-size:12px;color:var(--danger);margin-top:4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div style="display:flex;justify-content:flex-end;padding-top:4px;">
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-client.layouts.app>
