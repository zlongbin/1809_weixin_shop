<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div id="images"></div>
    <img src="" alt="" id="img0">
    <img src="" alt="" id="img1">
    <img src="" alt="" id="img2">
    <button id="img">选择图片</button>
</body>
</html>
<script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script src="/js/jquery/jquery-1.8.3.min.js"></script>
<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$js_config['appId']}}", // 必填，公众号的唯一标识
        timestamp: "{{$js_config['timestamp']}}", // 必填，生成签名的时间戳
        nonceStr: "{{$js_config['nonceStr']}}", // 必填，生成签名的随机串
        signature: "{{$js_config['signature']}}",// 必填，签名
        jsApiList: ['chooseImage','uploadImage','downloadImage','updateAppMessageShareData'] // 必填，需要使用的JS接口列表
    });
    wx.ready(function(){
        $(document).on('click','#img',function(){
            wx.chooseImage({
                count: 3, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    var img = ''
                    $.each(localIds,function(i,v){
                        img += v + ','
                        var none = '#img'+i
                        $(none).attr('src',v)
                        // 上传图片至服务器
                        wx.uploadImage({
                            localId: v, // 需要上传的图片的本地ID，由chooseImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var serverId = res.serverId; // 返回图片的服务器端ID
                                console.log(serverId)
                            }
                        });
                        // 下载图片
                        wx.downloadImage({
                            serverId: serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var localId = res.localId; // 返回图片下载后的本地ID
                                console.log(localId)
                            }
                        });
                    })
                    $.ajax({
                        url: '/js/getImg?img='+img,
                        type:'get',
                        success:function(res){
                            console.log(res)
                        }
                    })
                }
            });
        })
        wx.updateAppMessageShareData({ 
            title: '测试', // 分享标题
            desc: '分享测试', // 分享描述
            link: 'http://1809zhoubinbin.comcto.com/js/test', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: '/images/QQ图片20190107153840.jpg', // 分享图标
            success: function () {
            // 设置成功
            }
        })
    });
</script>