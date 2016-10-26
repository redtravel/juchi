<?php include APPPATH."views/mobile/header.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.js?v=version')?>"></script>

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/common.js?v=version')?>"></script>

<div class="views">
<div class="view view-main" data-page="index">
     <div class="pages">
<!-- 抽奖详情页 -->
<div class="page" data-page="login">
    <!--navbar start-->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"><a class="link back history-back" href="javascript:void(0)"> <i class="icon back"></i></a></div>
            <div class="center">一键登录（免注册）</div>
        </div>
    </div>
    <!--navbar end-->
    <div class="page-content article-bg" style="padding-top:50px;">
         <div class="sign-in">
              <div class="sign-in-pic"><img src="<?php echo static_style_url('mobile/img/logo2.0.png');?>" /></div>
              <div class="sing-in-lb">
                   <div class="item-username">
                        <div class="order-details-rr item-sign"><input class="txt-input txt-username" name="username" type="text" placeholder="请输入您的手机号" ></div>
                   </div>

                   <div class="item-username password-div" style="display:none">
                        <div class="order-details-rr item-sign"><input class="txt-input txt-username" name="password" type="password" placeholder="请输入密码" ></div>
                   </div>

                   <div class="order-details-rr item-sign item-sign2 yazhengma-div"><input class="txt-input txt-username" name="authcode" type="text" placeholder="请输入验证码" >
                   <a href="javascript:void(0)" onclick="sendYanzhengma();" class="yazhengma send-code">获取验证码</a>
                   
                   </div>
             </div>
             <div class="err-msg">会话过期，请刷新页面重试</div>
             <div class="order-details-rr click-login">
                  <div class="sing-in-tit">点击登录，表示您同意<a href="javascript:void(0)">《演示站服务协议》</a></div>
                  <div class="sign-in-bt">
                       <a class="btn-login btn-disabled loginbtn" href="javascript:void(0);">登录</a>
                       <a href="javascript:void(0);" class="using-account switchLogin" onclick="switchLoginType();">会员直接登录</a>
                  </div>
             </div>
         
         
         </div> 
         
         
  </div>
</div>


<!-- 抽奖页面 -->
<!--page-->
     </div>
	</div>

</div>






<?php include APPPATH . "views/mobile/common/footer-js.php";?>

<?php include APPPATH."views/mobile/footer.php"; ?>
