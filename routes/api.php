<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function(){
    Route::post('/add-product',[ProductController::class,'store']);
});