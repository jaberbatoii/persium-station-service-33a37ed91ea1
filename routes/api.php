<?php

declare(strict_types = 1);

/** @var \Laravel\Lumen\Routing\Router $router */

use Persium\Station\Http\Controllers\StationController;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'prefix' => 'stations',
    'namespace' => '\Persium\Station\Http\Controllers',
//    'middleware' => 'persium-oauth2:station-get',
], function () use ($router) {
    $router->get('/in-boundary', 'StationController@getInBoundary');
    $router->get('/{uuid}', 'StationController@show');
    $router->get('/{uuid}/latest-data', 'StationDataController@getLatestDataOneStation');
});


$router->get('/wikipedia', 'WikiController@index');
$router->get('/wind-map', 'WindMapController@index');
