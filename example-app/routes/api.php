<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/enter', [\App\Http\Controllers\CarsController::class, 'enterParking'])->name('api.register-car');
Route::post('/exit', [\App\Http\Controllers\CarsController::class, 'exitParking'])->name('api.unregister-car');
Route::get('/available', [\App\Http\Controllers\CarsController::class, 'getFreeSlots'])->name('api.get-free-parking-slots');
Route::get('/check/{registrationPlate}', [\App\Http\Controllers\CarsController::class, 'checkSum'])->name('api.get-free-parking-slots');
