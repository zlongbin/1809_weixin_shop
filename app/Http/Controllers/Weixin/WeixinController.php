<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use App\Model\WxUserModel;
use App\Model\WebUserModel;
use App\Model\TmpWxUserModel;
use GuzzleHttp\Client;

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
        $event = $xml_obj->Event;                  //事件类型
        $msg_type = $xml_obj->MsgType;             // 消息类型
        $eventkey = $xml_obj->EventKey;            //事件key值

        $goodsInfo = GoodsModel::orderBy('id','desc')->first();
        $PicUrl = "http://1809zhoubinbin.comcto.com/images/QQ图片20190107153840.jpg";
        $Url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goodsInfo['id'];
        if($event=="click"){
            
        }elseif($event=='subscribe'){        //扫码关注（未关注）
            if($eventkey==true){
                $tmp_user = TmpWxUserModel::where(['openid'=>$openid])->first();
                if(!$tmp_user){
                     // 用户信息入库
                     $user_Info=[
                        'openid'=>$openid
                    ];
                    $id = TmpWxUserModel::insert($user_Info);
                    $user="欢迎新用户";
                }else{
                    $user="欢迎回来";
                }
                //用户未关注
                $response = '<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>
                    <item>
                    <Title><![CDATA['.$user.']]></Title>
                    <Description><![CDATA[]]></Description>
                    <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                    <Url><![CDATA['.$Url.']]></Url>
                    </item>
                </Articles>
                </xml>';
                return  $response;
            }else{
                //关注事件
                // 根据openid判断用户是否存在
                $local_user = WxUserModel::where(['openid'=>$openid])->first();
                if($local_user){
                    echo '<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA['. '欢迎回来 '. $local_user['nickname'] .']]></Content>
                    </xml>';
                }else{
                    // 获取用户信息
                    $user =$this->getUserInfo($openid);
                    // 用户信息入库
                    $user_Info=[
                        'openid'=>$user['openid'],
                        'nickname'=>$user['nickname'],
                        'sex'=>$user['sex'],
                        'headimgurl'=>$user['headimgurl']
                    ];
                    $id = WxUserModel::insert($user_Info);
                    echo '<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA['. '欢迎关注 '. $user['nickname'] .']]></Content>
                    </xml>';
                }
            }
        }elseif($event=='SCAN'){        //扫码关注（已关注）
            $tmp_user = TmpWxUserModel::where(['openid'=>$openid])->first();
            if(!$tmp_user){
                 // 用户信息入库
                 $user_Info=[
                    'openid'=>$openid
                ];
                $id = TmpWxUserModel::insert($user_Info);
                $user="欢迎新用户";
            }else{
                $user="欢迎回来";
            }
            //用户已关注
            $response = '<xml>
            <ToUserName><![CDATA['.$openid.']]></ToUserName>
            <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
            <CreateTime>'.time().'</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>1</ArticleCount>
            <Articles>
                <item>
                <Title><![CDATA['.$user.']]></Title>
                <Description><![CDATA["烫死你"]]></Description>
                <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                <Url><![CDATA['.$Url.']]></Url>
                </item>
            </Articles>
            </xml>';
            return $response;
        }elseif($msg_type=='text'){                 //获取天气信息
            if(strpos($xml_obj->Content,"+天气")){
                $city=explode('+',$xml_obj->Content)[0];
                // echo "City : ".$city;
                $url = "https://free-api.heweather.net/s6/weather/now?key=HE1905221904151634&location=".$city;
                $arr = json_decode(file_get_contents($url),true);
                // echo '<pre>';print_r($arr);echo "</pre>";               
                if($arr['HeWeather6'][0]['status']=='ok'){
                    $fl = $arr['HeWeather6'][0]['now']['fl'];               //摄氏度
                    $wind_dir = $arr['HeWeather6'][0]['now']['wind_dir'];   //风向
                    $wind_sc = $arr['HeWeather6'][0]['now']['wind_sc'];     //风力
                    $hum = $arr['HeWeather6'][0]['now']['hum'];             //湿度
                    $str="城市 : $city \n"."摄氏度 : $fl \n"."风向 : $wind_dir \n"."风力 : $wind_sc \n"."湿度 : $hum \n";
    
                    $response_xml='<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA['.$str.']]></Content>
                    </xml>';
                }else{
                    $response_xml='<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA["城市名不正确"]]></Content>
                    </xml>';
                }
                return $response_xml;
            }elseif(strpos($xml_obj->Content,"最新商品")!==false){      //最新商品
                $response = '<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>
                    <item>
                    <Title><![CDATA['.$goodsInfo['name'].']]></Title>
                    <Description><![CDATA["烫死你"]]></Description>
                    <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                    <Url><![CDATA['.$Url.']]></Url>
                    </item>
                </Articles>
                </xml>';
                return  $response;
            }else{          //搜索商品
                $goods = GoodsModel::where('name','like',"%".$xml_obj->Content."%")->first();
                $PicUrl = "http://1809zhoubinbin.comcto.com/media/".$goods['img'];
                $Url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goods['id'];
                if($goods){
                    $response = '<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                        <item>
                        <Title><![CDATA['.$goods['name'].']]></Title>
                        <Description><![CDATA["您要搜索的商品"]]></Description>
                        <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                        <Url><![CDATA['.$Url.']]></Url>
                        </item>
                    </Articles>
                    </xml>';
                }else{
                    $count = GoodsModel::get()->count();
                    $goods = GoodsModel::where(['id' => rand(1,$count)])->first();
                    $PicUrl = "http://1809zhoubinbin.comcto.com/media/".$goods['img'];
                    $Url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goods['id'];
                    $response = '<xml>
                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                    <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                    <CreateTime>'.time().'</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                        <item>
                        <Title><![CDATA['.$goods['name'].']]></Title>
                        <Description><![CDATA["未搜索到您要找的商品，为您推荐以下商品"]]></Description>
                        <PicUrl><![CDATA['.$PicUrl.']]></PicUrl>
                        <Url><![CDATA['.$Url.']]></Url>
                        </item>
                    </Articles>
                    </xml>';
                }
                return $response;
            }
        }
    }
    // 获取微信用户信息
    public function getUserInfo($openid){
        // $openid='od-A-1FwnuCU3XUp3HU6wtIuDw48';
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.getAccessToken().'&openid='.$openid.'&lang=zh_CN';
        $data = file_get_contents($url);
        $user = json_decode($data,true);
        return $user;
    }
    public function wxWeb(){
        $redirect_uri = urlEncode('http://1809zhoubinbin.comcto.com/wxweb/getu');
        var_dump($redirect_uri);
        $url =  'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WX_APP_ID').'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        echo $url;
    }
    public function getU(){
        $code = $_GET['code'];
        // 获取授权access_token
        $access_token_url ='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SECRET').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($access_token_url),true);
        // echo "<pre>";print_r($response);echo "</pre>";
        $access_token = $response['access_token'];
        $openid = $response['openid'];
        // 获取用户信息
        $user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_Info = json_decode(file_get_contents($user_url),true);
        // echo "<pre>";print_r($user_Info);echo "</pre>";
        // 根据openid判断用户是否存在
        $wx_user = WebUserModel::where(['openid'=>$openid])->first();
        if($wx_user){
            echo '欢迎回来';die;
        }else{
            // 用户信息入库
            $Info=[
                'openid'=>$user_Info['openid'],
                'nickname'=>$user_Info['nickname'],
                'sex'=>$user_Info['sex'],
                'headimgurl'=>$user_Info['headimgurl']
            ];
            $id = WebUserModel::insert($Info);
            echo '欢迎访问此网页';die;
        }
    }
    public function menu(){
        $count = GoodsModel::get()->count();
        $goods = GoodsModel::where(['id' => rand(1,$count)])->first();
        $url = "http://1809zhoubinbin.comcto.com/goods/detail?goods_id=".$goods['id'];
        $code = $_GET['code'];
        // 获取授权access_token
        $access_token_url ='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SECRET').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($access_token_url),true);
        // echo "<pre>";print_r($response);echo "</pre>";
        $access_token = $response['access_token'];
        $openid = $response['openid'];
        // 获取用户信息
        $user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_Info = json_decode(file_get_contents($user_url),true);
        echo "欢迎".$user_Info['nickname']."访问此网页";
        header("refresh:3;url={$url}"); 
    }
    // 自定义菜单
    public function createMenu(){
        $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".getAccessToken();
        $response_url =  'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WX_APP_ID').'&redirect_uri=http://1809zhoubinbin.comcto.com/weixin/menu&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        $json_arr = [
            "button" => [
                [
                    'type' => 'view',
                    'name' => '最新福利',
                    'url' => $response_url
                ],
                [
                    'type' => 'click',
                    'name' => '签到',
                    'key' => 'key_click_1'
                ],
            ]
        ];
        $str = json_encode($json_arr,JSON_UNESCAPED_UNICODE);
        $client = new Client;
        $response = $client -> request('post',$menu_url,['body' => $str]);
        $body = $response->getBody();
        $arr = json_decode($body,true);
        echo "<pre>";print_r($arr);echo "</pre>";
        if($arr['errcode']>0){
            echo "创建菜单失败";
        }else{
            echo "创建菜单成功";
        }
    }
    /**
     * 群发
     */
    public function message(){
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".getAccessToken();
        $wx = WxUserModel::get('openid')->toArray();
        $openid=[];
        foreach($wx as $k=>$v){
            // echo $v['openid'];
            $openid[$k]=$v['openid'];
        }
        // var_dump($openid);die;
        $content = [
            0 => '真理惟一可靠的标准就是永远自相符合',
            1 => '时间是一切财富中最宝贵的财富',
            2 => '世界上一成不变的东西，只有“任何事物都是在不断变化的”这条真理',
            3 => '过放荡不羁的生活，容易得像顺水推舟，但是要结识良朋益友，却难如登天',
            4 => '如果你浪费了自己的年龄，那是挺可悲的。因为你的青春只能持续一点儿时间——很短的一点儿时间',
            5 => '在一个崇高的目标支持下，不停地工作，即使慢，也一定会获得成功',
            6 => '生活有度，人生添寿',
            7 => '我需要三件东西：爱情友谊和图书。然而这三者之间何其相通！炽热的爱情可以充实图书的内容，图书又是人们最忠实的朋友',
            8 => '相信谎言的人必将在真理之前毁灭',
            9 => '友谊是一棵可以庇荫的树',
            10 => '理想是人生的太阳',
            11 => '如果你浪费了自己的年龄，那是挺可悲的。因为你的青春只能持续一点儿时间——很短的一点儿时间',
            12 => '我读的书愈多，就愈亲近世界，愈明了生活的意义，愈觉得生活的重要',
            13 => '不达成功誓不休',
        ];
        // echo $content[rand(0,4)];die;
        $json_arr = [
            "touser"=>[
                $openid
            ],
             "msgtype"=> "text",
             "text"=> ["content"=>$content[rand(0,13)]] 
        ];
        $str = json_encode($json_arr,JSON_UNESCAPED_UNICODE);


        $client = new Client;
        $response = $client -> request('post',$url,['body' => $str]);
        $body = $response->getBody();
        $arr = json_decode($body,true);
        echo "<pre>";print_r($arr);echo "</pre>";
        if($arr['errcode']>0){
            echo "ok";
        }else{
            echo "no";
        }
    }
}