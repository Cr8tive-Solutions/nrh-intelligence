<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\IdentityType;
use App\Models\Package;
use App\Models\RequestCandidate;
use App\Models\ScopeType;
use App\Models\ScreeningRequest;
use Illuminate\Http\Request;

class CreateRequestController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $countries = Country::withCount('scopeTypes')->get();

        $scopes = ScopeType::all()->map(fn ($s) => [
            'id' => $s->id,
            'country_id' => $s->country_id,
            'name' => $s->name,
            'turnaround' => $s->turnaround,
            'price' => (float) $s->price,
        ]);

        $packages = Package::with('scopeTypes')
            ->where('customer_id', $customerId)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'country_id' => $p->country_id,
                'name' => $p->name,
                'scope_ids' => $p->scopeTypes->pluck('id')->all(),
                'price' => $p->scopeTypes->sum('price'),
            ]);

        $identityTypes = IdentityType::all();

        return view('client.request.create.index', compact('countries', 'scopes', 'packages', 'identityTypes'));
    }

    public function submit(Request $request)
    {
        $customerId = session('client_customer_id', 1);
        $userId = session('client_user_id', 1);

        $cart = json_decode($request->input('cart_data', '[]'), true);
        $candidates = json_decode($request->input('candidates_data', '[]'), true);

        $seq = ScreeningRequest::count() + 1;
        $reference = 'REQ-'.now()->format('Y').'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

        $screeningRequest = ScreeningRequest::create([
            'customer_id' => $customerId,
            'customer_user_id' => $userId,
            'reference' => $reference,
            'status' => 'new',
        ]);

        $scopeIds = collect($cart)->pluck('id')->all();

        foreach ($candidates as $c) {
            $candidate = RequestCandidate::create([
                'screening_request_id' => $screeningRequest->id,
                'identity_type_id' => (int) $c['identity_type_id'],
                'name' => $c['name'],
                'identity_number' => $c['identity_number'],
                'mobile' => $c['mobile'] ?? null,
                'remarks' => $c['remarks'] ?? null,
                'status' => 'new',
            ]);

            $candidate->scopeTypes()->attach(
                collect($scopeIds)->mapWithKeys(fn ($id) => [$id => ['status' => 'new']])->all()
            );
        }

        return redirect()->route('client.request.success');
    }

    public function successful()
    {
        return view('client.request.success');
    }
}
