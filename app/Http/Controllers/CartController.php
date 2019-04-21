<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        // echo  __METHOD__;
        $cart_list = CartModel::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get();
        if($cart_list){
            // echo '<pre>';print_r($cart_list->toArray());echo '</pre>';
            $cart_arr = $cart_list->toArray();
            // echo '<pre>';print_r($cart_arr);echo '</pre>'; 
            $total_price = 0;
            foreach($cart_arr as $k=>$v){
                $g = GoodsModel::where(['id'=>$v['goods_id']])->first();
                $total_price += $g['price'];
                $goods_list[] = $g;
            }
            // echo '<pre>';print_r($goods_list);echo '</pre>';die;
            //展示购物车商品
            $data = [
                'goods_list' => $goods_list,
                'total' => $total_price / 100
            ];
            return view('cart.index',$data);
        }else{
            header('Refresh:2;url=/');
            die("购物车为空,跳转到首页");
        }
    }


    //添加购物车
    public function add($goods_id=0)
    {
        // var_dump(Session::getId());die;

        if(empty($goods_id)){
            header('Refresh:2;url=/cart');
            die("×请选择商品×");
        }
        // echo $goods_id;

        $goods = GoodsModel::where(['id'=>$goods_id])->first();
        if($goods){
            if($goods->is_delete==1){
                header('Refresh:2;url=/cart');
                echo("商品已被删除，两秒后跳转首页");
                die;
            }

            //添加购物车
            $cart_info=[
                'goods_id' => $goods_id,
                'uid' => Auth::id(),
                'goods_name' => $goods->name,
                'goods_price' => $goods->price,
                'add_time'=> time(),
                'session_id'=>Session::getId()
            ];
            $cart_id = CartModel::insertGetId($cart_info); 
            if($cart_id){
                header('Refresh:2;url=/cart');
                die("添加购物车成功,2秒后跳转到购物车");
            }else{
                header('Refresh:2;url=/');
                die("添加购物车失败");
            }

        }else{
            echo "商品不存在";
        }
    }
}
