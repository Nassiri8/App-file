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

Route::get('/hello', 'UserController@hello');
Route::post('/user', 'UserController@register');
Route::post('/auth', 'UserController@login');

Route::group(['middleware' => 'auth:api'], function(){
    //Route User
    Route::post('/logout', 'UserController@logout');
    Route::delete('/delete', 'UserController@delete');
    Route::get('/users', 'UserController@getListUser');
    Route::put('/user/{id}', 'UserController@update');
    //File
    Route::post('/user/{id}/file', 'FileController@create');
    Route::delete('/file/{id}', 'FileController@delete');
    Route::put('/file/{id}', 'FileController@update');
    Route::get('/user/{id}/file', 'FileController@getListById');
});
