<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/shorten', [UrlController::class, 'store']);
Route::get('/{shortUrl}', [UrlController::class, 'redirect']);
