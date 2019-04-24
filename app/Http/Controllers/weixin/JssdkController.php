<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JssdkController extends Controller
{
    public function test()
    {
        // print_r($_SERVER);die; 
        $nonceStr = Str::random(10);
        $ticket = getJsapiTicket();
        $timestamp = time();
        $current_url = $_SERVER['REQUEST_SCHEME']. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $str = "jsapi_ticket=$ticket&nonceStr=$nonceStr&timestamp=$timestamp&url=$current_url";
        $sign = sha1($str);
        // echo $sign;die;

        $js_config = [
            'appId' => env('WX_APP_ID'),    //公众号ID
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,  //随机字符串 
            'signature' => $sign
        ];

        $data = [
            'js_config' => $js_config
        ];
        return view('weixin.jssdk',$data);
    

        
    }


    public function getImg()
    {
        echo '<pre>';print_r($_GET);echo '</pre>';
    }
}
