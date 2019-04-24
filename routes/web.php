<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//商品列表
Route::get('goods/index', 'GoodsController@index');
//商品浏览量
Route::get('goods/list', 'GoodsController@list');
//商品排行
// Route::get('goods/sort', 'GoodsController@sort'); 
//购物车
Route::get('cart', 'CartController@index');
//缓存商品信息
// Route::get('goods/cache/{goods_id?}', 'GoodsController@cache');
//添加购物车
Route::get('cart/add/{goods_id?}', 'CartController@add');
//订单处理
Route::get('order/create', 'Order\IndexController@create');
Route::get('order/list', 'Order\IndexController@oList'); 
//微信支付
Route::get('pay/weixin', 'weixin\PayController@pay');
//微信支付回调通知
Route::post('weixin/pay/notify', 'weixin\PayController@notify');
//查询订单支付状态
Route::get('order/paystatus', 'Order\IndexController@payStatus');
//支付成功
Route::get('pay/success', 'weixin\PayController@paySuccess');


//jssdk测试
Route::get('/js/test', 'weixin\JssdkController@test');
//获取上传的照片
Route::get('/js/getImg', 'weixin\JssdkController@getImg');


//日测
Route::get('exam/test', 'examd\ExamController@test');


