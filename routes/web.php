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




/**
 * Depot API's
 */
$router->group(['prefix' => 'depots/'], function () use ($router) {

    $router->get('all','DepotController@index');
    $router->post('create','DepotController@store');
    $router->get('depot/{id}','DepotController@show');
    $router->put('update/{id}','DepotController@update');
    $router->delete('delete/{id}','DepotController@delete');
});


/**
 * Product API's
 */
$router->group(['prefix' => 'products/'], function () use ($router) {

    $router->get('all','ProductController@index');
    $router->post('create', 'ProductController@store');
    $router->get('product/{id}', 'ProductController@show');
    $router->put('update/{id}', 'ProductController@update');
    $router->delete('delete/{id}', 'ProductController@delete');

});

/**
 * Provider API's
 */
$router->group(['prefix' => 'providers/'], function () use ($router) {

    $router->get('all', 'ProviderController@index');
    $router->post('create', 'ProviderController@store');
    $router->get('provider/{id}', 'ProviderController@show');
    $router->put('update/{id}', 'ProviderController@update');
    $router->delete('delete/{id}', 'ProviderController@delete');
});


/**
 * Invoice API's
 */
$router->group(['prefix' => 'invoices/'], function () use ($router) {

    $router->get('all', 'InvoiceController@index');
    $router->post('create', 'InvoiceController@store');
    $router->get('invoice/{id}', 'InvoiceController@show');
    $router->put('update/{id}', 'InvoiceController@update');
    $router->delete('delete/{id}', 'InvoiceController@delete');
});

