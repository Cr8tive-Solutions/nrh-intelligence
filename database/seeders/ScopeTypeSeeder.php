<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ScopeType;
use Illuminate\Database\Seeder;

class ScopeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $malaysia = Country::where('code', 'MY')->first();
        $singapore = Country::where('code', 'SG')->first();
        $indonesia = Country::where('code', 'ID')->first();
        $thailand = Country::where('code', 'TH')->first();

        // ── Malaysia ──────────────────────────────────────────────────────
        if ($malaysia) {
            // Remove stale scopes before re-seeding with real data
            ScopeType::where('country_id', $malaysia->id)->delete();

            $malaysiaScopes = [
                // DATA RELATED SCREENING — Security & Integrity Check
                [
                    'category' => 'Security & Integrity Check',
                    'name' => 'Personal Data – MyKAD Verification',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Verification of personal identity data against the National Registration Department (JPN) MyKAD database.',
                ],
                [
                    'category' => 'Security & Integrity Check',
                    'name' => 'Crime Risk Integrity Check',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Cross-check against Royal Malaysia Police (PDRM) criminal records database.',
                ],
                [
                    'category' => 'Security & Integrity Check',
                    'name' => 'INTERPOL Global Crime Data – Malaysian',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against INTERPOL international crime data relevant to Malaysian subjects.',
                ],
                [
                    'category' => 'Security & Integrity Check',
                    'name' => 'Anticorruption Compliance Check (MACC)',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening against Malaysian Anti-Corruption Commission (MACC) enforcement records.',
                ],
                [
                    'category' => 'Security & Integrity Check',
                    'name' => 'National Counter-Terrorism Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against national counter-terrorism and extremism watchlists.',
                ],

                // Anti-Money Laundering & Counter-Terrorism Financing (AML/CTF)
                [
                    'category' => 'Anti-Money Laundering & Counter-Terrorism Financing (AML/CTF)',
                    'name' => 'MACC Listing',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening against MACC AML/CTF watchlists and enforcement listings.',
                ],
                [
                    'category' => 'Anti-Money Laundering & Counter-Terrorism Financing (AML/CTF)',
                    'name' => 'BNM Listing',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against Bank Negara Malaysia (BNM) AML/CTF regulatory listings.',
                ],
                [
                    'category' => 'Anti-Money Laundering & Counter-Terrorism Financing (AML/CTF)',
                    'name' => 'Security Commission Listing',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening against Securities Commission Malaysia AML/CTF watchlists.',
                ],
                [
                    'category' => 'Anti-Money Laundering & Counter-Terrorism Financing (AML/CTF)',
                    'name' => 'KDN Listing',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against Kementerian Dalam Negeri (Home Ministry) regulatory listings.',
                ],

                // Securities Commission Malaysia (Capital Market Offences)
                [
                    'category' => 'Securities Commission Malaysia (Capital Market Offences)',
                    'name' => 'Financial Fraud',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening for capital market financial fraud offences recorded by the Securities Commission Malaysia.',
                ],
                [
                    'category' => 'Securities Commission Malaysia (Capital Market Offences)',
                    'name' => 'Breach of Trust',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for breach of trust offences under Securities Commission Malaysia records.',
                ],
                [
                    'category' => 'Securities Commission Malaysia (Capital Market Offences)',
                    'name' => 'Insider Trading',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening for insider trading violations recorded by the Securities Commission Malaysia.',
                ],
                [
                    'category' => 'Securities Commission Malaysia (Capital Market Offences)',
                    'name' => 'Securities Trading Violations',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for securities trading violations under Securities Commission Malaysia enforcement.',
                ],

                // Bursa Malaysia Berhad (Corporate Enforcement)
                [
                    'category' => 'Bursa Malaysia Berhad (Corporate Enforcement)',
                    'name' => 'Enforcement Actions on Market Participants',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening for Bursa Malaysia enforcement actions taken against market participants.',
                ],
                [
                    'category' => 'Bursa Malaysia Berhad (Corporate Enforcement)',
                    'name' => 'Sanctions on Company Advisors',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for Bursa Malaysia sanctions imposed on company advisors and professionals.',
                ],
                [
                    'category' => 'Bursa Malaysia Berhad (Corporate Enforcement)',
                    'name' => 'Sanctions on Directors & Individuals',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening for Bursa Malaysia sanctions on directors and individuals.',
                ],

                // Global Sanctions & Politically Exposed Persons (PEP)
                [
                    'category' => 'Global Sanctions & Politically Exposed Persons (PEP)',
                    'name' => 'OFAC – Blocked Persons List & SDN List',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against OFAC Specially Designated Nationals (SDN) and Blocked Persons List.',
                ],
                [
                    'category' => 'Global Sanctions & Politically Exposed Persons (PEP)',
                    'name' => 'United Nations Security Council Sanction',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Screening against the United Nations Security Council consolidated sanctions list.',
                ],
                [
                    'category' => 'Global Sanctions & Politically Exposed Persons (PEP)',
                    'name' => 'World Bank Sanction',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check against World Bank debarment and sanctions list.',
                ],
                [
                    'category' => 'Global Sanctions & Politically Exposed Persons (PEP)',
                    'name' => 'Politically Exposed Persons (PEP)',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Identification and screening of politically exposed persons and their associates.',
                ],

                // Financial Standing & Credit Records
                [
                    'category' => 'Financial Standing & Credit Records',
                    'name' => 'Credit Summons & Default',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for credit summons and payment default records.',
                ],
                [
                    'category' => 'Financial Standing & Credit Records',
                    'name' => 'Bankruptcy / Insolvency',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Search against Insolvency Department Malaysia records for bankruptcy and insolvency status.',
                ],
                [
                    'category' => 'Financial Standing & Credit Records',
                    'name' => 'Bank Negara Malaysia CCRIS Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Review of Central Credit Reference Information System (CCRIS) records via BNM.',
                ],
                [
                    'category' => 'Financial Standing & Credit Records',
                    'name' => 'Academic Loan Standing',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check of PTPTN and other academic loan repayment standing and default records.',
                ],

                // Legal & Civil Proceedings
                [
                    'category' => 'Legal & Civil Proceedings',
                    'name' => 'Industrial Relations & Labour Court Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Search for proceedings filed at the Industrial Relations Department and Labour Court.',
                ],
                [
                    'category' => 'Legal & Civil Proceedings',
                    'name' => 'Civil Litigation Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for civil litigation cases and court proceedings involving the subject.',
                ],

                // Driving & Licensing Records
                [
                    'category' => 'Driving & Licensing Records',
                    'name' => 'Driving & Motor Vehicle Offences',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check for driving offences and motor vehicle violations via JPJ records.',
                ],
                [
                    'category' => 'Driving & Licensing Records',
                    'name' => 'License Verification',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Verification of professional and driving licences with relevant issuing authorities.',
                ],

                // International Travel Restriction
                [
                    'category' => 'International Travel Restriction',
                    'name' => 'Travel Eligibility & Immigration Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Check of immigration records and travel eligibility status with the Immigration Department of Malaysia.',
                ],

                // Corporate Governance & Ownership Risk
                [
                    'category' => 'Corporate Governance & Ownership Risk',
                    'name' => 'Directorship & Shareholding Risk',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Review of company directorships and shareholding interests held by the subject via SSM.',
                ],
                [
                    'category' => 'Corporate Governance & Ownership Risk',
                    'name' => 'Corporate Registry Record (SSM)',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Official company search via Suruhanjaya Syarikat Malaysia (SSM) corporate registry.',
                ],

                // Digital Presence & Online Risk Records
                [
                    'category' => 'Digital Presence & Online Risk Records',
                    'name' => 'Social Media & Deep Web Intelligence Record',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Review of publicly available social media profiles and deep web data for red flags.',
                ],
                [
                    'category' => 'Digital Presence & Online Risk Records',
                    'name' => 'Dark Web Risk Intelligence Report',
                    'turnaround' => '1-2 Working Days',
                    'description' => 'Scan of dark web sources for compromised data, illicit activity, or identity exposure linked to the subject.',
                ],

                // Education, Skills, Employment & Reference Verification — Qualification
                [
                    'category' => 'Academic & Qualification Verification',
                    'name' => 'One Academic Credential Verification (Malaysian Institution)',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Verification of one academic qualification directly with the issuing Malaysian institution.',
                ],
                [
                    'category' => 'Academic & Qualification Verification',
                    'name' => 'Two Academic Credential Verification (Malaysian Institution)',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Verification of two academic qualifications directly with issuing Malaysian institutions.',
                ],
                [
                    'category' => 'Academic & Qualification Verification',
                    'name' => 'One Academic Credential Verification (Foreign Institution)',
                    'turnaround' => '5-7 Working Days',
                    'description' => 'Verification of one academic qualification with a foreign issuing institution.',
                ],
                [
                    'category' => 'Academic & Qualification Verification',
                    'name' => 'One Professional Body Membership Record',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Confirmation of active membership with a recognised professional body or association.',
                ],

                // Employment & Reference Verification
                [
                    'category' => 'Employment & Reference Verification',
                    'name' => 'One Employment Verification',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Direct verification of one employment record with the previous employer.',
                ],
                [
                    'category' => 'Employment & Reference Verification',
                    'name' => 'Two Employment Verification',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Direct verification of two employment records with previous employers.',
                ],
                [
                    'category' => 'Employment & Reference Verification',
                    'name' => 'One Reference Review',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Performance reference interview with one nominated referee, cross-verified with employer.',
                ],
                [
                    'category' => 'Employment & Reference Verification',
                    'name' => 'Two Reference Reviews',
                    'turnaround' => '3-5 Working Days',
                    'description' => 'Performance reference interviews with two nominated referees, cross-verified with employers.',
                ],
            ];

            foreach ($malaysiaScopes as $scope) {
                ScopeType::create([
                    'country_id' => $malaysia->id,
                    'category' => $scope['category'],
                    'name' => $scope['name'],
                    'turnaround' => $scope['turnaround'],
                    'price' => 0.00,
                    'price_on_request' => true,
                    'description' => $scope['description'],
                ]);
            }
        }

        // ── Singapore (keep existing) ─────────────────────────────────────
        if ($singapore) {
            $sgScopes = [
                ['category' => 'Criminal & Integrity', 'name' => 'Criminal Record Check', 'turnaround' => '5-7 days', 'price' => 90.00, 'description' => 'Checks against Singapore Police Force records.'],
                ['category' => 'Employment & Reference Verification', 'name' => 'Employment Verification', 'turnaround' => '7-10 days', 'price' => 110.00, 'description' => 'Verifies employment history with CPF Board and employers.'],
                ['category' => 'Academic & Qualification Verification', 'name' => 'Education Verification', 'turnaround' => '5-7 days', 'price' => 85.00, 'description' => 'Confirms qualifications with Singapore institutions.'],
                ['category' => 'Financial Standing & Credit Records', 'name' => 'Credit Check', 'turnaround' => '2-3 days', 'price' => 65.00, 'description' => 'Credit Bureau Singapore check.'],
            ];
            foreach ($sgScopes as $scope) {
                ScopeType::firstOrCreate(
                    ['country_id' => $singapore->id, 'name' => $scope['name']],
                    $scope
                );
            }
        }

        // ── Indonesia (keep existing) ─────────────────────────────────────
        if ($indonesia) {
            $idScopes = [
                ['category' => 'Criminal & Integrity', 'name' => 'Criminal Record Check', 'turnaround' => '7-10 days', 'price' => 75.00, 'description' => 'SKCK certificate verification via Kepolisian.'],
                ['category' => 'Employment & Reference Verification', 'name' => 'Employment Verification', 'turnaround' => '7-10 days', 'price' => 90.00, 'description' => 'Employment history verification with HR departments.'],
                ['category' => 'Academic & Qualification Verification', 'name' => 'Education Verification', 'turnaround' => '7-10 days', 'price' => 70.00, 'description' => 'Diploma and degree verification with institutions.'],
            ];
            foreach ($idScopes as $scope) {
                ScopeType::firstOrCreate(
                    ['country_id' => $indonesia->id, 'name' => $scope['name']],
                    $scope
                );
            }
        }

        // ── Thailand (keep existing) ──────────────────────────────────────
        if ($thailand) {
            $thScopes = [
                ['category' => 'Criminal & Integrity', 'name' => 'Criminal Record Check', 'turnaround' => '7-14 days', 'price' => 80.00, 'description' => 'Royal Thai Police criminal background check.'],
                ['category' => 'Employment & Reference Verification', 'name' => 'Employment Verification', 'turnaround' => '7-10 days', 'price' => 95.00, 'description' => 'Past employer verification.'],
            ];
            foreach ($thScopes as $scope) {
                ScopeType::firstOrCreate(
                    ['country_id' => $thailand->id, 'name' => $scope['name']],
                    $scope
                );
            }
        }
    }
}
