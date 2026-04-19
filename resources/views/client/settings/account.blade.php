<x-client.layouts.app pageTitle="Account Settings">

    @php
        $inputStyle = "width:100%;padding:10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:14px;color:var(--ink-900);outline:none;font-family:var(--font-ui);transition:border-color 120ms,box-shadow 120ms;";
        $labelStyle = "display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;";
    @endphp

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                Account <em style="font-style:italic;color:var(--emerald-700);">Settings</em>
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Company and contact information</p>
        </div>
    </div>

    <div style="max-width:640px;display:flex;flex-direction:column;gap:16px;">

        <div class="nrh-card">
            <div class="card-head">
                <h3>Company Information</h3>
            </div>
            <form method="POST" action="{{ route('client.settings.account') }}" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
                @csrf

                <div>
                    <label style="{{ $labelStyle }}">Company Name</label>
                    <input type="text" name="company_name" value="{{ $customer->name }}" style="{{ $inputStyle }}"
                        onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                        onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                </div>

                <div>
                    <label style="{{ $labelStyle }}">Company Registration No.</label>
                    <input type="text" name="registration_no" value="{{ $customer->registration_no }}" style="{{ $inputStyle }}"
                        onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                        onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                </div>

                <div>
                    <label style="{{ $labelStyle }}">Address</label>
                    <textarea name="address" rows="2" style="{{ $inputStyle }} resize:none;"
                        onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                        onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''">{{ $customer->address }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="{{ $labelStyle }}">Country</label>
                        <input type="text" name="country" value="{{ $customer->country }}" style="{{ $inputStyle }}"
                            onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                            onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                    </div>
                    <div>
                        <label style="{{ $labelStyle }}">Industry</label>
                        <input type="text" name="industry" value="{{ $customer->industry }}" style="{{ $inputStyle }}"
                            onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                            onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                    </div>
                </div>

                <div style="padding-top:16px;border-top:1px solid var(--line);">
                    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--ink-500);margin:0 0 16px;">Primary Contact</p>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="{{ $labelStyle }}">Full Name</label>
                            <input type="text" name="contact_name" value="{{ $customer->contact_name }}" style="{{ $inputStyle }}"
                                onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                                onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label style="{{ $labelStyle }}">Email</label>
                                <input type="email" name="contact_email" value="{{ $customer->contact_email }}" style="{{ $inputStyle }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                                    onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                            </div>
                            <div>
                                <label style="{{ $labelStyle }}">Phone</label>
                                <input type="tel" name="contact_phone" value="{{ $customer->contact_phone }}" style="{{ $inputStyle }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.12)'"
                                    onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;padding-top:4px;">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>

            </form>
        </div>

    </div>

</x-client.layouts.app>
