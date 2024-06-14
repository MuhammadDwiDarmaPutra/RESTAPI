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

$router->group(['middleware' => 'cors'], function ($router) {
    $router->post('/login', 'AuthController@login');
    $router->get('/logout', 'AuthController@logout');
    $router->get('/profile', 'AuthController@me');
// $router->post('/login', 'UserController@login');
// $router->get('/logout', 'UserController@logout');
// $router->get('/stuffs', 'StuffController@index');

$router->group(['prefix' => 'stuff'], function() use ($router) {
    $router->get('/data', 'StuffController@index');
    $router->post('/store', 'StuffController@store');
    $router->get('/trash', 'StuffController@trash');
    $router->get('{id}', 'StuffController@show');
    $router->patch('/{id}', 'StuffController@update');
    $router->delete('/{id}', 'StuffController@destroy');
    $router->get('/restore/{id}', 'StuffController@restore');
    $router->delete('/permanent/{id}', 'StuffController@deletePermanent');
});

$router->group(['prefix' => 'user'], function() use ($router) {
    $router->get('/data', 'UserController@index');
    $router->post('/store', 'UserController@store');
    $router->get('/trash', 'UserController@trash');
    $router->get('{id}', 'UserController@show');
    $router->patch('/{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->delete('/permanent/{id}', 'UserController@deletePermanent');
});

$router->group(['prefix' => 'inbound-stuff'], function() use ($router) {
    $router->get('/', 'InboundStuffController@index');
    $router->post('/store', 'InboundStuffController@store');
    //  $router->get('detail/{id}', 'InboundStuffController@show');
    // $router->patch('/update/{id}', 'InboundStuffController@update');
    // $router->delete('/delete/{id}', 'InboundStuffController@destroy');
    //  $router->get('restore/{id}', 'InboundStuffController@restore');
    //  $router->get('recycle-bin', 'InboundStuffController@recycleBin');
    //  $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');
});

$router->group(['prefix' => 'stuff-stock', 'middleware' => 'auth'], function() use ($router) {
    //  $router->get('/', 'StuffStockController@index');
    // $router->post('store', 'StuffStockController@store');
    //  $router->get('detail/{id}', 'StuffStockController@show');
    //  $router->patch('update/{id}', 'StuffStockController@update');
    //  $router->delete('delete/{id}', 'StuffStockController@destroy');
    //  $router->get('restore/{id}', 'StuffStockController@restore');
    //  $router->get('recycle-bin', 'StuffStockController@recycleBin');
    //  $router->get('force-delete/{id}', 'StuffStockController@forceDestroy');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
    // $router->post('sub-stock/{id}', 'StuffStockController@subStock');
});

$router->group(['prefix' => 'lending', 'middleware' => 'auth'], function() use ($router) {
    $router->get('/', 'LendingController@index');
    $router->post('/store', 'LendingController@store');
    //  $router->get('detail/{id}', 'InboundStuffController@show');
    $router->patch('/update/{id}', 'LendingController@update');
    $router->delete('/delete/{id}', 'LendingController@destroy');
    //  $router->get('restore/{id}', 'InboundStuffController@restore');
    //  $router->get('recycle-bin', 'InboundStuffController@recycleBin');
    //  $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');
});

$router->group(['prefix' => 'restoration/'], function() use ($router) {
    //  $router->get('/', 'InboundStuffController@index');
    $router->post('store', 'RestorationController@store');
    //  $router->get('detail/{id}', 'InboundStuffController@show');
    // $router->patch('update/{id}', 'LendingController@update');
    // $router->delete('delete/{id}', 'LendingController@destroy');
    //  $router->get('restore/{id}', 'InboundStuffController@restore');
    //  $router->get('recycle-bin', 'InboundStuffController@recycleBin');
    //  $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');
});
});