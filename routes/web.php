<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\CalendarController;
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
    Route::post('/set_role', [AuthController::class, 'setRole']); // do wywalenia

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'dashboard'])->name('profile');
        Route::post('/update', [ProfileController::class, 'update']);
        Route::put('/updateScore', [ProfileController::class, 'updateScore']);
        Route::post('/gallery', [ProfileController::class, 'gallery']);
    });

    Route::get('/profile/calendar', [CalendarController::class, 'index']);
    Route::post('/profil/events/create', [ProfileController::class, 'createEvent']);
    Route::delete('/profil/events/{id}/delete', [ProfileController::class, 'deleteEvent']);
    Route::post('/profil/updateStats', [ProfileController::class, 'updateStats']);
    Route::post('/profil/resetStats', [ProfileController::class, 'resetStats']);

    // Route::get('/profile/calendar', [CalendarController::class, 'index']);
    // Route::post('/profil/events/create', [ProfileController::class, 'createEvent']);
    // Route::delete('/profil/events/{id}/delete', [ProfileController::class, 'deleteEvent']);
    // Route::post('/profil/updateStats', [ProfileController::class, 'updateStats']);
    // Route::post('/profil/resetStats', [ProfileController::class, 'resetStats']);

    Route::get('/profil/communicator', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/conversations', [ChatController::class, 'getConversations']);
    Route::get('/profile/conversations', [ProfileController::class, 'fetchConversations'])->name('profile.conversations');
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    Route::post('/trainer/review/{uid}', [TrainerController::class, 'submitReview']);
    Route::post('/payment/promotion', [PaymentsController::class, 'promotion']);
});