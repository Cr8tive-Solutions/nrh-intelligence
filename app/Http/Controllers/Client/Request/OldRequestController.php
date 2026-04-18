<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OldRequestController extends Controller
{
    public function index()
    {
        $requests = collect([
            ['id' => 90, 'reference' => 'REQ-2026-0090', 'candidates_count' => 2, 'status_id' => 3, 'status' => 'Complete', 'created_at' => '2026-03-01', 'completed_at' => '2026-03-07'],
            ['id' => 85, 'reference' => 'REQ-2026-0085', 'candidates_count' => 4, 'status_id' => 3, 'status' => 'Complete', 'created_at' => '2026-02-14', 'completed_at' => '2026-02-20'],
            ['id' => 80, 'reference' => 'REQ-2026-0080', 'candidates_count' => 1, 'status_id' => 3, 'status' => 'Complete', 'created_at' => '2026-01-10', 'completed_at' => '2026-01-16'],
        ]);

        return view('client.history.index', compact('requests'));
    }

    public function details(int $id)
    {
        $request = [
            'id' => $id,
            'reference' => 'REQ-2026-0' . $id,
            'status_id' => 3,
            'status' => 'Complete',
            'created_at' => '2026-03-01',
            'completed_at' => '2026-03-07',
            'submitted_by' => 'Demo User',
            'scopes' => ['Criminal Record Check', 'Education Verification'],
            'candidates' => [
                ['id' => 1, 'name' => 'Lim Wei Kiat',  'identity_number' => '880512-10-1234', 'status_id' => 3, 'status' => 'Complete'],
                ['id' => 2, 'name' => 'Noor Farah bt Ismail', 'identity_number' => '921103-14-5678', 'status_id' => 3, 'status' => 'Complete'],
            ],
        ];

        return view('client.history.details', compact('request'));
    }
}
