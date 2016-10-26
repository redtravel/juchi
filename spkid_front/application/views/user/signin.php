<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>立即注册</title>
<link href="<?php echo static_style_url('pc/css/tank.css')?>" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
</head>
<body>
<div id="wrap-login">
     <div class="login-box">
           <div class="login-main">
              <div class="logo"><a href="/"><img src="<?php echo static_style_url('mobile/img/yy-logo.png')?>" width="200" alt="logo" /></a></div>
                 <div class="form-wrapper clearfix">
                      <form method="post" action="#" name="registerForm">
                      <ul class="register-now">
                        <p class="error"></p>
                      <li><label>手机号码</label><input type="tel" required name="mobile" class="register-Input" placeholder="请输入手机号码"></li>
                    <p class="error"></p>
                      <li><label>验证码</label><input type="text" required name="authcode" class="register-Input register-yzm" placeholder="请输入验证码" />
                      <a href="#" class="yanzhengma captcha" title="刷新"><img src="/user/show_verify"></a><a class="btn fsyzm">发送验证码</a></li>
                    <p class="error"></p>
                      <li><label>输入密码</label><input type="password" required name="password" class="register-Input" placeholder="请输入密码"></li>
                    <p class="error"></p>
                      <li><label>确认密码</label><input type="password" required name="password1" class="register-Input" placeholder="请输入密码"></li>
                      </ul>
                       
                     <div class="yuedu"><input type="checkbox" id="checkbox" class="regular-checkbox" /><label for="checkbox"></label><span>我已阅读并接受<a href="/about_us/service">演示站服务条款。</a></span></div>

                     <div class="zhuchen">
                         <button class="btn btn-default disabled" disabled="disabled" type="submit">立即注册</button>
                         <p class="fr">已有账号?<a href="/user/login">登录</a></p>
                     </div>
                    <p class="error"></p>
                      </form>
                    
                 </div><!--form-wrapper -->
           </div>
     </div>
</div>
</body>
<script>
$('input[type!="checkbox"]').focus(function(){
    $(this).parent().prev().text('');
})
$('#checkbox').change(function(){
    if ($(this).is(':checked')){
        $('.error').last().text('');
        if (0 < $('input[name=mobile]').val().length && 0 < $('input[name=authcode]').val().length && 0 < $('input[name=password]').val().length && 0 < $('input[name=password1]').val().length){
        $('button.disabled').removeClass('disabled').removeAttr('disabled');        
        }
    }
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
            p.prev().text('');
            $('.captcha').removeClass('captcha').hide();
            $('.fsyzm').css('display', 'inline-block');
            self.on('input propertychange', function(){
                if(4 == $(this).val().length){
                    if (0 < $('input[name=mobile]').val().length && 0 < $('input[name=authcode]').val().length && 0 < $('input[name=password]').val().length && 0 < $('input[name=password1]').val().length){
                        $('button.disabled').removeClass('disabled').removeAttr('disabled');        
                    }


                }
            })
        } else {
            p.prev().text(res);
        }
    })
    }
})
$('input[name^=password]').on('input propertychange', function(){
    if(4 == $(this).val().length){
        if (0 < $('input[name=mobile]').val().length && 0 < $('input[name=authcode]').val().length && 0 < $('input[name=password]').val().length && 0 < $('input[name=password1]').val().length){
            $('button.disabled').removeClass('disabled').removeAttr('disabled');        
        }
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
            data:{mobile:$('input[name=mobile]').val(), is_register:true},
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
$('form[name="registerForm"]').on('submit', function(e){
    e.preventDefault();
    if (!$('#checkbox').is(':checked')){
        $('.zhuchen').next().text('您还未接受演示站服务条款');
        return false;
    }
    $.ajax({url:'/user/proc_register', data:$(this).serialize(), method:'POST', dataType:'json', success:function(data){
        if ('' != data.error){
            //console.log(data.error);
            $('input[name='+data.name+']').parent().prev().html(data.error);
        } else {
            location.href = '/user/reg_success';
        }
    }
    });
    return false;
})
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
</html>
