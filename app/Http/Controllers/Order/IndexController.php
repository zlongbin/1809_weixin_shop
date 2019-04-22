<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;
use App\Model\OrderModel;
use App\Model\OrderDetailModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class IndexController extends Controller
{
    //
    public function create(){
        $goods = CartModel::where(['uid' => Auth::id(),'session_id' => Session::getId()])->get()->toArray();
        $order_amount = 0;
        foreach($goods as $k=>$v){
            $order_amount += $v['goods_price'];   //计算订单金额
        }
        $order_Info =[
            'uid' => Auth::id(),
            'order_sn' => OrderModel::generateOrderSN(Auth::id()),
            'order_amount' => $order_amount,
            'add_time' => time()
        ];
        $oid = OrderModel::insertGetId($order_Info);   //写入订单表

        foreach($goods as $k=>$v){
            $detail = [
                'oid' => $oid,
                'goods_id' => $v['goods_id'],
                'goods_name' => $v['goods_name'],
                'goods_price' => $v['goods_price'],
                'uid' => Auth::id()
            ];
            OrderDetailModel::insertGetId($detail);
        }
        echo "生成订单成功";
    }
    /**
     * 订单列表
     */
    public function orderList(){
        $list = OrderModel::where(['uid'=>Auth::id()])->orderBy('oid','desc')->get()->toArray();
        $data = [
            'list'=>$list
        ];
        return view('order/list',$data);
    }
}
