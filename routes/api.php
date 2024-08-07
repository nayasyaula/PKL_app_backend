<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToDoListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth'])->group(function () {
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('todolist', [ToDoListController::class, "test"])->name("test");
    Route::post('todolist/store', [ToDoListController::class, 'storeApi'])->name('ToDoList.store');
    Route::put('todolist/{id}/update', [ToDoListController::class, 'updateApi'])->name('ToDoList.update');
    Route::get('/create-document', [ToDoListController::class, 'createDocumentApi'])->name('word.tdl');
});
