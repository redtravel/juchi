<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv=Content-Type content="text/html;charset=utf-8">

<title><?php echo isset($title) ? $title : '演示站'?></title>
<meta name="Keywords" content="<?php echo isset($keywords) ? $keywords : '';?>">
<meta name="Description" content="<?php echo isset($description) ? $description : '';?>">

<link href="<?php echo static_style_url('pc/css/common.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/main.css?v=version')?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/jquery.cookie.js?v=version')?>"></script>

<script type="text/javascript" src="<?php echo static_style_url('pc/js/jquery-migrate-1.2.1.min.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/search.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/home.js?v=version')?>" type="text/javascript"></script>

<script>
$(function(){
	//给这个menu样式下的li 的a标签 绑定点击事件
	$('.menu li a').bind("click",function(){
		//移除menu样式下所有//
		$('.menu li a').removeClass('active');
		$(this).addClass('active');
	});
});
</script>

<!--[if lte IE 7]>
<div class="goodbye-modal hide"></div>
<div class="goodbye-ie hide" id="goodbyeIE">
<p>您的浏览器太旧啦~为了获得更好的体验，强烈建议您使用以下浏览器：</p>
<ul class="browers clearfix">
<li class="chrome">
<a target="_blank" href="https://www.google.com/intl/en/chrome/browser/"></a>
<span>chrome</span>
</li>
<li class="firefox">
<a target="_blank" href="http://www.firefox.com.cn/download/"></a>
<span>firefox</span>
</li>
<li class="ie9">
<a target="_blank" href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie"></a>
<span>IE9+</span>
</li>
</ul>
<p class="no-tip"><a id="iknow" href="javascript:void(0);">知道啦</a></p>
</div>
<![endif]-->
</head>

<body>
<!--header start-->
<div class="header">
    <div class="nav clearfix">
        <p class="nav-left">
        	
        </p>
        <div class="nav-func">
            
        </div>    
    </div>
</div>
<!--header end-->
<?php if(!isset($index)) $index = 0;$menus = array(array('name' => '首页', 'href' => '/'), array('name' => '全部商品', 'href' => '/category-0-0-0-0-11.html'), array('name' => '全部展品', 'href' => '/brand/lists'), array('name' => '课程表', 'href' => '/index/course.html'), array('name' => '视频', 'href' => '/video.html'));?>
<div class="navtion clearfix">
    <div id="logo"><a href="/" class="logo">演示站</a></div>
    <div class="menu">
        <ul>
          <?php foreach($menus as $key => $m): if ($index == $key):?>
            <li><a class="active" href="<?php echo $m['href']?>"><?php echo $m['name']?></a></li>
          <?php else:?>
            <li><a href="<?php echo $m['href']?>"><?php echo $m['name']?></a></li>
          <?php endif;endforeach?>
        </ul>
    </div>
    <div class="navtion-search">
        <div class="search-form nav-search" style="position:relative">
            <input name="" type="search" class="search-text nav-search-inpu" id="navtion-input" value="">
            <ul style="" class="autocomplete"></ul>
            <input name="" type="submit" class="nav-gg search-btn search_confirm">
        </div>
    </div>
</div>
