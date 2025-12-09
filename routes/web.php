<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsLogged;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/confirm_account', [AuthController::class, 'confirm_account']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/trainers', [TrainerController::class, 'index']);
Route::get('/trainer/{id}', [TrainerController::class, 'show']);

Route::middleware(EnsureUserIsLogged::class)->group(function () {
    Route::get('/profil', [UserController::class, 'dashboard'])->name('profile');
    Route::post('/profil/update', [UserController::class, 'update']);
    Route::put('/profil/updateScore', [UserController::class, 'updateScore']);
    Route::get('/profil/calendar', [UserController::class, 'calendar']);
    Route::post('/profil/events/create', [UserController::class, 'createEvent']);
    Route::delete('/profil/events/{id}/delete', [UserController::class, 'deleteEvent']);
    Route::post('/profil/updateStats', [UserController::class, 'updateStats']);

    Route::post('/trainer/review/{uid}', [TrainerController::class, 'submitReview']);
});