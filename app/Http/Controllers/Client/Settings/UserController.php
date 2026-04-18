<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = collect([
            ['id' => 1, 'name' => 'Demo User',       'email' => 'demo@nrh-intelligence.com',  'role' => 'Admin',    'status' => 'Active',   'created_at' => '2026-01-01'],
            ['id' => 2, 'name' => 'Sarah Lee',        'email' => 'sarah@nrh-intelligence.com', 'role' => 'User',     'status' => 'Active',   'created_at' => '2026-02-10'],
            ['id' => 3, 'name' => 'Hafiz Azman',      'email' => 'hafiz@nrh-intelligence.com', 'role' => 'User',     'status' => 'Inactive', 'created_at' => '2026-03-05'],
        ]);

        return view('client.settings.users.index', compact('users'));
    }
}
