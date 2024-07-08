<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ToDoListController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route::resource('todolist', ToDoListController::class);
Route::get('/ToDoList/index', [ToDoListController::class, 'index'])->name('ToDoList.index');
Route::get('/todolist/create', [ToDoListController::class, 'create'])->name('ToDoList.create');
Route::post('/todolist/store', [ToDoListController::class, 'store'])->name('ToDoList.store');
Route::get('/todolist/{todolist}/edit', [ToDoListController::class, 'edit'])->name('ToDoList.edit');
Route::put('/todolist/{todolist}/update', [ToDoListController::class, 'update'])->name('ToDoList.update');
Route::delete('/todolist/{id}/delete', [ToDoListController::class, 'destroy'])->name('ToDoList.destroy');
Route::put('/todolist/{id}/updateStatus', [ToDoListController::class, 'updateStatus'])->name('ToDoList.updateStatus');

