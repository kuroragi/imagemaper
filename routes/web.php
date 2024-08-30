<?php

use App\CPU\Helpers;
use App\Http\Controllers\GroupdeviceController;
use App\Http\Controllers\ImagemapController;
use Illuminate\Http\Request;
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
Route::get('/assetGroup/{id}', [GroupdeviceController::class, 'showGroup']);
Route::resource('/imagemap', ImagemapController::class);
Route::post('/imageMapApi', [ImagemapController::class, 'storeMap']);
Route::delete('/deleteMap/{imagemap}', [ImagemapController::class, 'destroyMap']);

// Route::get('/test', function(){
//     $data = [
//         'id_group' => 1, 
//         'name' => 'port 4', 
//         'coordinate' => '923,871,923,871,972.6363636363637,910,972.6363636363637,910,1081.3636363636365,904.0909090909091,1081.3636363636365,904.0909090909091,1019.909090909091,862.7272727272727,1019.909090909091,862.7272727272727,930.0909090909091,866.2727272727274', 
//         'shape' => 'poly', 
//         'status' => 'baik', 
//         'description' => 'Menuju ABTB', 
//         'id_asset' => ''
//     ];
//     $test = Helpers::postMap($data);
// });
