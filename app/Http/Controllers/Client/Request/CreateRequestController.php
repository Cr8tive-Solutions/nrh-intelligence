<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateRequestController extends Controller
{
    public function index()
    {
        // Stub data — replace with real queries once models exist
        $countries = collect([
            ['id' => 1, 'name' => 'Malaysia', 'code' => 'MY', 'flag' => '🇲🇾'],
            ['id' => 2, 'name' => 'Singapore', 'code' => 'SG', 'flag' => '🇸🇬'],
            ['id' => 3, 'name' => 'Indonesia', 'code' => 'ID', 'flag' => '🇮🇩'],
            ['id' => 4, 'name' => 'Thailand', 'code' => 'TH', 'flag' => '🇹🇭'],
        ]);

        $scopes = collect([
            ['id' => 1, 'name' => 'Criminal Record Check', 'country_id' => 1, 'turnaround' => '3-5 days', 'price' => 50.00, 'required_docs' => ['consent']],
            ['id' => 2, 'name' => 'Employment Verification', 'country_id' => 1, 'turnaround' => '5-7 days', 'price' => 80.00, 'required_docs' => ['consent', 'cv']],
            ['id' => 3, 'name' => 'Education Verification', 'country_id' => 1, 'turnaround' => '5-7 days', 'price' => 60.00, 'required_docs' => ['consent']],
            ['id' => 4, 'name' => 'Credit Check', 'country_id' => 1, 'turnaround' => '2-3 days', 'price' => 45.00, 'required_docs' => ['consent']],
            ['id' => 5, 'name' => 'Reference Check', 'country_id' => 1, 'turnaround' => '3-5 days', 'price' => 70.00, 'required_docs' => ['consent']],
            ['id' => 6, 'name' => 'Social Media Screening', 'country_id' => 1, 'turnaround' => '1-2 days', 'price' => 40.00, 'required_docs' => []],
            ['id' => 7, 'name' => 'Criminal Record Check', 'country_id' => 2, 'turnaround' => '5-7 days', 'price' => 90.00, 'required_docs' => ['consent']],
            ['id' => 8, 'name' => 'Employment Verification', 'country_id' => 2, 'turnaround' => '7-10 days', 'price' => 110.00, 'required_docs' => ['consent', 'cv']],
        ]);

        $packages = collect([
            ['id' => 1, 'name' => 'Standard Package', 'country_id' => 1, 'scope_ids' => [1, 3], 'price' => 100.00],
            ['id' => 2, 'name' => 'Premium Package', 'country_id' => 1, 'scope_ids' => [1, 2, 3, 4], 'price' => 200.00],
        ]);

        $identityTypes = collect([
            ['id' => 1, 'name' => 'NRIC'],
            ['id' => 2, 'name' => 'Passport'],
            ['id' => 3, 'name' => 'Army / Police ID'],
        ]);

        return view('client.request.create.index', compact('countries', 'scopes', 'packages', 'identityTypes'));
    }

    public function submit(Request $request)
    {
        return redirect()->route('client.request.success');
    }

    public function successful()
    {
        return view('client.request.success');
    }
}
