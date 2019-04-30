<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        file_put_contents("logs\yuekao.log",$str);
        $xml = simplexml_load_string($content);
        echo "ToUserName:" .$xml->ToUserName;echo "</br>";          //公众号id
        echo "FromUserName:" .$xml->FromUserName;echo "</br>";      //用户id
        echo "MsgType:" .$xml->MsgType;echo "</br>";                //事件类型
        echo "Content:" .$xml->Content;echo "</br>";                //文本消息

//         <xml><ToUserName><![CDATA[gh_6a4f70b5eed6]]></ToUserName>^M
// <FromUserName><![CDATA[od-A-1FwnuCU3XUp3HU6wtIuDw48]]></FromUserName>^M
// <CreateTime>1555396243</CreateTime>^M
// <MsgType><![CDATA[text]]></MsgType>^M
// <Content><![CDATA[最新商品]]></Content>^M
// <MsgId>22267922250759760</MsgId>^M
// </xml>^M

    }
}
