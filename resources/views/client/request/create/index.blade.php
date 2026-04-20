@php
    $typeLabels = [
        'employment_malaysia' => [
            'title'  => 'Malaysia Employment Screening',
            'kicker' => 'Employment Screening',
            'h1'     => ['prefix' => '🇲🇾 Malaysia', 'em' => 'Screening'],
            'desc'   => 'Background verification for candidates based in Malaysia. Select checks from our full Malaysian scope library.',
        ],
        'employment_global' => [
            'title'  => 'Global Employment Screening',
            'kicker' => 'Employment Screening',
            'h1'     => ['prefix' => 'Global', 'em' => 'Screening'],
            'desc'   => 'Multi-country background verification. Choose a country, then browse and select verification scopes.',
        ],
    ];
    $typeInfo = $typeLabels[$screeningType ?? 'employment_global'] ?? $typeLabels['employment_global'];
    $lockedCountry = $lockedCountryId ? $countries->firstWhere('id', $lockedCountryId) : null;
@endphp
<x-client.layouts.app pageTitle="{{ $typeInfo['title'] }}">

{{-- Page header --}}
<div class="page-head">
    <div>
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:6px;">{{ $typeInfo['kicker'] }}</div>
        <h1>{{ $typeInfo['h1']['prefix'] }} <em>{{ $typeInfo['h1']['em'] }}</em></h1>
        <div class="sub">{{ $typeInfo['desc'] }}</div>
    </div>
</div>

<div x-data="newRequest()" x-init="init()">

    {{-- ── Step indicator ── --}}
    <div style="display:flex;align-items:center;padding:16px 20px;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);margin-bottom:24px;">
        @php
            $steps = [
                ['num' => 1, 'label' => 'Select Scopes',    'desc' => 'Pick checks'],
                ['num' => 2, 'label' => 'Add Candidates',   'desc' => 'Enter subjects'],
                ['num' => 3, 'label' => 'Upload Documents', 'desc' => 'Attach files'],
                ['num' => 4, 'label' => 'Review & Submit',  'desc' => 'Confirm order'],
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
                        <div style="font-size:11px;color:var(--ink-400);">{{ $s['desc'] }}</div>
                    </div>
                </div>

                {{-- Connector --}}
                @if ($i < count($steps) - 1)
                    <div style="flex:1;height:1px;margin:0 16px;position:relative;overflow:hidden;background:var(--line);">
                        <div style="position:absolute;inset:0;background:var(--emerald-600);transition:transform 400ms ease;transform-origin:left;"
                            :style="step > {{ $s['num'] }} ? 'transform:scaleX(1)' : 'transform:scaleX(0)'"></div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         STEP 1 — Select Scopes
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="step === 1"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0">

        <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

            {{-- Left: browser --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Country picker (global only) --}}
                @if(!$lockedCountryId)
                <div class="card" style="padding:18px 20px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:10px;">Select Country</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach ($countries as $country)
                            <button @click="selectedCountry = {{ $country['id'] }}"
                                style="display:flex;align-items:center;gap:8px;border-radius:var(--radius);border:1px solid var(--line);padding:8px 14px;font-size:13px;font-weight:500;cursor:pointer;transition:all 120ms;font-family:var(--font-ui);"
                                :style="selectedCountry === {{ $country['id'] }}
                                    ? 'background:var(--emerald-700);color:white;border-color:var(--emerald-700);'
                                    : 'background:var(--card);color:var(--ink-700);'">
                                <span>{{ $country['flag'] }}</span>
                                <span>{{ $country['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Scopes / Packages / Favourites tabs --}}
                <div class="card">
                    <div style="display:flex;align-items:center;border-bottom:1px solid var(--line);padding:0 4px;">
                        @if($lockedCountry)
                            <div style="display:flex;align-items:center;gap:6px;padding:12px 12px 12px 8px;border-right:1px solid var(--line);margin-right:4px;flex-shrink:0;">
                                <span style="font-size:16px;">{{ $lockedCountry['flag'] }}</span>
                                <span style="font-size:12px;font-weight:600;color:var(--ink-700);">{{ $lockedCountry['name'] }}</span>
                            </div>
                        @endif
                        @foreach (['scopes' => 'Verification Scopes', 'packages' => 'Packages', 'favourites' => 'My Favourites'] as $tab => $label)
                            <button @click="scopeTab = '{{ $tab }}'"
                                style="padding:14px;font-size:13px;font-weight:500;cursor:pointer;border:none;background:none;border-bottom:2px solid transparent;margin-bottom:-1px;white-space:nowrap;transition:color 120ms,border-color 120ms;font-family:var(--font-ui);"
                                :style="scopeTab === '{{ $tab }}' ? 'border-bottom-color:var(--emerald-600);color:var(--emerald-700);font-weight:600;' : 'color:var(--ink-500);'"
                            >{{ $label }}</button>
                        @endforeach
                    </div>

                    <div style="padding:16px;display:flex;flex-direction:column;gap:8px;">

                        {{-- Scopes tab --}}
                        <div x-show="scopeTab === 'scopes'">
                            <template x-for="scope in filteredScopes" :key="scope.id">
                                <div style="display:flex;align-items:center;justify-content:space-between;border-radius:var(--radius);border:1px solid var(--line);padding:14px 16px;margin-bottom:8px;transition:border-color 120ms,background 120ms;"
                                    :style="isInCart(scope.id) ? 'border-color:rgba(5,150,105,0.35);background:rgba(5,150,105,0.03);' : ''">
                                    <div style="flex:1;min-width:0;padding-right:16px;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="scope.name"></p>
                                            <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;color:var(--ink-400);font-family:var(--font-mono);background:var(--paper);border:1px solid var(--line);border-radius:999px;padding:2px 7px;flex-shrink:0;">
                                                <svg style="width:9px;height:9px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                <span x-text="scope.turnaround"></span>
                                            </span>
                                        </div>
                                        <p style="font-size:12px;color:var(--ink-500);margin:0;line-height:1.5;" x-text="scope.description || ''"></p>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                                        <div style="text-align:right;">
                                            <div style="font-size:13px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="scope.price.toFixed(2)"></span></div>
                                            <div style="font-size:10px;color:var(--ink-400);">per candidate</div>
                                        </div>
                                        <button @click="toggleScope(scope)"
                                            style="width:32px;height:32px;border-radius:var(--radius);border:1px solid;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 120ms;flex-shrink:0;"
                                            :style="isInCart(scope.id)
                                                ? 'background:rgba(196,69,58,0.06);color:var(--danger);border-color:rgba(196,69,58,0.25);'
                                                : 'background:var(--emerald-700);color:white;border-color:var(--emerald-700);'">
                                            <template x-if="isInCart(scope.id)">
                                                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                            </template>
                                            <template x-if="!isInCart(scope.id)">
                                                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="filteredScopes.length === 0">
                                <p style="padding:40px 0;text-align:center;font-size:13px;color:var(--ink-400);">No scopes available for this country.</p>
                            </template>
                        </div>

                        {{-- Packages tab --}}
                        <div x-show="scopeTab === 'packages'">
                            <template x-for="pkg in filteredPackages" :key="pkg.id">
                                <div style="display:flex;align-items:center;justify-content:space-between;border-radius:var(--radius);border:1px solid var(--line);padding:14px 16px;margin-bottom:8px;">
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 2px;" x-text="pkg.name"></p>
                                        <p style="font-size:12px;color:var(--ink-500);margin:0;"><span x-text="pkg.scope_ids.length"></span> scopes included</p>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
                                        <div style="text-align:right;">
                                            <div style="font-size:13px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="pkg.price.toFixed(2)"></span></div>
                                            <div style="font-size:10px;color:var(--ink-400);">per candidate</div>
                                        </div>
                                        <button @click="addPackage(pkg)" class="btn btn-primary" style="font-size:11px;padding:6px 14px;">Add All</button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="filteredPackages.length === 0">
                                <p style="padding:40px 0;text-align:center;font-size:13px;color:var(--ink-400);">No packages for this country.</p>
                            </template>
                        </div>

                        {{-- Favourites tab --}}
                        <div x-show="scopeTab === 'favourites'">
                            <div style="padding:40px 0;text-align:center;">
                                <svg style="width:36px;height:36px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/></svg>
                                <p style="font-size:13px;color:var(--ink-400);margin:0 0 12px;">No saved favourites yet.</p>
                                <a href="{{ route('client.settings.packages') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);text-decoration:none;">Create one in Settings →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Cart --}}
            <div style="position:sticky;top:68px;">
                <div class="card">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">Selected Scopes</h3>
                        <span style="border-radius:999px;background:rgba(5,150,105,0.1);color:var(--emerald-700);font-size:11px;font-weight:700;padding:2px 8px;font-family:var(--font-mono);" x-text="cart.length"></span>
                    </div>
                    <div style="padding:14px 18px;min-height:80px;">
                        <template x-if="cart.length === 0">
                            <p style="padding:20px 0;text-align:center;font-size:12px;color:var(--ink-400);">No scopes selected yet.</p>
                        </template>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <template x-for="item in cart" :key="item.id">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-size:12px;font-weight:600;color:var(--ink-800);line-height:1.4;margin:0;" x-text="item.name"></p>
                                        <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;font-family:var(--font-mono);">MYR <span x-text="item.price.toFixed(2)"></span></p>
                                    </div>
                                    <button @click="removeFromCart(item.id)"
                                        style="color:var(--ink-300);background:none;border:none;cursor:pointer;flex-shrink:0;padding:2px;"
                                        onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-300)'">
                                        <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <template x-if="cart.length > 0">
                            <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--line);">
                                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);margin-bottom:6px;">
                                    <span>Per candidate</span>
                                    <span style="font-weight:600;font-family:var(--font-mono);color:var(--ink-900);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                                </div>
                                <button @click="clearCart()" style="font-size:11px;color:var(--ink-400);background:none;border:none;cursor:pointer;padding:0;"
                                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-400)'">Clear all</button>
                            </div>
                        </template>
                    </div>
                    <div style="padding:0 18px 18px;">
                        <button @click="nextStep()" :disabled="cart.length === 0" class="btn btn-primary"
                            style="width:100%;justify-content:center;"
                            :style="cart.length === 0 ? 'opacity:0.35;cursor:not-allowed;' : ''">
                            Continue to Candidates
                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         STEP 2 — Add Candidates
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="step === 2"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0">

        <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

            {{-- Left: form + table --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Add candidate form --}}
                <div class="card">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">Add Candidate</h3>
                        <p style="font-size:12px;color:var(--ink-400);margin:3px 0 0;">Each candidate will be screened against all selected scopes.</p>
                    </div>
                    <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                        <div class="field-row field-row-2">
                            <div class="field" style="grid-column:span 2;">
                                <label>Full Name <span style="color:var(--danger)">*</span></label>
                                <input x-model="newCandidate.name" type="text" placeholder="As per identity document"
                                    @keydown.enter.prevent="addCandidate()" />
                            </div>
                            <div class="field">
                                <label>Identity Type <span style="color:var(--danger)">*</span></label>
                                <select x-model="newCandidate.identity_type_id">
                                    <option value="">Select type</option>
                                    @foreach ($identityTypes as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Identity Number <span style="color:var(--danger)">*</span></label>
                                <input x-model="newCandidate.identity_number" type="text" placeholder="e.g. 900101-14-5678"
                                    @keydown.enter.prevent="addCandidate()" />
                            </div>
                            <div class="field">
                                <label>Mobile Number</label>
                                <input x-model="newCandidate.mobile" type="tel" placeholder="+60 12 345 6789" />
                            </div>
                            <div class="field">
                                <label>Remarks</label>
                                <input x-model="newCandidate.remarks" type="text" placeholder="Optional note" />
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
                            <p x-show="candidateError" style="font-size:12px;color:var(--danger);margin:0;" x-text="candidateError"></p>
                            <button @click="addCandidate()" class="btn btn-primary" style="margin-left:auto;">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Add Candidate
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Candidates table --}}
                <div class="card">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--line);">
                        <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">
                            Candidates
                            <span style="margin-left:6px;border-radius:999px;background:var(--paper);border:1px solid var(--line);font-size:11px;font-weight:700;padding:2px 8px;color:var(--ink-500);font-family:var(--font-mono);" x-text="candidates.length"></span>
                        </h3>
                    </div>
                    <template x-if="candidates.length === 0">
                        <div style="padding:40px 20px;text-align:center;">
                            <svg style="width:32px;height:32px;color:var(--ink-200);margin:0 auto 10px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            <p style="font-size:13px;color:var(--ink-400);margin:0;">No candidates added yet.</p>
                        </div>
                    </template>
                    <template x-if="candidates.length > 0">
                        <div style="overflow-x:auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Name</th>
                                        <th>Identity</th>
                                        <th>Mobile</th>
                                        <th style="width:80px;"></th>
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
                                                <button @click="removeCandidate(c._id)"
                                                    style="font-size:12px;font-weight:600;color:var(--danger);background:none;border:none;cursor:pointer;font-family:var(--font-ui);"
                                                    onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Remove</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: scope summary + nav --}}
            <div style="position:sticky;top:68px;display:flex;flex-direction:column;gap:12px;">
                <div class="card" style="padding:18px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:10px;">Scope Summary</div>
                    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
                        <template x-for="item in cart" :key="item.id">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;font-size:12px;gap:8px;">
                                <span style="color:var(--ink-600);line-height:1.4;flex:1;min-width:0;" x-text="item.name"></span>
                                <span style="color:var(--ink-500);flex-shrink:0;font-family:var(--font-mono);">MYR <span x-text="item.price.toFixed(2)"></span></span>
                            </div>
                        </template>
                    </div>
                    <div style="border-top:1px solid var(--line);padding-top:12px;display:flex;flex-direction:column;gap:6px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>Per candidate</span>
                            <span style="font-weight:600;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>Candidates</span>
                            <span style="font-weight:600;color:var(--ink-900);" x-text="candidates.length || '—'"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:var(--ink-900);border-top:1px solid var(--line);padding-top:10px;margin-top:2px;">
                            <span>Est. Total</span>
                            <span style="font-family:var(--font-mono);">MYR <span x-text="(cartTotal * (candidates.length || 0)).toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <button @click="nextStep()" :disabled="candidates.length === 0" class="btn btn-primary"
                    style="width:100%;justify-content:center;"
                    :style="candidates.length === 0 ? 'opacity:0.35;cursor:not-allowed;' : ''">
                    Continue to Documents
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
                <button @click="prevStep()" class="btn btn-ghost" style="width:100%;justify-content:center;">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         STEP 3 — Upload Documents
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="step === 3"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0">

        <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
            <div style="display:flex;flex-direction:column;gap:14px;">

                {{-- Info banner --}}
                <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;background:rgba(212,175,55,0.06);border:1px solid rgba(212,175,55,0.25);border-radius:var(--radius);">
                    <svg style="width:15px;height:15px;color:var(--gold-600);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    <p style="font-size:12px;color:var(--ink-600);margin:0;line-height:1.6;">Upload the required documents per candidate. Accepted formats: PDF, DOC, DOCX, JPG, PNG — max 5 MB each.</p>
                </div>

                {{-- Per-candidate upload cards --}}
                <template x-for="(candidate, ci) in candidates" :key="candidate._id">
                    <div class="card">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--line);">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:28px;height:28px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--emerald-700);flex-shrink:0;" x-text="ci + 1"></div>
                                <div>
                                    <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="candidate.name"></p>
                                    <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:2px 0 0;" x-text="candidate.identity_number"></p>
                                </div>
                            </div>
                            <span x-show="candidateDocsComplete(candidate._id)" class="pill pill-clear">
                                <span class="dot"></span>Docs ready
                            </span>
                        </div>
                        <div style="padding:16px 20px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                            <template x-for="docType in requiredDocTypes" :key="docType.id">
                                <div style="border-radius:var(--radius);border:2px dashed var(--line);padding:20px 12px;text-align:center;cursor:pointer;transition:border-color 120ms,background 120ms;"
                                    :style="getUploadedFile(candidate._id, docType.id) ? 'border-color:rgba(5,150,105,0.4);background:rgba(5,150,105,0.04);border-style:solid;' : ''"
                                    @click="$refs['file_' + candidate._id + '_' + docType.id].click()"
                                    onmouseover="if(!this.style.borderColor.includes('150')){this.style.borderColor='var(--emerald-400)';}"
                                    onmouseout="if(!this.style.borderColor.includes('150')){this.style.borderColor='var(--line)';}">
                                    <input type="file" style="display:none;"
                                        :ref="'file_' + candidate._id + '_' + docType.id"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                        @change="handleFileUpload($event, candidate._id, docType.id)" />
                                    <template x-if="!getUploadedFile(candidate._id, docType.id)">
                                        <div>
                                            <svg style="width:24px;height:24px;color:var(--ink-200);margin:0 auto 8px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                            </svg>
                                            <p style="font-size:12px;font-weight:600;color:var(--ink-700);margin:0;" x-text="docType.label"></p>
                                            <p style="font-size:11px;color:var(--ink-400);margin:3px 0 0;">Click to upload</p>
                                            <span x-show="docType.required" style="display:inline-block;margin-top:6px;font-size:10px;font-weight:600;color:var(--gold-700);background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:999px;padding:2px 8px;">Required</span>
                                        </div>
                                    </template>
                                    <template x-if="getUploadedFile(candidate._id, docType.id)">
                                        <div>
                                            <svg style="width:24px;height:24px;color:var(--emerald-600);margin:0 auto 8px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                            <p style="font-size:12px;font-weight:600;color:var(--emerald-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;" x-text="getUploadedFile(candidate._id, docType.id).name"></p>
                                            <button @click.stop="removeFile(candidate._id, docType.id)"
                                                style="font-size:11px;color:var(--ink-400);background:none;border:none;cursor:pointer;margin-top:4px;font-family:var(--font-ui);"
                                                onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--ink-400)'">Remove</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Right: nav --}}
            <div style="position:sticky;top:68px;display:flex;flex-direction:column;gap:12px;">
                <div class="card" style="padding:18px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:10px;">Upload Progress</div>
                    <template x-for="(c, ci) in candidates" :key="c._id">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--line);">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:20px;height:20px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--emerald-700);" x-text="ci + 1"></div>
                                <span style="font-size:12px;color:var(--ink-700);font-weight:500;" x-text="c.name"></span>
                            </div>
                            <template x-if="candidateDocsComplete(c._id)">
                                <svg style="width:14px;height:14px;color:var(--emerald-600);" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </template>
                            <template x-if="!candidateDocsComplete(c._id)">
                                <span style="font-size:11px;color:var(--ink-400);">Pending</span>
                            </template>
                        </div>
                    </template>
                </div>
                <button @click="nextStep()" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Continue to Review
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
                <button @click="prevStep()" class="btn btn-ghost" style="width:100%;justify-content:center;">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         STEP 4 — Review & Submit
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="step === 4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0">

        <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Scopes --}}
                <div class="card">
                    <div class="card-head">
                        <h3>Verification Scopes</h3>
                        <span style="font-family:var(--font-mono);font-size:11px;color:var(--ink-500);" x-text="cart.length + ' selected'"></span>
                    </div>
                    <template x-for="item in cart" :key="item.id">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;border-bottom:1px solid var(--line);gap:12px;">
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:600;color:var(--ink-800);margin:0;" x-text="item.name"></p>
                                <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;display:flex;align-items:center;gap:3px;">
                                    <svg style="width:9px;height:9px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    <span x-text="item.turnaround"></span>
                                </p>
                            </div>
                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);margin:0;flex-shrink:0;">MYR <span x-text="item.price.toFixed(2)"></span></p>
                        </div>
                    </template>
                </div>

                {{-- Candidates --}}
                <div class="card">
                    <div class="card-head">
                        <h3>Candidates</h3>
                        <span style="font-family:var(--font-mono);font-size:11px;color:var(--ink-500);" x-text="candidates.length + ' added'"></span>
                    </div>
                    <template x-for="(c, i) in candidates" :key="c._id">
                        <div style="display:flex;align-items:center;gap:12px;padding:12px 24px;border-bottom:1px solid var(--line);">
                            <div style="width:26px;height:26px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--emerald-700);flex-shrink:0;" x-text="i + 1"></div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;" x-text="c.name"></p>
                                <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:2px 0 0;" x-text="c.identity_number"></p>
                            </div>
                            <template x-if="candidateDocsComplete(c._id)">
                                <span style="font-size:11px;font-weight:600;color:var(--emerald-700);display:flex;align-items:center;gap:4px;">
                                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Docs ready
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: cost + submit --}}
            <div style="position:sticky;top:68px;display:flex;flex-direction:column;gap:12px;">
                <div class="card" style="padding:20px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:14px;">Cost Breakdown</div>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>Scopes per candidate</span>
                            <span style="font-family:var(--font-mono);font-weight:600;color:var(--ink-700);">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-500);">
                            <span>No. of candidates</span>
                            <span style="font-weight:600;color:var(--ink-700);" x-text="candidates.length"></span>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin-bottom:14px;">
                        <span style="font-size:14px;font-weight:700;color:var(--ink-900);">Total</span>
                        <span style="font-size:16px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">MYR <span x-text="(cartTotal * candidates.length).toFixed(2)"></span></span>
                    </div>
                    <p style="font-size:11px;color:var(--ink-400);line-height:1.6;margin:0 0 16px;">Payment via monthly billing or direct bank transfer. Invoice issued at end of month.</p>

                    <form method="POST" action="{{ route('client.request.submit') }}" @submit.prevent="submitForm($event)">
                        @csrf
                        <input type="hidden" name="screening_type" value="{{ $screeningType ?? 'employment_global' }}">
                        <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">
                        <input type="hidden" name="candidates_data" :value="JSON.stringify(candidates)">
                        <button type="submit" :disabled="submitting" class="btn btn-primary"
                            style="width:100%;justify-content:center;"
                            :style="submitting ? 'opacity:0.5;cursor:not-allowed;' : ''">
                            <template x-if="!submitting">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                                    Submit Request
                                </span>
                            </template>
                            <template x-if="submitting">
                                <span>Submitting…</span>
                            </template>
                        </button>
                    </form>
                </div>

                <button @click="prevStep()" class="btn btn-ghost" style="width:100%;justify-content:center;">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function newRequest() {
    return {
        step: 1,
        selectedCountry: {{ $lockedCountryId ?? ($countries->first()['id'] ?? 1) }},
        scopeTab: 'scopes',
        cart: [],
        candidates: [],
        uploads: [],
        newCandidate: { name: '', identity_type_id: '', identity_number: '', mobile: '', remarks: '' },
        candidateError: '',
        submitting: false,

        allScopes:   @json($scopes),
        allPackages: @json($packages),

        requiredDocTypes: [
            { id: 1, label: 'Consent Form',    required: true  },
            { id: 2, label: 'CV / Resume',     required: false },
            { id: 3, label: 'Extra Document',  required: false },
        ],

        init() {},

        get filteredScopes()   { return this.allScopes.filter(s => s.country_id === this.selectedCountry); },
        get filteredPackages() { return this.allPackages.filter(p => p.country_id === this.selectedCountry); },
        get cartTotal()        { return this.cart.reduce((sum, i) => sum + i.price, 0); },

        isInCart(id) { return this.cart.some(i => i.id === id); },

        toggleScope(scope) {
            this.isInCart(scope.id) ? this.removeFromCart(scope.id) : this.cart.push({ ...scope });
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
            if (!this.newCandidate.name.trim())            { this.candidateError = 'Full name is required.'; return; }
            if (!this.newCandidate.identity_type_id)       { this.candidateError = 'Identity type is required.'; return; }
            if (!this.newCandidate.identity_number.trim()) { this.candidateError = 'Identity number is required.'; return; }
            if (this.candidates.some(c => c.identity_number === this.newCandidate.identity_number)) {
                this.candidateError = 'A candidate with this identity number already exists.'; return;
            }
            this.candidates.push({ ...this.newCandidate, _id: Date.now() });
            this.newCandidate = { name: '', identity_type_id: '', identity_number: '', mobile: '', remarks: '' };
        },
        removeCandidate(id) { this.candidates = this.candidates.filter(c => c._id !== id); },

        handleFileUpload(event, candidateId, docTypeId) {
            const file = event.target.files[0];
            if (!file) { return; }
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

        nextStep() { if (this.step < 4) { this.step++; } },
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
