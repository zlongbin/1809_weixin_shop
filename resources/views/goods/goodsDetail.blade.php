<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    {{$goods_detail->name}}----{{$goods_detail->price}}
                </div>
                <div>
                <table border=1>
                    <tr>
                        <td>商品</td>
                        <td>浏览时间</td>
                    </tr>
                    @foreach($history_Info as $k=>$v)
                        <tr>
                            <td>{{$v['name']}}----{{$v['price']}}----{{$v['store']}}</td>
                            <td>{{$v['time']}}</td>
                        </tr>
                    @endforeach
                </table>
                <table border=1>
                    <tr>
                        <td>商品</td>
                        <td>浏览次数</td>
                    </tr>
                    @foreach($sort_Info as $k=>$v)
                        <tr>
                            <td>{{$v['name']}}----{{$v['price']}}----{{$v['store']}}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </table>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$js_config['appId']}}", // 必填，公众号的唯一标识
        timestamp: "{{$js_config['timestamp']}}", // 必填，生成签名的时间戳
        nonceStr: "{{$js_config['nonceStr']}}", // 必填，生成签名的随机串
        signature: "{{$js_config['signature']}}",// 必填，签名
        jsApiList: ['updateAppMessageShareData'] // 必填，需要使用的JS接口列表
    });
    wx.ready(function(){
        wx.updateAppMessageShareData({ 
            title: '测试', // 分享标题
            desc: '分享测试', // 分享描述
            link: 'http://1809zhoubinbin.comcto.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: '/images/QQ图片20190107153840.jpg', // 分享图标
            success: function () {
            // 设置成功
            }
        })
    })
</script>
