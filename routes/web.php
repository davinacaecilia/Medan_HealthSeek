<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\RumahSakitController; // 1. TAMBAHKAN INI


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pencarian', [PencarianController::class, 'index'])->name('pencarian');


Route::get('/detail/{id}', [RumahSakitController::class, 'show'])->name('rumahSakit.detail');
