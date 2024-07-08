<?php

use App\Http\Controllers\AttendanceModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToDoListController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('template');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route::resource('todolist', ToDoListController::class);
Route::get('/ToDoList/index', [ToDoListController::class, 'index'])->name('ToDoList.index');
Route::get('/todolist/create', [ToDoListController::class, 'create'])->name('ToDoList.create');
Route::post('/todolist/store', [ToDoListController::class, 'store'])->name('ToDoList.store');
Route::get('/todolist/{id}/edit', [ToDoListController::class, 'edit'])->name('ToDoList.edit');
Route::put('/todolist/{id}/update', [ToDoListController::class, 'update'])->name('ToDoList.update');
Route::delete('/todolist/{id}/delete', [ToDoListController::class, 'destroy'])->name('ToDoList.destroy');
Route::put('/todolist/{id}/updateStatus', [ToDoListController::class, 'updateStatus'])->name('ToDoList.updateStatus');

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

