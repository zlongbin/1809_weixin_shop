<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JssdkController extends Controller
{
    //
    public function jsTest(){
        //计算签名
        $ticket = getJsapiTicket();     //jsapi_ticket
        $noncestr = Str::random(16);    //随机字符串
        $timestamp = time();        //时间戳
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];      //当前网页URL
        $string1 = "jsapi_ticket=$ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";         //ASCLL码拼接为字符串
        $signature = sha1($string1);    //签名
        $js_config = [
            'appId' => env('WX_APP_ID'),                //微信测试号APPID
            'timestamp' => $timestamp,            //时间戳
            'nonceStr' => $noncestr,             //随机字符串
            'signature' => $signature,            //签名
        ];
        $data = [
            'js_config' => $js_config
        ];
        return view('weixin/jssdk',$data);
        return view('goods/goodsDetail',$data);
    }
    public function getImg(){
        // echo "<pre>";print_r($_GET);echo "</pre>";
        // $media_id = $_GET['media_id'];
        $media_id = "dZla2NK2BY6bmZ4Z4tNNklQiZOORiiQuXKE76DaTn3k6eP3D394mThknSoBwQ6o5";
        $access_token = getAccessToken();
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;
        $response = json_decode(file_get_contents($url),true);
        echo "<pre>";print_r($response);echo "</pre>";die;
        if(!file_exists('/images/uploads')){

        }
    }
}
