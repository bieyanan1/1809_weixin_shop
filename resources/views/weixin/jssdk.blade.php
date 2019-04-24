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
    <button id="btn1">选择照片</button>
    <script src="js/jquery/jquery-1.8.3.min.js"></script>
    <img src="" alt="" id="imgs0" width="260"><hr>
    <img src="" alt="" id="imgs1" width="260"><hr>
    <img src="" alt="" id="imgs2" width="260"><hr>
    


    <script>
         wx.config({
            appId: "{{$js_config['appId']}}",
            timestamp: "{{$js_config['timestamp']}}", 
            nonceStr: "{{$js_config['nonceStr']}}", 
            signature: "{{$js_config['signature']}}",
            jsApiList: ['chooseImage','uploadImage']
        });


        wx.ready(function(){
            $("#btn1").click(function(){

                wx.chooseImage({
                    count: 3, // 默认9
                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function ( res) {
                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                        var img = "";

                        $.each(localIds,function(i,v){
                            img += v+',';
                            var node = "#imgs"+i;
                            $(node).attr('src',v);
                        })


                        $.ajax({
                            url : '/js/getImg?img='+img,     
                            type: 'get',
                            success:function(d){
                                console.log(d);
                            }
                        });
                        console.log(img);
                    }
                });
                wx.updateAppMessageShareData({
                    title: '最新商品', // 分享标题
                    desc: 'laravel', // 分享描述
                    link: 'http://1809bieyanan.comcto.com/wx/pay/test', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: 'http://1809bieyanan.comcto.com/image/logo.jpg', // 分享图标
                    success: function () {
                        alert("分享成功");
                    }
                })
            });
        });
        

    </script>
</body>
</html>