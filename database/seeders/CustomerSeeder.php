<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\IdentityType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Package;
use App\Models\RequestCandidate;
use App\Models\ScopeType;
use App\Models\ScreeningRequest;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo customer ──────────────────────────────────────────────────
        $customer = Customer::firstOrCreate(
            ['contact_email' => 'demo@nrh-intelligence.com'],
            [
                'name' => 'NRH Intelligence Sdn. Bhd.',
                'registration_no' => '202001234567',
                'address' => 'Level 12, Menara NRH, No. 1 Jalan Ampang, 50450 Kuala Lumpur',
                'country' => 'Malaysia',
                'industry' => 'Financial Services',
                'contact_name' => 'Ahmad bin Razali',
                'contact_phone' => '+60 12 345 6789',
                'balance' => 1250.00,
            ]
        );

        // ── Demo admin user ────────────────────────────────────────────────
        $adminUser = CustomerUser::firstOrCreate(
            ['email' => 'demo@nrh-intelligence.com'],
            [
                'customer_id' => $customer->id,
                'name' => 'Ahmad bin Razali',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Additional users
        $users = [
            ['name' => 'Siti Aminah', 'email' => 'siti@nrh-intelligence.com', 'role' => 'user'],
            ['name' => 'Raj Kumar',   'email' => 'raj@nrh-intelligence.com',  'role' => 'user'],
        ];
        foreach ($users as $u) {
            CustomerUser::firstOrCreate(
                ['email' => $u['email']],
                ['customer_id' => $customer->id, 'password' => Hash::make('password'), 'status' => 'active'] + $u
            );
        }

        // ── Agreement ──────────────────────────────────────────────────────
        Agreement::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'type' => 'Annual Service Agreement',
                'start_date' => Carbon::parse('2026-01-01'),
                'expiry_date' => Carbon::parse('2026-12-31'),
                'sla_tat' => '5 Business Days',
                'billing' => 'Monthly',
                'payment' => 'Bank Transfer',
                'terms' => [
                    'Minimum 10 checks per month',
                    'Turnaround time as per agreed SLA',
                    'Reports delivered via secure portal',
                    'Data handled in compliance with PDPA 2010',
                    'Invoiced at end of each calendar month',
                ],
            ]
        );

        // ── Countries & scopes ─────────────────────────────────────────────
        $malaysia = Country::where('code', 'MY')->first();
        $singapore = Country::where('code', 'SG')->first();
        $nric = IdentityType::where('name', 'NRIC')->first();
        $passport = IdentityType::where('name', 'Passport')->first();

        $myCriminal = ScopeType::where('country_id', $malaysia->id)->where('name', 'Criminal Record Check')->first();
        $myEmployment = ScopeType::where('country_id', $malaysia->id)->where('name', 'Employment Verification')->first();
        $myEducation = ScopeType::where('country_id', $malaysia->id)->where('name', 'Education Verification')->first();
        $myCredit = ScopeType::where('country_id', $malaysia->id)->where('name', 'Credit Check')->first();

        // ── Packages ───────────────────────────────────────────────────────
        if ($customer->packages()->count() === 0) {
            $stdPackage = Package::create([
                'customer_id' => $customer->id,
                'country_id' => $malaysia->id,
                'name' => 'Standard Screening',
            ]);
            $stdPackage->scopeTypes()->attach([$myCriminal->id, $myEducation->id]);

            $premPackage = Package::create([
                'customer_id' => $customer->id,
                'country_id' => $malaysia->id,
                'name' => 'Premium Package',
            ]);
            $premPackage->scopeTypes()->attach([$myCriminal->id, $myEmployment->id, $myEducation->id, $myCredit->id]);
        }

        // ── Active screening requests ──────────────────────────────────────
        if ($customer->screeningRequests()->count() === 0) {
            $requestsData = [
                ['ref' => 'REQ-2026-0101', 'status' => 'in_progress', 'scopes' => [$myCriminal, $myEmployment],
                    'candidates' => [
                        ['name' => 'Ahmad bin Razali',   'id' => '900101-14-5678', 'id_type' => $nric,     'status' => 'in_progress'],
                        ['name' => 'Siti Aisyah binti M', 'id' => '950210-10-3456', 'id_type' => $nric,     'status' => 'new'],
                        ['name' => 'Rajesh Kumar',        'id' => 'A12345678',      'id_type' => $passport, 'status' => 'complete'],
                    ]],
                ['ref' => 'REQ-2026-0102', 'status' => 'new', 'scopes' => [$myCriminal],
                    'candidates' => [
                        ['name' => 'Lee Wei Liang',       'id' => '880305-14-1234', 'id_type' => $nric, 'status' => 'new'],
                    ]],
                ['ref' => 'REQ-2026-0103', 'status' => 'in_progress', 'scopes' => [$myCriminal, $myEmployment, $myEducation],
                    'candidates' => [
                        ['name' => 'Nurul Ain Hassan',    'id' => '920714-10-5678', 'id_type' => $nric, 'status' => 'in_progress'],
                        ['name' => 'Mohd Farid Osman',    'id' => '910523-14-3456', 'id_type' => $nric, 'status' => 'in_progress'],
                        ['name' => 'Priya Krishnan',      'id' => '931201-10-7890', 'id_type' => $nric, 'status' => 'new'],
                        ['name' => 'Tan Boon Leong',      'id' => '880912-14-2345', 'id_type' => $nric, 'status' => 'complete'],
                        ['name' => 'Zuraidah binti Amin', 'id' => '950430-10-6789', 'id_type' => $nric, 'status' => 'new'],
                    ]],
                ['ref' => 'REQ-2026-0104', 'status' => 'new', 'scopes' => [$myCredit, $myCriminal],
                    'candidates' => [
                        ['name' => 'Hassan bin Ibrahim',  'id' => '870220-14-4567', 'id_type' => $nric, 'status' => 'new'],
                        ['name' => 'Wan Norashikin',      'id' => '900815-10-8901', 'id_type' => $nric, 'status' => 'new'],
                    ]],
            ];

            foreach ($requestsData as $rd) {
                $req = ScreeningRequest::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $adminUser->id,
                    'reference' => $rd['ref'],
                    'status' => $rd['status'],
                ]);

                foreach ($rd['candidates'] as $cd) {
                    $candidate = RequestCandidate::create([
                        'screening_request_id' => $req->id,
                        'identity_type_id' => $cd['id_type']->id,
                        'name' => $cd['name'],
                        'identity_number' => $cd['id'],
                        'status' => $cd['status'],
                    ]);

                    foreach ($rd['scopes'] as $scope) {
                        $candidate->scopeTypes()->attach($scope->id, ['status' => $cd['status']]);
                    }
                }
            }
        }

        // ── Completed requests (history) ───────────────────────────────────
        if ($customer->screeningRequests()->complete()->count() === 0) {
            $completedData = [
                ['ref' => 'REQ-2026-0098', 'scopes' => [$myCriminal, $myEducation],
                    'candidates' => [
                        ['name' => 'Azman bin Yusof',    'id' => '850612-14-2345', 'id_type' => $nric],
                        ['name' => 'Norzaihan Ahmad',    'id' => '900901-10-5678', 'id_type' => $nric],
                    ]],
                ['ref' => 'REQ-2026-0095', 'scopes' => [$myCriminal, $myEmployment, $myEducation, $myCredit],
                    'candidates' => [
                        ['name' => 'Fauziah Ismail',     'id' => '780423-10-7890', 'id_type' => $nric],
                        ['name' => 'Khairul Anwar',      'id' => '820715-14-3456', 'id_type' => $nric],
                        ['name' => 'Lim Siew Ling',      'id' => '870309-14-6789', 'id_type' => $nric],
                        ['name' => 'Mohamed Rizal',      'id' => '900204-14-1234', 'id_type' => $nric],
                        ['name' => 'Suhaila Mansor',     'id' => '911122-10-5678', 'id_type' => $nric],
                    ]],
            ];

            foreach ($completedData as $i => $rd) {
                $daysAgo = ($i + 1) * 15;
                $req = ScreeningRequest::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $adminUser->id,
                    'reference' => $rd['ref'],
                    'status' => 'complete',
                    'created_at' => now()->subDays($daysAgo + 7),
                    'updated_at' => now()->subDays($daysAgo),
                ]);

                foreach ($rd['candidates'] as $cd) {
                    $candidate = RequestCandidate::create([
                        'screening_request_id' => $req->id,
                        'identity_type_id' => $cd['id_type']->id,
                        'name' => $cd['name'],
                        'identity_number' => $cd['id'],
                        'status' => 'complete',
                    ]);

                    foreach ($rd['scopes'] as $scope) {
                        $candidate->scopeTypes()->attach($scope->id, ['status' => 'complete']);
                    }
                }
            }
        }

        // ── Invoices ───────────────────────────────────────────────────────
        if ($customer->invoices()->count() === 0) {
            $invoicesData = [
                ['number' => 'INV-2026-003', 'period' => 'March 2026',    'status' => 'paid',   'issued' => '2026-03-31', 'due' => '2026-04-30',
                    'items' => [
                        ['desc' => 'REQ-2026-0098 — Criminal Record Check (2 candidates)',    'qty' => 2, 'price' => 50.00],
                        ['desc' => 'REQ-2026-0098 — Education Verification (2 candidates)',   'qty' => 2, 'price' => 60.00],
                        ['desc' => 'REQ-2026-0095 — Criminal Record Check (5 candidates)',    'qty' => 5, 'price' => 50.00],
                        ['desc' => 'REQ-2026-0095 — Employment Verification (5 candidates)', 'qty' => 5, 'price' => 80.00],
                        ['desc' => 'REQ-2026-0095 — Credit Check (5 candidates)',             'qty' => 5, 'price' => 45.00],
                    ]],
                ['number' => 'INV-2026-002', 'period' => 'February 2026', 'status' => 'paid',   'issued' => '2026-02-28', 'due' => '2026-03-30',
                    'items' => [
                        ['desc' => 'REQ-2026-0089 — Criminal Record Check (4 candidates)',    'qty' => 4, 'price' => 50.00],
                        ['desc' => 'REQ-2026-0089 — Employment Verification (4 candidates)', 'qty' => 4, 'price' => 80.00],
                        ['desc' => 'REQ-2026-0087 — Education Verification (3 candidates)',   'qty' => 3, 'price' => 60.00],
                    ]],
                ['number' => 'INV-2026-001', 'period' => 'January 2026',  'status' => 'paid',   'issued' => '2026-01-31', 'due' => '2026-03-02',
                    'items' => [
                        ['desc' => 'REQ-2026-0080 — Criminal Record Check (3 candidates)',    'qty' => 3, 'price' => 50.00],
                        ['desc' => 'REQ-2026-0078 — Credit Check (2 candidates)',             'qty' => 2, 'price' => 45.00],
                    ]],
            ];

            foreach ($invoicesData as $id) {
                $subtotal = collect($id['items'])->sum(fn ($i) => $i['qty'] * $i['price']);
                $tax = round($subtotal * 0.06, 2);

                $invoice = Invoice::create([
                    'customer_id' => $customer->id,
                    'number' => $id['number'],
                    'period' => $id['period'],
                    'status' => $id['status'],
                    'issued_at' => $id['issued'],
                    'due_at' => $id['due'],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                ]);

                foreach ($id['items'] as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['desc'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['price'],
                        'total' => $item['qty'] * $item['price'],
                    ]);
                }
            }
        }

        // ── Transactions ───────────────────────────────────────────────────
        if ($customer->transactions()->count() === 0) {
            $txns = [
                ['type' => 'topup',   'amount' => 2000.00, 'method' => 'Bank Transfer',   'ref' => 'TXN-2026-APR-001', 'status' => 'completed', 'date' => '2026-04-01'],
                ['type' => 'payment', 'amount' => 1179.50, 'method' => 'Auto-debit',       'ref' => 'INV-2026-003',     'status' => 'completed', 'date' => '2026-04-15'],
                ['type' => 'topup',   'amount' => 1500.00, 'method' => 'Bank Transfer',   'ref' => 'TXN-2026-MAR-001', 'status' => 'completed', 'date' => '2026-03-05'],
                ['type' => 'payment', 'amount' => 926.80,  'method' => 'Auto-debit',       'ref' => 'INV-2026-002',     'status' => 'completed', 'date' => '2026-03-15'],
                ['type' => 'topup',   'amount' => 1000.00, 'method' => 'Credit Card',      'ref' => 'TXN-2026-FEB-001', 'status' => 'completed', 'date' => '2026-02-01'],
            ];

            foreach ($txns as $t) {
                Transaction::create([
                    'customer_id' => $customer->id,
                    'type' => $t['type'],
                    'amount' => $t['amount'],
                    'method' => $t['method'],
                    'reference' => $t['ref'],
                    'status' => $t['status'],
                    'created_at' => Carbon::parse($t['date']),
                    'updated_at' => Carbon::parse($t['date']),
                ]);
            }
        }
    }
}
