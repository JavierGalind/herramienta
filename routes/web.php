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


Route::resource('/prueba', 'LecturaController');
Route::get('/cxc', 'PrincipalController@cxc')->name('cxc');
Route::get('/cxcindex', 'PrincipalController@index')->name('cxc.index');
Route::get('/', 'PrincipalController@empresa')->name('empresa');
Route::get('/verregistro', 'PrincipalController@ver_regisrto')->name('verregistro');
Route::get('/sesion', 'PrincipalController@sesion')->name('sesion');
Route::get('/cargardbf', 'PrincipalController@cargardbf')->name('cargardbf');


///CUENTAS POR PAGAR///
Route::get('/cxpindex', 'CXPController@index')->name('cxp.index');
Route::get('/cxp', 'CXPController@cxp')->name('cxp');
