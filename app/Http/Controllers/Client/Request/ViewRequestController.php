<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewRequestController extends Controller
{
    public function index()
    {
        $requests = collect([
            ['id' => 101, 'reference' => 'REQ-2026-0101', 'candidates_count' => 3, 'status_id' => 2, 'status' => 'In Progress', 'created_at' => '2026-04-15'],
            ['id' => 102, 'reference' => 'REQ-2026-0102', 'candidates_count' => 1, 'status_id' => 1, 'status' => 'New',         'created_at' => '2026-04-17'],
            ['id' => 103, 'reference' => 'REQ-2026-0103', 'candidates_count' => 5, 'status_id' => 2, 'status' => 'In Progress', 'created_at' => '2026-04-10'],
            ['id' => 104, 'reference' => 'REQ-2026-0104', 'candidates_count' => 2, 'status_id' => 1, 'status' => 'New',         'created_at' => '2026-04-18'],
        ]);

        return view('client.requests.index', compact('requests'));
    }

    public function details(int $id)
    {
        $request = [
            'id' => $id,
            'reference' => 'REQ-2026-0' . $id,
            'status_id' => 2,
            'status' => 'In Progress',
            'created_at' => '2026-04-15',
            'submitted_by' => 'Demo User',
            'scopes' => ['Criminal Record Check', 'Employment Verification'],
            'candidates' => [
                ['id' => 1, 'name' => 'Ahmad bin Razali',    'identity_number' => '900101-14-5678', 'status_id' => 2, 'status' => 'In Progress'],
                ['id' => 2, 'name' => 'Siti Aisyah Mohd',   'identity_number' => '950210-10-3456', 'status_id' => 1, 'status' => 'New'],
                ['id' => 3, 'name' => 'Rajesh Kumar',        'identity_number' => 'A12345678',      'status_id' => 3, 'status' => 'Complete'],
            ],
        ];

        return view('client.requests.details', compact('request'));
    }
}
