<?php

namespace App\Http\Controllers\examd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class ExamController extends Controller
{
    //首次接入
//    public function valid()
//    {
//        echo $_GET['echostr'];
//    }

    public function getAccessToken()
    {
        $redis_key = 'wx_access_token';
        $token = Redis::get($redis_key);
        if($token){
            echo 'ok:';echo '</br>';
        }else{
            echo 'No:';echo '</br>';
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SEC');
            $json_str = file_get_contents($url);
            $arr = json_decode($json_str,true);
//             echo '<pre>';print_r($arr);echo '</pre>';die;
            Redis::set($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);    //设置过期时间
        }
        // echo $token;
        return $token;
    }

    public function wxEvent()
    {
        $xml_str = file_get_contents("php://input");
        $log_str = '>>>>>>>>>'. date("Y-m-d H:i:s") . $xml_str . "\n";
        file_put_contents('/logs/wx_exam_event.log',$log_str,FILE_APPEND);//日志文件
        $xml_obj = simplexml_load_string($xml_str);

        $msg_type = $xml_obj->MsgType;  //消息类型
        $app = $xml_obj->ToUserName;
        $event = $xml_obj->Event;          //事件类型
        $openid = $xml_obj->FromUserName;   //用户openid


        if(strpos($xml_obj->Content,"最新商品")!==false){
            $media_id = $xml_obj->MediaId;
            $response_xml = '<xml>
                                        <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                        <FromUserName><![CDATA['.$app.']]></FromUserName>
                                        <CreateTime>'.time().'</CreateTime>
                                        <MsgType><![CDATA[news]]></MsgType>
                                        <ArticleCount>1</ArticleCount>
                                        <Articles>
                                            <item>
                                            <Title><![CDATA[Vans]]></Title>
                                            <Description><![CDATA[Vans联名自由定制]]></Description>
                                            <PicUrl><![CDATA[http://1809bieyanan.comcto.com/image/logo.jpg]]></PicUrl>
                                            <Url><![CDATA[https://customs.vans.com.cn/customizer.slip-on-classic.html]]></Url>
                                            </item>
                                        </Articles>
                                    </xml>';
            echo $response_xml;
        }
    }
}
