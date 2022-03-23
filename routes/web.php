<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawController;
use App\Http\Controllers\ResourceController;
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

Route::get('/', [CrawController::class, 'index'])->name('home');

Route::post('/craw', [CrawController::class, 'craw'])->name('craw');

Route::get('/listApps', [ResourceController::class, 'displayListApps'])->name('apps.list');

Route::get('/listProducts/{appId}', [ResourceController::class, 'displayListProducts'])->name('products.list');

Route::get('/listReviews/{productId}', [ResourceController::class, 'displayListReviews'])->name('reviews.list');

Route::get('/test', [CrawController::class, 'test'])->name('test');