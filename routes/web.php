<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UrlController::class, 'dashboard'])->name('dashboard');
    
    // URL Management
    Route::get('/urls/create', [UrlController::class, 'create'])->name('urls.create');
    Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');
    Route::get('/urls/{url}', [UrlController::class, 'show'])->name('urls.show');
    Route::get('/urls/{url}/edit', [UrlController::class, 'edit'])->name('urls.edit');
    Route::put('/urls/{url}', [UrlController::class, 'update'])->name('urls.update');
    Route::delete('/urls/{url}', [UrlController::class, 'destroy'])->name('urls.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // URLs Management
    Route::get('/urls', [AdminController::class, 'urls'])->name('urls');
    Route::post('/urls/{url}/toggle', [AdminController::class, 'toggleUrlStatus'])->name('urls.toggle');
    Route::delete('/urls/{url}', [AdminController::class, 'deleteUrl'])->name('urls.delete');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdminStatus'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
});

/*
|--------------------------------------------------------------------------
| Short URL Redirect (Must be last - catches all short codes)
|--------------------------------------------------------------------------
*/

Route::get('/{shortCode}', [UrlController::class, 'redirect'])
    ->where('shortCode', '[a-zA-Z0-9_-]{4,10}')
    ->name('url.redirect');
