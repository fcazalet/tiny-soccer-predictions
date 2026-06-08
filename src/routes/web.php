<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Auth\OtpController;

Route::get('/', function () {
    return view('welcome');
});

// Auth OTP
Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpController::class, 'showEmailForm'])->name('login');
    Route::post('/login/send', [OtpController::class, 'sendOtp'])->name('auth.otp.send');
    Route::get('/login/verify', [OtpController::class, 'showOtpForm'])->name('auth.otp.form');
    Route::post('/login/verify', [OtpController::class, 'verifyOtp'])->name('auth.otp.verify');
});

Route::post('/logout', [OtpController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (protégé)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


// Dashboard & pronostics (joueurs connectés)
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/predictions/{match}', [PredictionController::class, 'store'])->name('predictions.store');
    Route::post('/predictions/{match}/json', [PredictionController::class, 'storeJson'])->name('predictions.store.json');
});

// Administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/matches', [AdminMatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/create', [AdminMatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [AdminMatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/{match}/score', [AdminMatchController::class, 'editScore'])->name('matches.score');
    Route::put('/matches/{match}/score', [AdminMatchController::class, 'updateScore'])->name('matches.updateScore');
});
