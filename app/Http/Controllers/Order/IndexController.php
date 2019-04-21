<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Model\CartModel;
use App\Model\OrderModel;
use App\Model\OrderDetailModel;

class IndexController extends Controller
{
    public function create()
    {
        // echo  'order_sn: '.OrderModel::generateOrderSN(Auth::id());die;

        //计算订单金额
        $goods = CartModel::where(['uid' => Auth::id(),'session_id' => Session::getId()])->get()->toArray();
            // echo '<pre>';print_r($goods);echo '</pre>';
            $order_amount = 0;
            foreach($goods as $k => $v){
                $order_amount+=$v['goods_price'];//订单金额
            }

            $order_info = [
                'uid'               => Auth::id(),
                'order_sn'          => OrderModel::generateOrderSN(Auth::id()),     //订单编号
                'order_amount'      => $order_amount,
                'add_time'          => time()
            ];

        $oid = OrderModel::insertGetId($order_info);   //写入订单表

        //订单详情
        foreach($goods as $k=>$v){
            $detail = [
                'oid'           => $oid,
                'goods_id'      => $v['goods_id'],
                'goods_name'    => $v['goods_name'],
                'goods_price'   => $v['goods_price'],
                'uid'           => Auth::id()
            ];
            //写入订单详情表
            OrderDetailModel::insertGetId($detail);
        }

        header('Refresh:2;url=/order/list');
        echo "生成订单成功";
    }


    //订单列表
    public function oList()
    {
        $list = OrderModel::where(['uid'=>Auth::id()])->orderBy("oid","desc")->get()->toArray();
        $data = [
            'list'  => $list
        ];
        return view('order.list',$data);
    }

    //查询订单支付状态
    public function payStatus()
    {
        $oid = intval($_GET['oid']);
        $info = OrderModel::where(['oid'=>$oid])->first();
        $response = [];
        if($info){
            if($info->pay_time>0){
                $response = [
                    'status'    => 0,
                    'msg'       => 'ok'
                ];
            }
        }else{
            die("订单不存在");
        }
        die(json_encode($response));
    }
}
