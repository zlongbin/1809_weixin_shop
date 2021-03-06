<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('/goods', GoodsController::class);
    $router->resource('/orders', OrderController::class);
    $router->resource('/users', WxUserController::class);
    // $router->resource('users', WxUserController::class);
    $router->get('media/add', 'MediaController@add');
    $router->post('media/addDo', 'MediaController@addDo');
    $router->get('/a', 'WxUserController@a');

    $router->post('group/text', 'WxUserController@textGroup');


});
