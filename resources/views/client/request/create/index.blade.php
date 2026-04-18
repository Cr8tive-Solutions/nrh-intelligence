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
    class="max-w-6xl mx-auto"
>

    {{-- Step indicator --}}
    <div class="mb-8">
        <div class="flex items-center gap-0">
            @php
                $steps = [
                    ['num' => 1, 'label' => 'Select Scopes'],
                    ['num' => 2, 'label' => 'Add Candidates'],
                    ['num' => 3, 'label' => 'Upload Documents'],
                    ['num' => 4, 'label' => 'Review & Submit'],
                ];
            @endphp
            @foreach ($steps as $i => $s)
                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2 shrink-0">
                        <div
                            class="size-8 rounded-full flex items-center justify-center text-sm font-semibold transition-colors"
                            :class="{
                                'bg-brand-600 text-white': step === {{ $s['num'] }},
                                'bg-brand-600 text-white': step > {{ $s['num'] }},
                                'bg-slate-100 text-slate-400': step < {{ $s['num'] }}
                            }"
                        >
                            <template x-if="step > {{ $s['num'] }}">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                            </template>
                            <template x-if="step <= {{ $s['num'] }}">
                                <span>{{ $s['num'] }}</span>
                            </template>
                        </div>
                        <span
                            class="hidden sm:block text-sm font-medium transition-colors"
                            :class="step >= {{ $s['num'] }} ? 'text-slate-900' : 'text-slate-400'"
                        >{{ $s['label'] }}</span>
                    </div>
                    @if ($i < count($steps) - 1)
                        <div class="flex-1 mx-3 h-px bg-slate-200">
                            <div class="h-px bg-brand-600 transition-all duration-500" :style="step > {{ $s['num'] }} ? 'width:100%' : 'width:0%'"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── STEP 1: Select Scopes ── --}}
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Scope browser --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Country tabs --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Select Country</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($countries as $country)
                            <button
                                @click="selectedCountry = {{ $country['id'] }}"
                                :class="selectedCountry === {{ $country['id'] }}
                                    ? 'bg-brand-600 text-white border-brand-600'
                                    : 'bg-white text-slate-700 border-slate-200 hover:border-brand-300 hover:text-brand-600'"
                                class="flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors"
                            >
                                <span>{{ $country['flag'] }}</span>
                                <span>{{ $country['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tabs: Scopes / Packages / My Favourites --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="flex border-b border-slate-200 px-4">
                        @foreach (['scopes' => 'Verification Scopes', 'packages' => 'Packages', 'favourites' => 'My Favourites'] as $tab => $label)
                            <button
                                @click="scopeTab = '{{ $tab }}'"
                                :class="scopeTab === '{{ $tab }}' ? 'border-b-2 border-brand-600 text-brand-600' : 'text-slate-500 hover:text-slate-700'"
                                class="px-4 py-3 text-sm font-medium -mb-px transition-colors"
                            >{{ $label }}</button>
                        @endforeach
                    </div>

                    <div class="p-4">

                        {{-- Scopes tab --}}
                        <div x-show="scopeTab === 'scopes'">
                            <div class="space-y-2">
                                <template x-for="scope in filteredScopes" :key="scope.id">
                                    <div
                                        :class="isInCart(scope.id) ? 'border-brand-300 bg-brand-50' : 'border-slate-200 hover:border-slate-300'"
                                        class="flex items-center justify-between rounded-lg border p-3.5 transition-colors"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900" x-text="scope.name"></p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                    <span x-text="scope.turnaround"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 ml-3 shrink-0">
                                            <span class="text-sm font-semibold text-slate-900">MYR <span x-text="scope.price.toFixed(2)"></span></span>
                                            <button
                                                @click="toggleScope(scope)"
                                                :class="isInCart(scope.id) ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-brand-600 text-white hover:bg-brand-700'"
                                                class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition-colors"
                                            >
                                                <span x-text="isInCart(scope.id) ? 'Remove' : 'Add'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredScopes.length === 0">
                                    <p class="py-8 text-center text-sm text-slate-400">No scopes available for this country.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Packages tab --}}
                        <div x-show="scopeTab === 'packages'">
                            <div class="space-y-2">
                                <template x-for="pkg in filteredPackages" :key="pkg.id">
                                    <div class="flex items-start justify-between rounded-lg border border-slate-200 hover:border-slate-300 p-3.5 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900" x-text="pkg.name"></p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                <span x-text="pkg.scope_ids.length"></span> scopes included
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3 ml-3 shrink-0">
                                            <span class="text-sm font-semibold text-slate-900">MYR <span x-text="pkg.price.toFixed(2)"></span></span>
                                            <button
                                                @click="addPackage(pkg)"
                                                class="rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-700 transition-colors"
                                            >Add All</button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredPackages.length === 0">
                                    <p class="py-8 text-center text-sm text-slate-400">No packages available for this country.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Favourites tab --}}
                        <div x-show="scopeTab === 'favourites'">
                            <p class="py-8 text-center text-sm text-slate-400">
                                No saved favourites yet.
                                <a href="{{ route('client.settings.packages') }}" class="text-brand-600 hover:text-brand-700 font-medium">Create one in Settings →</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Cart --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 sticky top-20">
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">Selected Scopes</h3>
                        <span class="rounded-full bg-brand-100 text-brand-700 text-xs font-semibold px-2 py-0.5" x-text="cart.length"></span>
                    </div>
                    <div class="p-4">
                        <template x-if="cart.length === 0">
                            <p class="py-6 text-center text-sm text-slate-400">No scopes selected yet.</p>
                        </template>
                        <div class="space-y-2">
                            <template x-for="item in cart" :key="item.id">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-slate-800 leading-snug" x-text="item.name"></p>
                                        <p class="text-xs text-slate-400 mt-0.5">MYR <span x-text="item.price.toFixed(2)"></span></p>
                                    </div>
                                    <button @click="removeFromCart(item.id)" class="text-slate-300 hover:text-red-500 transition-colors shrink-0 mt-0.5">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <template x-if="cart.length > 0">
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Per candidate</span>
                                    <span>MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                                </div>
                                <button @click="clearCart()" class="mt-2 text-xs text-slate-400 hover:text-red-500 transition-colors">Clear all</button>
                            </div>
                        </template>
                    </div>

                    <div class="px-4 pb-4">
                        <button
                            @click="nextStep()"
                            :disabled="cart.length === 0"
                            :class="cart.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-brand-700'"
                            class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors"
                        >
                            Continue
                            <svg class="inline-block ml-1 size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Add candidate form --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Add Candidate</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input x-model="newCandidate.name" type="text" placeholder="As per identity document"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Identity Type <span class="text-red-500">*</span></label>
                            <select x-model="newCandidate.identity_type_id"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors bg-white">
                                <option value="">Select type</option>
                                @foreach ($identityTypes as $type)
                                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Identity Number <span class="text-red-500">*</span></label>
                            <input x-model="newCandidate.identity_number" type="text" placeholder="e.g. 900101-14-5678"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Mobile Number</label>
                            <input x-model="newCandidate.mobile" type="tel" placeholder="+60 12 345 6789"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Remarks</label>
                            <input x-model="newCandidate.remarks" type="text" placeholder="Optional"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p x-show="candidateError" class="text-sm text-red-600" x-text="candidateError"></p>
                        <div class="ml-auto">
                            <button @click="addCandidate()"
                                class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                                Add Candidate
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Candidates table --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">
                            Candidates
                            <span class="ml-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold px-2 py-0.5" x-text="candidates.length"></span>
                        </h3>
                    </div>
                    <template x-if="candidates.length === 0">
                        <p class="py-10 text-center text-sm text-slate-400">No candidates added yet.</p>
                    </template>
                    <template x-if="candidates.length > 0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">#</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Name</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Identity</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Mobile</th>
                                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wide">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(c, i) in candidates" :key="c._id">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-3 text-slate-400 text-xs" x-text="i + 1"></td>
                                            <td class="px-5 py-3 font-medium text-slate-900" x-text="c.name"></td>
                                            <td class="px-5 py-3 text-slate-500 text-xs font-mono" x-text="c.identity_number"></td>
                                            <td class="px-5 py-3 text-slate-500 text-xs" x-text="c.mobile || '—'"></td>
                                            <td class="px-5 py-3 text-right">
                                                <button @click="removeCandidate(c._id)" class="text-xs font-medium text-red-500 hover:text-red-700 transition-colors">Remove</button>
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
                <div class="bg-white rounded-xl border border-slate-200 sticky top-20 p-4">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Scope Summary</h3>
                    <div class="space-y-1.5 mb-4">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-600 leading-snug" x-text="item.name"></span>
                                <span class="text-slate-500 shrink-0 ml-2">MYR <span x-text="item.price.toFixed(2)"></span></span>
                            </div>
                        </template>
                    </div>
                    <div class="border-t border-slate-100 pt-3 space-y-1 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Per candidate</span>
                            <span class="font-medium">MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Candidates</span>
                            <span class="font-medium" x-text="candidates.length"></span>
                        </div>
                        <div class="flex justify-between font-semibold text-slate-900 text-sm pt-1 border-t border-slate-100 mt-1">
                            <span>Est. Total</span>
                            <span>MYR <span x-text="(cartTotal * candidates.length).toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="mt-6 flex items-center justify-between">
            <button @click="prevStep()" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
            <button
                @click="nextStep()"
                :disabled="candidates.length === 0"
                :class="candidates.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-brand-700'"
                class="flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors"
            >
                Continue
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── STEP 3: Upload Documents ── --}}
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="space-y-4">

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="size-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                <div class="text-sm text-amber-800">
                    <p class="font-medium">Documents required</p>
                    <p class="mt-0.5 text-xs text-amber-700">Upload the required documents for each candidate. Accepted formats: PDF, DOC, DOCX, JPG, PNG (max 5MB each).</p>
                </div>
            </div>

            <template x-for="(candidate, ci) in candidates" :key="candidate._id">
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-slate-100">
                        <div class="size-7 rounded-full bg-brand-100 flex items-center justify-center text-xs font-semibold text-brand-700" x-text="ci + 1"></div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900" x-text="candidate.name"></p>
                            <p class="text-xs text-slate-400 font-mono" x-text="candidate.identity_number"></p>
                        </div>
                        <div class="ml-auto">
                            <span x-show="candidateDocsComplete(candidate._id)" class="flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Complete
                            </span>
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <template x-for="docType in requiredDocTypes" :key="docType.id">
                            <div
                                class="relative rounded-xl border-2 border-dashed p-4 text-center transition-colors cursor-pointer"
                                :class="getUploadedFile(candidate._id, docType.id) ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 hover:border-brand-300 hover:bg-brand-50'"
                                @click="$refs['file_' + candidate._id + '_' + docType.id].click()"
                            >
                                <input
                                    type="file"
                                    class="hidden"
                                    :ref="'file_' + candidate._id + '_' + docType.id"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    @change="handleFileUpload($event, candidate._id, docType.id)"
                                />
                                <template x-if="!getUploadedFile(candidate._id, docType.id)">
                                    <div>
                                        <svg class="mx-auto size-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                        </svg>
                                        <p class="text-xs font-medium text-slate-700" x-text="docType.label"></p>
                                        <p class="text-xs text-slate-400 mt-0.5">Click to upload</p>
                                        <span x-show="docType.required" class="mt-1 inline-block rounded-full bg-red-50 border border-red-200 px-2 py-0.5 text-xs text-red-600">Required</span>
                                    </div>
                                </template>
                                <template x-if="getUploadedFile(candidate._id, docType.id)">
                                    <div>
                                        <svg class="mx-auto size-8 text-emerald-500 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <p class="text-xs font-medium text-emerald-700 truncate" x-text="getUploadedFile(candidate._id, docType.id).name"></p>
                                        <button @click.stop="removeFile(candidate._id, docType.id)" class="mt-1 text-xs text-slate-400 hover:text-red-500 transition-colors">Remove</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <button @click="prevStep()" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </button>
            <button @click="nextStep()" class="flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                Continue
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── STEP 4: Review & Submit ── --}}
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-4">

                {{-- Scopes --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">Selected Scopes</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex items-center justify-between px-5 py-3">
                                <p class="text-sm text-slate-800" x-text="item.name"></p>
                                <p class="text-sm font-medium text-slate-900">MYR <span x-text="item.price.toFixed(2)"></span></p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Candidates --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">
                            Candidates
                            <span class="ml-1.5 text-slate-400 font-normal" x-text="'(' + candidates.length + ')'"></span>
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <template x-for="(c, i) in candidates" :key="c._id">
                            <div class="flex items-center gap-4 px-5 py-3">
                                <div class="size-6 rounded-full bg-brand-100 flex items-center justify-center text-xs font-semibold text-brand-700 shrink-0" x-text="i + 1"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900" x-text="c.name"></p>
                                    <p class="text-xs text-slate-400 font-mono" x-text="c.identity_number"></p>
                                </div>
                                <span x-show="candidateDocsComplete(c._id)" class="text-xs text-emerald-600 font-medium">✓ Docs ready</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Cost breakdown --}}
            <div>
                <div class="bg-white rounded-xl border border-slate-200 p-5 sticky top-20">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Cost Breakdown</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Scopes per candidate</span>
                            <span>MYR <span x-text="cartTotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>No. of candidates</span>
                            <span x-text="candidates.length"></span>
                        </div>
                        <div class="border-t border-slate-100 pt-3 mt-2 flex justify-between font-bold text-slate-900 text-base">
                            <span>Total</span>
                            <span>MYR <span x-text="(cartTotal * candidates.length).toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2.5 text-xs text-slate-500">
                        Payment via monthly billing or direct bank transfer. Invoice will be issued at end of month.
                    </div>

                    <form method="POST" action="{{ route('client.request.submit') }}" @submit.prevent="submitForm($event)">
                        @csrf
                        <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">
                        <input type="hidden" name="candidates_data" :value="JSON.stringify(candidates)">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="mt-4 w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-3 focus:ring-brand-500/30 transition-colors disabled:opacity-50"
                        >
                            <span x-show="!submitting">Submit Request</span>
                            <span x-show="submitting">Submitting...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button @click="prevStep()" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
