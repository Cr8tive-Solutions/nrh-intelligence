<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ScopeType;
use Illuminate\Database\Seeder;

class ScopeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $scopes = [
            // Malaysia
            'MY' => [
                ['name' => 'Criminal Record Check',    'turnaround' => '3-5 days',  'price' => 50.00,  'description' => 'Checks against Royal Malaysia Police criminal database.'],
                ['name' => 'Employment Verification',  'turnaround' => '5-7 days',  'price' => 80.00,  'description' => 'Verifies past employment history with previous employers.'],
                ['name' => 'Education Verification',   'turnaround' => '5-7 days',  'price' => 60.00,  'description' => 'Confirms academic qualifications with issuing institutions.'],
                ['name' => 'Credit Check',             'turnaround' => '2-3 days',  'price' => 45.00,  'description' => 'Reviews credit history via CCRIS and CTOS databases.'],
                ['name' => 'Reference Check',          'turnaround' => '3-5 days',  'price' => 70.00,  'description' => 'Professional reference interviews with provided contacts.'],
                ['name' => 'Social Media Screening',   'turnaround' => '1-2 days',  'price' => 40.00,  'description' => 'Reviews public social media profiles for red flags.'],
                ['name' => 'Bankruptcy Search',        'turnaround' => '1-2 days',  'price' => 35.00,  'description' => 'Searches Insolvency Department records for bankruptcy status.'],
                ['name' => 'Directorship Search',      'turnaround' => '1-2 days',  'price' => 35.00,  'description' => 'Checks SSM records for company directorships held.'],
            ],
            // Singapore
            'SG' => [
                ['name' => 'Criminal Record Check',    'turnaround' => '5-7 days',  'price' => 90.00,  'description' => 'Checks against Singapore Police Force records.'],
                ['name' => 'Employment Verification',  'turnaround' => '7-10 days', 'price' => 110.00, 'description' => 'Verifies employment history with CPF Board and employers.'],
                ['name' => 'Education Verification',   'turnaround' => '5-7 days',  'price' => 85.00,  'description' => 'Confirms qualifications with Singapore institutions.'],
                ['name' => 'Credit Check',             'turnaround' => '2-3 days',  'price' => 65.00,  'description' => 'Credit Bureau Singapore check.'],
            ],
            // Indonesia
            'ID' => [
                ['name' => 'Criminal Record Check',    'turnaround' => '7-10 days', 'price' => 75.00,  'description' => 'SKCK certificate verification via Kepolisian.'],
                ['name' => 'Employment Verification',  'turnaround' => '7-10 days', 'price' => 90.00,  'description' => 'Employment history verification with HR departments.'],
                ['name' => 'Education Verification',   'turnaround' => '7-10 days', 'price' => 70.00,  'description' => 'Diploma and degree verification with institutions.'],
            ],
            // Thailand
            'TH' => [
                ['name' => 'Criminal Record Check',    'turnaround' => '7-14 days', 'price' => 80.00,  'description' => 'Royal Thai Police criminal background check.'],
                ['name' => 'Employment Verification',  'turnaround' => '7-10 days', 'price' => 95.00,  'description' => 'Past employer verification.'],
            ],
        ];

        foreach ($scopes as $code => $countryScopes) {
            $country = Country::where('code', $code)->first();
            if (! $country) {
                continue;
            }

            foreach ($countryScopes as $scope) {
                ScopeType::firstOrCreate(
                    ['country_id' => $country->id, 'name' => $scope['name']],
                    $scope
                );
            }
        }
    }
}
