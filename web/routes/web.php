<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

Route::middleware(['web'])->group(function () {
    Route::get('/', 'HomeController@index');

    Route::get('login', 'AuthController@index');
    Route::get('logout', 'AuthController@logout');
    Route::post('login', 'AuthController@login');
    // Route::get('home', 'AuthController@index');

    Route::resource('books', BookController::class);
    Route::resource('authors', AuthorController::class);
});
