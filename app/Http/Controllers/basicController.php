<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class basicController extends Controller
{
    public function createAccount(Request $request) {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }

    // public function login(Request $request) {
    //     $credentials = $request->only('email', 'password');
    //     if (auth()->attempt($credentials)) {
    //         return response()->json([
    //             'message' => 'Login successful',
    //             'user' => auth()->user(),
    //         ]);
    //     } else {
    //         return response()->json([
    //             'message' => 'Invalid credentials',
    //         ], 401);
    //     }
    // }

    // public function logout(Request $request) {
    //     auth()->logout();
    //     return response()->json([
    //         'message' => 'Logout successful',
    //     ]);
    // }

    // public function getData(Request $request) {
    //     $data = "Hi Im server";
    //     return response()->json([
    //         'data' => $data,
    //     ], 200);
    // }
}
