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

Route::get('/unauthorized', function() {
    return Api::generateErrorResponse(401, 'AuthenticationError', 'User  is not authorized.');
})->name('user.unauthorized');


Route::group(['prefix' => '/v1'], function () {

    // User routes.
    Route::group(['prefix' => '/user'], function() {
        Route::get('/', 'Users@index')->middleware('auth:api');
        Route::post('/login', 'Users@login');
        Route::post('/signup', 'Users@signup');
    });


    // Experts routes.
    Route::group(['prefix' => '/experts', 'middleware' => 'auth:api'], function() {
        Route::get('/', 'Experts@index');
        Route::get('/{id}', 'Experts@show');
    });


    // Organization categories endpoints.
    Route::resource('/organizations/categories', 'OrganizationCategories');


    // Job routes.
    Route::group(['prefix' => '/jobs', 'middleware' => 'auth:api'], function() {
        Route::get('/', 'Jobs@index');
        Route::get('/{id}', 'Jobs@show');
        Route::post('/', 'Jobs@create')->middleware('check_user_type:organization', 'check_user_verified');
    });

});
