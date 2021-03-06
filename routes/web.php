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



//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::resource('info','prueba');
Route::resource('logon', 'LogController');

Route::resource('/equipo','equipo');

Route::resource('/competencia','competencia');

Route::resource('usuario','UsuarioController');

Route::get('logout','LogController@logout');

Route::resource('proyectos','ProyectoController');

Route::get('admin','FrontController@admin');

Route::resource('todos','TodosController');
Route::get('todos/{cat}', 'TodosController@show');

#Route::resource('usuario','StateController');
Route::get('/usuario/towns/{id}','UsuarioController@getTowns');

Route::resource('miembros','MiembrosController');

Route::resource('competencias','CompetenciasController');

Route::resource('info/donar', 'informacionController@pago');