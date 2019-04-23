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
                            


                            wx.uploadImage({
                                localId: v, // 需要上传的图片的本地ID，由chooseImage接口获得
                                isShowProgressTips: 1, // 默认为1，显示进度提示
                                success: function (res1) {
                                    var serverId = res1.serverId; // 返回图片的服务器端ID
                                    console.log(res1);
                                }
                            });
                        })


                        $.ajax({
                            url : '/js/getImg?img='+img,     //将上传的照片id发送给后端
                            type: 'get',
                            success:function(d){
                                console.log(d);
                            }
                        });
                        console.log(img);
                    }
                });
            });
        });
        

    </script>
</body>
</html>