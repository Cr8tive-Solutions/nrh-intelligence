<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\RequestCandidate;
use App\Models\ScreeningRequest;
use App\Support\Pii;
use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    public function index()
    {
        return view('client.requests.track', ['query' => '', 'results' => null, 'requestMatch' => null]);
    }

    public function search(Request $request)
    {
        $customerId = session('client_customer_id');
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
                    ->orWhereHas('screeningRequest', fn ($r) => $r->where('reference', 'ilike', "%{$query}%"));

                // identity_number is encrypted at rest, so substring search is
                // impossible — match the full IC exactly via the blind index.
                // When encryption is off, keep the legacy substring behaviour.
                if (Pii::enabled()) {
                    $q->orWhere(fn ($m) => $m->whereIdentityNumber($query));
                } else {
                    $q->orWhere('identity_number', 'ilike', "%{$query}%");
                }
            })
            ->get();

        return view('client.requests.track', compact('query', 'results', 'requestMatch'));
    }
}
