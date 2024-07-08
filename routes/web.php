<?php

use App\Http\Controllers\AttendanceModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/admin/pages', [AttendanceModelController::class, 'index'])->name('home');
    Route::post('/admin/store', [AttendanceModelController::class, 'store'])->name('admin.store');
    Route::post('/admin/update', [AttendanceModelController::class, 'update'])->name('admin.update');
});
