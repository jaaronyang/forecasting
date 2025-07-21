<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index',[
            "title" => "Login"
        ]);
    }
    public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email:dns',
        'password' => 'required'
    ]);

    if(Auth::attempt($credentials)) {
    $request->session()->regenerate();

    // Redirect sesuai role
    $role = Auth::user()->role;
    if ($role === 'ppic') {
    return redirect()->route('ppic.dashboard');
} elseif ($role === 'manajer') {
    return redirect()->route('manajer.dashboard');
}


    return redirect()->intended('/dashboard');
}
}

}
