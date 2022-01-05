<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /* Process the logout request */
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login')->with(['msg_body' => 'You signed out!']);
    }
}
