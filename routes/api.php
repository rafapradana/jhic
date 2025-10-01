<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Product CRUD Routes
Route::apiResource('products', ProductController::class);

// Additional Product Routes
Route::get('products-categories', [ProductController::class, 'categories']);