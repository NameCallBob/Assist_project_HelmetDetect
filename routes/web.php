<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

/** @var \Laravel\Lumen\Routing\Router $router */

// $router->group(['middleware' => ['auth', 'checkPrivilege']], function () use ($router) {
$router->get("/api/token/verify/",'AuthController@checkToken');
// });
$router->post('/upload-image','User@uploadImage');
$router->put('/updateUser', 'User@updateUser');
$router->get('/getRoles','User@getRoles');


$router->post('/login', 'AuthController@login');
$router->post('/register', 'User@register');

$router->post('/readimg','User@readimg');

// forget password
$router->post('/send-reset-code', 'AuthController@sendResetCode');
$router->post('/reset-password', 'AuthController@resetPassword');



$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('users', 'UserController@index');
    $router->post('users', 'UserController@store');
    $router->get('users/{id}', 'UserController@show');
    $router->put('users/{id}', 'UserController@update');
    $router->delete('users/{id}', 'UserController@destroy');

    $router->get('pictures', 'PictureController@index');
    $router->post('pictures', 'PictureController@store');
    $router->get('pictures/{id}', 'PictureController@show');
    $router->put('pictures/{id}', 'PictureController@update');
    $router->delete('pictures/{id}', 'PictureController@destroy');

    $router->get('results', 'ResultController@index');
    $router->post('results', 'ResultController@store');
    $router->get('results/{id}', 'ResultController@show');
    $router->put('results/{id}', 'ResultController@update');
    $router->delete('results/{id}', 'ResultController@destroy');

    $router->get('comments', 'CommentController@index');
    $router->post('comments', 'CommentController@store');
    $router->get('comments/{id}', 'CommentController@show');
    $router->put('comments/{id}', 'CommentController@update');
    $router->delete('comments/{id}', 'CommentController@destroy');
});
