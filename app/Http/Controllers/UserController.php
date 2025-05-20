<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChangeEmail;

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
            if (! $request->hasValidSignature()) {
                return response()->json(['message' => 'Invalid or expired verification link.'], 403);
            }
            $user = User::findOrFail($request->route('id'));
            $changeRequest = DB::table("email_change_tokens")
                ->where('email', $user->email)
                ->where('expiration', '>', now())
                ->first();
            if (! hash_equals(sha1($changeRequest->email.$changeRequest->new_email), (string) $request->route('token'))) {
                return response()->json(['message' => 'Invalid hash.'], 403);
            }
            $user->email = $changeRequest->new_email;
            $user->save();
            DB::table("email_change_tokens")
                ->where('email', $changeRequest->email)
                ->delete();
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
                'message' => 'Request failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeEmailRequest(Request $request) {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
            ]);
    
            $user = $request->user();
            $token = sha1($user->email.$request->email);
            Mail::to($request->email)->send(new ChangeEmail($user, $token));
            DB::table("email_change_tokens")->upsert(
                ["email" => $user->email, "new_email" => $request->email, 'expiration' => now()->addMinutes(60)],
                ['email'],
                ['new_email', 'expiration'],
            );
            return response()->json([
                'message' => 'A verification email has been sent to your new email address',
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
                'file' => 'required|image|mimes:jpeg,jpg|max:2048',
            ]);
    
            $user = $request->user();
            $file = $request->file('file')->store('avatar', 'local');
            $oldPath = $user->avatar;
            if ($oldPath != null && Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
            $user->avatar = $file;
            $user->save();
    
            return response()->json([
                'message' => 'Profile picture updated successfully',
                'avatar' => $user->avatar,
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

    public function forgotPassword(Request $request) {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
    
            $status = Password::sendResetLink(
                $request->only('email'), function ($user, $token) {
                    $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    Cache::put($code.$user->email, $token, 3600);
                    $user->sendPasswordResetNotification($code);
                }
            );
    
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Password reset link sent',
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to send password reset link',
                    'errors' => [$status],
                ], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);
         
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ]);
                    $user->save();
         
                    event(new PasswordReset($user));
                }
            );
            return response()->json([
                'message' => 'Password reset successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Reset failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
                'device_name' => 'required',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            event(new Registered($user));
            $token = $user->createToken($request->device_name)->plainTextToken;
            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            Log::error($e);
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

    public function verifyEmail(Request $request) {
        try {
            if (! $request->hasValidSignature()) {
                return response()->json(['message' => 'Invalid or expired verification link.'], 403);
            }
    
            $user = User::findOrFail($request->route('id'));
    
            if (! hash_equals(sha1($user->email), (string) $request->hash)) {
                return response()->json(['message' => 'Invalid hash.'], 403);
            }
    
            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified.']);
            }
    
            $user->markEmailAsVerified();
    
            return view('pages/verified-notice');
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'objects' => $request->all(),
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Verification failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
