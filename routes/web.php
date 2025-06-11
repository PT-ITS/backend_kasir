<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;



Route::get('/password/{id}', [EmailController::class, 'password'])->name('password');
Route::post('/aktivasi', [EmailController::class, 'aktivasi'])->name('aktivasi');

    // Pastikan bahwa request bukan untuk route aktivasi
    if (request()->path() !== 'password' && request()->path() !== 'aktivasi') {
        Route::get('/{any?}', function () {
            return view('welcome');
        })->where('any', '.*');
    } 
