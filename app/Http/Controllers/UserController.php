<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login(Request $request) {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required|string',
                'device_name' => 'required',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
    
            $token = $user->createToken($request->device_name)->plainTextToken;
    
            return response()->json([
                'message' => 'Login succeeded',
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        }
    }

    public function updateEmail(Request $request) {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
            ]);
    
            $user = $request->user();
            $user->email = $request->email;
            $user->save();
    
            return response()->json([
                'message' => 'Email updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePassword(Request $request) {
        try {
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|confirmed',
            ]);
    
            $user = $request->user();
    
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'message' => 'Old password is incorrect',
                ], 401);
            }
    
            $user->password = bcrypt($request->new_password);
            $user->save();
    
            return response()->json([
                'message' => 'Password updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateName(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
    
            $user = $request->user();
            $user->name = $request->name;
            $user->save();
    
            return response()->json([
                'message' => 'Name updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateAvatar(Request $request) {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,jpg|max:2048',
            ]);
    
            $user = $request->user();
            $file = $request->file('profile_picture')->store('avatar', 'local');
            $oldPath = $user->avatar;
            if ($oldPath != "avatar/default.jpg" && Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
            $user->profile_picture = $file;
            $user->save();
    
            return response()->json([
                'message' => 'Profile picture updated successfully',
                'profile_picture' => $user->profile_picture,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStreak(Request $request) {
        try {
            $request->validate([
                'streak' => 'required|integer',
            ]);
    
            $user = $request->user();
            $user->streak = $request->streak;
            $user->save();
    
            return response()->json([
                'message' => 'Streak updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'name' => 'required|string|max:255',
                'password' => 'required|string|confirmed',
                'device_name' => 'required',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            $token = $user->createToken($request->device_name)->plainTextToken;
            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        }
    }

    public function logout(Request $request) {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
