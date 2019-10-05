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

Route::get('/', function (){    return view('welcome');     })->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/users', 'UsersController');

Route::resource('/users/{user}/datasets', 'DatasetsController');
Route::get('/users/{user}/datasets/{dataset}/download/', 'DatasetsController@download')->name("datasets.download");

Route::resource('/users/{user}/networks', 'NetworksController');
Route::get('/users/{user}/networks/{network}/download/', 'NetworksController@download')->name("networks.download");