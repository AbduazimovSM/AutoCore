<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ProductController;

Route::delete('references', [ReferenceController::class, 'destroySelected']);
Route::apiResource('references', ReferenceController::class)->except(['show']);

Route::get('/products/generate-barcode', [ProductController::class, 'generateBarcode']);
Route::delete('products', [ProductController::class, 'destroySelected']);
Route::apiResource('products', ProductController::class)->except(['show']);
