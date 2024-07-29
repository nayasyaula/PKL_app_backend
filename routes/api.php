<?php

use App\Http\Controllers\UserController;
use Illuminate\Routing\Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);