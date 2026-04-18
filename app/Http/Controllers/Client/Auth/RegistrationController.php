<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('client.auth.register');
    }

    public function submit(Request $request)
    {
        return redirect()->route('client.register.success');
    }

    public function success()
    {
        return view('client.auth.register-success');
    }
}
