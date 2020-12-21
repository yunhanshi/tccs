<?php

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

Route::post('auth/login', 'Admin\AuthController@login');
Route::post('auth/register', 'Admin\AuthController@register');
Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'Admin\UserController');
    Route::get('auth/user', 'Admin\AuthController@user');
    Route::post('auth/logout', 'Admin\AuthController@logout');

    // TodoList
    Route::apiResource('todos', 'Admin\Todo\TodoController');
    Route::put('finishtodos/{id}', 'Admin\Todo\TodoController@finish');
    Route::put('redotodos/{id}', 'Admin\Todo\TodoController@redo');
    Route::get('burndown', 'Admin\Todo\TodoController@burndown');
});

