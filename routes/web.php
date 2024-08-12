<?php

use App\Http\Controllers\GroupdeviceController;
use App\Http\Controllers\ImagemapController;
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
    return view('welcome');
});

Route::resource('/groupdevice', GroupdeviceController::class);
Route::resource('/imagemap', ImagemapController::class);
