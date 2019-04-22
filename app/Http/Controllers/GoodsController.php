<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GoodsModel;
use App\Model\GoodsLookModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;


class GoodsController extends Controller
{
    //
    public function index(){
        $goods_Info = GoodsModel::get()->toArray();
        // echo "<pre>";print_r($goods_Info);echo "</pre>";
        $data = [
            'goods_Info' => $goods_Info
        ];
        return view('goods/index',$data);
    }
    public function detail(){
        $goods_id = $_GET['goods_id'];
        $goods = GoodsLookModel::where(['id'=>$goods_id])->first();
        $key = $goods_id;
        $history = Redis::incr($key);
        // echo $history;die;
        $look_num = $goods->look_num += 1;
        if($goods){
            $look_num = GoodsModel::where(['id'=>$goods_id])->update(['look_num'=>$look_num]);
        }else{
            $look = [
                'goods_id' => $goods_id,
                'uid' => Auth::id(),
                'look_num' => $look_num
            ];
            GoodsLookModel::insertGetId($look);
        }
        // echo "<pre>";print_r($goods);echo "</pre>";
        $data = [
            'goods' => $goods
        ];
        return view('goods/goodsDetail',$data);
    }
}