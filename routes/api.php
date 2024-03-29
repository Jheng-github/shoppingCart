<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function(){
    Route::post('/',[ProductController::class,'store']);
    Route::get('/',[ProductController::class,'index']);
    Route::get('/{product}',[ProductController::class,'show']);
    Route::put('/{product}',[ProductController::class,'update']);
    Route::post('/addimage',[ProductController::class,'addImageToTmp']);
});