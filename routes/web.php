<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RumahSakitController;


Route::get('/', [RumahSakitController::class, 'home'])->name('rumahSakit.home');
Route::get('/search', [RumahSakitController::class, 'search'])->name('rumahSakit.list');
Route::get('/search/detail/{id}', [RumahSakitController::class, 'detail'])->name('rumahSakit.detail');
// Route khusus untuk Chatbot (AJAX)
Route::post('/api/chat', [RumahSakitController::class, 'chat'])->name('api.chat');
Route::get('/terdekat', [RumahSakitController::class, 'terdekat'])
     ->name('rumahSakit.terdekat');
