<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::resource('/products', ProductController::class);
Route::resource('/users', UserController::class);

Route::get('/', function () {
    return view('welcome');
});
