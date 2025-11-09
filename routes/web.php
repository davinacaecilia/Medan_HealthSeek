<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PencarianController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rumah-sakit/{id}', [HomeController::class, 'detail'])->name('rumahSakit.detail');

Route::get('/pencarian', [PencarianController::class, 'index'])->name('pencarian');
