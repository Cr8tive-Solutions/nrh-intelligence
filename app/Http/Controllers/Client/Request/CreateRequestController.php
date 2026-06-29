<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\CandidateDocument;
use App\Models\ConsentRecord;
use App\Models\Country;
use App\Models\Customer;
use App\Models\IdentityType;
use App\Models\Package;
use App\Models\RequestCandidate;
use App\Models\ScopeType;
use App\Models\ScreeningRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CreateRequestController extends Controller
{
    // ── Employment Screening ──────────────────────────────────────────────

    public function index()
    {
        return $this->employmentView(null, 'employment_global');
    }

    public function malaysia()
    {
        return $this->employmentView(1, 'employment_malaysia');
    }

    public function global()
    {
        return $this->employmentView(null, 'employment_global');
    }

    // ── Due Diligence ─────────────────────────────────────────────────────

    public function kyc()
    {
        return $this->dueDiligenceView('kyc');
    }

    public function kyb()
    {
        return $this->dueDiligenceView('kyb');
    }

    public function kys()
    {
        return $this->dueDiligenceView('kys');
    }

    // ── Submit ────────────────────────────────────────────────────────────

    public function submit(Request $request)
    {
        $cart = json_decode($request->input('cart_data', '[]'), true) ?: [];
        $candidates = json_decode($request->input('candidates_data', '[]'), true) ?: [];
        $type = $request->input('screening_type', 'employment_global');

        $scopeIds = collect($cart)->pluck('id')->filter()->values()->all();

        // Per-scope required documents (set by admin). The union across all selected
        // scopes is what every candidate must upload. Falls back to the legacy
        // consent flow when no scope declares any required documents.
        $requiredDocs = $this->requiredDocsFor($scopeIds);

        // Either checkbox consent OR a complete set of uploaded documents — never both, never neither.
        if (! empty($requiredDocs)) {
            $this->validateDocumentUploads($request, count($candidates), $requiredDocs);
        } else {
            $request->validate(['consent' => ['accepted']], [
                'consent.accepted' => 'PDPA consent must be accepted before submitting the request.',
            ]);
        }

        $customerId = session('client_customer_id', 1);
        $userId = session('client_user_id', 1);

        $seq = ScreeningRequest::count() + 1;
        $reference = 'REQ-'.now()->format('Y').'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

        $createdRequest = DB::transaction(function () use ($request, $customerId, $userId, $reference, $type, $scopeIds, $candidates, $requiredDocs) {
            $screeningRequest = ScreeningRequest::create([
                'customer_id' => $customerId,
                'customer_user_id' => $userId,
                'reference' => $reference,
                'status' => 'new',
                'type' => $type,
            ]);

            foreach ($candidates as $idx => $c) {
                $candidate = RequestCandidate::create([
                    'screening_request_id' => $screeningRequest->id,
                    'identity_type_id' => (int) $c['identity_type_id'],
                    'name' => $c['name'],
                    'identity_number' => $c['identity_number'],
                    'nationality' => $c['nationality'] ?? null,
                    'date_of_birth' => ! empty($c['date_of_birth']) ? $c['date_of_birth'] : null,
                    'mobile' => $c['mobile'] ?? null,
                    'remarks' => $c['remarks'] ?? null,
                    'status' => 'new',
                ]);

                $candidate->scopeTypes()->attach(
                    collect($scopeIds)->mapWithKeys(fn ($id) => [
                        $id => ['status' => 'new', 'assigned_at' => now()],
                    ])->all()
                );

                if (! empty($requiredDocs)) {
                    $this->storeCandidateDocuments($request, $screeningRequest, $candidate, $idx, $requiredDocs, $customerId);
                } else {
                    $this->recordConsent($request, $candidate, 'digital_form', null);
                }
            }

            return $screeningRequest;
        });

        return $this->redirectAfterSubmit($createdRequest);
    }

    /**
     * The ordered union of required documents across the selected scopes.
     * Returns [] when the column is absent or no scope requires any document.
     *
     * @param  array<int, int|string>  $scopeIds
     * @return list<string>
     */
    protected function requiredDocsFor(array $scopeIds): array
    {
        if (empty($scopeIds) || ! Schema::hasColumn('scope_types', 'required_documents')) {
            return [];
        }

        $union = ScopeType::whereIn('id', $scopeIds)->get()
            ->flatMap(function ($s) {
                $docs = $s->required_documents;
                if (is_string($docs)) {
                    $docs = json_decode($docs, true) ?: [];
                }

                return is_array($docs) ? $docs : [];
            })
            ->unique()
            ->all();

        // Canonical display/validation order.
        return array_values(array_filter(
            ['consent', 'nric', 'resume', 'certificate'],
            fn ($key) => in_array($key, $union, true)
        ));
    }

    /**
     * Require every candidate to have uploaded each required document, throwing 422 if any are missing.
     *
     * @param  list<string>  $requiredDocs
     */
    protected function validateDocumentUploads(Request $request, int $candidateCount, array $requiredDocs): void
    {
        if ($candidateCount < 1) {
            throw ValidationException::withMessages([
                'candidates' => 'At least one candidate is required.',
            ]);
        }

        $labels = $this->documentLabels();
        $rules = [];
        $messages = [];

        for ($i = 0; $i < $candidateCount; $i++) {
            foreach ($requiredDocs as $key) {
                $field = "documents.{$i}.{$key}";
                $rules[$field] = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
                $label = $labels[$key] ?? $key;
                $messages["{$field}.required"] = "{$label} is missing for candidate ".($i + 1).'.';
                $messages["{$field}.file"] = "{$label} for candidate ".($i + 1).' must be a file.';
                $messages["{$field}.mimes"] = "{$label} for candidate ".($i + 1).' must be a PDF, DOC, DOCX, JPG, or PNG.';
                $messages["{$field}.max"] = "{$label} for candidate ".($i + 1).' must not exceed 10MB.';
            }
        }

        $request->validate($rules, $messages);
    }

    /**
     * Persist a candidate's uploaded documents: consent → consent_records (PDPA),
     * everything else → candidate_documents.
     *
     * @param  list<string>  $requiredDocs
     */
    protected function storeCandidateDocuments(Request $request, ScreeningRequest $screeningRequest, RequestCandidate $candidate, int $idx, array $requiredDocs, int $customerId): void
    {
        $dir = "candidate-documents/{$customerId}/{$screeningRequest->reference}/candidate-{$candidate->id}";
        $consentFilePath = null;

        foreach ($requiredDocs as $key) {
            /** @var UploadedFile|null $upload */
            $upload = $request->file("documents.{$idx}.{$key}");
            if (! $upload) {
                continue;
            }

            $storedPath = $upload->storeAs($dir, $key.'.'.$upload->getClientOriginalExtension(), 'local');

            if ($key === 'consent') {
                $consentFilePath = $storedPath;

                continue;
            }

            CandidateDocument::create([
                'request_candidate_id' => $candidate->id,
                'screening_request_id' => $screeningRequest->id,
                'type' => $key,
                'file_path' => $storedPath,
                'original_name' => $upload->getClientOriginalName(),
            ]);
        }

        $this->recordConsent(
            $request,
            $candidate,
            in_array('consent', $requiredDocs, true) ? 'paper_signed' : 'digital_form',
            $consentFilePath
        );
    }

    /** @return array<string, string> */
    protected function documentLabels(): array
    {
        return [
            'consent' => 'Signed consent form',
            'nric' => 'NRIC / ID copy',
            'resume' => 'Resume / CV',
            'certificate' => 'Certificate copy',
        ];
    }

    public function submitDueDiligence(Request $request)
    {
        $request->validate([
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'PDPA consent must be accepted before submitting the request.',
        ]);

        $customerId = session('client_customer_id', 1);
        $userId = session('client_user_id', 1);
        $type = $request->input('screening_type', 'kyc');

        $subject = json_decode($request->input('subject_data', '{}'), true);
        $checks = json_decode($request->input('checks_data', '[]'), true);

        $seq = ScreeningRequest::count() + 1;
        $reference = 'REQ-'.now()->format('Y').'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

        $createdRequest = DB::transaction(function () use ($request, $customerId, $userId, $reference, $type, $subject, $checks) {
            $screeningRequest = ScreeningRequest::create([
                'customer_id' => $customerId,
                'customer_user_id' => $userId,
                'reference' => $reference,
                'status' => 'new',
                'type' => $type,
                'meta' => ['checks' => $checks, 'subject' => $subject],
            ]);

            $candidate = RequestCandidate::create([
                'screening_request_id' => $screeningRequest->id,
                'identity_type_id' => (int) ($subject['identity_type_id'] ?? 1),
                'name' => $subject['name'] ?? 'Unknown',
                'identity_number' => $subject['identity_number'] ?? '',
                'nationality' => $subject['nationality'] ?? null,
                'date_of_birth' => ! empty($subject['dob']) ? $subject['dob'] : null,
                'mobile' => $subject['mobile'] ?? null,
                'remarks' => $subject['remarks'] ?? null,
                'status' => 'new',
            ]);

            $this->recordConsent($request, $candidate);

            return $screeningRequest;
        });

        return $this->redirectAfterSubmit($createdRequest);
    }

    protected function redirectAfterSubmit(ScreeningRequest $screeningRequest): RedirectResponse
    {
        $customer = Customer::with('agreement')->find($screeningRequest->customer_id);

        if ($customer?->isCashBilled()) {
            return redirect()
                ->route('client.requests.details', hid($screeningRequest->id))
                ->with('status', 'Request created. Please complete payment to begin processing.');
        }

        // Monthly-billed customers start immediately — no payment gate.
        $screeningRequest->update(['status' => 'in_progress']);

        return redirect()->route('client.request.success');
    }

    protected function recordConsent(Request $request, RequestCandidate $candidate, string $evidenceType = 'digital_form', ?string $evidenceFilePath = null): void
    {
        ConsentRecord::create([
            'request_candidate_id' => $candidate->id,
            'consented_at' => now(),
            'consent_version' => config('consent.current_version'),
            'consent_text_snapshot' => config('consent.standard_text'),
            'evidence_type' => $evidenceType,
            'evidence_file_path' => $evidenceFilePath,
            'captured_ip' => $request->ip(),
            'captured_user_agent' => $request->userAgent(),
            'captured_by_admin_id' => null,
        ]);
    }

    public function successful()
    {
        return view('client.request.success');
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function employmentView(?int $lockedCountryId, string $type)
    {
        $customerId = session('client_customer_id', 1);

        $countries = Country::withCount('scopeTypes')->get();

        // Build a keyed map of customer-specific prices
        $customerPrices = Customer::find($customerId)
            ?->scopePrices()
            ->pluck('price', 'scope_type_id') ?? collect();

        $hasSignedConsentColumn = Schema::hasColumn('scope_types', 'requires_signed_consent');
        $hasRequiredDocsColumn = Schema::hasColumn('scope_types', 'required_documents');

        $scopes = ScopeType::orderBy('id')->get()
            ->map(function ($s) use ($customerPrices, $hasSignedConsentColumn, $hasRequiredDocsColumn) {
                $hasCustomPrice = $customerPrices->has($s->id);

                return [
                    'id' => $s->id,
                    'country_id' => $s->country_id,
                    'category' => $s->category,
                    'name' => $s->name,
                    'description' => $s->description,
                    'turnaround' => $s->turnaround,
                    'price' => $hasCustomPrice ? (float) $customerPrices->get($s->id) : (float) $s->price,
                    'price_on_request' => ! $hasCustomPrice && $s->price_on_request,
                    // Scopes flagged by admin require an uploaded signed consent form per candidate
                    // (PDPA — checkbox-only consent is not allowed for these). Falls back to false
                    // until the admin portal ships the requires_signed_consent column.
                    'requires_signed_consent' => $hasSignedConsentColumn ? (bool) ($s->requires_signed_consent ?? false) : false,
                    // Documents the customer must upload for this scope (consent/nric/resume/certificate).
                    // The request form renders an upload slot per document and blocks submission if any are missing.
                    'required_documents' => $hasRequiredDocsColumn ? (array) ($s->required_documents ?? []) : [],
                ];
            })
            ->reject(fn ($s) => $s['price_on_request'])
            ->values();

        $packages = Package::with('scopeTypes')
            ->where('customer_id', $customerId)
            ->get()
            ->map(function ($p) use ($customerPrices) {
                $total = $p->scopeTypes->sum(function ($s) use ($customerPrices) {
                    return $customerPrices->has($s->id)
                        ? (float) $customerPrices->get($s->id)
                        : (float) $s->price;
                });

                return [
                    'id' => $p->id,
                    'country_id' => $p->country_id,
                    'name' => $p->name,
                    'scope_ids' => $p->scopeTypes->pluck('id')->all(),
                    'price' => $total,
                ];
            });

        $identityTypes = IdentityType::all();

        return view('client.request.create.index', compact(
            'countries', 'scopes', 'packages', 'identityTypes'
        ) + ['lockedCountryId' => $lockedCountryId, 'screeningType' => $type]);
    }

    private function dueDiligenceView(string $type): View
    {
        $checks = $this->dueDiligenceChecks();
        $config = $this->dueDiligenceConfig();

        return view('client.request.create.due-diligence', [
            'type' => $type,
            'checks' => $checks[$type],
            'config' => $config[$type],
        ]);
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    private function dueDiligenceChecks(): array
    {
        return [
            'kyc' => [
                ['id' => 'identity',      'name' => 'Identity Verification',  'desc' => 'Verify national ID or passport against government databases.',      'turnaround' => '1–2 days',  'price' => 45.00],
                ['id' => 'sanctions',     'name' => 'Sanctions Screening',     'desc' => 'Screen against OFAC, UN, EU, and Malaysian sanctions lists.',         'turnaround' => '1 day',     'price' => 35.00],
                ['id' => 'pep',           'name' => 'PEP Check',               'desc' => 'Politically Exposed Person check against global databases.',          'turnaround' => '1 day',     'price' => 40.00],
                ['id' => 'adverse_media', 'name' => 'Adverse Media',           'desc' => 'Negative news screening across global and regional sources.',         'turnaround' => '2–3 days',  'price' => 50.00],
                ['id' => 'credit',        'name' => 'Credit Bureau Check',     'desc' => 'Credit history via CCRIS and CTOS databases.',                        'turnaround' => '2–3 days',  'price' => 45.00],
                ['id' => 'criminal',      'name' => 'Criminal Record Check',   'desc' => 'National criminal database check via Royal Malaysia Police.',         'turnaround' => '3–5 days',  'price' => 50.00],
            ],
            'kyb' => [
                ['id' => 'corporate_reg',       'name' => 'Corporate Registry Search',  'desc' => 'SSM verification of company registration, status, and filing history.',    'turnaround' => '1–2 days',  'price' => 60.00],
                ['id' => 'sanctions',           'name' => 'Sanctions Screening',         'desc' => 'Screen entity against OFAC, UN, EU, and regional watchlists.',              'turnaround' => '1 day',     'price' => 35.00],
                ['id' => 'beneficial_ownership', 'name' => 'Beneficial Ownership',        'desc' => 'Identify ultimate beneficial owners and shareholding structure.',            'turnaround' => '3–5 days',  'price' => 120.00],
                ['id' => 'adverse_media',       'name' => 'Adverse Media',               'desc' => 'Negative news and media screening for the entity.',                         'turnaround' => '2–3 days',  'price' => 50.00],
                ['id' => 'litigation',          'name' => 'Litigation Search',           'desc' => 'Court records and legal action search across jurisdictions.',               'turnaround' => '3–5 days',  'price' => 80.00],
                ['id' => 'financial_health',    'name' => 'Financial Health Assessment', 'desc' => 'Financial statements analysis and credit rating review.',                   'turnaround' => '5–7 days',  'price' => 150.00],
                ['id' => 'directors_pep',       'name' => 'Directors PEP Check',         'desc' => 'PEP screening for all listed directors and key shareholders.',              'turnaround' => '1–2 days',  'price' => 40.00],
            ],
            'kys' => [
                ['id' => 'company_reg',      'name' => 'Company Registration',          'desc' => 'Verify registration status and standing with local authorities.',            'turnaround' => '1–2 days',  'price' => 60.00],
                ['id' => 'sanctions',        'name' => 'Sanctions & Embargo Check',     'desc' => 'Screen against global sanctions, debarment, and embargo lists.',             'turnaround' => '1 day',     'price' => 35.00],
                ['id' => 'adverse_media',    'name' => 'Adverse Media Screening',       'desc' => 'Negative news and reputational risk screening for the supplier.',            'turnaround' => '2–3 days',  'price' => 50.00],
                ['id' => 'financial',        'name' => 'Financial Stability',           'desc' => 'Assess financial health, solvency, and payment track record.',               'turnaround' => '5–7 days',  'price' => 100.00],
                ['id' => 'esg',              'name' => 'ESG / Sustainability Rating',   'desc' => 'Environmental, Social, and Governance risk assessment.',                     'turnaround' => '7–10 days', 'price' => 180.00],
                ['id' => 'anti_bribery',     'name' => 'Anti-Bribery & Corruption',     'desc' => 'ABAC compliance check per MACC Act and ISO 37001 standards.',               'turnaround' => '3–5 days',  'price' => 90.00],
                ['id' => 'trade_reference',  'name' => 'Trade Reference Check',         'desc' => 'Verification interviews with provided trade references.',                    'turnaround' => '3–5 days',  'price' => 70.00],
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function dueDiligenceConfig(): array
    {
        return [
            'kyc' => [
                'label' => 'KYC — Know Your Customer',
                'badge' => 'Individual Due Diligence',
                'description' => 'Individual identity verification and compliance screening for customers, investors, and counterparties.',
                'subject_label' => 'Individual Subject',
                'subject_fields' => 'individual',
                'doc_types' => [
                    ['id' => 1, 'label' => 'IC / Passport Copy', 'required' => true],
                    ['id' => 2, 'label' => 'Consent Form',        'required' => true],
                    ['id' => 3, 'label' => 'Photo / Selfie',      'required' => false],
                ],
            ],
            'kyb' => [
                'label' => 'KYB — Know Your Business',
                'badge' => 'Corporate Due Diligence',
                'description' => 'Business entity verification and compliance screening for corporate customers, partners, and investors.',
                'subject_label' => 'Business Entity',
                'subject_fields' => 'business',
                'doc_types' => [
                    ['id' => 1, 'label' => 'Certificate of Incorporation', 'required' => true],
                    ['id' => 2, 'label' => 'SSM Form 9 / 24 / 49',        'required' => true],
                    ['id' => 3, 'label' => 'Director ICs',                 'required' => true],
                    ['id' => 4, 'label' => 'Consent Form',                 'required' => true],
                    ['id' => 5, 'label' => 'M&A / Constitution',           'required' => false],
                ],
            ],
            'kys' => [
                'label' => 'KYS — Know Your Supplier',
                'badge' => 'Supplier Due Diligence',
                'description' => 'Vendor and supplier risk assessment including sanctions screening, ESG rating, and financial stability checks.',
                'subject_label' => 'Supplier / Vendor',
                'subject_fields' => 'supplier',
                'doc_types' => [
                    ['id' => 1, 'label' => 'Business Registration Certificate', 'required' => true],
                    ['id' => 2, 'label' => 'Consent Form',                      'required' => true],
                    ['id' => 3, 'label' => 'Financial Statements',              'required' => false],
                    ['id' => 4, 'label' => 'Bank Account Details',              'required' => false],
                ],
            ],
        ];
    }
}
