<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;

class UserController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $users = CustomerUser::where('customer_id', $customerId)
            ->orderBy('name')
            ->get();

        return view('client.settings.users.index', compact('users'));
    }
}
