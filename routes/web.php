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

Route::view('/','welcome');
Route::get('select2front', [\App\Http\Controllers\Frontend\Select2::class, 'response']);


Route::get('/logout', [\App\Http\Controllers\LogOut::class, 'logOut']);

Route::get('/test', \App\Http\Controllers\TestController::class);
