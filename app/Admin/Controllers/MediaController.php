<?php

namespace App\Admin\Controllers;

use App\Model\MediaModel;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function add(){
        // echo 'sa';
        $access_token = getAccessToken();
        // echo $access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE';
        return view('admin/aaa');
    }
    /*
    * 执行文件上传
    * */
    public function prUploadsDo(Request $request){
        $rp_tatil = $request->input('rp_tatil',null);
        $type = $request->input('rp_type',null);
        $rp_desc = $request->input('rp_desc',null);
        $time     = time();
        if($request->hasFile('file')){
            $file = $request->file;
            $re = MaterialModel::upLoadsFile($file);
            $imgpath = $re['imgpath'];
            $data = $re['data'];
            $count = strpos($data,'/');
            $data = substr($data,0,$count);
    //            print_r($data);die;

            $arr = [
                'media' => new \CURLFile(realpath($imgpath))
            ];
            $url    = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->accessToken()."&type=$data";
            $objurl = new \url();
            $json   = $objurl -> sendPost($url,$arr);

            //存入数据库
            $jsonData = json_decode($json,true);
            $media_id = $jsonData['media_id'];
            $rp_url = $jsonData['url'];


            $data = [
                'rp_type'     => $type,
                'rp_tatil'    => $rp_tatil,
                'rp_desc'     => $rp_desc,
                'rp_url'      => $rp_url,
                'media_id'    => $media_id,
                'rp_upload'   => $imgpath,
                'create_time' => $time
            ];
            $res = RpmaterialModel::insertGetId($data);
            if($res){
                echo ('添加成功');
            }else{
                echo ('添加失败');
            }
        }else{
            $data = [
                'rp_type'     => $type,
                'rp_tatil'    => $rp_tatil,
                'rp_desc'     => $rp_desc,
                'rp_url'      => null,
                'media_id'    => null,
                'rp_upload'   => null,
                'create_time' => $time
            ];
            $res = RpmaterialModel::insertGetId($data);
            if($res){
                echo ('添加成功');
            }else{
                echo ('添加失败');
            }
        }
    }
}