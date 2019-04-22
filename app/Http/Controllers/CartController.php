<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Model\CartModel;
use App\Model\GoodsModel;

class CartController extends Controller
{
    //
    public function index(){
        // echo __METHOD__;echo "<hr>";
        $cart_list = CartModel::where(['uid' => Auth::id(),'session_id' => Session::getId()])->get();
        if($cart_list){
            $cart_arr = $cart_list->toArray();
            foreach($cart_arr as $k=>$v){
                $goods_list[] = GoodsModel::where(['id'=>$v['goods_id']])->first()->toArray();
            }
            $data = [
                'goods_list'=>$goods_list 
            ];
            return view('cart/index',$data);
        }else{
            header('Refrech:2;url:/');
            die("购物车为空");
        }

    }
    public function add($goods_id=2){
        if(empty($goods_id)){
            header('Refresh:3;url=/cart');
            die("请选择商品");
        }
        $goods = GoodsModel::where(['id'=>$goods_id])->first();
        if($goods){
            if($goods->is_delete==1){
                header('Refresh:3;url=/cart');
                echo "该商品已下架";
                die;
            }
            // 添加购物车
            $cart_Info = [
                'goods_id' => $goods_id,
                'goods_name' => $goods->name,
                'goods_price' => $goods->price,
                'uid' => Auth::id(),
                'add_time' => time(),
                'session_id' => Session::getId()
            ];
            echo "<pre>";print_r($cart_Info);echo "</pre>";
            $cart_id = CartModel::insertGetId($cart_Info);
            if($cart_id){
                die("添加购物车成功");
            }else{
                die("添加购物车失败");
            }
        }else{
            echo "商品不存在";
        }
    }
}
