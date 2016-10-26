<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv=Content-Type content="text/html;charset=utf-8">
<title>个人中心-演示站</title>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/common.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/main.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/personal.css?v=version')?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/jquery.cookie.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/bootstrap.min.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/search.js?v=version')?>" type="text/javascript"></script>
<script>
  //页面加载完成执行
  $(function(){
    $('.personal-center-left li a').each(function(){
      if($(this).text() == $('.order-details-bt,.page-title').text()){
        $('.personal-center-left li a').removeClass('active');
        $(this).addClass('active');
      }
    })
  });
</script>
</head>

<body>
    <!--nav start-->
    <div class="nav-wrap min-wrapper">
        <div class="nav2 clearfix">
            <a href="/" class="logo"><img src="<?php echo static_style_url('pc/images/logo2.png?v=version');?>" alt="演示站"></a>
            <div class="nav-right2">
                <div class="textbox">
                    <div class="nav-search2">
                        <input name="" type="search" class="nav-search2-input" id="navtion-input">
                        <ul style="" class="autocomplete"></ul>
                        <span class="search-ico search_confirm"></span>
                    </div>
                  </div>
                <div class="nav-lb">
                  <div class="naver-login">
                  <?php if($this->session->userdata('user_id')){ ?>
                            <img src="<?php echo static_style_url('mobile/touxiang/'.$this->session->userdata('advar'))?>" height="28">
                    <a href="#" class="nav-user"><?php echo $this->session->userdata('user_name')?><span class="menu_tips"></span></a> 
        <ul class="menu_items" style="display:none">
        <li><a href="/user/index.html">个人中心</a></li>
        <li><a href="/account/privilege.html">我的优惠</a></li>
        <li><a href="/collect/index.html">我的关注</a></li>
        <li><a href="/user/my_response.html">我的回复(<span id="response_num"></span>)</a></li>
        <li><a href="/user/logout.html">退出</a></li></ul>
	                <?php }else{ ?>
	                    <a href="/user/login" class="nav-user">登录</a>
	                <?php } ?>
                  </div>
                  <a href="/user/order_list" class="nav-order">我的订单</a>
                  <a href="/cart" class="nav-cart">购物车</a>
                </div>
            </div>
        </div>
    </div>
    <!--nav end-->

    <div class="min-personal">
     <div class="home-wrapper">
          <div class="personal-center clearfix">
               <ul class="personal-center-left clearfix">
               <li><a href="/user/order_list">我的订单</a></li>
               <li><a href="/account/privilege.html">我的优惠</a></li>
               <li><a href="/account/integral.html">我的积分</a></li>
               <li><a href="/collect/index.html">我的关注</a></li>
               <li class="split"></li>
               <li><a href="/user/index.html">个人中心</a></li>
               <li><a href="/user/my_liuyan">我的评价</a></li>
               <li><a href="/user/my_discussions">我的讨论</a></li>
               <li><a href="/user/my_response">回复提醒</a></li>
               <li class="split"></li>
               <li><a href="/user/profile.html">个人信息</a></li>
               <li><a href="/user/address.html">收货地址</a></li>
               <!-- <li><a href="#">安全设置</a></li> -->
               </ul>  

