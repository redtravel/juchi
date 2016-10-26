<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv=Content-Type content="text/html;charset=utf-8">
<title>演示站首页</title>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css" media="all">
<link href="<?php echo static_style_url('pc/css/common.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/main.css?v=version')?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/search.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/home.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/bootstrap.min.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/comm_tool.js?v=version')?>" type="text/javascript"></script>
</head>

<body>
<!--nav start-->
<div class="nav-wrap min-wrapper">
    <div class="nav2 clearfix">
          <a href="/" class="logo"><img src="<?php echo static_style_url('pc/images/logo2.png')?>"></a>
          <?php if(!isset($page_type)): ?>
          <div id="navContentBox" class="nav-contentbox" style="right: 320px;"><img src="<?php echo static_style_url('pc/images/cart-img'.((isset($page) && $page == 'success')? '2' : '1').'.png')?>" class="order-flow"></div>
          <?php endif; ?>
          <div class="nav-right2">
               
              <div class="nav-lb">
                   <div class="nav-users">
                        <a class="nav-user-icon" href="/user/index.html"><img src="<?php echo static_style_url('mobile/touxiang/'.$user->user_advar)?>"></a>
                        <span class="nav-username"><?=$user->user_name?></span>
                   </div>
            </div>
          </div>
    </div>  
<!--header end-->
</div>