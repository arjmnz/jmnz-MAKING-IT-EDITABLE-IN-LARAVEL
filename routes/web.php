<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'today'])->name('home');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::resource('customers', \App\Http\Controllers\CustomerController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::resource('users', \App\Http\Controllers\UserController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
