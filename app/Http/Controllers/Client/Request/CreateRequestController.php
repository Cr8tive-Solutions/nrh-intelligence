<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\IdentityType;
use App\Models\Package;
use App\Models\RequestCandidate;
use App\Models\ScopeType;
use App\Models\ScreeningRequest;
use Illuminate\Http\Request;
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
        $customerId = session('client_customer_id', 1);
        $userId = session('client_user_id', 1);

        $cart = json_decode($request->input('cart_data', '[]'), true);
        $candidates = json_decode($request->input('candidates_data', '[]'), true);
        $type = $request->input('screening_type', 'employment_global');

        $seq = ScreeningRequest::count() + 1;
        $reference = 'REQ-'.now()->format('Y').'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

        $screeningRequest = ScreeningRequest::create([
            'customer_id' => $customerId,
            'customer_user_id' => $userId,
            'reference' => $reference,
            'status' => 'new',
            'type' => $type,
        ]);

        $scopeIds = collect($cart)->pluck('id')->all();

        foreach ($candidates as $c) {
            $candidate = RequestCandidate::create([
                'screening_request_id' => $screeningRequest->id,
                'identity_type_id' => (int) $c['identity_type_id'],
                'name' => $c['name'],
                'identity_number' => $c['identity_number'],
                'mobile' => $c['mobile'] ?? null,
                'remarks' => $c['remarks'] ?? null,
                'status' => 'new',
            ]);

            $candidate->scopeTypes()->attach(
                collect($scopeIds)->mapWithKeys(fn ($id) => [$id => ['status' => 'new']])->all()
            );
        }

        return redirect()->route('client.request.success');
    }

    public function submitDueDiligence(Request $request)
    {
        $customerId = session('client_customer_id', 1);
        $userId = session('client_user_id', 1);
        $type = $request->input('screening_type', 'kyc');

        $subject = json_decode($request->input('subject_data', '{}'), true);
        $checks = json_decode($request->input('checks_data', '[]'), true);

        $seq = ScreeningRequest::count() + 1;
        $reference = 'REQ-'.now()->format('Y').'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

        $screeningRequest = ScreeningRequest::create([
            'customer_id' => $customerId,
            'customer_user_id' => $userId,
            'reference' => $reference,
            'status' => 'new',
            'type' => $type,
            'meta' => ['checks' => $checks, 'subject' => $subject],
        ]);

        RequestCandidate::create([
            'screening_request_id' => $screeningRequest->id,
            'identity_type_id' => (int) ($subject['identity_type_id'] ?? 1),
            'name' => $subject['name'] ?? 'Unknown',
            'identity_number' => $subject['identity_number'] ?? '',
            'mobile' => $subject['mobile'] ?? null,
            'remarks' => $subject['remarks'] ?? null,
            'status' => 'new',
        ]);

        return redirect()->route('client.request.success');
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

        $scopes = ScopeType::all()->map(function ($s) use ($customerPrices) {
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
            ];
        });

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
