<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentsController;
use App\Http\Middleware\EnsureUserIsLogged;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login_google', [AuthController::class, 'login_google']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/confirm_account', [AuthController::class, 'confirm_account']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/trainers', [TrainerController::class, 'index']);
Route::get('/trainer/{id}', [TrainerController::class, 'show']);

Route::middleware(EnsureUserIsLogged::class)->group(function () {
    Route::get('/profile', [UserController::class, 'dashboard'])->name('profile');
    Route::post('/set_role', [AuthController::class, 'setRole']);
    Route::post('/profil/update', [UserController::class, 'update']);
    Route::put('/profil/updateScore', [UserController::class, 'updateScore']);
    Route::post('/profil/gallery', [UserController::class, 'gallery']);
    Route::get('/profil/calendar', [UserController::class, 'calendar']);
    Route::post('/profil/events/create', [UserController::class, 'createEvent']);
    Route::delete('/profil/events/{id}/delete', [UserController::class, 'deleteEvent']);
    Route::post('/profil/updateStats', [UserController::class, 'updateStats']);
    Route::post('/profil/resetStats', [UserController::class, 'resetStats']);

    Route::get('/profil/communicator', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/conversations', [ChatController::class, 'getConversations']);
    Route::get('/profile/conversations', [UserController::class, 'fetchConversations'])->name('profile.conversations');
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    Route::post('/trainer/review/{uid}', [TrainerController::class, 'submitReview']);
    Route::post('/payment/promotion', [PaymentsController::class, 'promotion']);
});