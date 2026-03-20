<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\StudyProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::get('/choose-identity', function () {
    return view('choose-identity');
})->name('identity.choose');

Route::post('/guest/start', [GuestController::class, 'start'])->name('guest.start');
Route::post('/guest/logout', [GuestController::class, 'logout'])->name('guest.logout');

Route::get('/study-profile', [StudyProfileController::class, 'index'])->name('study.profile');

// 练习模块
Route::get('/practice', [PracticeController::class, 'show'])->name('practice.show');
Route::post('/practice/submit', [PracticeController::class, 'submit'])->name('practice.submit');

require __DIR__.'/auth.php';