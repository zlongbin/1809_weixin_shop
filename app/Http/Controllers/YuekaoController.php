<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\YuekaoModel;


class YuekaoController extends Controller
{
    //
    public function yuekao(){
        echo $_GET['echostr'];
    }
    public function Event(){
        $content = file_get_contents("php://input");
        $time = date("Y-m-d H:i:s");
        $str = $time . $content . "\n";
        file_put_contents("logs/yuekao.log",$str);
        $xml = simplexml_load_string($content);
        // echo "ToUserName:" .$xml->ToUserName;echo "</br>";          //公众号id
        // echo "FromUserName:" .$xml->FromUserName;echo "</br>";      //用户id
        // echo "MsgType:" .$xml->MsgType;echo "</br>";               
        // echo "Content:" .$xml->Content;echo "</br>";                //文本消息

        // echo $this->access_token();
        $wx_id = $xml->ToUserName;
        $openid = $xml->FromUserName;
        $Event = $xml->Event;
        if($Event=="subscribe"){
            $user = YuekaoModel::where(['openid'=>$openid])->first();
            if(!$user){
                $res = YuekaoModel::insertGetId(['openid'=>$openid]);
                $response ='<xml>
                <ToUserName><![CDATA['.$wx_id.']]></ToUserName>
                <FromUserName><![CDATA['.$openid.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[请输入商品名字字样]]></Content>
                </xml>';
            }
            return $response;
        }
    }
    public function access_token(){
        $key = "access_token";
        $access_token = Redis::get($key);
        if($access_token){
            return $access_token;
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SECRET');
            $response = json_decode(file_get_contents($url),true);
            if(isset($response['access_token'])){
                Redis::set($key,$response['access_token']);
                Redis::expire($key,3600);
                return $response['access_token'];
            }else{
                return false;
            }
        }
    }
}
