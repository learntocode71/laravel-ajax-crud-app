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

Route::get('/', 'StateController@index');

Route::post('/addCity', 'CityController@store');
Route::get('/getCities', 'CityController@index');
Route::post('/getCityById', 'CityController@edit');
Route::post('/updateCity', 'CityController@update');
Route::post('/deleteCityById', 'CityController@destroy');
