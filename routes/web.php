<?php

use App\Http\Controllers\AttendanceModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToDoListController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('template');
});

// Authentication routes
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Attendance routes
    Route::get('/pages', [AttendanceModelController::class, 'index'])->name('pages');
    Route::post('/store', [AttendanceModelController::class, 'store'])->name('store');
    Route::patch('/update/{id}', [AttendanceModelController::class, 'update'])->name('update');
    Route::get('/attendance/create-document', [AttendanceModelController::class, 'createDocumentWeb'])->name('word.attendance');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('show.user');
    Route::put('/user/updateStatus/{id}', [UserController::class, 'updateStatus'])->name('user.updateStatus');
    Route::post('/user/pesan/{id}', [UserController::class, 'pesan'])->name('user.pesan');

    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/user/show/{id}', [UserController::class, 'show'])->name('show.user');

    Route::get('/ToDoList/index', [ToDoListController::class, 'index'])->name('ToDoList.index');
    Route::get('/todolist/create', [ToDoListController::class, 'create'])->name('ToDoList.create');
    Route::post('/todolist/store', [ToDoListController::class, 'storeWeb'])->name('ToDoList.store');
    Route::get('/todolist/{todolist}/edit', [ToDoListController::class, 'edit'])->name('ToDoList.edit');
    Route::put('/todolist/{todolist}/update', [ToDoListController::class, 'updateWeb'])->name('ToDoList.update');
    Route::delete('/todolist/{id}/delete', [ToDoListController::class, 'destroy'])->name('ToDoList.destroy');
    // Route di web.php
Route::get('/todolist/{id}/upload-file', [ToDoListController::class, 'showUploadForm'])->name('ToDoList.uploadFile');
Route::post('/todolist/{id}/upload-file', [ToDoListController::class, 'uploadFile'])->name('ToDoList.uploadFile.store');

    Route::put('/todolist/{id}/updateStatus', [ToDoListController::class, 'updateStatus'])->name('ToDoList.updateStatus');
    Route::post('/todolist/{id}/pesan', [ToDoListController::class, 'pesan'])->name('ToDoList.pesan');

    Route::get('/create-document', [ToDoListController::class, 'createDocumentWeb'])->name('word.tdl');
    // Route::get('/generate-qr/{userId}', [QRCodeController::class, 'showQRCode'])->name('generate-qr');
    // Route::get('/somepage', [QRCodeController::class, 'showPageWithQRCodeButton'])->name('somepage');

    // Rute untuk menandai absensi
Route::get('/mark-attendance', [AttendanceModelController::class, 'markAttendance'])->name('mark-attendance');

// Rute untuk menghasilkan QR Code
Route::get('/generate-qr', [QRCodeController::class, 'generateQRCode'])->name('generate-qr');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/edit', [ProfileController::class, 'showEditProfileForm'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change_password');
Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.update_password');

});
