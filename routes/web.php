<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\Profile\PromotionController;
use App\Http\Controllers\Profile\ChatController;
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

    Route::prefix('profile/calendar')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');
        Route::post('/events/create', [CalendarController::class, 'createEvent']);
        Route::delete('/events/{id}/delete', [CalendarController::class, 'deleteEvent']);
    });

    Route::prefix('profile/communicator')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/conversations', [ChatController::class, 'getConversations'])->name('profile.conversations');
    });

    Route::prefix('profile/promotion')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('promotion.index');
        Route::post('/', [PromotionController::class, 'promotion']);
    });

    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    Route::post('/trainer/review/{uid}', [TrainerController::class, 'submitReview']);
});