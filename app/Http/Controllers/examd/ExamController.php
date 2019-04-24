<?php

namespace App\Http\Controllers\examd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;


class ExamController extends Controller
{
    public function getAccessToken()
    {
        $key = 'wx_access_token';
        //判断是否有缓存
        $access_token = Redis::get($key);
        if($access_token){
            return $access_token;
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SEC');
            $response = json_decode(file_get_contents($url),true);
            if(isset($response['access_token'])){
                Redis::set($key,$response['access_token']);
                Redis::expire($key,3600);
                return $response['access_token'];
            }else{
                return false;
            }
        }
    }

    public function wxEvent()
    {
        $xml_str = file_get_contents("php://input");
        $log_str = '>>>>>>>>>'. date("Y-m-d H:i:s") . $xml_str . "\n";
        file_put_contents('/tmp/wx_exam.log',$log_str,FILE_APPEND);//日志文件
        $xml_obj = simplexml_load_string($xml_str);
        //处理业务逻辑

        //处理图片素材
        $msg_type = $xml_obj->MsgType;  //消息
        $app = $xml_obj->ToUserName;类型
        $event = $xml_obj->Event;
        $openid = $xml_obj->FromUserName;


        if($msg_type=='text'){
            if(strpos("图片推送")){
                $str = "123";
                $response_xml = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                                        <FromUserName><![CDATA['.$app.']]></FromUserName>
                                        <CreateTime>'.time().'</CreateTime>
                                        <MsgType><![CDATA[text]]></MsgType>
                                        <Content><![CDATA['.$str.']]></Content>
                                    </xml>';
                echo $response_xml;
            }else{
                $response_xml = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                                        <FromUserName><![CDATA['.$app.']]></FromUserName>
                                        <CreateTime>'.time().'</CreateTime>
                                        <MsgType><![CDATA[text]]></MsgType>
                                        <Content><![CDATA["查询格式有误"]]></Content>
                                    </xml>';
                echo $response_xml;
            }
        }
    }
}
