<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // 🔑 Tambahkan ini

class RegisterController extends Controller
{
    public function index()
    {
        return view('register.index', [
            "title" => "Register"
        ]);
    }

    public function store(Request $request)
{
    $validateData = $request->validate([
        'name'     => 'required|max:255',
        'username' => 'required|unique:users|min:3|max:255',
        'email'    => 'required|email|unique:users|max:255',
        'password' => 'required|min:5|max:255',
        'role'     => 'required|in:ppic,manajer',
    ]);

    // Hash password
    $validateData['password'] = bcrypt($validateData['password']);

    User::create($validateData);

    return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
}
}
