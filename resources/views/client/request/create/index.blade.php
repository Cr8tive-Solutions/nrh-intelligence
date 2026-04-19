<x-client.layouts.app pageTitle="New Request">

@php
    $countriesJson  = $countries->toJson();
    $scopesJson     = $scopes->toJson();
    $packagesJson   = $packages->toJson();
    $identityJson   = $identityTypes->toJson();
@endphp

<div
    x-data="newRequest()"
    x-init="init()"
    style="max-width:1100px;"
>

    {{-- Step indicator --}}
    <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;">
            @php
                $steps = [
                    ['num' => 1, 'label' => 'Select Scopes'],
                    ['num' => 2, 'label' => 'Add Candidates'],
                    ['num' => 3, 'label' => 'Upload Documents'],
                    ['num' => 4, 'label' => 'Review & Submit'],
                ];
            @endphp
            @foreach ($steps as $i => $s)
                <div style="display:flex;align-items:center;{{ $i < count($steps) - 1 ? 'flex:1;' : '' }}">
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:background 200ms,color 200ms;"
                            :style="step >= {{ $s['num'] }} ? 'background:var(--emerald-700);color:white;' : 'background:var(--line);color:var(--ink-400);'"
                        >
                            <template x-if="step > {{ $s['num'] }}">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                            </template>
                            <template x-if="step <= {{ $s['num'] }}">
                                <span>{{ $s['num'] }}</span>
                            </template>
                        </div>
                        <span
                            style="font-size:13px;font-weight:500;transition:color 200ms;"
                            :style="step >= {{ $s['num'] }} ? 'color:var(--ink-900);' : 'color:var(--ink-400);'"
                        >{{ $s['label'] }}</span>
                    </div>
                    @if ($i < count($steps) - 1)
                        <div style="flex:1;height:1px;background:var(--line);margin:0 12px;position:relative;overflow:hidden;">
                            <div style="position:absolute;left:0;top:0;bottom:0;background:var(--emerald-600);transition:width 400ms ease;"
                                :style="step > {{ $s['num'] }} ? 'width:100%' : 'width:0%'"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── STEP 1: Select Scopes ── --}}
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">

            {{-- Left: Scope browser --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Country tabs --}}
                <div class="nrh-card" style="padding:18px 20px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 12px;">Select Country</h3>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach ($countries as $country)
                            <button
                                @click="selectedCountry = {{ $country['id'] }}"
                                style="display:flex;align-items:center;gap:8px;border-radius:var(--radius);border:1px solid var(--line);padding:8px 14px;font-size:13px;font-weight:500;cursor:pointer;transition:background 120ms,border-color 120ms,color 120ms;font-family:var(--font-ui);"
                                :style="selectedCountry === {{ $country['id'] }}
                                    ? 'background:var(--emerald-700);color:white;border-color:var(--emerald-700);'
                                    : 'background:var(--card);color:var(--ink-700);'"
                            >
                                <span>{{ $country['flag'] }}</span>
                                <span>{{ $country['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tabs: Scopes / Packages / Favourites --}}
                <div class="nrh-card">
                    <div style="display:flex;border-bottom:1px solid var(--line);padding:0 4px;">
                        @foreach (['scopes' => 'Verification Scopes', 'packages' => 'Packages', 'favourites' => 'My Favourites'] as $tab => $label)
                            <button
                                @click="scopeTab = '{{ $tab }}'"
                                style="padding:12px 14px;font-size:13px;font-weight:500;cursor:pointer;border:none;background:none;border-bottom:2px solid transparent;margin-bottom:-1px;transition:color 120ms,border-color 120ms;font-family:var(--font-ui);"
                                :style="scopeTab === '{{ $tab }}' ? 'border-bottom-color:var(--emerald-600);color:var(--emerald-700);' : 'color:var(--ink-500);'"
                            >{{ $label }}</button>
                        @endforeach
                    </div>

                    <div style="padding:16px;">

                        {{-- Scopes tab --}}
                        <div x-show="scopeTab === 'scopes'">
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                <template x-for="scope in filteredScopes" :key="scope.id">
                                    <div
                                        style="display:flex;align-items:center;justify-content:space-between;border-radius:var(--radius);border:1px solid var(--line);padding:12px 14px;transition:border-color 120ms,background 120ms;cursor:default;"
                                        :style="isInCart(scope.id) ? 'border-color:rgba(5,150,105,0.4);background:rgba(5,150,105,0.04);' : ''"
                                    >
                                        <div style="flex:1;min-width:0;">
                                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="scope.name"></p>
                                            <span style="font-size:11px;color:var(--ink-400);display:flex;align-items:center;gap:4px;margin-top:3px;">
                                                <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                <span x-text="scope.turnaround"></span>
                                            </span>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:12px;margin-left:12px;flex-shrink:0;">
                                            <span style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="scope.price.toFixed(2)"></span></span>
                                            <button
                                                @click="toggleScope(scope)"
                                                style="border-radius:var(--radius);border:1px solid;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;transition:background 120ms,color 120ms;font-family:var(--font-ui);"
                                                :style="isInCart(scope.id)
                                                    ? 'background:rgba(196,69,58,0.06);color:var(--danger);border-color:rgba(196,69,58,0.25);'
                                                    : 'background:var(--emerald-700);color:white;border-color:var(--emerald-700);'"
                                            >
                                                <span x-text="isInCart(scope.id) ? 'Remove' : 'Add'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredScopes.length === 0">
                                    <p style="padding:32px 0;text-align:center;font-size:13px;color:var(--ink-400);">No scopes available for this country.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Packages tab --}}
                        <div x-show="scopeTab === 'packages'">
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                <template x-for="pkg in filteredPackages" :key="pkg.id">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;border-radius:var(--radius);border:1px solid var(--line);padding:12px 14px;">
                                        <div style="flex:1;min-width:0;">
                                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="pkg.name"></p>
                                            <p style="font-size:12px;color:var(--ink-500);margin:3px 0 0;">
                                                <span x-text="pkg.scope_ids.length"></span> scopes included
                                            </p>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:12px;margin-left:12px;flex-shrink:0;">
                                            <span style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="pkg.price.toFixed(2)"></span></span>
                                            <button @click="addPackage(pkg)" class="btn-primary" style="font-size:11px;padding:6px 12px;">Add All</button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredPackages.length === 0">
                                    <p style="padding:32px 0;text-align:center;font-size:13px;color:var(--ink-400);">No packages available for this country.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Favourites tab --}}
                        <div x-show="scopeTab === 'favourites'">
                            <p style="padding:32px 0;text-align:center;font-size:13px;color:var(--ink-400);">
                                No saved favourites yet.
                                <a href="{{ route('client.settings.packages') }}" style="color:var(--emerald-700);font-weight:600;text-decoration:none;">Create one in Settings →</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Cart --}}
            <div>
                <div class="nrh-card" style="position:sticky;top:80px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">Selected Scopes</h3>
                        <span style="border-radius:999px;background:rgba(5,150,105,0.1);color:var(--emerald-700);font-size:11px;font-weight:700;padding:2px 8px;font-family:var(--font-mono);" x-text="cart.length"></span>
                    </div>
                    <div style="padding:14px 18px;">
                        <template x-if="cart.length === 0">
                            <p style="padding:24px 0;text-align:center;font-size:12px;color:var(--ink-400);">No scopes selected yet.</p>
                        </template>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <template x-for="item in cart" :key="item.id">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-size:12px;font-weight:600;color:var(--ink-800);line-height:1.4;margin:0;" x-text="item.name"></p>
                                        <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;font-family:var(--font-mono);">MYR <span x-text="item.price.toFixed(2)"></span></p>
                                    </div>
                                    <button @click="removeFromCart(item.id)"
                                        style="color:var(--ink-300);background:none;border:none;cursor:pointer;flex-shrink:0;margin-top:2px;padding:0;"
                                        onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-300)'">
                                        <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <template x-if="cart.length > 0">
                            <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--line);">
                                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);margin-bottom:4px;">
                                    <span>Per candidate</span>
                                    <span style="font-family:var(--font-mono);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                                </div>
                                <button @click="clearCart()" style="font-size:11px;color:var(--ink-400);background:none;border:none;cursor:pointer;padding:0;margin-top:6px;"
                                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-400)'">Clear all</button>
                            </div>
                        </template>
                    </div>

                    <div style="padding:0 18px 18px;">
                        <button
                            @click="nextStep()"
                            :disabled="cart.length === 0"
                            class="btn-primary"
                            style="width:100%;justify-content:center;"
                            :style="cart.length === 0 ? 'opacity:0.4;cursor:not-allowed;' : ''"
                        >
                            Continue
                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STEP 2: Add Candidates ── --}}
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        @php
            $inp2 = "width:100%;padding:10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);box-sizing:border-box;";
        @endphp
        <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">

            {{-- Add candidate form + table --}}
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="nrh-card" style="padding:20px 24px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Add Candidate</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div style="grid-column:span 2;">
                            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Full Name <span style="color:var(--danger)">*</span></label>
                            <input x-model="newCandidate.name" type="text" placeholder="As per identity document" style="{{ $inp2 }}"
                                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Identity Type <span style="color:var(--danger)">*</span></label>
                            <select x-model="newCandidate.identity_type_id" style="{{ $inp2 }}"
                                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'">
                                <option value="">Select type</option>
                                @foreach ($identityTypes as $type)
                                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Identity Number <span style="color:var(--danger)">*</span></label>
                            <input x-model="newCandidate.identity_number" type="text" placeholder="e.g. 900101-14-5678" style="{{ $inp2 }}"
                                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Mobile Number</label>
                            <input x-model="newCandidate.mobile" type="tel" placeholder="+60 12 345 6789" style="{{ $inp2 }}"
                                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Remarks</label>
                            <input x-model="newCandidate.remarks" type="text" placeholder="Optional" style="{{ $inp2 }}"
                                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
                        <p x-show="candidateError" style="font-size:12px;color:var(--danger);margin:0;" x-text="candidateError"></p>
                        <div style="margin-left:auto;">
                            <button @click="addCandidate()" class="btn-primary">Add Candidate</button>
                        </div>
                    </div>
                </div>

                {{-- Candidates table --}}
                <div class="nrh-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">
                            Candidates
                            <span style="margin-left:6px;border-radius:999px;background:var(--paper);border:1px solid var(--line);font-size:11px;font-weight:700;padding:1px 7px;color:var(--ink-500);font-family:var(--font-mono);" x-text="candidates.length"></span>
                        </h3>
                    </div>
                    <template x-if="candidates.length === 0">
                        <p style="padding:40px 0;text-align:center;font-size:13px;color:var(--ink-400);">No candidates added yet.</p>
                    </template>
                    <template x-if="candidates.length > 0">
                        <div style="overflow-x:auto;">
                            <table class="nrh-table">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Name</th>
                                        <th>Identity</th>
                                        <th>Mobile</th>
                                        <th style="width:80px;text-align:right;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(c, i) in candidates" :key="c._id">
                                        <tr>
                                            <td style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);" x-text="i + 1"></td>
                                            <td style="font-weight:600;color:var(--ink-900);" x-text="c.name"></td>
                                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);" x-text="c.identity_number"></td>
                                            <td style="font-size:12px;color:var(--ink-500);" x-text="c.mobile || '—'"></td>
                                            <td style="text-align:right;">
                                                <button @click="removeCandidate(c._id)" style="font-size:12px;font-weight:600;color:var(--danger);background:none;border:none;cursor:pointer;font-family:var(--font-ui);"
                                                    onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Remove</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Scope summary sidebar --}}
            <div>
                <div class="nrh-card" style="padding:18px;position:sticky;top:80px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 12px;">Scope Summary</h3>
                    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
                        <template x-for="item in cart" :key="item.id">
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span style="color:var(--ink-600);line-height:1.4;" x-text="item.name"></span>
                                <span style="color:var(--ink-500);flex-shrink:0;margin-left:8px;font-family:var(--font-mono);">MYR <span x-text="item.price.toFixed(2)"></span></span>
                            </div>
                        </template>
                    </div>
                    <div style="border-top:1px solid var(--line);padding-top:12px;display:flex;flex-direction:column;gap:6px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>Per candidate</span>
                            <span style="font-weight:600;font-family:var(--font-mono);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>Candidates</span>
                            <span style="font-weight:600;" x-text="candidates.length"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;color:var(--ink-900);border-top:1px solid var(--line);padding-top:10px;margin-top:4px;">
                            <span>Est. Total</span>
                            <span style="font-family:var(--font-mono);">MYR <span x-text="(cartTotal * candidates.length).toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;">
            <button @click="prevStep()" class="btn-ghost">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
            <button @click="nextStep()" :disabled="candidates.length === 0" class="btn-primary"
                :style="candidates.length === 0 ? 'opacity:0.4;cursor:not-allowed;' : ''">
                Continue
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
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
                    <p style="font-size:12px;color:var(--ink-600);margin:3px 0 0;">Upload the required documents for each candidate. Accepted: PDF, DOC, DOCX, JPG, PNG (max 5MB each).</p>
                </div>
            </div>

            <template x-for="(candidate, ci) in candidates" :key="candidate._id">
                <div class="nrh-card">
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--line);">
                        <div style="width:26px;height:26px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--emerald-700);" x-text="ci + 1"></div>
                        <div>
                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="candidate.name"></p>
                            <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:2px 0 0;" x-text="candidate.identity_number"></p>
                        </div>
                        <div style="margin-left:auto;">
                            <span x-show="candidateDocsComplete(candidate._id)" class="pill pill-clear">
                                <span class="dot"></span>Complete
                            </span>
                        </div>
                    </div>
                    <div style="padding:20px;display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
                        <template x-for="docType in requiredDocTypes" :key="docType.id">
                            <div
                                style="position:relative;border-radius:var(--radius);border:2px dashed var(--line);padding:20px 16px;text-align:center;transition:border-color 120ms,background 120ms;cursor:pointer;"
                                :style="getUploadedFile(candidate._id, docType.id) ? 'border-color:rgba(5,150,105,0.4);background:rgba(5,150,105,0.04);' : ''"
                                @click="$refs['file_' + candidate._id + '_' + docType.id].click()"
                                onmouseover="if(!this.style.borderColor.includes('150')) { this.style.borderColor='var(--emerald-400)'; }"
                                onmouseout="if(!this.style.borderColor.includes('150')) { this.style.borderColor='var(--line)'; }"
                            >
                                <input
                                    type="file"
                                    style="display:none;"
                                    :ref="'file_' + candidate._id + '_' + docType.id"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    @change="handleFileUpload($event, candidate._id, docType.id)"
                                />
                                <template x-if="!getUploadedFile(candidate._id, docType.id)">
                                    <div>
                                        <svg style="width:28px;height:28px;color:var(--ink-200);margin:0 auto 8px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                        </svg>
                                        <p style="font-size:12px;font-weight:600;color:var(--ink-700);margin:0;" x-text="docType.label"></p>
                                        <p style="font-size:11px;color:var(--ink-400);margin:3px 0 0;">Click to upload</p>
                                        <span x-show="docType.required" class="pill pill-review" style="display:inline-flex;margin-top:6px;font-size:10px;">Required</span>
                                    </div>
                                </template>
                                <template x-if="getUploadedFile(candidate._id, docType.id)">
                                    <div>
                                        <svg style="width:28px;height:28px;color:var(--emerald-600);margin:0 auto 8px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <p style="font-size:12px;font-weight:600;color:var(--emerald-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;" x-text="getUploadedFile(candidate._id, docType.id).name"></p>
                                        <button @click.stop="removeFile(candidate._id, docType.id)" style="font-size:11px;color:var(--ink-400);background:none;border:none;cursor:pointer;margin-top:4px;font-family:var(--font-ui);"
                                            onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-400)'">Remove</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;">
            <button @click="prevStep()" class="btn-ghost">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
            <button @click="nextStep()" class="btn-primary">
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

                {{-- Scopes --}}
                <div class="nrh-card">
                    <div class="card-head">
                        <h3>Selected Scopes</h3>
                    </div>
                    <div>
                        <template x-for="item in cart" :key="item.id">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;border-bottom:1px solid var(--line);">
                                <p style="font-size:13px;color:var(--ink-800);margin:0;" x-text="item.name"></p>
                                <p style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);margin:0;">MYR <span x-text="item.price.toFixed(2)"></span></p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Candidates --}}
                <div class="nrh-card">
                    <div class="card-head">
                        <h3>Candidates <span style="color:var(--ink-400);font-weight:400;" x-text="'(' + candidates.length + ')'"></span></h3>
                    </div>
                    <div>
                        <template x-for="(c, i) in candidates" :key="c._id">
                            <div style="display:flex;align-items:center;gap:12px;padding:12px 24px;border-bottom:1px solid var(--line);">
                                <div style="width:24px;height:24px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--emerald-700);flex-shrink:0;" x-text="i + 1"></div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="c.name"></p>
                                    <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:2px 0 0;" x-text="c.identity_number"></p>
                                </div>
                                <span x-show="candidateDocsComplete(c._id)" style="font-size:12px;font-weight:600;color:var(--emerald-700);">✓ Docs ready</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Cost breakdown + submit --}}
            <div>
                <div class="nrh-card" style="padding:20px;position:sticky;top:80px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Cost Breakdown</h3>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--ink-500);">
                            <span>Scopes per candidate</span>
                            <span style="font-family:var(--font-mono);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--ink-500);">
                            <span>No. of candidates</span>
                            <span x-text="candidates.length"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;color:var(--ink-900);border-top:1px solid var(--line);padding-top:12px;margin-top:4px;">
                            <span>Total</span>
                            <span style="font-family:var(--font-mono);">MYR <span x-text="(cartTotal * candidates.length).toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div style="margin-top:14px;padding:10px 12px;background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);font-size:11px;color:var(--ink-500);">
                        Payment via monthly billing or direct bank transfer. Invoice will be issued at end of month.
                    </div>

                    <form method="POST" action="{{ route('client.request.submit') }}" @submit.prevent="submitForm($event)" style="margin-top:16px;">
                        @csrf
                        <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">
                        <input type="hidden" name="candidates_data" :value="JSON.stringify(candidates)">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="btn-primary"
                            style="width:100%;justify-content:center;"
                            :style="submitting ? 'opacity:0.5;cursor:not-allowed;' : ''"
                        >
                            <span x-show="!submitting">Submit Request</span>
                            <span x-show="submitting">Submitting...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;">
            <button @click="prevStep()" class="btn-ghost">
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
function newRequest() {
    return {
        step: 1,
        selectedCountry: {{ $countries->first()['id'] ?? 1 }},
        scopeTab: 'scopes',
        cart: [],
        candidates: [],
        uploads: [],     // { candidateId, docTypeId, file }
        newCandidate: { name: '', identity_type_id: '', identity_number: '', mobile: '', remarks: '' },
        candidateError: '',
        submitting: false,

        allScopes:   @json($scopes),
        allPackages: @json($packages),

        requiredDocTypes: [
            { id: 1, label: 'Consent Form', required: true },
            { id: 2, label: 'CV / Resume',  required: false },
            { id: 3, label: 'Extra Document', required: false },
        ],

        init() {},

        get filteredScopes() {
            return this.allScopes.filter(s => s.country_id === this.selectedCountry);
        },
        get filteredPackages() {
            return this.allPackages.filter(p => p.country_id === this.selectedCountry);
        },
        get cartTotal() {
            return this.cart.reduce((sum, i) => sum + i.price, 0);
        },

        isInCart(id) { return this.cart.some(i => i.id === id); },

        toggleScope(scope) {
            if (this.isInCart(scope.id)) {
                this.removeFromCart(scope.id);
            } else {
                this.cart.push({ ...scope });
            }
        },
        removeFromCart(id) { this.cart = this.cart.filter(i => i.id !== id); },
        clearCart()        { this.cart = []; },

        addPackage(pkg) {
            pkg.scope_ids.forEach(sid => {
                const scope = this.allScopes.find(s => s.id === sid);
                if (scope && !this.isInCart(sid)) { this.cart.push({ ...scope }); }
            });
        },

        addCandidate() {
            this.candidateError = '';
            if (!this.newCandidate.name.trim())            { this.candidateError = 'Name is required.'; return; }
            if (!this.newCandidate.identity_type_id)       { this.candidateError = 'Identity type is required.'; return; }
            if (!this.newCandidate.identity_number.trim()) { this.candidateError = 'Identity number is required.'; return; }
            const dup = this.candidates.find(c => c.identity_number === this.newCandidate.identity_number);
            if (dup) { this.candidateError = 'A candidate with this identity number already exists.'; return; }

            this.candidates.push({ ...this.newCandidate, _id: Date.now() });
            this.newCandidate = { name: '', identity_type_id: '', identity_number: '', mobile: '', remarks: '' };
        },
        removeCandidate(id) { this.candidates = this.candidates.filter(c => c._id !== id); },

        handleFileUpload(event, candidateId, docTypeId) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploads = this.uploads.filter(u => !(u.candidateId === candidateId && u.docTypeId === docTypeId));
            this.uploads.push({ candidateId, docTypeId, file, name: file.name });
        },
        getUploadedFile(candidateId, docTypeId) {
            return this.uploads.find(u => u.candidateId === candidateId && u.docTypeId === docTypeId) || null;
        },
        removeFile(candidateId, docTypeId) {
            this.uploads = this.uploads.filter(u => !(u.candidateId === candidateId && u.docTypeId === docTypeId));
        },
        candidateDocsComplete(candidateId) {
            return this.requiredDocTypes
                .filter(dt => dt.required)
                .every(dt => this.getUploadedFile(candidateId, dt.id));
        },

        nextStep() { if (this.step < 4) this.step++; },
        prevStep() { if (this.step > 1) this.step--; },

        submitForm(event) {
            this.submitting = true;
            event.target.submit();
        },
    };
}
</script>
@endpush

</x-client.layouts.app>
