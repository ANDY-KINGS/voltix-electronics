<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('mpesa')->group(function () {
    Route::post('callback', [MpesaController::class, 'callback']);
    Route::get('checkStatus/{order}', [MpesaController::class, 'checkStatus']);
});
