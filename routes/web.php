<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'home']);

    //Spedizioni
    Route::get('spedizione', [\App\Http\Controllers\Frontend\SpedizioneController::class, 'index']);
    Route::get('/spedizione-download/{id}', [\App\Http\Controllers\Frontend\SpedizioneController::class, 'downloadAllegato']);


});


Route::get('/logout', [\App\Http\Controllers\LogOut::class, 'logOut']);

Route::get('/test', \App\Http\Controllers\TestController::class);
