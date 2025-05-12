<?php
use App\Http\Controllers\ScrapperController;

Route::get('/test_scrap', [ScrapperController::class, 'testScrap']);