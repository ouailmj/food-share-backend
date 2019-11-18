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
Route::post('/login_google','Auth\ForgotPasswordController@logingoogle');
Route::get('user/{id}','MessageController@getUser');
Route::group(['middleware' => ['auth:api','cors']], function () {
    Route::get('user_details','ApiAuthController@getUser');
    Route::post('validate_account','ApiAuthController@verifieAccount');
    Route::post('update_photo','ApiAuthController@update_profile_photo');
    Route::get('annonces','AnnonceController@index');
    Route::post('annonces','AnnonceController@store');
    Route::get('cloturer/{id}','AnnonceController@cloturer');
    Route::get('annonces/{id}','AnnonceController@show');
    Route::post('annonce/update','AnnonceController@update');
    Route::get('comments/{id}','CommentaireController@get');
    Route::post('comments','CommentaireController@insert');
    Route::post('comments/update','CommentaireController@update');
    Route::get('comments/delete/{id}','CommentaireController@delete');
    Route::get('historique/{id}','HistoriqueController@getDonnation');
    Route::post('message','MessageController@submit');
    Route::get('messages','MessageController@getChat');
    Route::get('messages/{id}','MessageController@getMessage');
    Route::post('search','SearshController@search');
    Route::get('motifs','SignalisationController@getMotif');
    Route::post('signalisation','SignalisationController@signaliserUtilisateur');
    Route::get('notifications','NotificationController@getNotification');
    Route::post('desactivate','ApiAuthController@desactivateAcc');
});

