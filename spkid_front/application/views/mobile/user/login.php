<?php include APPPATH . "views/mobile/header.php"; ?>

<div class="view view-main signin2" data-page="login">
    <div class="content-block">
         <div class="hu-fanhuis"><a href="/" class="hongme-ico external"></a></div>
	 <div class="hu-logos"><img src="<?php echo static_style_url('mobile/img/yy-logo.png');?>"></div>
	 
	 <div class="logins-anniu">
	      <a href="/user/signin/register-step1" class="button button-big external zhuchen-baise">注册演示站</a>
	      <a href="#" class="button button-big hu-denglu" onclick="checkLogin('<?=$back_url?>');">登陆演示站</a>
	 </div>
	 
	 <span class="hu-cyzh">常用账号登录:</span>
	 <div class="hu-changyong">
	       <a href="/user/qq_login" title="QQ登录" class="qq_login external hu-QQ"></a>    
	       <a href="/user/xinlang_login" title="新浪微博" class="sina_login external hu-sina-weibo"></a>
	 </div>
    </div>
    
</div>
<?php
include APPPATH . "views/mobile/common/footer-js.php";
 ?>
<?php include APPPATH."views/mobile/footer.php"; ?>
