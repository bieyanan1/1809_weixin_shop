<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    //购物车
    public function index()
    {
        $cart_list = CartModel::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
        if($cart_list){
            $total_price = 0;
            foreach($cart_list as $k=>$v){
                $g= GoodsModel::where(['id'=>$v['goods_id']])->first()->toArray();
                $total_price += $g['price'];
                $goods_list[] = $g;
            }
            //展示购物车
            $data = [
                'goods_list' => $goods_list,
                'total'    => $total_price
            ];
            return view('cart.index',$data);
        }else{
            header('Refresh:2;url=/');
            die("购物车为空,跳转至首页");
        }
    }
}
