<?php

namespace App\Http\Controllers\Crontab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;

class CrontabController extends Controller
{
    public function del()
    {
        $arr = OrderModel::all()->toArray();
        foreach($arr as $k=>$v){
            if(time() - $v['add_time'] > 1800 &&  $v['pay_time']==0){
                OrderModel::where(['oid'=>$v['oid']])->update(['is_delete'=>1]);
            }
        }
        print_r($arr);
    }
}
