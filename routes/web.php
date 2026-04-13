<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::get('/items/{item}/lendings', [\App\Http\Controllers\ItemController::class, 'showLendings'])->name('items.lendings');
    Route::resource('/items', \App\Http\Controllers\ItemController::class);
    Route::post('/lendings/{lending}/return', [\App\Http\Controllers\LendingController::class, 'returnItem'])->name('lendings.return');
    Route::resource('/lendings', \App\Http\Controllers\LendingController::class)->except(['create', 'show', 'edit', 'update']);
});

require __DIR__.'/auth.php';
