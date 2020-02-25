<?php

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

Route::resource('/prueba', 'LecturaController');
Route::get('/cxc', 'PrincipalController@cxc')->name('cxc');
Route::get('/cxcindex', 'PrincipalController@index')->name('cxc.index');
