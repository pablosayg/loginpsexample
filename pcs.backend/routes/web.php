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

// Clases
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/user/createAdmin', 'UserController@createAdmin');
Route::post('/api/user/register', 'UserController@register');
Route::post('/api/user/login', 'UserController@login');
//Route::put('/api/user/update', 'UserController@update')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('/api/user/update', 'UserController@update');
