<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\RequestCandidate;
use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    public function index()
    {
        return view('client.requests.track', ['query' => '', 'results' => null]);
    }

    public function search(Request $request)
    {
        $customerId = session('client_customer_id', 1);
        $query = trim($request->input('q', ''));

        $results = RequestCandidate::with(['screeningRequest', 'scopeTypes', 'identityType'])
            ->whereHas('screeningRequest', fn ($q) => $q->where('customer_id', $customerId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('identity_number', 'like', "%{$query}%");
            })
            ->get();

        return view('client.requests.track', compact('query', 'results'));
    }
}
