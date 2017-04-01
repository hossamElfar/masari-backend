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
    Route::post('update', 'API\AuthAPIController@update');
    /**
     * View profile API
     */
    Route::get('profile', 'API\UserController@show');
    /**
     * Get Assessments names
     */
    Route::get('getAssessments', 'API\UserController@getAssessmentsNames');
    /**
     * Get a assessment's questions
     */
    Route::get('getAssessment/{id}', 'API\UserController@getAssessment');
    /**
     * Post assessment score
     */
    Route::post('assessment', 'API\UserController@storeAssessment');
    /**
     * News resource
     */
    Route::resource('news', 'API\NewsController');
    /**
     * News verification by admin
     */
    Route::get('news/verify/{id}', 'API\NewsController@verify');
    /**
     * Videos resource
     */
    Route::resource('videos', 'API\VideosController');
    /**
     * Videos verification by admin
     */
    Route::get('videos/verify/{id}', 'API\VideosController@verify');
    /**
     * Links resource
     */
    Route::resource('links', 'API\LinksController');
    /**
     * Links verification by admin
     */
    Route::get('links/verify/{id}', 'API\LinksController@verify');
    /**
     * Messages resource
     */
    Route::resource('messages', 'API\MessagesController');
    /**
     * Send message in a thread
     */
    Route::post('messages/thread/{id}', 'API\MessagesController@sendMessage');

    /**
     * Programmes resource
     */
    Route::resource('programs', 'API\ProgrammesController');
    /**
     * Programmes verification by admin
     */
    Route::get('programs/verify/{id}', 'API\ProgrammesController@verify');

    /**
     * Events resource
     */
    Route::resource('events', 'API\EventsController');
    /**
     * Events verification by admin
     */
    Route::get('events/verify/{id}', 'API\EventsController@verify');
    /**
     * Get experts
     */
    Route::get('users/experts', 'API\UserController@experts');
    /**
     * Get all fields
     */
    Route::get('/users/experts/fields','API\UserController@getFields');
});
