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
// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/', function () use ($router) {
    return response()->json([
        "service_name" => "PHP Service APP",
        "status" => "Running"
    ]);
});

$router->get('/hello-lumen', function () {
    return "<h1>Lumen</h1><p>Hi good developer, thank for using Lumen<p>";   
});

$router->get('/hello-lumen/{name}', function ($name) {
    return "<h1>Lumen</h1><p>Hi " . $name . ", thank for using Lumen<p>";   
});

$router->get('/scores',['middleware' => 'login', function () {
    return "<h1>Selamat</h1><p>Nilai anda 100<p>";
}]);



$router->get('/users', 'UsersController@index');
$router->get('/users/{userId}', 'UsersController@show');

// $router->get('posts', 'PostsController@index');

// Post REST API Routes
// $router->get('/posts', 'PostsController@index');
// $router->post('/posts', 'PostsController@store');
// $router->get('/post/{id}', 'PostsController@show');
// $router->put('/post/{id}', 'PostsController@update');
// $router->delete('/post/{id}', 'PostsController@destroy');

// Tantangan REST CRUD Products
$router->get('/products', 'ProductsController@index');
$router->post('/products', 'ProductsController@store');
$router->get('/product/{id}', 'ProductsController@show');
$router->put('/product/{id}', 'ProductsController@update');
$router->delete('/product/{id}', 'ProductsController@destroy');

// authentication
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

// Post
$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/posts', 'PostsController@index');
    $router->post('/posts', 'PostsController@store');
    $router->get('/post/{id}', 'PostsController@show');
    $router->put('/post/{id}', 'PostsController@update');
    $router->delete('/post/{id}', 'PostsController@destroy');
});