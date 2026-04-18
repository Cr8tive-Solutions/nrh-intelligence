<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    public function index()
    {
        return view('client.requests.track', ['results' => null, 'query' => '']);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $results = collect([
            ['candidate_name' => 'Ahmad bin Razali', 'identity_number' => '900101-14-5678', 'request_reference' => 'REQ-2026-0101', 'request_id' => 101, 'status_id' => 2, 'status' => 'In Progress', 'scopes' => ['Criminal Record Check', 'Employment Verification'], 'updated_at' => '2026-04-17'],
        ])->filter(fn ($r) => str_contains(strtolower($r['candidate_name']), strtolower($query))
            || str_contains($r['identity_number'], $query));

        return view('client.requests.track', ['results' => $results, 'query' => $query]);
    }
}
