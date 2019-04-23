<?php
use Illuminate\Support\Facades\Redis;

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
?>