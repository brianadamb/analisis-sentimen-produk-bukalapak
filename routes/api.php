<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MerchantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/validated-token', [AuthController::class, 'validatedToken']);

Route::middleware('auth:sanctum')->group(function() { 
    Route::post('/logout', [AuthController::class, 'logout']);

    // //Merchant
    // Route::get('/merchant', [MerchantController::class, 'index']);
    // Route::post('/merchant/store', [MerchantController::class, 'store']);
    // Route::post('/merchant/update/{id}', [MerchantController::class, 'update']);
    // Route::get('/merchant/delete/{id}', [MerchantController::class, 'delete']);
    // Route::get('/merchant/show/{id}', [MerchantController::class, 'show']);

    // //Product
    // Route::get('/product', [ProductController::class, 'index']);
    // Route::post('/product/store', [ProductController::class, 'store']);
    // Route::post('/product/update/{id}', [ProductController::class, 'update']);
    // Route::get('/product/delete/{id}', [ProductController::class, 'delete']);
    // Route::get('/product/show/{id}', [ProductController::class, 'show']);

    // //Review
    // Route::get('/review', [ReviewController::class, 'index']);
    // Route::post('/review/store', [ReviewController::class, 'store']);
    // Route::post('/review/update/{id}', [ReviewController::class, 'update']);
    // Route::get('/review/delete/{id}', [ReviewController::class, 'delete']);
    // Route::get('/review/show/{id}', [ReviewController::class, 'show']);
});