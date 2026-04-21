<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestCandidate;
use App\Models\ScreeningRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ScreeningRequest::with('customer')
            ->withCount('candidates');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'ilike', "%{$search}%")
                    ->orWhereHas('customer', fn ($r) => $r->where('name', 'ilike', "%{$search}%"));
            });
        }

        $requests = $query->latest()->get();

        $counts = [
            'all' => ScreeningRequest::count(),
            'new' => ScreeningRequest::where('status', 'new')->count(),
            'in_progress' => ScreeningRequest::where('status', 'in_progress')->count(),
            'flagged' => ScreeningRequest::where('status', 'flagged')->count(),
            'complete' => ScreeningRequest::where('status', 'complete')->count(),
        ];

        return view('admin.requests.index', compact('requests', 'counts', 'status', 'search'));
    }

    public function show(int $id)
    {
        $request = ScreeningRequest::with([
            'customer',
            'candidates.scopeTypes',
            'candidates.identityType',
            'submittedBy',
        ])->findOrFail($id);

        return view('admin.requests.show', compact('request'));
    }

    public function updateStatus(Request $httpRequest, int $id)
    {
        $httpRequest->validate([
            'status' => ['required', 'in:new,in_progress,flagged,complete'],
        ]);

        ScreeningRequest::findOrFail($id)->update(['status' => $httpRequest->status]);

        return back()->with('success', 'Request status updated.');
    }

    public function updateCandidateStatus(Request $httpRequest, int $id)
    {
        $httpRequest->validate([
            'status' => ['required', 'in:new,in_progress,flagged,complete'],
        ]);

        RequestCandidate::findOrFail($id)->update(['status' => $httpRequest->status]);

        return back()->with('success', 'Candidate status updated.');
    }
}
