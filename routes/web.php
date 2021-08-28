<?php

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

Route::post('/encher', 'GarrafaController@encher')->name('encher');
Route::get('/beber', 'GarrafaController@beber')->name('beber');
Route::get('/consumo', 'ConsumoController@index')->name('consumo');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('check.admin')->get('/consumo', 'ConsumoController@index')->name('consumo');
