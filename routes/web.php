<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('tasks.index')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Login', []))->name('login');
    Route::get('/register', fn () => Inertia::render('Register', []))->name('register');

    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/tasks', fn() => Inertia::render('Tasks/Index', []))->name('tasks.index');
    Route::get('/stats', fn() => Inertia::render('Stats/Index', []))->name('stats.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
