<?php

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
use Illuminate\Support\Facades\DB;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/debug-sentry', function () {
    throw new Exception('This is My first Sentry error!');
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\V1'], function ($api) {

        $api->get('version', function () {
            return response()->json(['status' => 'success', 'message' => env('APP_VERSION')], 200);
        });

        $api->group(['middleware' => 'auth'], function() use($api) {
            $api->get('version/auth', function () {
                return response()->json(['status' => 'success', 'message' => env('APP_VERSION')], 200);
            });
            
        });

        //crud
        $api->group(['prefix' => '{database}/user'], function () use($api){
            $api->get('/', 'UserController@index');
            $api->get('/{id}', 'UserController@show');
            $api->post('/', 'UserController@store');
            $api->patch('/{id}', 'UserController@update');
            $api->delete('/{id}', 'UserController@delete');
        });

        //auth
        $api->group(['prefix' => 'auth'], function () use($api){
            $api->post('/login', 'AuthController@login');
            $api->post('/check', 'AuthController@check');
            $api->post('/logout', 'AuthController@logout');
        });

        //email
        $api->group(['prefix' => 'email'], function () use($api){
            $api->post('/send', 'CommunicationController@sentEmail');
        });
    });
});
