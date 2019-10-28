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
Route::get('/home', 'HomeController@index')->name('home');

// Auth and Users routes
Auth::routes();
Route::resource('/users', 'UsersController');

// Datasets routes
Route::resource('/users/{user}/datasets', 'DatasetsController');
Route::get('/users/{user}/datasets/{dataset}/download', 'DatasetsController@download')->name("datasets.download");

// Models routes
Route::resource('/users/{user}/networks', 'NetworksController');
Route::get('/users/{user}/networks/{network}/download', 'NetworksController@download')->name("networks.download");

// Compilations routes
//Route::get('/users/{user}/networks/{network}/compile', 'CompilationsController@create')->name("compilations.create");
//Route::post('/users/{user}/networks/{network}/compile', 'CompilationsController@store')->name("compilations.store");
Route::resource('/users/{user}/networks/{network}/compilations', 'CompilationsController', [
    'only' => ['create', 'store']
]);


// Training routes
Route::resource('/users/{user}/trainings', 'TrainingsController');
Route::post('/users/{user}/trainings/{training}/start', 'TrainingsController@start')->name("trainings.start");
Route::post('/users/{user}/trainings/{training}/pause', 'TrainingsController@pause')->name("trainings.pause");
Route::post('/users/{user}/trainings/{training}/resume', 'TrainingsController@resume')->name("trainings.resume");
Route::post('/users/{user}/trainings/{training}/stop', 'TrainingsController@stop')->name("trainings.stop");
Route::post('/users/{user}/trainings/{training}/getTrainingInfo', 'TrainingsController@getTrainingInfo')->name("trainings.getInfo");