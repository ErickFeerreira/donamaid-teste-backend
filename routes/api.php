<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('clients', "ClientController@create");
    Route::delete('clients/{id}', "ClientController@delete");
    Route::put('clients/{id}', 'ClientController@update');
    Route::get('clients/{id}', 'ClientController@read');
    Route::get('clients', 'ClientController@readAll');
    Route::get('clients/getBy', 'ClientController@readAndFilter');

    Route::post('adresses', "AdressController@create");
    Route::delete('adresses/{id}', "AdressController@delete");
    Route::put('adresses/{id}', 'AdressController@update');
    Route::get('adresses/{id}', 'AdressController@read');
    Route::get('adresses', 'AdressController@readAll');

    Route::post('professionals', "ProfessionalController@create");
    Route::delete('professionals/{id}', "ProfessionalController@delete");
    Route::put('professionals/{id}', 'ProfessionalController@update');
    Route::get('professionals/{id}', 'ProfessionalController@read');
    Route::get('idleprofessionals', 'ProfessionalController@getIdle');

    Route::get('professionals', 'ProfessionalController@readAll');

    Route::post('orders', "OrderController@create");
    Route::delete('orders/{id}', "OrderController@delete");
    Route::put('orders/{id}', 'OrderController@update');
    Route::get('orders/{id}', 'OrderController@read');
    Route::get('orders', 'OrderController@readAll');
    Route::get('ordersby', 'OrderController@readAndFilter');
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
});