<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RumahSakitController; 


Route::get('/', [RumahSakitController::class, 'home'])->name('rumahSakit.home');
Route::get('/search', [RumahSakitController::class, 'search'])->name('rumahSakit.list');
Route::get('/search/detail/{id}', [RumahSakitController::class, 'detail'])->name('rumahSakit.detail');
