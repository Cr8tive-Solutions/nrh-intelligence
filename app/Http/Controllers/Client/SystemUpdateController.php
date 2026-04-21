<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SystemUpdate;

class SystemUpdateController extends Controller
{
    public function index()
    {
        $updates = SystemUpdate::published()
            ->orderByDesc('released_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn ($u) => $u->released_at->format('Y-m-d'));

        return view('client.updates.index', compact('updates'));
    }
}
