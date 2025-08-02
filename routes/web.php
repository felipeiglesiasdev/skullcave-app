<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});


// Rotas de Autenticação Manual
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas Protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
