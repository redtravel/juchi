<?php echo $expire_time?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="theme-color" content="#2196f3">
    <meta name="Keywords" content="<?php echo isset($keywords) ? $keywords : '';?>">
    <meta name="Description" content="<?php echo isset($description) ? $description : '';?>">
	
    <title><?php echo isset($title) ? $title : '演示站'?></title>
    <link rel="stylesheet" href="<?php echo STATIC_HOST.'/mobile/css/framework7.material.css'?>">
    <link rel="stylesheet" href="<?php echo STATIC_HOST.'/mobile/css/main.css'?>">


<style>
a.hu-tiaoguo{  display: inline-block;
  margin: 15px 15px 0;
  padding: .5em 2em;
  font-size: 26px;
  font-size: 1em;
  text-decoration: none;
  outline: none;
  color: #fff;
  background-color: #2889af; border: none;
  border-radius: 5px;}
a.btn {
  display: inline-block;
  margin: 15px 15px 0;
  padding: .5em 2em;
  font-size: 26px;
  font-size: 1em;
  text-decoration: none;
  outline: none;
  color: #fff;
  background-color: #fe4365;
  border-radius: 5px;
  -webkit-background-clip: padding-box;
  background-clip: padding-box;
  -webkit-box-shadow: 0 0 0 -2px #cff09e, 0 0 0 -1px #fe4365;
  box-shadow: 0 0 0 -2px #cff09e, 0 0 0 -1px #fe4365;
  border: none;
  -webkit-transition: -webkit-box-shadow .3s;
  transition: box-shadow .3s;
}
a.btn:hover, a.btn:focus {
  -webkit-box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #ff0364;
  box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #ff0364;
  -webkit-transition-timing-function: cubic-bezier(0.6, 4, 0.3, 0.8);
  transition-timing-function: cubic-bezier(0.6, 4, 0.3, 0.8);
  -webkit-animation: gelatine 0.5s 1;
  animation: gelatine 0.5s 1;
}

a.btn-secondary {
  background: #c8c8a9;
  -webkit-box-shadow: 0 0 0 -2px #cff09e, 0 0 0 -1px #c8c8a9;
  box-shadow: 0 0 0 -2px #cff09e, 0 0 0 -1px #c8c8a9;
}
a.btn-secondary:hover {
  -webkit-box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #bebe99;
  box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #bebe99;
}

a.btn:active,
a.btn-secondary:active {
  background: #4ecdc4;
  -webkit-transition-duration: 0;
  transition-duration: 0;
  -webkit-box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #3ac7bd;
  box-shadow: 0 0 0 2px #cff09e, 0 0 0 4px #3ac7bd;
}

/**
 * $keyframes \ gelatine 
 **/
@keyframes gelatine {
  from, to {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
  }

  25% {
    -webkit-transform: scale(0.9, 1.1);
    transform: scale(0.9, 1.1);
  }

  50% {
    -webkit-transform: scale(1.1, 0.9);
    transform: scale(1.1, 0.9);
  }

  75% {
    -webkit-transform: scale(0.95, 1.05);
    transform: scale(0.95, 1.05);
  }

  from, to {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
  }

  25% {
    -webkit-transform: scale(0.9, 1.1);
    transform: scale(0.9, 1.1);
  }

  50% {
    -webkit-transform: scale(1.1, 0.9);
    transform: scale(1.1, 0.9);
  }

  75% {
    -webkit-transform: scale(0.95, 1.05);
    transform: scale(0.95, 1.05);
  }
}
@-webkit-keyframes gelatine {
  from, to {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
  }

  25% {
    -webkit-transform: scale(0.9, 1.1);
    transform: scale(0.9, 1.1);
  }

  50% {
    -webkit-transform: scale(1.1, 0.9);
    transform: scale(1.1, 0.9);
  }

  75% {
    -webkit-transform: scale(0.95, 1.05);
    transform: scale(0.95, 1.05);
  }

  from, to {
    -webkit-transform: scale(1, 1);
    transform: scale(1, 1);
  }

  25% {
    -webkit-transform: scale(0.9, 1.1);
    transform: scale(0.9, 1.1);
  }

  50% {
    -webkit-transform: scale(1.1, 0.9);
    transform: scale(1.1, 0.9);
  }

  75% {
    -webkit-transform: scale(0.95, 1.05);
    transform: scale(0.95, 1.05);
  }
}



</style>


</head>
<body>
<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>
<div class="view view-main">
    <div class="pages">
<div class="page" data-page="index">
<div style="position: absolute; right:0; top:0; height: 44px; width: 100%; text-align: right; z-index:9999" ><a href="/" id="jump" class="hu-tiaoguo">跳过</a></div>
<div class="page-content" style="padding-top:0;cursor:pointer">
      
  <!-- Slider -->
  <div class="swiper-container" style="height:100%">
    <div class="swiper-wrapper">
    <?php
    $count=count($list);
    --$count;
    foreach($list as $index=>$row):
    ?>
    <div class="swiper-slide" style=" background-image: url(<?php echo img_url($row->focus_img)?>); background-size:100% 100%; width: 100%; height: 100%; background-repeat: no-repeat; background-color:#000;">
    <?php if ($index==$count): ?>
    
         <div style="position: absolute; width: 100%; text-align: center; bottom: 50px;">
         <a href="<?php echo $url?>" class="enter-btn  btn"><?php echo $btn_name?></a>
         </div> 
    <?php endif; ?>   
         </div>
    <?php endforeach;?>
    
        

    </div>
    <div class="swiper-pagination"></div>
    


  </div>
</div>

  

</div>
    </div><!-- pages -->
</div>
</div><!-- views -->
<script type="text/javascript" data-cfasync="false" src="<?php echo STATIC_HOST.'/mobile/js/framework7.js'?>"></script>
<script data-cfasync="false">
// @author wangwei
function setCookie(objName,objValue,objHours){//添加cookie
    var str = objName + "=" + escape(objValue);
    if(objHours > 0){//为0时不设定过期时间，浏览器关闭时cookie自动消失
        var date = new Date();
        var ms = objHours*3600*1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString();
    }
    document.cookie = str;
}
var $$ = Dom7;
var swiper=new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        <?php if('v'==$direction):?>
        direction: 'vertical'
        <?php endif;?>
});

/*swiper.on('reachEnd', function(swiper){
    //append button
    if (1==$$('.yywtoolbar-inner').children().length)
    {	 
        var a=$$('.right');
        a.on('click',function(e){
            e.preventDefault();
            setCookie('fp-started',true,24);
            //进入页面
            location.href='/user/signin/register-step1';
        })
            $$('.yywtoolbar-inner').append(a);
    }

})
 */
$$('.enter-btn').on('click',function(e){
    e.preventDefault();
          setCookie('fp-started',true,24);
          //进入页面
          location.href='<?php echo $url?>';
})
$$('#jump').on('click',function(e){
    e.preventDefault();
    setCookie('fp-started',true,24);
    location.href='/';
})

</script>
