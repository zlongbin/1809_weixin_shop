<?php
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;


    /**
     * 获取AccessToken
     */
    function getAccessToken(){
        $key = "wx_access_token";
        $access_token = Redis::get($key);
        if($access_token){
            return  $access_token;
        }else{
            //获取Access_Token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SECRET');
            $response = json_decode(file_get_contents($url),true);
            // var_dump($response);die;
            if(isset($response['access_token'])){
                Redis::set($key,$response['access_token']); 
                Redis::expire($key,3600);
                return  $response['access_token'];
            }else{
                return false;
            }
        }
    }
    /**
     * 获取Jsapi_Ticket
     */
    function getJsapiTicket(){
        $key = "wx_jsapi_ticket";
        $jsapi_ticket = Redis::get($key);
        if($jsapi_ticket){
            return  $jsapi_ticket;
        }else{
            //获取Access_Token
            $access_token = getAccessToken();            
            //获取Jsapi_Ticket
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $response = json_decode(file_get_contents($url),true);
            // var_dump($response);die;
            if(isset($response['ticket'])){
                Redis::set($key,$response['ticket']); 
                Redis::expire($key,3600);
                return  $response['ticket'];
            }else{
                return false;
            }
        }
    } 
    /**
     *  
     */  
    function getJsConfig(){
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

        return $js_config;
    }
        /**
     * 获取网页授权AccessToken
     */
    function getWebAccessToken($code){
        $key = "response_Info";
        $response_Info = Redis::get($key);
        if($response_Info){
            return  $response_Info;
        }else{
            //获取Access_Token
            $web_access_token_url ='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SECRET').'&code='.$code.'&grant_type=authorization_code';
            $response = json_decode(file_get_contents($web_access_token_url),true);
            // var_dump($response);die;
            if(isset($response['access_token'])){
                $response_Info = [
                    'access_token' => $response['access_token'],
                    'openid' => $response['openid']
                ];
                Redis::set($key,$response_Info); 
                Redis::expire($key,3600);
                return  $response_Info;
            }else{
                return false;
            }
        }
    }
    /**
     * 获取带参数的二维码(sence_str)
     */
    function getStrTicket($sence_str){
        $url ="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".getAccessToken();
        $json = [
            'expire_seconds' => 2592000,
            'action_name' => 'QR_STR_SCENE',
            'action_info' => ['scene' => ['sence_str' => $sence_str]]
        ];
        $json_file = json_encode($json,JSON_UNESCAPED_UNICODE);
        $client = new Client;
        $response = $client ->request('POST',$url ,[
            'body'=>$json_file
        ]);
        $body = $response -> getBody();
        $json = json_decode($body,true);
        $ticket = UrlEncode($json['ticket']);
        $ticket_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        return $ticket_url;
    }
?>