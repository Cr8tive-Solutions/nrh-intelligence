<x-client.layouts.auth
    title="Register"
    authTitle='Create an <em>account.</em>'
    authSub='Fill in your company details to request portal access.'
    step="—"
    stepLabel="INSTITUTIONAL ACCESS"
    footerText="Already registered?"
    footerLink="Sign in →"
    :footerHref="route('client.login')"
>

    @if ($errors->any())
        <div style="display:flex;flex-direction:column;gap:4px;padding:12px 14px;background:rgba(196,69,58,0.06);border:1px solid rgba(196,69,58,0.2);border-radius:var(--radius);margin-bottom:16px;">
            @foreach ($errors->all() as $error)
                <p style="font-size:12px;color:var(--danger);margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('client.register.submit') }}" style="display:flex;flex-direction:column;gap:14px;">
        @csrf

        <div class="field">
            <label class="field-label">Company name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" class="auth-input" placeholder="ACME Sdn. Bhd."
                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="field">
                <label class="field-label">First name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="auth-input" placeholder="Ahmad"
                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
            </div>
            <div class="field">
                <label class="field-label">Last name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="auth-input" placeholder="Razali"
                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
            </div>
        </div>

        <div class="field">
            <label class="field-label">Work email <span style="color:var(--danger)">*</span></label>
            <div class="input-wrap">
                <svg class="lead" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                </svg>
                <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="you@company.com"
                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
            </div>
        </div>

        <div class="field">
            <label class="field-label">Phone number <span style="color:var(--danger)">*</span></label>
            <input type="tel" name="phone" value="{{ old('phone') }}" class="auth-input" placeholder="+60 12 345 6789"
                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
        </div>

        <div class="field">
            <label class="field-label">Company address</label>
            <textarea name="address" rows="2" class="auth-input" placeholder="No. 1, Jalan..."
                style="resize:none;" onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">{{ old('address') }}</textarea>
        </div>

        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
            <input type="checkbox" name="agree" required style="width:14px;height:14px;margin-top:2px;accent-color:var(--emerald-700);flex-shrink:0;">
            <span style="font-size:13px;color:var(--ink-600);">
                I agree to the
                <a href="#" style="color:var(--emerald-700);font-weight:600;text-decoration:none;">Terms of Service</a>
                and
                <a href="#" style="color:var(--emerald-700);font-weight:600;text-decoration:none;">Privacy Policy</a>
            </span>
        </label>

        <button type="submit" class="btn-auth">Submit registration</button>
    </form>

</x-client.layouts.auth>
