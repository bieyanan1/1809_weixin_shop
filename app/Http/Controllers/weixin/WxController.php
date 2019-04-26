<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WxUserModel;

class WxController extends Controller
{
    public function test(){
        echo 111;
    }


    //首次接入
    public function valid()
    {
        echo $_GET['echostr'];
    }

    public function getAccessToken()
    {
        //先获取缓存,不存在的情况下在请求接口
        $redis_key = 'wx_access_token';
        $token = Redis::get($redis_key);
        if($token){
            echo 'ok:';echo '</br>';
        }else{
            echo 'No:';echo '</br>';
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_APP_SEC');
            // echo $url;die;
            $json_str = file_get_contents($url);
            $arr = json_decode($json_str,true);
            // echo '<pre>';print_r($arr);echo '</pre>';
            Redis::set($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);    //设置过期时间
        }
        // echo $token;
        return $token;
    }

    //接受微信服务器推送
    public function wxEvent()
    {
        $xml_str = file_get_contents("php://input");
        $log_str = '>>>>>>>>>'. date("Y-m-d H:i:s") . $xml_str . "\n";
        file_put_contents('/tmp/wx_event.log',$log_str,FILE_APPEND);//日志文件
        $xml_obj = simplexml_load_string($xml_str);
        //处理业务逻辑

        //处理图片素材
        $msg_type = $xml_obj->MsgType;  //消息类型
        $app = $xml_obj->ToUserName;
        $event = $xml_obj->Event;          //事件类型
        $openid = $xml_obj->FromUserName;   //用户openid


        if($event=='subscribe'){            //扫码关注事件
            //根据openid判断用户是否已存在
            $local_user = WxUserModel::where(['openid'=>$openid])->first();
            if($local_user){        //用户之前关注过
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$app.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $local_user['nickname'] .']]></Content></xml>';
            }else{              //用户首次关注
                //获取用户信息
                $u = $this->getUserInfo($openid);
                //用户信息入库
                $u_info = [
                    'openid'    => $u['openid'],
                    'nickname'  => $u['nickname'],
                    'sex'  => $u['sex'],
                    'headimgurl'  => $u['headimgurl'],
                ];
                $id = WxUserModel::insert($u_info);
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$app.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
            }
        }elseif($msg_type=='image'){      //处理图片素材
            $media_id = $xml_obj->MediaId;
            $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;
            $response = $client->get(new Uri($url));
            $headers = $response->getHeaders();     //获取 响应 头信息
            $file_info = $headers['Content-disposition'][0];            //获取文件名
            $file_name =  rtrim(substr($file_info,-20),'"');
            $new_file_name = 'weixin/' .substr(md5(time().mt_rand()),10,8).'_'.$file_name;
            //保存文件
            $rs = Storage::put($new_file_name, $response->getBody());       //保存文件
            if($rs){
                //TODO 保存成功
            }else{
                //TODO 保存失败
            }
        }elseif($msg_type=='voice'){
            $media_id = $xml_obj->MediaId;
            $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;
            $amr = file_get_contents($url);
            $file_name = time() . mt_rand(11111,99999) . '.amr';
            $rs = file_put_contents('wx/voice/'.$file_name,$amr);     //保存录音文件
            var_dump($rs);
        }elseif($msg_type=='text'){
            //自动回复天气
            if(strpos($xml_obj->Content,"+天气")){
                // echo $xml_obj->Content;
                //获取城市名
                $city = explode('+',$xml_obj->Content)[0];
                // echo 'City: '.$city;die;
                $url = 'https://free-api.heweather.net/s6/weather/now?key=HE1904161034501555&location='.$city;
                $arr = json_decode(file_get_contents($url),true);
                // echo '<pre>';print_r($arr);echo '</pre>';die;
                if($arr['HeWeather6'][0]['status']=='ok'){
                    $fl = $arr['HeWeather6'][0]['now']['fl'];                   //体感温度
                    $wind_dir = $arr['HeWeather6'][0]['now']['wind_dir'];       //风向
                    $cond_txt = $arr['HeWeather6'][0]['now']['cond_txt'];       //实时天气状况描写
                    $wind_sc = $arr['HeWeather6'][0]['now']['wind_sc'];         //风力
                    $hum = $arr['HeWeather6'][0]['now']['hum'];                 //湿度
                    $str = "城市：".$city."\n"."温度: ".$fl."℃"."\n" . "风向: ".$wind_dir ."\n" . "风力： ".$wind_sc ."级"."\n" . "湿度：".$hum ."\n" . "天气：".$cond_txt ."\n";

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
                                        <Content><![CDATA["城市名称不正确"]]></Content>
                                    </xml>';
                    echo $response_xml;
                }
            }elseif(strpos($xml_obj->Content,"最新商品")!==false){
                $media_id = $xml_obj->MediaId;
                $response_xml = '<xml>
                                        <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                        <FromUserName><![CDATA['.$app.']]></FromUserName>
                                        <CreateTime>'.time().'</CreateTime>
                                        <MsgType><![CDATA[news]]></MsgType>
                                        <ArticleCount>1</ArticleCount>
                                        <Articles>
                                            <item>
                                            <Title><![CDATA[最新商品]]></Title>
                                            <Description><![CDATA[laravel]]></Description>
                                            <PicUrl><![CDATA[http://1809bieyanan.comcto.com/image/logo.jpg]]></PicUrl>
                                            <Url><![CDATA[http://1809bieyanan.comcto.com/wx/pay/test]]></Url>
                                            </item>
                                        </Articles>
                                    </xml>';
                echo $response_xml;
            }
        }
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
