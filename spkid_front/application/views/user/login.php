<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>登录</title>
<link href="<?php echo static_style_url('pc/css/tank.css?v=version')?>" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
</head>
<body>
<div id="wrap-login">
    <div class="login-box">
         <div class="login-main">
              <div class="logo"><a href="/"><img src="<?php echo static_style_url('mobile/img/yy-logo.png')?>" width="200" alt="logo" /></a></div>
              <div class="form-wrapper clearfix"> 
               <form method="post" action="#" name="loginForm">
                    <p class="error"></p>
                    <div class="loginUser Logincom">
                         <label for="username" class="userLogo loginplic"></label>
		  				 <input class="loginInput" name="username"  placeholder="请输入手机号码" >
                    </div>
                    <p class="error"></p>
                    <div class="loginUser Logincom">
                         <label for="username" class="passWord loginplic"></label>
		  				 <input class="loginInput" name="password" id="password"  placeholder="请输入密码" type="password" >
                    </div>
                    
                    <div class="Logincom checkgroup">
                         <input type="checkbox" id="checkbox-1-1" class="regular-checkbox" /><label for="checkbox-1-1"></label><span>下次自动登录</span>
                         <div class="forget-mm"><a href="/user/forgot">忘记密码？</a></div>
                    </div>
                    
                    <div class="Logincom clearfix">
                           <button class="btn btn-default disabled" type="submit">登录</button>
                           <p class="fr">还没账号?<a href="/user/signin">注册</a></p>
                     </div>
              </form>
              <div class="horizontal"><span>可以使用以下方式登录</span></div>
              <div class="other">
                  <a href="/user/qq_login" class="qq"></a>                  
                  <a href="/user/weixin_login" class="weixin"></a>		  
                  <a href="/user/alipay_login" class="alipay"></a>                 
                  <a href="/user/xinlang_login" class="sina"></a>
              </div>
              
              
           </div>
     </div>     
  </div>
</div>

<script>
//$('p.error').hide();
$('#password').on('input propertychange', function(){
    var username = $('input[name="username"]'), psw = $('#password');
    if (0 < username.length && 0 < psw.length){
        $('button.disabled').removeClass('disabled').removeAttr('disabled');        
    }
})
var username = $('input[name="username"]');
username.blur(function(){
    if ('' == username.val()){
        username.parent().prev().text('请输入账号');
    } else {
        username.parent().prev().text('');
    }
})
$('form[name="loginForm"]').on('submit', function(e){
    e.preventDefault();
    var psw = $('#password');    
        
    if ('' == psw.val()){
        psw.parent().prev().text('请输入密码');
        $('button.disabled').attr('disabled', 'disabled');
    } else {
        $('button.disabled').removeClass('disabled').removeAttr('disabled');
        $.ajax({url:'/user/proc_login', data:$(this).serialize(), method:'POST', dataType:'json', success:function(data){
            if (1 == data.error){
                $('input[name='+data.name+']').parent().prev().text(data.message);
            }else if(0 == data.error) {
                if( !data.back_url ) data.back_url ='/';
                location.href = data.back_url;
            }
        }
        })
    }
    //alert(username.val()+' '+psw.val());
    return false;
})
</script>
</body>
</html>
