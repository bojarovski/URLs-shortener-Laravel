<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/urls', [UrlController::class, 'index']);
Route::get('/url/{id}', [UrlController::class, 'show']);
Route::post('/shorten', [UrlController::class, 'store']);
Route::get('/{code}', [UrlController::class, 'redirect']);
