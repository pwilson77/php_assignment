<?php

use App\Http\Controllers\StockDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('stockDataForm');
});

Route::get('/stockdata', function () {
    return view('stockData');
});

Route::post('/request-stock-history', [StockDataController::class, 'getStockData']);
