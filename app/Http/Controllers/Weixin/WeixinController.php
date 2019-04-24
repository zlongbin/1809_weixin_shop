<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;

class WeixinController extends Controller
{
    //
    public function valid(){
        echo $_GET['echostr'];
    }
    public function wxEvent(){
        $content = file_get_contents("php://input");
        $time = date('Y-m-d H:i:s');
        $str = $time . $content ."\n";
        file_put_contents("logs/weixin_event.log",$str,FILE_APPEND);
        $xml_obj = simplexml_load_string($content);
        // echo 'ToUserName: '. $xml_obj->ToUserName;echo '</br>';        // 公众号ID
        // echo 'FromUserName: '. $xml_obj->FromUserName;echo '</br>';    // 用户OpenID
        // echo 'CreateTime: '. $xml_obj->CreateTime;echo '</br>';        // 时间戳
        // echo 'MsgType: '. $xml_obj->MsgType;echo '</br>';              // 消息类型
        // echo 'Event: '. $xml_obj->Event;echo '</br>';                  // 事件类型
        // echo 'EventKey: '. $xml_obj->EventKey;echo '</br>';
        // echo 'Content: '. $xml_obj->Content;echo '</br>';              //文字内容
        
        $wx_id = $xml_obj->ToUserName;             // 公众号ID
        $openid = $xml_obj->FromUserName;          //用户OpenID
        $msg_type = $xml_obj->MsgType;             // 消息类型
        if($msg_type=="text"){
            if(strpos($xml_obj->Content,"最新商品")!==false){
                $goodsInfo = GoodsModel::orderBy('id','desc')->first();
                // echo "<pre>";print_r($goodsInfo);echo "</pre>";
                // echo $goodsInfo['id'];
                $PicUrl = "http://1809zhoubinbin.comcto.com/images/QQ图片20190107153840.jpg";
                $Url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goodsInfo['id'];
                $response = '<xml>
                <ToUserName><![CDATA['.$wx_id.']]></ToUserName>
                <FromUserName><![CDATA['.$openid.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>
                    <item>
                    <Title><![CDATA['.$goodsInfo['name'].']]></Title>
                    <Description><![CDATA[description1]]></Description>
                    <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                    <Url><![CDATA['.$Url.']]></Url>
                    </item>
                </Articles>
                </xml>';
            }
            echo $response;
        }
    }
}