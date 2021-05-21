<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Auth
$router->post('api/auth/login', 'AuthController@login');
$router->post('api/auth/register', 'AuthController@register');
$router->post('api/auth/logout', 'AuthController@logout');
$router->post('api/auth/refresh', 'AuthController@refresh');

// Books
$router->get('api/books', 'BookController@index');
$router->get('api/books/{id}', 'BookController@show');
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['middleware' => 'encryption'], function () use ($router) {
        $router->post('api/books', 'BookController@store');
        $router->put('api/books/{id}', 'BookController@update');
        $router->delete('api/books/{id}', 'BookController@delete');
    });
});

// Authors
$router->get('api/authors', 'AuthorController@index');
$router->get('api/authors/{id}', 'AuthorController@show');
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['middleware' => 'encryption'], function () use ($router) {
        $router->post('api/authors', 'AuthorController@store');
        $router->put('api/authors/{id}', 'AuthorController@update');
        $router->delete('api/authors/{id}', 'AuthorController@delete');
    });
});

// Favourites
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('api/favourites', 'FavouriteController@index');
    $router->post('api/favourites/{id}', 'FavouriteController@favourite');
});
