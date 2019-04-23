<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GoodsModel;
use App\Model\GoodsLookModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;


class GoodsController extends Controller
{
    /**
     * 商品
     */
    public function index(){
        $goods_Info = GoodsModel::get()->toArray();
        $data = [
            'goods_Info' => $goods_Info
        ];
        return view('goods/index',$data);
    }
    /**
     * 商品详情
     */
    public function detail(){
        $goods_id = $_GET['goods_id'];
        $sort_Info = $this->getSort($goods_id);
        $history_Info = $this->history($goods_id);
        // $goods = GoodsLookModel::where(['id'=>$goods_id])->first();
        // 数据库
        // if($goods){
        //     $look_num = $goods->look_num += 1;
        //     $look = GoodsLookModel::where(['id'=>$goods_id])->update(['look_num'=>$look_num]);
        // }else{
        //     $look = [
        //         'goods_id' => $goods_id,
        //         'uid' => Auth::id(),
        //         'look_num' => 1,
        //         'look_time' => time()
        //     ];
        //     GoodsLookModel::insertGetId($look);
        // }
        $goods_detail = GoodsModel::where(['id'=>$goods_id])->first();
        // echo "<pre>";print_r($goods);echo "</pre>";
        $data = [
            'goods_detail' => $goods_detail,
            'history_Info' =>$history_Info,
            'sort_Info' => $sort_Info
        ];
        return view('goods/goodsDetail',$data);
    }
    /**
     * 哈希
     */
    public function cacheGoods($goods_id){
        $goods_id = intval($goods_id);
        $redis_cache_goods_key = "h:goods_Info:".$goods_id;
        $cache_Info = Redis::hGetAll($redis_cache_goods_key);       //获取缓存
        if($cache_Info){
            echo "Cache";                           //有缓存
        }else{
            echo "No Cache";                        //无缓存
            $goods_Info = GoodsModel::where(['id'=>$goods_id])->first()->toArray();         
            Redis::hMset($redis_cache_goods_key,$goods_Info);       //存储缓存
        }
    }
    /**
     * 获取商品浏览排名
     */
    public function getSort($goods_id){
        $redis_view_key = "count:view:goods_id:".$goods_id;     //浏览量
        $view = Redis::incr($redis_view_key);                   //浏览量+n
        $redis_ss_view = "ss:goods:view";                       //浏览排名
        $redis = Redis::zAdd($redis_ss_view,$view,$goods_id);            //有序集合记录  浏览排名
        // $list = Redis::zRangeByScore($key,0,10000,['withscores'=>true]);
        $list = Redis::zRevRange($redis_ss_view,0,10000,true);
            // echo "<pre>";print_r($list);echo "</pre>";
        foreach($list as $k=>$v){
            // $sort_Info[] = GoodsModel::where(['id'=>$k])->first()->toArray();
            $sort_Info[] = Redis::hGetAll("h:goods_history:".$k.Auth::id());
            $sort_Info['view']=$v;
        }
            // var_dump($sort_Info);
        return $sort_Info;
    }
    /**
     * 浏览历史
     */
    public function history($goods_id){
        $redis_history_goods_key = "h:goods_history:".$goods_id.Auth::id();
        $cache_Info = Redis::hGetAll($redis_history_goods_key);       //获取缓存
        if($cache_Info){
            $history=Redis::hSet($redis_history_goods_key,'time',time()); 
        }else{
            // echo '7788';die;
            $goods_Info = GoodsModel::where(['id'=>$goods_id])->first()->toArray();      
            $goods_Info['uid']=Auth::id();
            $goods_Info['time']=time();
            $history=Redis::hMset($redis_history_goods_key,$goods_Info);       //存储缓存
            
        }
        // die;
        $redis_ss_history = "ss:goods:history";                       //浏览排名
        $zadd = Redis::zAdd($redis_ss_history,$cache_Info['time'],$goods_id);            //有序集合记录  浏览排名

        $lists = Redis::zRevRange($redis_ss_history,0,9999999999,true);
        echo "<pre>";print_r($lists);echo "</pre>";
        foreach($lists as $k=>$v){
            // $goods_Info[] = GoodsModel::where(['id'=>$k])->first()->toArray();
            $goods_Info[] = Redis::hGetAll("h:goods_history:".$k.Auth::id());
            Redis::hSet("h:goods_history:".$k.Auth::id(),'time',date());
        }
        // var_dump($goods_Info);
        return $goods_Info;
    }
}