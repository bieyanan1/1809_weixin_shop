<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    //商品管理
    $router->resource('goods', GoodsController::class);
    //订单管理
    $router->resource('order', OrderController::class);
    //微信用户管理
    $router->resource('userlist', WxUserController::class);
    //素材管理
    $router->resource('addsource', SourceController::class);
});

