<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin() {
        return view('account/login');
    }

    public function showRegister() {
        return view('account/register');
    }

    public function login(Request $request): RedirectResponse {
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return to_route('dashboard');
        } else {
            return redirect()->back()->withErrors(['Invalid credentials']);
        }
    }

    public function register(Request $request): RedirectResponse {
        try {
            $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|confirmed',
            ]);

            $user = Admin::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        }
    }

    public function updateAdmin(Request $request) {

    }

    public function logout(Request $request) {
        Log::info("Using AuthController");
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
