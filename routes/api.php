<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesOrderController;

Route::get('/sales-orders', [SalesOrderController::class, 'index']);
Route::get('/sales-orders/{id}', [SalesOrderController::class, 'show']);
Route::post('/sales-orders', [SalesOrderController::class, 'store']);
Route::put('/sales-orders/{id}', [SalesOrderController::class, 'update']);
Route::delete('/sales-orders/{id}', [SalesOrderController::class, 'destroy']);
