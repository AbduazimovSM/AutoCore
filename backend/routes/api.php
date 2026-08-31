<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('references', ReferenceController::class)->except(['show']);
Route::delete('references', [ReferenceController::class, 'destroySelected']);

Route::apiResource('products', ProductController::class)->except(['show']);
Route::delete('products', [ProductController::class, 'destroySelected']);

