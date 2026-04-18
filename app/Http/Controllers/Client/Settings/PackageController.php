<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = collect([
            ['id' => 1, 'name' => 'Standard Screening', 'country' => 'Malaysia', 'scopes' => ['Criminal Record Check', 'Employment Verification'], 'created_at' => '2026-02-01'],
            ['id' => 2, 'name' => 'Full Background',    'country' => 'Malaysia', 'scopes' => ['Criminal Record Check', 'Employment Verification', 'Education Verification', 'Credit Check'], 'created_at' => '2026-03-15'],
        ]);

        $allScopes = collect([
            ['id' => 1, 'name' => 'Criminal Record Check'],
            ['id' => 2, 'name' => 'Employment Verification'],
            ['id' => 3, 'name' => 'Education Verification'],
            ['id' => 4, 'name' => 'Credit Check'],
            ['id' => 5, 'name' => 'Reference Check'],
            ['id' => 6, 'name' => 'Social Media Screening'],
        ]);

        return view('client.settings.packages.index', compact('packages', 'allScopes'));
    }
}
