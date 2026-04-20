<x-client.layouts.app pageTitle="Account Settings">

    <div class="page-head">
        <div>
            <h1>Account <em>Settings</em></h1>
            <div class="sub">Company and contact information</div>
        </div>
    </div>

    <div style="max-width:640px;display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-head">
                <h3>Company Information</h3>
            </div>
            <form method="POST" action="{{ route('client.settings.account') }}" class="form-body">
                @csrf

                <div class="field">
                    <label>Company Name</label>
                    <input type="text" name="company_name" value="{{ $customer->name }}"/>
                </div>

                <div class="field">
                    <label>Company Registration No.</label>
                    <input type="text" name="registration_no" value="{{ $customer->registration_no }}"/>
                </div>

                <div class="field">
                    <label>Address</label>
                    <textarea name="address" rows="2">{{ $customer->address }}</textarea>
                </div>

                <div class="field-row field-row-2">
                    <div class="field">
                        <label>Country</label>
                        <input type="text" name="country" value="{{ $customer->country }}"/>
                    </div>
                    <div class="field">
                        <label>Industry</label>
                        <input type="text" name="industry" value="{{ $customer->industry }}"/>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Primary Contact</p>

                    <div class="field">
                        <label>Full Name</label>
                        <input type="text" name="contact_name" value="{{ $customer->contact_name }}"/>
                    </div>

                    <div class="field-row field-row-2">
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="contact_email" value="{{ $customer->contact_email }}"/>
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="tel" name="contact_phone" value="{{ $customer->contact_phone }}"/>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>

    </div>

</x-client.layouts.app>
