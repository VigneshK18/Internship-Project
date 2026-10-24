<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
| REST API (prefix /api). Authenticate by requesting a token:
|   POST /api/tokens  {"email": "...", "password": "..."}
| then send it as:  Authorization: Bearer <token>
*/

Route::post('/tokens', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('api.tokens.store');

Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user()->only('id', 'name', 'email'))->name('api.user');
    Route::delete('/tokens/current', [AuthController::class, 'destroy'])->name('api.tokens.destroy');
    Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('api.orders.show');
});
