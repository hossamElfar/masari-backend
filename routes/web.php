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
    Route::any('upload','API\AuthAPIController@upload');
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
     * Post values assessment score
     */
    Route::post('assessment/values', 'API\UserController@storeValuesAssessment');
    /**
     * Post values assessment score
     */
    Route::post('assessment/multi', 'API\UserController@storeMultiAssessment');
    /**
     * Post text assessment score
     */
    Route::post('assessment/text', 'API\UserController@storeTextAssessment');
    /**
     * Post kteer assessment score
     */
    Route::post('assessment/kteer', 'API\UserController@storeKteerAssessment');
    /**
     * Post values assessment sorted score
     */
    Route::post('assessment/values/sort', 'API\UserController@storeValuesAssessmentSorted');
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
    Route::get('/users/experts/fields', 'API\UserController@getFields');
    /**
     * Qs resource
     */
    Route::resource('qs', 'API\QsController');
    /**
     * Posting answers
     */
    Route::post('qs/{id}', 'API\QsController@addAnswer');
    /**
     * Updating answers
     */
    Route::post('qs/update/{answer_id}', 'API\QsController@updateAnswer');
    /**
     * Getting the experts os a certain field in the user's country
     */
    Route::get('experts/{field_id}', 'API\ScheduleController@getExperts');
    /**
     * Request a schedule meeting to an expert
     */
    Route::post('experts/{timing_id}','API\ScheduleController@reserveExpert');
    /**
     * Posting a timing
     */
    Route::post('experts/add/timing','API\ScheduleController@addTiming');
    /**
     * Get expert timings
     */
    Route::get('experts/timings/me','API\ScheduleController@getTimingsExpert');
    /**
     * Get a list of requested timings
     */
    Route::get('experts/get/requested','API\ScheduleController@requestedTiming');
    /**
     * Get a list of approved timings
     */
    Route::get('experts/get/approved','API\ScheduleController@approvedTiming');
    /**
     * Approve a request
     */
    Route::get('experts/approve/{request_id}','API\ScheduleController@approveTiming');
    /**
     * Get user scores of an Assessment
     */
    Route::get('experts/scores/{user_code}','API\UserController@getScore');
    /**
     * Get answers of an Assessment
     */
    Route::get('experts/answers/{user_code}','API\UserController@getAnswers');
    /**
     * Get all clients 
     */
    Route::get('experts/clients/','API\UserController@getClients');
    /**
     * Get a list of assessments that a client hs taken
     */
    Route::get('experts/users/{user_code}','API\UserController@getUserAssessment');
    /**
     * Remove a assessment the user has taken
     */
    Route::get('admin/users/remove/{user_code}','API\UserController@removeUserAssessment');
    /**
     * Get unverified news
     */
    Route::get('admin/news','AdminController@getUnverifiedNews');
    /**
     * Get unverified videos
     */
    Route::get('admin/videos','AdminController@getUnverifiedVideos');
    /**
     * Get unverified events
     */
    Route::get('admin/events','AdminController@getUnverifiedEvents');
    /**
     * Get unverified news
     */
    Route::get('admin/programs','AdminController@getUnverifiedPrograms');
    /**
     * Get unverified questions
     */
    Route::get('admin/questions','AdminController@getUnverifiedQuestions');
    /**
     * Get unverified questions and answers
     */
    Route::get('admin/questions/{id}','AdminController@getUnverifiedQuestionAndAnswers');
    /**
     * Verify an answer
     */
    Route::get('admin/questions/answers/{id}','AdminController@verifyAnswer');
    /**
     * get user's assessments
     */
    Route::get('admin/users/assessments/{user_code}','AdminController@getUserAssessments');
    /**
     * Remove user's assessments
     */
    Route::get('admin/users/remove/assessments/{user_code}','AdminController@removeUserAssessments');
    /**
     * Register a new low admin
     */
    Route::post('admin/create/low','AdminController@registerLow');
    /**
     * Register a new high admin
     */
    Route::post('admin/create/high','AdminController@registerHigh');
});
