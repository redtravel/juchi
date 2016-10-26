<?php include APPPATH . 'views/common/header.php'?>
<div class="rec-main">
    <div class="common-suc clearfix">
          <div class="fl-left registered-img"><img src="<?php echo static_style_url('pc/images/logo3.png')?>" width="103"></div>
          <div class="fl-left registered-jump">
                <h1>恭喜您注册成功。您获得了<span>500积分</span>的奖励,请前往个人中心完善个人资料，将会继续赠送您500积分</h1>
                <div class="registered-jump-con">您的注册账号为手机号：<?php echo $mobile?>，可用来登录，找回密码，订购产品。<p>页面即将跳转至演示站首页。如果没有自动跳转，<a href="/">请点击这里</a></p></div>
        </div>
   </div>
</div>
<script>
            setTimeout(function(){location.href = '/'},5000);
</script>
<?php include APPPATH . 'views/common/footer.php'?>
