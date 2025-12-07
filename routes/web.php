<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsLogged;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Home');
// });

Route::get('/test', function () {
    $start = Carbon::now()->startOfMonth();
    $end   = Carbon::now()->endOfMonth();

    $days = collect(CarbonPeriod::create($start, $end))
        ->map(function ($day) {
            return [
                'date' => $day->toDateString(),
                'weekday' => $day->locale('pl')->dayName,
                'hours' => collect(range(6, 23)) // godziny od 6 do 23
                    ->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00')
                    ->toArray()
            ];
        });
    dd($days);
});

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

    Route::post('/trainer/review/{uid}', [TrainerController::class, 'submitReview']);
});