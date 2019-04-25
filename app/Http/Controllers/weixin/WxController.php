<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Redis;
use App\Model\WxUserModel;

class WxController extends Controller
{
    public function test(){
        echo 111;
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

        $wxModel= WxUserModel::where(['opneid'=>$openid])->first();
        if($wxModel){
            echo "又，来了老弟";
        }else {
            //用户信息入库
            $u_info = [
                'openid' => $res['openid'],
                'nickname' => $res['nickname'],
                'sex' => $res['sex'],
                'headimgurl' => $res['headimgurl'],
            ];
            $id = WxUserModel::insert($u_info);
            echo "热烈欢迎您关注此网站！";
        }
    }
}
