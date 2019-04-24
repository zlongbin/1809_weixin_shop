<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JssdkController extends Controller
{
    //
    public function jsTest(){
        $js_config = getJsConfig();
        $data = [
            'js_config' => $js_config
        ];
        return view('weixin/jssdk',$data);
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
