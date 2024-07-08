<?php

use App\Http\Controllers\AttendanceModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/pages', [AttendanceModelController::class, 'index'])->name('pages');
    Route::post('/store', [AttendanceModelController::class, 'store'])->name('store');
    Route::patch('/update/{id}', [AttendanceModelController::class, 'update'])->name('update');

    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/user/show/{id}', [UserController::class, 'show'])->name('show');

    // Route::middleware(['checkUserRole'])->group(function () {
    //     // Routes yang membutuhkan pengecekan role
    // });
});
