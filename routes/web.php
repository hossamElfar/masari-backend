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

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'api/v1'], function () {
    /**
     * Users Authentication
     */
    Route::post('register', 'API\AuthAPIController@register');
    Route::get('register/verify/{token}', 'API\AuthAPIController@verify');
    Route::post('login', 'API\AuthAPIController@login');
    Route::post('logout', 'API\AuthAPIController@logout');
});
