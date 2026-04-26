<x-client.layouts.app pageTitle="{{ $config['label'] }}">

@php
    $h1Em = ['kyc' => 'Customer', 'kyb' => 'Business', 'kys' => 'Supplier'][$type] ?? strtoupper($type);
@endphp
{{-- Page header --}}
<div class="page-head">
    <div>
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:6px;">Due Diligence</div>
        <h1>Know Your <em>{{ $h1Em }}</em></h1>
        <div class="sub">{{ $config['description'] }}</div>
    </div>
</div>

<div
    x-data="dueDiligence()"
    x-init="init()"
    style=""
>

    {{-- Step indicator --}}
    <div class="step-indicator" style="display:flex;align-items:center;padding:16px 20px;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);margin-bottom:24px;">
            @php
                $steps = [
                    ['num' => 1, 'label' => 'Subject Info'],
                    ['num' => 2, 'label' => 'Select Checks'],
                    ['num' => 3, 'label' => 'Upload Documents'],
                    ['num' => 4, 'label' => 'Review & Submit'],
                ];
            @endphp
            @foreach ($steps as $i => $s)
                <div style="display:flex;align-items:center;{{ $i < count($steps) - 1 ? 'flex:1;' : '' }}">

                    {{-- Step node --}}
                    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;transition:all 200ms;"
                            :style="{
                                background: step >= {{ $s['num'] }} ? 'var(--emerald-700)' : 'var(--paper)',
                                color:      step >= {{ $s['num'] }} ? 'white' : 'var(--ink-400)',
                                boxShadow:  step === {{ $s['num'] }} ? '0 0 0 4px rgba(5,150,105,0.15)' : '',
                                border:     step < {{ $s['num'] }} ? '1.5px solid var(--line)' : ''
                            }">
                            <svg x-show="step > {{ $s['num'] }}" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            <span x-show="step <= {{ $s['num'] }}">{{ $s['num'] }}</span>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;transition:color 200ms;"
                                :style="{ color: step >= {{ $s['num'] }} ? 'var(--ink-900)' : 'var(--ink-400)' }">{{ $s['label'] }}</div>
                        </div>
                    </div>

                    {{-- Connector --}}
                    @if ($i < count($steps) - 1)
                        <div style="flex:1;height:1px;margin:0 16px;position:relative;overflow:hidden;background:var(--line);">
                            <div style="position:absolute;inset:0;background:var(--emerald-600);transition:transform 400ms ease;transform-origin:left;"
                                :style="{ transform: step > {{ $s['num'] }} ? 'scaleX(1)' : 'scaleX(0)' }"></div>
                        </div>
                    @endif

                </div>
            @endforeach
    </div>

    {{-- ── STEP 1: Subject Info ── --}}
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">

            <div class="card" style="padding:24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 20px;">{{ $config['subject_label'] }} Details</h3>

                @php $inp = "width:100%;padding:10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);box-sizing:border-box;"; @endphp

                @if ($config['subject_fields'] === 'individual')
                {{-- KYC: Individual fields --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Full Name <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.name" type="text" placeholder="As per identity document" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Identity Type <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.identity_type_id" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select type</option>
                            <option value="1">NRIC (MyKad)</option>
                            <option value="2">Passport</option>
                            <option value="3">Army / Police ID</option>
                            <option value="4">MyPR</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Identity Number <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.identity_number" type="text" placeholder="e.g. 900101-14-5678" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Date of Birth <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.dob" type="date" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Nationality <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.nationality" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select nationality</option>
                            <option>Malaysian</option>
                            <option>Singaporean</option>
                            <option>Indonesian</option>
                            <option>Thai</option>
                            <option>Filipino</option>
                            <option>Vietnamese</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Email Address</label>
                        <input x-model="subject.email" type="email" placeholder="Optional" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Mobile Number</label>
                        <input x-model="subject.mobile" type="tel" placeholder="+60 12 345 6789" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Remarks</label>
                        <input x-model="subject.remarks" type="text" placeholder="Optional" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                </div>

                @elseif ($config['subject_fields'] === 'business')
                {{-- KYB: Business entity fields --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Company Name <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.name" type="text" placeholder="Full registered company name" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Registration No. <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.identity_number" type="text" placeholder="e.g. 202301023456" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Business Type <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.business_type" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select type</option>
                            <option>Sdn Bhd (Private Limited)</option>
                            <option>Berhad (Public Limited)</option>
                            <option>Limited Liability Partnership</option>
                            <option>Partnership</option>
                            <option>Sole Proprietorship</option>
                            <option>Foreign Company Branch</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Country of Incorporation <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.country" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select country</option>
                            <option>Malaysia</option>
                            <option>Singapore</option>
                            <option>Indonesia</option>
                            <option>Thailand</option>
                            <option>Philippines</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Date of Incorporation</label>
                        <input x-model="subject.incorporation_date" type="date" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div style="grid-column:span 2;border-top:1px solid var(--line);padding-top:16px;margin-top:4px;">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:12px;">Primary Contact</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Contact Name</label>
                                <input x-model="subject.contact_name" type="text" placeholder="Authorised representative" style="{{ $inp }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Contact Email</label>
                                <input x-model="subject.email" type="email" placeholder="contact@company.com" style="{{ $inp }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                            </div>
                        </div>
                    </div>
                </div>

                @else
                {{-- KYS: Supplier fields --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Supplier / Vendor Name <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.name" type="text" placeholder="Full registered name" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Registration No. <span style="color:var(--danger)">*</span></label>
                        <input x-model="subject.identity_number" type="text" placeholder="Company registration number" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Country <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.country" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select country</option>
                            <option>Malaysia</option>
                            <option>Singapore</option>
                            <option>Indonesia</option>
                            <option>Thailand</option>
                            <option>Philippines</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Supplier Category <span style="color:var(--danger)">*</span></label>
                        <select x-model="subject.category" style="{{ $inp }}"
                            onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                            <option value="">Select category</option>
                            <option>Manufacturing</option>
                            <option>IT & Technology</option>
                            <option>Professional Services</option>
                            <option>Construction & Engineering</option>
                            <option>Logistics & Supply Chain</option>
                            <option>Healthcare & Pharma</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div style="grid-column:span 2;border-top:1px solid var(--line);padding-top:16px;margin-top:4px;">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:12px;">Primary Contact</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Contact Name</label>
                                <input x-model="subject.contact_name" type="text" placeholder="Account manager / PIC" style="{{ $inp }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Contact Email</label>
                                <input x-model="subject.email" type="email" placeholder="contact@vendor.com" style="{{ $inp }}"
                                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                            </div>
                        </div>
                    </div>
                @endif

                <p x-show="subjectError" style="font-size:12px;color:var(--danger);margin:16px 0 0;" x-text="subjectError"></p>
            </div>

            {{-- Right: summary card --}}
            <div>
                <div class="card" style="padding:18px;position:sticky;top:80px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:12px;">Request Type</div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px;background:var(--paper);border-radius:var(--radius);margin-bottom:16px;">
                        <div style="width:36px;height:36px;border-radius:var(--radius);background:var(--emerald-700);display:grid;place-items:center;flex-shrink:0;">
                            @if($type === 'kyc')
                                <svg style="width:18px;height:18px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                            @elseif($type === 'kyb')
                                <svg style="width:18px;height:18px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                            @else
                                <svg style="width:18px;height:18px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            @endif
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ strtoupper($type) }}</div>
                            <div style="font-size:11px;color:var(--ink-500);">{{ $config['badge'] }}</div>
                        </div>
                    </div>
                    <p style="font-size:12px;color:var(--ink-500);line-height:1.6;margin:0 0 16px;">Fill in the subject details, then select the compliance checks required on the next step.</p>
                    <button @click="nextStep()" :disabled="!subjectValid"
                        class="btn btn-primary" style="width:100%;justify-content:center;"
                        :style="!subjectValid ? 'opacity:0.4;cursor:not-allowed;' : ''">
                        Continue to Checks
                        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STEP 2: Select Checks ── --}}
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">

            {{-- Left: check cards --}}
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="font-size:13px;font-weight:600;color:var(--ink-700);margin-bottom:2px;">Select the compliance checks to run for this subject.</div>
                @foreach ($checks as $check)
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;border-radius:var(--radius);border:1px solid var(--line);padding:14px 16px;transition:border-color 120ms,background 120ms;cursor:default;"
                        :style="{
                            'border-color': isChecked('{{ $check['id'] }}') ? 'rgba(5,150,105,0.4)' : 'var(--line)',
                            background:     isChecked('{{ $check['id'] }}') ? 'rgba(5,150,105,0.04)' : ''
                        }"
                    >
                        <div style="flex:1;min-width:0;padding-right:16px;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">{{ $check['name'] }}</p>
                                <span style="font-size:10px;font-weight:600;color:var(--ink-400);font-family:var(--font-mono);background:var(--paper);border:1px solid var(--line);border-radius:999px;padding:2px 7px;">{{ $check['turnaround'] }}</span>
                            </div>
                            <p style="font-size:12px;color:var(--ink-500);margin:0;line-height:1.5;">{{ $check['desc'] }}</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                            <span style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);">MYR {{ number_format($check['price'], 2) }}</span>
                            <button
                                @click="toggleCheck('{{ $check['id'] }}', '{{ $check['name'] }}', {{ $check['price'] }}, '{{ $check['turnaround'] }}')"
                                style="border-radius:var(--radius);border:1px solid;padding:6px 14px;font-size:11px;font-weight:700;cursor:pointer;transition:background 120ms,color 120ms,border-color 120ms;font-family:var(--font-ui);"
                                :style="{
                                    background:   isChecked('{{ $check['id'] }}') ? 'rgba(196,69,58,0.06)' : 'var(--emerald-700)',
                                    color:        isChecked('{{ $check['id'] }}') ? 'var(--danger)' : 'white',
                                    'border-color': isChecked('{{ $check['id'] }}') ? 'rgba(196,69,58,0.25)' : 'var(--emerald-700)'
                                }"
                            >
                                <span x-text="isChecked('{{ $check['id'] }}') ? 'Remove' : 'Add'"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Right: selected checks cart --}}
            <div>
                <div class="card" style="position:sticky;top:80px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">Selected Checks</h3>
                        <span style="border-radius:999px;background:rgba(5,150,105,0.1);color:var(--emerald-700);font-size:11px;font-weight:700;padding:2px 8px;font-family:var(--font-mono);" x-text="selectedChecks.length"></span>
                    </div>
                    <div style="padding:14px 18px;">
                        <p x-show="selectedChecks.length === 0" style="padding:24px 0;text-align:center;font-size:12px;color:var(--ink-400);margin:0;">No checks selected yet.</p>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <template x-for="item in selectedChecks" :key="item.id">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-size:12px;font-weight:600;color:var(--ink-800);line-height:1.4;margin:0;" x-text="item.name"></p>
                                        <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;font-family:var(--font-mono);">MYR <span x-text="item.price.toFixed(2)"></span></p>
                                    </div>
                                    <button @click="removeCheck(item.id)"
                                        x-data="{ h: false }" @mouseenter="h=true" @mouseleave="h=false"
                                        :style="{ color: h ? 'var(--danger)' : 'var(--ink-300)' }"
                                        style="background:none;border:none;cursor:pointer;flex-shrink:0;margin-top:2px;padding:0;">
                                        <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div x-show="selectedChecks.length > 0" style="margin-top:14px;padding-top:12px;border-top:1px solid var(--line);">
                            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                                <span>Total</span>
                                <span style="font-weight:600;font-family:var(--font-mono);">MYR <span x-text="checksTotal.toFixed(2)"></span></span>
                            </div>
                        </div>
                    </div>
                    <div style="padding:0 18px 18px;">
                        <button @click="nextStep()" :disabled="selectedChecks.length === 0"
                            class="btn btn-primary" style="width:100%;justify-content:center;"
                            :style="selectedChecks.length === 0 ? 'opacity:0.4;cursor:not-allowed;' : ''">
                            Continue
                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;">
            <button @click="prevStep()" class="btn btn-ghost">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
        </div>
    </div>

    {{-- ── STEP 3: Upload Documents ── --}}
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:rgba(212,175,55,0.06);border:1px solid rgba(212,175,55,0.25);border-radius:var(--radius);">
                <svg style="width:16px;height:16px;color:var(--gold-600);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--ink-800);margin:0;">Documents required</p>
                    <p style="font-size:12px;color:var(--ink-600);margin:3px 0 0;">Upload the required documents for this subject. Accepted: PDF, DOC, DOCX, JPG, PNG (max 5MB each).</p>
                </div>
            </div>

            <div class="card">
                <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--line);">
                    <div style="width:26px;height:26px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--emerald-700);">1</div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="subject.name || 'Subject'"></p>
                        <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:2px 0 0;" x-text="subject.identity_number"></p>
                    </div>
                </div>
                <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
                    @foreach ($config['doc_types'] as $docType)
                        <div x-data="{ hovered: false }"
                            style="border-radius:var(--radius);border:2px dashed var(--line);padding:20px 12px;text-align:center;cursor:pointer;transition:border-color 120ms,background 120ms;"
                            :style="{
                                'border-color': getDoc({{ $docType['id'] }}) ? 'rgba(5,150,105,0.45)' : (hovered ? 'var(--emerald-500)' : 'var(--line)'),
                                background:     getDoc({{ $docType['id'] }}) ? 'rgba(5,150,105,0.04)' : (hovered ? 'rgba(5,150,105,0.02)' : ''),
                                'border-style': getDoc({{ $docType['id'] }}) ? 'solid' : 'dashed'
                            }"
                            @mouseenter="hovered = true" @mouseleave="hovered = false"
                            @click="$refs['doc_{{ $docType['id'] }}'].click()">
                            <input type="file" style="display:none;" x-ref="doc_{{ $docType['id'] }}"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                @change="handleDoc($event, {{ $docType['id'] }})" />

                            {{-- Empty state --}}
                            <div x-show="!getDoc({{ $docType['id'] }})">
                                <svg style="width:22px;height:22px;color:var(--ink-300);margin:0 auto 8px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                </svg>
                                <p style="font-size:12px;font-weight:600;color:var(--ink-700);margin:0;">{{ $docType['label'] }}</p>
                                <p style="font-size:11px;color:var(--ink-400);margin:4px 0 0;">Click to upload</p>
                                @if($docType['required'])
                                    <span style="display:inline-block;margin-top:7px;font-size:10px;font-weight:600;color:var(--gold-700);background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:999px;padding:2px 8px;">Required</span>
                                @endif
                            </div>

                            {{-- Uploaded state --}}
                            <div x-show="getDoc({{ $docType['id'] }})">
                                <div style="width:36px;height:36px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                                    <svg style="width:18px;height:18px;color:var(--emerald-600);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                </div>
                                <p style="font-size:12px;font-weight:600;color:var(--emerald-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;" x-text="getDoc({{ $docType['id'] }})?.name ?? ''"></p>
                                <button @click.stop="removeDoc({{ $docType['id'] }})"
                                    x-data="{ btnHov: false }"
                                    :style="{ color: btnHov ? 'var(--danger)' : 'var(--ink-400)' }"
                                    @mouseenter="btnHov = true" @mouseleave="btnHov = false"
                                    style="font-size:11px;background:none;border:none;cursor:pointer;margin-top:5px;font-family:var(--font-ui);">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;">
            <button @click="prevStep()" class="btn btn-ghost">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
            <button @click="nextStep()" class="btn btn-primary">
                Continue
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── STEP 4: Review & Submit ── --}}
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">

            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Subject summary --}}
                <div class="card">
                    <div class="card-head"><h3>{{ $config['subject_label'] }}</h3></div>
                    <div style="padding:16px 24px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">Name</div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink-900);" x-text="subject.name || '—'"></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">ID / Reg. Number</div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);" x-text="subject.identity_number || '—'"></div>
                        </div>
                        @if ($config['subject_fields'] === 'individual')
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">Nationality</div>
                            <div style="font-size:13px;color:var(--ink-700);" x-text="subject.nationality || '—'"></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">Date of Birth</div>
                            <div style="font-size:13px;color:var(--ink-700);" x-text="subject.dob || '—'"></div>
                        </div>
                        @else
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">Country</div>
                            <div style="font-size:13px;color:var(--ink-700);" x-text="subject.country || '—'"></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:3px;">Contact</div>
                            <div style="font-size:13px;color:var(--ink-700);" x-text="subject.email || '—'"></div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Selected checks --}}
                <div class="card">
                    <div class="card-head">
                        <h3>Selected Checks <span style="color:var(--ink-400);font-weight:400;" x-text="'(' + selectedChecks.length + ')'"></span></h3>
                    </div>
                    <div>
                        <template x-for="item in selectedChecks" :key="item.id">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;border-bottom:1px solid var(--line);">
                                <div>
                                    <p style="font-size:13px;color:var(--ink-800);margin:0;" x-text="item.name"></p>
                                    <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;" x-text="item.turnaround"></p>
                                </div>
                                <p style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);margin:0;">MYR <span x-text="item.price.toFixed(2)"></span></p>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Cost + submit --}}
            <div>
                <div class="card" style="padding:20px;position:sticky;top:80px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Cost Breakdown</h3>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <template x-for="item in selectedChecks" :key="item.id">
                            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                                <span x-text="item.name" style="line-height:1.4;"></span>
                                <span style="font-family:var(--font-mono);flex-shrink:0;margin-left:8px;">MYR <span x-text="item.price.toFixed(2)"></span></span>
                            </div>
                        </template>
                        <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;color:var(--ink-900);border-top:1px solid var(--line);padding-top:12px;margin-top:4px;">
                            <span>Total</span>
                            <span style="font-family:var(--font-mono);">MYR <span x-text="checksTotal.toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div style="margin-top:14px;padding:10px 12px;background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);font-size:11px;color:var(--ink-500);">
                        Payment via monthly billing or direct bank transfer. Invoice will be issued at end of month.
                    </div>

                    <form method="POST" action="{{ route('client.request.due-diligence.submit') }}" @submit.prevent="submitForm($event)" style="margin-top:16px;">
                        @csrf
                        <input type="hidden" name="screening_type" value="{{ $type }}">
                        <input type="hidden" name="subject_data" :value="JSON.stringify(subject)">
                        <input type="hidden" name="checks_data" :value="JSON.stringify(selectedChecks)">
                        <button type="submit" :disabled="submitting" class="btn btn-primary"
                            style="width:100%;justify-content:center;"
                            :style="submitting ? 'opacity:0.5;cursor:not-allowed;' : ''">
                            <span x-show="!submitting">Submit Request</span>
                            <span x-show="submitting">Submitting...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;">
            <button @click="prevStep()" class="btn btn-ghost">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
        </div>
    </div>

</div>

@push('scripts')
<script>
function dueDiligence() {
    return {
        step: 1,
        subject: {
            name: '', identity_type_id: '1', identity_number: '', dob: '', nationality: '',
            email: '', mobile: '', remarks: '', business_type: '', country: '',
            incorporation_date: '', contact_name: '', category: '',
        },
        subjectError: '',
        selectedChecks: [],
        docs: [],
        submitting: false,

        init() {},

        get subjectValid() {
            return this.subject.name.trim() !== '' && this.subject.identity_number.trim() !== '';
        },
        get checksTotal() {
            return this.selectedChecks.reduce((sum, c) => sum + c.price, 0);
        },

        isChecked(id) { return this.selectedChecks.some(c => c.id === id); },
        toggleCheck(id, name, price, turnaround) {
            if (this.isChecked(id)) {
                this.removeCheck(id);
            } else {
                this.selectedChecks.push({ id, name, price, turnaround });
            }
        },
        removeCheck(id) { this.selectedChecks = this.selectedChecks.filter(c => c.id !== id); },

        handleDoc(event, docTypeId) {
            const file = event.target.files[0];
            if (!file) { return; }
            this.docs = this.docs.filter(d => d.docTypeId !== docTypeId);
            this.docs.push({ docTypeId, file, name: file.name });
        },
        getDoc(docTypeId) {
            return this.docs.find(d => d.docTypeId === docTypeId) || null;
        },
        removeDoc(docTypeId) {
            this.docs = this.docs.filter(d => d.docTypeId !== docTypeId);
        },

        nextStep() {
            if (this.step === 1 && !this.subjectValid) {
                this.subjectError = 'Name and ID/registration number are required.';
                return;
            }
            this.subjectError = '';
            if (this.step < 4) { this.step++; }
        },
        prevStep() { if (this.step > 1) { this.step--; } },

        submitForm(event) {
            this.submitting = true;
            event.target.submit();
        },
    };
}
</script>
@endpush

</x-client.layouts.app>
