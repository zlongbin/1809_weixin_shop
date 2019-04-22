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

// 购物车
Route::get('/cart', 'CartController@index');
Route::get('/cart/add/{goods_id?}', 'CartController@add');

// 订单
Route::get('/order/list', 'Order\IndexController@orderList');          //订单列表
Route::get('/order/create', 'Order\IndexController@create');        //生成订单

// 微信支付
Route::get('/weixin/pay',"Weixin\PayController@pay");       
Route::post('/weixin/pay/notify',"Weixin\PayController@notify");

// 商品
Route::get('/goods/index',"GoodsController@index");   
Route::get('/goods/detail',"GoodsController@detail");       

