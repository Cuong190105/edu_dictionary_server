<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FlashcardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VocabController;

Route::middleware('auth:sanctum')->group(function (){
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
    Route::post('/user/changeEmail', [UserController::class, 'updateEmail']);
    Route::post('/user/changePassword', [UserController::class, 'updatePassword']);
    Route::post('/user/changeName', [UserController::class, 'updateName']);
    Route::post('/user/changeStreak', [UserController::class, 'updateStreak']);
    Route::post('/sync/uploadFlashcards', [FlashcardController::class, 'uploadCardSets']);
    Route::get('/sync/downloadFlashcards', [FlashcardController::class, 'downloadCardSets']);
    Route::post('/sync/uploadWords', [VocabController::class, 'uploadWords']);
    Route::get('/sync/downloadWords', [VocabController::class, 'downloadWords']);
    Route::get('/sync/files', [DownloadController::class, 'downloadFiles']);
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
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