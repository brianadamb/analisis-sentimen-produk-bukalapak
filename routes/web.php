<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\TextProcController;
use App\Http\Controllers\UlasanProcessedText;
use App\Http\Controllers\PengujianController;
use App\Http\Controllers\KlasifikasiController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
require_once 'scrapper.php';

Route::get('/', function () {
    return redirect('merchant');
});

Route::get('/login', function () {
    return view('auth.login');
});

// Route::get('/merchant', function () {
//     return view('pages.merchant.list');
// });

    Route::get('/merchant', [MerchantController::class, 'index']);
    Route::post('/merchant/store', [MerchantController::class, 'store']);
    Route::post('/merchant/update/{id}', [MerchantController::class, 'update']);
    Route::get('/merchant/delete/{id}', [MerchantController::class, 'delete']);

// Route::get('/product', function () {
//     return view('pages.product.list');
// });

    Route::get('/product', [ProductController::class, 'index']);
    Route::post('/product/store', [ProductController::class, 'store']);
    Route::post('/product/update/{id}', [ProductController::class, 'update']);
    Route::get('/product/delete/{id}', [ProductController::class, 'delete']);
    Route::get('/product/show/{id}', [ProductController::class, 'show']);
    Route::get('/get-product', [ReviewController::class, 'product'])->name('get-product');

    Route::get('/review', [ReviewController::class, 'index']);
    Route::get('/review-export', [ReviewController::class, 'export']);
    Route::get('/review-get-product/{id}', [ReviewController::class, 'product']);

    Route::get('/pre_processing/cleaning', [TextProcController::class, 'cleaning']);
    Route::get('/pre_processing/casefolding', [TextProcController::class, 'caseFolding']);
    Route::get('/pre_processing/normalization', [TextProcController::class, 'normalization']);
    Route::get('/pre_processing/tokenizing', [TextProcController::class, 'tokenizing']);
    Route::get('/pre_processing/stopword', [TextProcController::class, 'stopwordRemoval']);
    Route::get('/pre_processing/stemming', [TextProcController::class, 'stemming']);

    Route::get('/ulasan_processed_text/labeling_data_latih', [UlasanProcessedText::class, 'labelingDatLatih']);
    Route::get('/ulasan_processed_text/data_uji', [UlasanProcessedText::class, 'dataUji']);

    Route::get('/ulasan_processed_text/hasil_prediksi', [PengujianController::class, 'run']);

    Route::get('/klasifikasi/data', [KlasifikasiController::class, 'data']);
    Route::get('/klasifikasi/evaluasi', [KlasifikasiController::class, 'evaluasi']);
    Route::get('/klasifikasi/visualisasi', [KlasifikasiController::class, 'visualisasi']);

    Route::post('/get-data', [TextProcController::class, 'getData']);