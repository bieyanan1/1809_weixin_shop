<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\OrderModel;
use App\Model\WxUserModel;

class WxController extends Controller
{
    public function test(){
        echo 111;
    }

    public function getAccessToken()
    {
        //先获取缓存,不存在的情况下在请求接口
        $redis_key = 'wx_access_token';
        $access_token = Redis::get($redis_key);
        if($access_token){

        }else{

            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SEC');
            // echo $url;die;
            $json_str = file_get_contents($url);
            $arr = json_decode($json_str,true);
            Redis::set($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);    //设置过期时间
        }
        return $access_token;
    }


    public function getU()
    {
        $code = $_GET['code'];
        $token = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SEC').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($token),true);
        $access_token = $response['access_token'];
        $openid = $response['openid'];
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $res = json_decode(file_get_contents($url),true);
        $wx_orders = OrderModel::where(['opneid'=>$openid])->first();
        if($wx_orders){
            echo "又，来了老弟";
        }else {
            //用户信息入库
            $u_info = [
                'openid' => $u['openid'],
                'nickname' => $u['nickname'],
                'sex' => $u['sex'],
                'headimgurl' => $u['headimgurl'],
            ];
            $id = WxUserModel::insert($u_info);
            echo "热烈欢迎您关注此网站！";
        }

    }
}
