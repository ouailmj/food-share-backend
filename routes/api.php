<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register','ApiAuthController@register');
Route::post('/login','ApiAuthController@login');
Route::post('/password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset','Auth\ForgotPasswordController@resetPassword');
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('user_details','ApiAuthController@getUser');
    Route::post('validate_account','ApiAuthController@verifieAccount');
    Route::get('annonces','AnnonceController@index');
    Route::post('annonces','AnnonceController@store');
    Route::get('annonces/{id}','AnnonceController@show');
    Route::post('annonce/update','AnnonceController@update');
});

