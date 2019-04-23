<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GoodsModel;
use Illuminate\Support\Facades\Redis;

class GoodsController extends Controller
{
    public function index()
    {
        $list = GoodsModel::get()->toArray();
        $data = [  
            'list' => $list
        ];
        return view('goods.index',$data);
    }

    public function list()
    {
        $goods_id = $_GET['goods_id'];
        $key = $goods_id;
        $redis_view = 'ss:goods:view';    
        $history = Redis::incr($key);                     //浏览量
        $goods_view = time();
        Redis::zAdd($redis_view,$history,$goods_id);      //商品浏览量排行
        $res = GoodsModel::where(['id'=>$goods_id])->first();
        if($res){
            GoodsModel::where(['id'=>$goods_id])->update(['look'=>$res['look']+1]);
        }else{
            $detail = [
                'id'=> $goods_id,
                'name'=> $res ->name,
                'look'=> $res['look'] +1
            ];
            GoodsModel::insertGetId($detail);
        }
        $data = [
            'res' => $res
        ];
        $list = Redis::zRangeByScore($redis_view,0,1000,['withscores' => true]);    //正序
        $lists = Redis::zRevRange($redis_view,0,1000,true);                         //倒序

        $info = [];
        foreach($lists as $k=>$v){
            $info[] = GoodsModel::where(['id'=>$k])->first()->toArray();
        }
        return view('goods.list',$data,['info'=>$info]);
    }
}
