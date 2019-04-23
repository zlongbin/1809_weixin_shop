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
Route::get('/cart', 'CartController@index');            //购物车
Route::get('/cart/add/{goods_id?}', 'CartController@add');          //购物车详情

// 订单
Route::get('/order/list', 'Order\IndexController@orderList');          //订单列表
Route::get('/order/create', 'Order\IndexController@create');        //生成订单

// 微信支付
Route::get('/weixin/pay',"Weixin\PayController@pay");               //微信支付   
Route::post('/weixin/pay/notify',"Weixin\PayController@notify");        //微信支付回调

// 商品
Route::get('/goods/index',"GoodsController@index");             //商品表首页
Route::get('/goods/detail',"GoodsController@detail");           //商品详情
Route::get('/goods/cache/{id?}',"GoodsController@cacheGoods");          //哈希
Route::get('/goods/sort',"GoodsController@getSort");        //浏览记录排名
Route::get('/goods/history',"GoodsController@history");         //浏览历史

// 微信JSSDK
Route::get('/js/test',"Weixin\JssdkController@jsTest");         //浏览历史
