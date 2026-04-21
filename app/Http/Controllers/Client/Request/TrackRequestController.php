<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\RequestCandidate;
use App\Models\ScreeningRequest;
use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    public function index()
    {
        return view('client.requests.track', ['query' => '', 'results' => null, 'requestMatch' => null]);
    }

    public function search(Request $request)
    {
        $customerId = session('client_customer_id', 1);
        $query = trim($request->input('q', ''));

        if ($query === '') {
            return redirect()->route('client.requests.track');
        }

        // If query looks like a request reference, search at request level
        $requestMatch = null;
        if (str_contains(strtoupper($query), 'REQ-') || ctype_digit(str_replace('-', '', $query))) {
            $requestMatch = ScreeningRequest::with(['candidates.scopeTypes', 'candidates.identityType'])
                ->where('customer_id', $customerId)
                ->where('reference', 'ilike', "%{$query}%")
                ->first();
        }

        $results = RequestCandidate::with(['screeningRequest', 'scopeTypes', 'identityType'])
            ->whereHas('screeningRequest', fn ($q) => $q->where('customer_id', $customerId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('identity_number', 'ilike', "%{$query}%")
                    ->orWhereHas('screeningRequest', fn ($r) => $r->where('reference', 'ilike', "%{$query}%"));
            })
            ->get();

        return view('client.requests.track', compact('query', 'results', 'requestMatch'));
    }
}
