<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\OrderModel;

class CrontabController extends Controller
{
    /**
     * 删除过期订单
     */
    public function delOrder(){
        $orderInfo = OrderModel::where(['status'=>0])->get()->toArray();
        // var_dump($orderInfo);
        foreach($orderInfo as $k=>$v){
            if((time()-$v['add_time'])>1800){
                OrderModel::where(['oid'=>$v['oid']])->update(['is_delete'=>1]);
            }
        }
    }
}
