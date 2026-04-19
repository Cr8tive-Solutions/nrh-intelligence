<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RequestCandidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CandidatesController extends Controller
{
    public function show(int $id): View|RedirectResponse
    {
        $customerId = session('client_customer_id', 1);

        $candidate = RequestCandidate::whereHas(
            'screeningRequest',
            fn ($q) => $q->where('customer_id', $customerId)
        )
            ->with(['screeningRequest.submittedBy', 'scopeTypes', 'identityType'])
            ->findOrFail($id);

        return view('client.candidates.show', compact('candidate'));
    }

    public function index(): View
    {
        $customerId = session('client_customer_id', 1);

        $allCandidates = RequestCandidate::whereHas(
            'screeningRequest',
            fn ($q) => $q->where('customer_id', $customerId)
        )->with(['screeningRequest.submittedBy', 'scopeTypes'])->get();

        $stats = [
            'total' => $allCandidates->count(),
            'active' => $allCandidates->whereIn('status', ['new', 'in_progress', 'flagged'])->count(),
            'consent' => $allCandidates->where('status', 'new')->count(),
            'collecting' => $allCandidates->where('status', 'in_progress')->count(),
            'review' => $allCandidates->where('status', 'flagged')->count(),
            'complete' => $allCandidates->where('status', 'complete')->count(),
        ];

        $candidates = RequestCandidate::whereHas(
            'screeningRequest',
            fn ($q) => $q->where('customer_id', $customerId)
        )
            ->with(['screeningRequest.submittedBy', 'scopeTypes'])
            ->latest()
            ->paginate(12);

        return view('client.candidates.index', [
            'stats' => $stats,
            'candidates' => $candidates,
        ]);
    }
}
