<x-client.layouts.auth
    title="Register your organisation"
    authTitle='Register your <em>organisation.</em>'
    authSub='Enter your company and contact details. The NRH team will verify them and email <b>{{ $registration->email }}</b> once your account is approved.'
    step="1 · 1"
    stepLabel="REGISTRATION"
    footerText="Already have an account?"
    footerLink="Sign in →"
    :footerHref="route('client.login')"
>
    <style>
        .reg-input, .reg-select, .reg-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 8px;
            font-family: var(--font-ui);
            font-size: 14px;
            color: var(--ink-900);
            outline: none;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        .reg-textarea { resize: vertical; min-height: 76px; }
        .reg-input:focus, .reg-select:focus, .reg-textarea:focus { border-color: var(--emerald-600); box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
        .reg-input::placeholder, .reg-textarea::placeholder { color: var(--ink-400); }
        .reg-input[readonly] { background: var(--paper); color: var(--ink-500); cursor: not-allowed; }
        .reg-section {
            font-family: var(--font-mono); font-size: 10px; text-transform: uppercase;
            letter-spacing: 0.22em; color: var(--emerald-800); font-weight: 600;
            padding-top: 6px; border-top: 1px solid var(--line);
        }
        .reg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 480px) { .reg-row { grid-template-columns: 1fr; } }
        .req { color: #c4453a; }
        .field-error { font-size: 12px; color: #c4453a; }
    </style>

    @if ($errors->any())
        <div style="display:flex;align-items:center;gap:10px;background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);border-left:3px solid #c4453a;border-radius:6px;padding:10px 14px;">
            <svg style="width:15px;height:15px;color:#c4453a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            <p style="font-size:13px;color:#c4453a;font-weight:500;margin:0;">{{ $errors->has('email') ? $errors->first('email') : 'Please check the highlighted fields.' }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.registration.submit', $token) }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf

        <div class="reg-section">Company</div>

        <div class="field">
            <label class="field-label" for="company_name"><span>Company name <span class="req">*</span></span></label>
            <input class="reg-input" id="company_name" name="company_name" value="{{ old('company_name', $registration->company_name) }}" required maxlength="255" autocomplete="organization">
            @error('company_name')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="reg-row">
            <div class="field">
                <label class="field-label" for="registration_no"><span>Registration no. <span class="req">*</span></span></label>
                <input class="reg-input" id="registration_no" name="registration_no" value="{{ old('registration_no') }}" required maxlength="100" placeholder="SSM no.">
                @error('registration_no')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label class="field-label" for="industry"><span>Industry</span></label>
                <input class="reg-input" id="industry" name="industry" value="{{ old('industry') }}" maxlength="100">
                @error('industry')<span class="field-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="address"><span>Registered address <span class="req">*</span></span></label>
            <textarea class="reg-textarea" id="address" name="address" required maxlength="2000" autocomplete="street-address">{{ old('address') }}</textarea>
            @error('address')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <label class="field-label" for="country"><span>Country <span class="req">*</span></span></label>
            @php $country = old('country', 'Malaysia'); @endphp
            <select class="reg-select" id="country" name="country" required>
                <option value="">Select…</option>
                @foreach ($countries as $name)
                    <option value="{{ $name }}" @selected($country === $name)>{{ $name }}</option>
                @endforeach
            </select>
            @error('country')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="reg-section">Contact person</div>

        <div class="field">
            <label class="field-label" for="contact_name"><span>Full name <span class="req">*</span></span></label>
            <input class="reg-input" id="contact_name" name="contact_name" value="{{ old('contact_name', $registration->contact_name) }}" required maxlength="255" autocomplete="name">
            @error('contact_name')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="reg-row">
            <div class="field">
                <label class="field-label" for="contact_designation"><span>Designation</span></label>
                <input class="reg-input" id="contact_designation" name="contact_designation" value="{{ old('contact_designation') }}" maxlength="100" placeholder="e.g. HR Manager" autocomplete="organization-title">
                @error('contact_designation')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label class="field-label" for="contact_phone"><span>Phone <span class="req">*</span></span></label>
                <input class="reg-input" id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}" required maxlength="50" autocomplete="tel">
                @error('contact_phone')<span class="field-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="email"><span>Work email</span></label>
            <input class="reg-input" id="email" type="email" value="{{ $registration->email }}" readonly>
        </div>

        <div class="reg-section">Sign-in</div>

        <div class="reg-row">
            <div class="field">
                <label class="field-label" for="password"><span>Password <span class="req">*</span></span></label>
                <input class="reg-input" id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Min 8 characters">
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label class="field-label" for="password_confirmation"><span>Confirm <span class="req">*</span></span></label>
                <input class="reg-input" id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
            </div>
        </div>

        <label style="display:flex;gap:10px;align-items:flex-start;font-size:13px;color:var(--ink-700);line-height:1.5;">
            <input type="checkbox" name="declaration" value="1" required @checked(old('declaration')) style="margin-top:3px;accent-color:var(--emerald-700);">
            <span>I confirm these details are accurate and that I am authorised to register this organisation.</span>
        </label>
        @error('declaration')<span class="field-error">{{ $message }}</span>@enderror

        <button type="submit" class="btn-auth">
            Submit for approval
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </form>

    <div class="session-strip">
        <span>Link expires {{ $registration->expires_at->format('d M Y') }}</span>
        <span style="text-align:right;">SECURE · TLS 1.3</span>
    </div>
</x-client.layouts.auth>
