<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Product CRUD Routes
Route::apiResource('products', ProductController::class);