<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\NoticeController;

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
    return view('welcome');
});

// 天気予報取得APIの呼出・DB登録
Route::get('/store', [WeatherController::class, 'store']);

// LINE NOTIFYによるユーザー通知実行
Route::get('/notice', [NoticeController::class, 'notice']);
