<?php
$id = intval($_GET['course_id']);
$res = json_decode(file_get_contents('http://39.107.241.122/api/public/index.php/hsxz/Course/getCourseInfo?id='.$id), true);
if($res && intval($res['status'])){
    $url = $res['data']['video'];
}
//$url = 'http://rs.qubaobei.com/video16118236853256109.mp4';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="Author" content="ycl">
    <meta name="Description" content="看视频">
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="full-screen" content="true" />
    <meta name="screen-orientation" content="portrait" />
    <meta name="x5-fullscreen" content="true" />
    <meta name="360-fullscreen" content="true" />
    <title>自定义视频播放器</title>
    <link href="./public/css/video_2.css" rel="stylesheet">
</head>
<body>
    <div class="video_box">
        <!-- <video id="my_video" src="../api/public/upload/videos/20210126/0fa3d305ee3686dfafc1bfd17af47bff.mp4"></video> -->
        <video id="my_video" autoplay playsinline="true" webkit-playsinline="true" src="<?php echo $url;?>"></video>
        <div class="loading"></div>
        <div class="control">
            <div class="control_com play_state_icon">
                <i class="play_bt_icon"></i>
            </div>
            <div class="control_com progress">
                <div class="progress_show"></div>
                <div class="progress_show_now"></div>      
            </div>
            <div class="control_com play_show_time">
                    <span class="play_show_time_time">00:00</span>/<span class="play_show_time_all">00:00</span>
            </div>
            <div class="control_com play_show_full">
                <i class="play_show_full_icon">
                    
                </i>
            </div>
        </div>
    </div>
<script src="./public/js/jquery.min.js"></script>
<script>
$(function(){
    var video = $('#my_video');
    var videoPlay = $('.play_bt_icon');
    var videoPlayIcon = true;
    var videoScreen = true;
    console.log(video)
    screenRotate();//横屏播放
    //视频准备就绪
    video.on('canplay', function(){
        console.log('就绪')
        //取消加载图
        $('.loading').hide();
        //显示总时长
        $('.play_show_time_all').text(formatTime(video[0].duration));
    });

    //播放实时监控
    video.on('timeupdate', function(){
        //计算进度
        var p = video[0].currentTime/video[0].duration;
        if(p == 1){
            videoPlayIcon = false;
            videoPlay.css({
                'background':'url("./public/images/play_0.png")',
                'background-size': "100% 100%"
            }); 
        }
        var p = p*100 + '%';
        $('.progress_show_now').css({'width':p});
        //实时显示时间
        $('.play_show_time_time').text(formatTime(video[0].currentTime));
    });

    //播放与暂停
    $('.play_state_icon').click(function(){
        playControl();
    });

    //跳播
    $('.progress_show').click(function(e){
        //获取总长度
        var w = this.clientWidth;
        //获取鼠标偏移量
        var c_w = e.offsetX;

        //根据偏移量获取总时间的百分比
        var now_time = video[0].duration * (c_w/w);
        video[0].currentTime = now_time;
    });
    
    $('.play_show_full').click(function(){
        screenRotate();
    });
    function playControl() {
        videoPlayIcon = !videoPlayIcon;
        var tmpStatus = videoPlayIcon ? 1 : 0;
        if(videoPlayIcon){
            video[0].play();
        }else{
            video[0].pause();
        }		
        videoPlay.css({
            'background':'url("./public/images/play_'+tmpStatus+'.png")',
            'background-size': "100% 100%"
        }); 
    }
    function screenRotate(){
        var conW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var conH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

        videoScreen = !videoScreen;
        var tmpStatus = videoScreen ? 1 : 0;
        if(videoScreen){
            // transform 竖屏      
            $('.video_box').css({
                "transform":"rotate(0) translate(0,0)",
                "width":conW+"px",
                "height":"230px",
                //"margin-top":iosTopHe+"px",
                // "border-left":iosTopHe+"px solid #000",
                "transform-origin":"center center",
                "-webkit-transform-origin": "center center"
            });
        }else{
            // transform 横屏
            $('.video_box').css({
                "transform":"rotate(90deg) translate("+((conH-conW)/2)+"px,"+((conH-conW)/2)+"px)",
                "width":conH+"px",
                "height":conW+"px",
                //"margin-top":iosTopHe+"px",
                // "border-left":iosTopHe+"px solid #000",
                "transform-origin":"center center",
                "-webkit-transform-origin": "center center"
            });
        }
        $('.play_show_full_icon').css({
            'background':'url("./public/images/full_'+tmpStatus+'.png")',
            'background-size': "100% 100%"
        }); 
    }
    //时间格式化 参数-秒
    function formatTime(time){
        if(typeof(time)!=="number"){
            return '00:00';
        }

        //获取分钟
        var minute=parseInt(time%(60*60)/60);
        minute = minute<10?'0'+minute:minute;

        //获取秒钟
        var second=Math.ceil(time%60);
        second = second<10?'0'+second:second;

        //获取hour
        var hour=parseInt(time/(60*60));
        if(hour){
            hour = hour<10?'0'+hour:hour;
            return `${hour}:${minute}:${second}`;
        }
        return `${minute}:${second}`;
    }
})
    window.onload=function(){
        var video = document.querySelector('#my_video');
        //开始查找视频，加载开始
        video.onloadstart = function(){
            console.log('加载')
        };
        //视频时长已更改
        video.ondurationchange = function(){
            console.log('时长变更')
        };
        //加载视频元数据
        video.onloadedmetadata = function(){
            console.log('加载元数据')
        };
        //加载当前帧
        video.onloadeddata = function(){
            console.log('无法加载下一帧时')
        };
        //正在下载
        video.onprogress = function(){
            console.log('正在下载')
        };
    }
</script>
</body>
</html>