<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\IdentityType;
use App\Models\RequestCandidate;
use App\Models\ScreeningRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddCandidateController extends Controller
{
    private const BLOCKED_STATUSES = ['complete', 'updated', 'rejected'];

    public function store(Request $request, int $requestId)
    {
        $customerId = session('client_customer_id');

        $screeningRequest = ScreeningRequest::with(['candidates.scopeTypes', 'customer.agreement'])
            ->where('customer_id', $customerId)
            ->findOrFail($requestId);

        abort_if($screeningRequest->customer->isCashBilled(), 403, 'Only available for credit-billed accounts.');
        abort_if(! is_null($screeningRequest->invoice_id), 403, 'This request has already been invoiced.');
        abort_if(in_array($screeningRequest->status, self::BLOCKED_STATUSES), 403, 'Cannot add candidates to a completed or rejected request.');

        $existingScopeIds = $screeningRequest->candidates
            ->flatMap(fn ($c) => $c->scopeTypes->pluck('id'))
            ->unique()
            ->values()
            ->all();

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'identity_number'  => 'required|string|max:100',
            'identity_type_id' => 'required|integer|exists:identity_types,id',
            'scope_type_ids'   => 'required|array|min:1',
            'scope_type_ids.*' => 'integer|in:'.implode(',', $existingScopeIds ?: [0]),
            'mobile'           => 'nullable|string|max:30',
            'remarks'          => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($screeningRequest, $data) {
            $candidate = RequestCandidate::create([
                'screening_request_id' => $screeningRequest->id,
                'identity_type_id'     => $data['identity_type_id'],
                'name'                 => $data['name'],
                'identity_number'      => $data['identity_number'],
                'mobile'               => $data['mobile'] ?? null,
                'remarks'              => $data['remarks'] ?? null,
                'status'               => 'new',
            ]);

            $candidate->scopeTypes()->attach(
                collect($data['scope_type_ids'])->mapWithKeys(
                    fn ($id) => [$id => ['status' => 'new', 'assigned_at' => now()]]
                )->all()
            );
        });

        return redirect()->route('client.requests.details', $requestId)
            ->with('success', "Candidate {$data['name']} added to request.");
    }
}
