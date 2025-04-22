<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use GuzzleHttp\Psr7\Request;

Route::get('/', function () {
    return view('pages/homepage');
});
    
Route::middleware('guest')->group(function (){
    Route::get('/account/login', [AuthController::class, 'showLogin']);
    Route::get('/account/register', [AuthController::class, 'showRegister']);
    Route::post('/account/register', [AuthController::class, 'register']);
    Route::post('/account/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function (){
    Route::get('activity', function() {
        return view('pages/activity');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
Route::get('/email/verifyChange/{id}/{token}', [UserController::class, 'updateEmail'])->name('users.update');
Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/email/verify', function () {
    return response()->json([
        'message' => 'Your email must be verified before using.',
    ]);
})->name('verification.notice');
Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthorized.',
    ], 401);
})->name('login');
