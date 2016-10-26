<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>重置密码</title>
<link href="<?php echo static_style_url('pc/css/tank.css')?>" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
</head>
<body>
<div id="wrap-login">
     <div class="login-box">
          <div class="login-main">
              <div class="logo"><a href="#"><img src="<?php echo static_style_url('mobile/img/yy-logo.png')?>" width="200" alt="logo" /></a></div>
               <div class="find-step01"></div>
               <div class="form-wrapper clearfix">
                      <form method="post" action="#" name="forgotForm">
                      <ul class="register-now forgot-now">
                    <p class="error"></p>
                      <li><label>账号</label><input type="tel" required name="mobile" class="register-Input" placeholder="请输入账号"></li>
                    <p class="error"></p>
<li><label>验证码</label><input type="text" name="authcode" class="register-Input register-yzm" placeholder="请输入验证码">
                      <a href="#" class="yanzhengma captcha" title="刷新"><img src="/user/show_verify"></a><a class="btn fsyzm">发送验证码</a></li>
                      </ul>
<input id="step" name="step" type="hidden" value="step2" />
                      
                      <div class="zhuchen">
                         <button class="btn btn-default disabled" disabled="disabled" type="submit">提交</button>
                         <p class="fr">想起密码?<a href="/user/login">登录</a></p>
                     </div>
                    </form>
               </div>                     
         </div>              
     </div>
</div>
<div id="step3">
    <p class="error"></p>
      <li><label>输入密码</label><input type="password" required name="password" class="register-Input" placeholder="请输入密码"></li>
    <p class="error"></p>
      <li><label>确认密码</label><input type="password" required name="password1" class="register-Input" placeholder="请输入密码"></li>
</div>
<script>
//disabled="disabled" 

$('input[name=authcode]').on('input propertychange', function(){
    //if($('.fsyzm').is(':hidden')){
    if(4 == $(this).val().length){
        $(this).trigger('blur');
    }
    //}
})
$('.captcha').click(function(e){
    e.preventDefault();
    $('.captcha>img').attr('src', '/user/show_verify?v='+Math.random());
})
$('input[name=authcode]').on('blur', function(){
    var self = $(this);
    var p = $(this).parent();
    if ('' != $.trim(self.val())){
    $.get('/user/validate_code', {captcha:self.val()}, function(res){
        if (!res){
            //show mobile code
            self.val('').off('blur');
            self.off('input propertychange');
           //self.on('blur', function(){
                if(0 < $('input[name=mobile]').val().length)
                    $('button.disabled').removeClass('disabled').removeAttr('disabled');
            //});
            p.prev().text('');
            $('.captcha').removeClass('captcha').hide();
            $('.fsyzm').css('display', 'inline-block');
        } else {
            p.prev().text(res);
        }
    })
    }
})
btn = $('.fsyzm');
checked = false;
var retry = 0;
btn.click(function(e){
    e.preventDefault();
    if (checked) return;
    if (0 == retry){
        $.ajax({
        url:'/user/reg_auth',
            async:false,
            dataType:'json',
            data:{mobile:$('input[name=mobile]').val(), is_register:0},
            success:function(data){
                if(data.mobile_check_err){
                    $('input[name=mobile]').parent().prev().text(data.mobile_check_err);
                    btn.html('重新发送');
                } else {
                    btn.addClass('disabled').attr('disabled', 'disabled');
                    checked = true;
                    smscount(60);
                    retry = 1;
                }
            }
    })
    } else {        
        $.getJSON('/user/reg_auth', {retry:1}, function(data){
            if (0 == data.msg_send_result){
                btn.addClass('disabled').attr('disabled', 'disabled');
                checked = true;
                smscount(60);
            } else {
                btn.html('重新发送');
            }
        })

    }
});
$('form').on('submit', function(e){
    e.preventDefault();
    $.ajax({url:'/user/new_password', data:$(this).serialize(), method:'POST', dataType:'json', success:function(data){
        if (false !== data.error){
            $('input[name="'+data.name+'"]').parent().prev().html(data.error);
        } else {
            if ( 'step2' == data.name )
            { 
                $('.find-step01').removeClass('find-step01').addClass('find-step02');
                $('#step').val('step3');
                $('.forgot-now').empty().append($('#step3').html());
            } else if ( 'step3' == data.name ){
                $('.find-step02').removeClass('find-step02').addClass('find-step03');
                $('.zhuchen').empty().html('<a href="/user/login" class="btn btn-default">登录</a>');
                $('.forgot-now').replaceWith('<div class="success">恭喜您密码重置成功！</div>');
            }
        }
    }
    });
    return false;
});
function smscount(n){
        n--;
        if (n<0){
            btn.removeClass('disabled').removeAttr('disabled').html('重新发送');
            checked=false;
        } else { 
            btn.html('重新发送('+n+')');
            setTimeout(function(){smscount(n)},1000);
        }
}
</script>
</body>
</html>
