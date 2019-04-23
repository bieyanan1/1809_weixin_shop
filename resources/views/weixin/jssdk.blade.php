<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mr.</title>
    <script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
</head>
<body>
    <button id="aa">选择照片</button>
    <script src="js/jquery/jquery-1.8.3.min.js"></script>
    <img src="" alt="" id="imgs0" width="260"><hr>
    <img src="" alt="" id="imgs1" width="260"><hr>
    <img src="" alt="" id="imgs2" width="260"><hr>
    


    <script>
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: "{{$js_config['appId']}}", // 必填，公众号的唯一标识
            timestamp: "{{$js_config['timestamp']}}", // 必填，生成签名的时间戳
            nonceStr: "{{$js_config['nonceStr']}}", // 必填，生成签名的随机串
            signature: "{{$js_config['signature']}}",// 必填，签名
            jsApiList: ['chooseImage'] // 必填，需要使用的JS接口列表
        });


        wx.ready(function(){
            wx.chooseImage({
                count: 3, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    var img = "";
                    $.each(localIds,function(i,v){
                        img += v+',';
                        var node = "#imgs"+i;
                        $(node).attr('src',v);

                        console.log(img);
                    })

                    $.ajax({
                            url : '/js/getImg?img='+img,     //将上传的照片id发送给后端
                            type: 'get',
                            success:function(d){
                                console.log(d);
                            }
                        });
                }
            });
        })


    </script>
</body>
</html>