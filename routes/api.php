<?php

use App\Helpers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => '/v1'], function () {

    // User routes.
    Route::group(['prefix' => '/user'], function() {
        Route::post('/login', 'Users@login');
        Route::post('/signup', 'Users@signup');

        Route::group(['middleware' => 'auth:api'], function() {
            Route::get('/', 'Users@index');
            Route::match(['PUT', 'PATCH'], '/', 'Users@update');
        });
    });


    // Experts routes.
    Route::group(['prefix' => '/experts'], function() {
        Route::get('/', 'Experts@index');
        Route::get('/{expert}', 'Experts@show');
    });


    // Student routes.
    Route::group(['prefix' => '/student', 'middleware' => 'auth:api'], function() {
        Route::post('/{student}', 'Students@updateVisits')->middleware('check_user_type:organization');

        Route::group(['middleware' => 'check_user_type:student'], function() {
            Route::get('/overview', 'Students@profileOverview');
            Route::post('/apply/{job}', 'Students@applyForJob')->middleware('check_user_verified');
        });
    });


    Route::group(['prefix' => '/company', 'middleware' => ['auth:api', 'check_user_type:organization']], function() {
        Route::get('/jobs', 'Organizations@jobs');
        Route::get('/jobs/applications/{job}', 'Organizations@jobApplications');
        Route::post('/jobs/applications/update/{jobApplication}', 'Organizations@updateJobApplication');
        Route::post('/jobs/close/{job}', 'Organizations@closeJob');
    });


    // Organization categories endpoints.
    Route::resource('/organizations/categories', 'OrganizationCategories');


    // Job routes.
    Route::group(['prefix' => '/jobs', 'middleware' => 'auth:api'], function() {
        Route::get('/', 'Jobs@index');
        Route::get('/{job}', 'Jobs@show');
        Route::post('/', 'Jobs@create')->middleware('check_user_type:organization', 'check_user_verified');
    });

});
