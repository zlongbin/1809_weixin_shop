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
// phpinfo
Route::get('/info', function () {
    phpinfo();
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
Route::get('/js/test',"Weixin\JssdkController@jsTest");         //
Route::get('/js/getImg',"Weixin\JssdkController@getImg");         //

//微信事件推送
Route::get('/wx/valid',"Weixin\WeixinController@valid");         //首次处理
Route::post('/wx/valid',"Weixin\WeixinController@wxEvent");         //事件推送

// 计划任务
Route::get('/crontab/del_orders',"CrontabController@delOrder");         //删除过期订单

//网页授权
Route::get('/wxweb',"Weixin\WeixinController@wxWeb");         //链接
Route::get('/wxweb/getu',"Weixin\WeixinController@getU");         //网页授权

//生成二维码
Route::get('/ticket',"QRcodeController@ticket");         //删除过期订单

//创建自定义菜单
Route::get('/weixin/createMenu',"Weixin\WeixinController@createMenu");         //创建菜单
Route::get('/weixin/menu',"Weixin\WeixinController@menu");         //回调


// 月考
Route::get('/weixin/yuekao',"YuekaoController@yuekao");         //首次接入
Route::post('/weixin/yuekao',"YuekaoController@Event");         //事件推送
Route::get('/weixin/id',"YuekaoController@id");         //事件推送
Route::get('/weixin/add',"YuekaoController@add");         //事件推送

// 群发消息
Route::get('/weixin/message',"Weixin\WeixinController@message");         //群发消息