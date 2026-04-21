<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — NRH Intelligence</title>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:400,500,600,700|fraunces:400,500,600,700ital|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--paper);padding:24px;">

    <div style="width:100%;max-width:400px;">

        {{-- Logo --}}
        <div style="text-align:center;margin-bottom:32px;">
            <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:48px;width:auto;margin:0 auto 12px;display:block;">
            <div style="font-family:var(--font-display);font-size:20px;font-weight:600;color:var(--ink-900);">
                NRH <em style="color:var(--gold-600);">Admin</em>
            </div>
            <p style="font-size:12px;color:var(--ink-500);margin:4px 0 0;text-transform:uppercase;letter-spacing:0.14em;">Operations Portal</p>
        </div>

        {{-- Card --}}
        <div class="card" style="padding:28px;">
            <h2 style="font-size:15px;font-weight:600;color:var(--ink-900);margin:0 0 20px;">Sign in to Admin Portal</h2>

            <form method="POST" action="{{ route('admin.login.submit') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf

                <div class="field">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus placeholder="admin@nrhintelligence.com"
                           style="{{ $errors->has('email') ? 'border-color:#c4453a;' : '' }}"/>
                    @error('email') <p class="hint">{{ $message }}</p> @enderror
                </div>

                <div class="field" x-data="{ show: false }">
                    <label>Password</label>
                    <div class="pw-wrap">
                        <input :type="show ? 'text' : 'password'" name="password" placeholder="••••••••"/>
                        <button type="button" @click="show = !show" class="toggle">
                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
                    Sign In
                </button>
            </form>
        </div>

        <p style="text-align:center;font-size:11px;color:var(--ink-400);margin-top:20px;">
            Restricted access · NRH Intelligence staff only
        </p>
    </div>

</body>
</html>
