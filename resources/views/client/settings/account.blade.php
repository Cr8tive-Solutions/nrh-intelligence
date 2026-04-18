<x-client.layouts.app pageTitle="Account Settings">

    <div class="max-w-2xl space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-5">Company Information</h3>
            <form method="POST" action="{{ route('client.settings.account') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Company Name</label>
                    <input type="text" name="company_name" value="{{ $account['company_name'] }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Company Registration No.</label>
                    <input type="text" name="registration_no" value="{{ $account['registration_no'] }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors resize-none">{{ $account['address'] }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Country</label>
                        <input type="text" name="country" value="{{ $account['country'] }}"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Industry</label>
                        <input type="text" name="industry" value="{{ $account['industry'] }}"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <h4 class="text-xs font-semibold text-slate-700 mb-3 mt-2">Primary Contact</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Full Name</label>
                            <input type="text" name="contact_name" value="{{ $account['contact_name'] }}"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                                <input type="email" name="contact_email" value="{{ $account['contact_email'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Phone</label>
                                <input type="tel" name="contact_phone" value="{{ $account['contact_phone'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-client.layouts.app>
