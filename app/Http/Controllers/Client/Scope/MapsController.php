<?php

namespace App\Http\Controllers\Client\Scope;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    public function index()
    {
        $countries = collect([
            ['id' => 1, 'name' => 'Malaysia',   'code' => 'MY', 'flag' => '🇲🇾', 'scope_count' => 6, 'region' => 'Southeast Asia'],
            ['id' => 2, 'name' => 'Singapore',  'code' => 'SG', 'flag' => '🇸🇬', 'scope_count' => 4, 'region' => 'Southeast Asia'],
            ['id' => 3, 'name' => 'Indonesia',  'code' => 'ID', 'flag' => '🇮🇩', 'scope_count' => 5, 'region' => 'Southeast Asia'],
            ['id' => 4, 'name' => 'Thailand',   'code' => 'TH', 'flag' => '🇹🇭', 'scope_count' => 4, 'region' => 'Southeast Asia'],
            ['id' => 5, 'name' => 'Philippines','code' => 'PH', 'flag' => '🇵🇭', 'scope_count' => 3, 'region' => 'Southeast Asia'],
            ['id' => 6, 'name' => 'Vietnam',    'code' => 'VN', 'flag' => '🇻🇳', 'scope_count' => 3, 'region' => 'Southeast Asia'],
        ]);

        return view('client.scope.maps', compact('countries'));
    }

    public function countries(int $countryId)
    {
        $country = ['id' => $countryId, 'name' => 'Malaysia', 'flag' => '🇲🇾'];
        $scopes = collect([
            ['name' => 'Criminal Record Check',   'turnaround' => '3-5 days',  'description' => 'Checks against national criminal databases.'],
            ['name' => 'Employment Verification', 'turnaround' => '5-7 days',  'description' => 'Verifies past employment history and tenure.'],
            ['name' => 'Education Verification',  'turnaround' => '5-7 days',  'description' => 'Confirms academic qualifications and certificates.'],
            ['name' => 'Credit Check',            'turnaround' => '2-3 days',  'description' => 'Reviews credit history and financial standing.'],
            ['name' => 'Reference Check',         'turnaround' => '3-5 days',  'description' => 'Professional reference interviews conducted.'],
            ['name' => 'Social Media Screening',  'turnaround' => '1-2 days',  'description' => 'Reviews public social media profiles.'],
        ]);

        return view('client.scope.country', compact('country', 'scopes'));
    }
}
