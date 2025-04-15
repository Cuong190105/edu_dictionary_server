<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\basicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('pages/homepage');
});
    
Route::middleware('guest')->group(function (){
    Route::get('/account/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/account/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/account/register', [AuthController::class, 'register']);
    Route::post('/account/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function (){
    Route::get('activity', function() {
        return view('pages/activity');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});