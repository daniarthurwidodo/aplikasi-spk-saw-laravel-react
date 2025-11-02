<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login page
Route::get('/login', function () {
    return view('app');
})->name('login');

// Dashboard (protected route example)
Route::get('/dashboard', function () {
    return view('app');
})->name('dashboard');
