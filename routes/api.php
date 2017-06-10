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
Route::get('/', function () {
    return view('welcome');
});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('token', 'UserController@token');
Route::get('users/current', 'UserController@current');

Route::resource('cloudwares', 'CloudwareController');
Route::resource('users', 'UserController');

Route::group(['middleware' => ['jwt.auth']], function () {
   Route::resource('instances', 'InstanceController');
   Route::post('users/current/password', 'UserController@password');
});

Route::get('settings', 'IndexController@settings');