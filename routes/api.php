<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FlashcardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VocabController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

Route::middleware(['auth:sanctum', 'verified'])->group(function (){
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            "name" => $user->name,
            "email" => $user->email,
            "avatar" => $user->avatar,
        ]);
    });
    Route::get('/user/avatar', [DownloadController::class, 'downloadAvatar']);
    Route::post('/user/changeAvatar', [UserController::class, 'updateAvatar']);
    Route::post('/user/changeEmail', [UserController::class, 'changeEmailRequest']);
    Route::post('/user/changePassword', [UserController::class, 'updatePassword']);
    Route::post('/user/changeName', [UserController::class, 'updateName']);
    Route::post('/user/changeStreak', [UserController::class, 'updateStreak']);
    Route::post('/sync/uploadFlashcards', [FlashcardController::class, 'uploadCardSets']);
    Route::get('/sync/downloadFlashcards', [FlashcardController::class, 'downloadCardSets']);
    Route::post('/sync/uploadWords', [VocabController::class, 'uploadWords']);
    Route::get('/sync/downloadWords', [VocabController::class, 'downloadWords']);
    Route::get('/sync/files', [DownloadController::class, 'downloadFiles']);
    Route::get('/testverify', function (Request $request) {
        return response()->json([
            'message' => 'Test succeeded',
        ]);
    });
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/verify-reset', function(Request $request) {
    $key = $request->code.$request->email;
    if(!Cache::has($key)) {
        return response()->json([
            'message' => 'Invalid code',
        ], 422);
    }
    $token = Cache::get($key);
    Cache::forget($key);
    return response()->json([
        'message' => 'Valid code',
        'token' => $token,
    ]);
});
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::post('/verification-notification', function(Request $request) {
    $user = $request->user();
    if ($user->hasVerifiedEmail()) {
        return response()->json([
            'message' => 'Email already verified',
        ], 422);
    }
    $user->sendEmailVerificationNotification();
    return response()->json([
        'message' => 'Verification email sent',
    ]);
})->middleware(['auth:sanctum', 'throttle:6,1']);



Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => 'Test succeeded',
    ]);
});

Route::post('/test', function (Request $request) {
    return response()->json([
        'message' => 'Test post succeeded',
        'sent' => $request->all(),
    ]);
});