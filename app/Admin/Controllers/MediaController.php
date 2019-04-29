<?php

namespace App\Admin\Controllers;

use App\Model\MediaModel;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Encore\Admin\Controllers\HasResourceActions;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    use HasResourceActions;
    
    public function add(){
        return view('admin/media_add');
        
    }
    public function addDo(Request $request){
        // echo "<pre>";print_r($request->all());echo "</pre>";
        // die;
        $img_file = $request->file('img');     //接收文件
        $img_orign_name = $img_file->getClientOriginalName();     //文件名
        // echo "<pre>";print_r($img_orign_name);echo "</pre>";
        $file_ext = $img_file->getClientOriginalExtension();       //文件类型
        // echo "<pre>";print_r($file_ext);echo "</pre>";
        $new_file_name = 'lb'.date('Y-m-d').Str::random(10).'.'.$file_ext;      //文件名
        $save_file_path = $request->file('img')->storeAs('images',$new_file_name);    //返回保存成功之后的路径
        // echo "<pre>";print_r($save_file_path);echo "</pre>";
        // die;
        // echo 'sa';
        $access_token = getAccessToken();
        // echo $access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type=image';
        $client = new Client;
        $response = $client->request('POST', $url, [
            'multipart' => [
                [
                    'name'     => 'img',
                    'contents' => fopen($save_file_path, 'r')
                ],
            ]
        ]);
        $body = $response -> getBody();
        $json = json_decode($body,true);
        // echo "<pre>";print_r($json);echo "</pre>";die;
        $data=[
            'media_id'=>$json['media_id'],
            'add_time'=>time(),
            'type'=>$json['type']
        ];
        $res=MediaModel::insertGetId($data);
        if($res){
            echo '添加成功';
        }else{
            echo '添加失败';
        }
    }
}