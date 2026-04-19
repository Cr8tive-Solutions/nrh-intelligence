<?php

namespace App\Http\Controllers\Client\Scope;

use App\Http\Controllers\Controller;
use App\Models\Country;

class MapsController extends Controller
{
    public function index()
    {
        $countries = Country::withCount('scopeTypes')->get();

        return view('client.scope.maps', compact('countries'));
    }

    public function country(int $countryId)
    {
        $country = Country::findOrFail($countryId);
        $scopes = $country->scopeTypes()->orderBy('name')->get();

        return view('client.scope.country', compact('country', 'scopes'));
    }
}
