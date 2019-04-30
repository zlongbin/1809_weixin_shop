<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\YuekaoModel;
use App\Model\GoodsModel;
use GuzzleHttp\Client;


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
        $MsgType = $xml->MsgType;
        if($Event=="subscribe"){
            $user = YuekaoModel::where(['openid'=>$openid])->first();
            if($user){
                echo '<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[欢迎回来]]></Content>
                </xml>';
            }else{
                $res = YuekaoModel::insertGetId(['openid'=>$openid]);
                echo '<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[请输入商品名字字样]]></Content>
                </xml>';
            }
        }elseif($MsgType=='text'){
            $goods = GoodsModel::where('name','like',"%".$xml->Content."%")->first();
            $picurl = "http://1809zhoubinbin.comcto.com/images/".$goods['img'];
            $url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goods['id'];
            if($goods){
                $key = "goods_name";
                Redis::lpush($key,$goods['name']);
                $response ='<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>
                  <item>
                    <Title><![CDATA['.$goods['name'].']]></Title>
                    <Description><![CDATA[商品]]></Description>
                    <PicUrl><![CDATA['.$picurl.']]></PicUrl>
                    <Url><![CDATA['.$url.']]></Url>
                  </item>
                </Articles>
              </xml>';
            }else{
                $response ='<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[没有此商品]]></Content>
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
    public function id(){
        $goods = Redis::lrange('goods_name','0','-1');
        echo "<pre>";print_r($goods);echo "</pre>";
    }
}