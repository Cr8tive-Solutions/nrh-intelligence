<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updates = [
            // v1.0 — Initial launch
            [
                'version'     => 'v1.0',
                'title'       => 'Platform Launch',
                'body'        => 'NRH Intelligence portal is now live. Clients can log in, submit screening requests, and track their cases.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Email 2FA Verification',
                'body'        => 'Two-factor authentication via 6-digit OTP is now required at login for enhanced account security.',
                'type'        => 'security',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Malaysia Screening — 42 Scope Types',
                'body'        => 'Full Malaysia screening library is now available across 13 regulatory categories including AML/CTF, Sanctions & PEP, Credit Records, and more.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Global Screening',
                'body'        => 'Submit screening requests for candidates in Singapore, Indonesia, Thailand, and other countries.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Due Diligence — KYC, KYB, KYS',
                'body'        => 'Three due diligence service types are now available: Know Your Customer (individual), Know Your Business (company), and Know Your Supplier (vendor).',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Active Screenings Dashboard',
                'body'        => 'View and track all active screening requests with per-candidate status and scope details.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Billing — Invoices & Transactions',
                'body'        => 'Monthly invoices with line-item breakdown and full payment transaction history are now accessible under Billing.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],
            [
                'version'     => 'v1.0',
                'title'       => 'Notifications Centre',
                'body'        => 'Receive alerts for agreement expiry, unpaid invoices, completed screenings, and account activity.',
                'type'        => 'feature',
                'released_at' => '2026-04-19',
            ],

            // v1.1 — Scopes & Pricing
            [
                'version'     => 'v1.1',
                'title'       => 'Per-Customer Scope Pricing',
                'body'        => 'Scope prices are now customised per customer agreement. Malaysia scopes display "Price on request" until pricing is confirmed with your account manager.',
                'type'        => 'improvement',
                'released_at' => '2026-04-21',
            ],
            [
                'version'     => 'v1.1',
                'title'       => 'Scope Browser — Category Grouping',
                'body'        => 'When submitting a screening request, scopes are now grouped by regulatory category for easier browsing and selection.',
                'type'        => 'improvement',
                'released_at' => '2026-04-21',
            ],
            [
                'version'     => 'v1.1',
                'title'       => 'Track Request — Global Search',
                'body'        => 'You can now search for any screening request by reference number, candidate name, or identity number directly from the top navigation bar (⌘K).',
                'type'        => 'feature',
                'released_at' => '2026-04-21',
            ],
            [
                'version'     => 'v1.1',
                'title'       => 'Profile Photo Upload',
                'body'        => 'You can now upload, change, and remove your profile photo from My Profile settings.',
                'type'        => 'feature',
                'released_at' => '2026-04-21',
            ],
            [
                'version'     => 'v1.1',
                'title'       => 'Country Dropdown Fix',
                'body'        => 'Fixed an issue where the country selector in the global screening form was being clipped by the card boundary.',
                'type'        => 'fix',
                'released_at' => '2026-04-21',
            ],
            [
                'version'     => 'v1.1',
                'title'       => 'System Updates Page',
                'body'        => 'This page. You can now view all platform updates and release notes directly from the portal.',
                'type'        => 'feature',
                'released_at' => '2026-04-21',
            ],
        ];

        foreach ($updates as $update) {
            \App\Models\SystemUpdate::firstOrCreate(
                ['title' => $update['title'], 'released_at' => $update['released_at']],
                $update
            );
        }
    }
}
