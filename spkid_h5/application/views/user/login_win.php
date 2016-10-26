<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/login.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
<div id="content">
	<!--<div class="songjin_top"></div>-->
	<!-- <div class="login_line">您所在的位置：<a href="/" class="c33">首 页</a> > 用户登录</div> -->
	
	<div class="registerLogin">
		<div class="<?php if (!$is_register): ?>loginMain<?php else: ?>hide<?php endif; ?>">
			<div class="<?php if (!$is_register): ?>login l<?php else: ?>reg r hide<?php endif; ?>">
				<h2>
					<s class="s_l"></s>
					用户登录
					<s class="sr"></s></h2>
				<div class="login_c">
					<table width="370" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="80" height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">用户账号：</div>
							</td>
							<td width="240">
								<input type="text" name="user_name" class="t_w217_c99 gray" onkeydown="javascript:KeyDown();" onFocus="javascript:login_focus(this)" onBlur="javascript:login_blur(this);" value="Email地址或手机号码" />
								<div class="ts_block_box">
									<div id="l_email" class="ts_block_email" style="display:none;">推荐，QQ邮箱注册</div>
									<div id="l_email_error" class="ts_block" style="display:none;">帐号只能由字母数字及下划线组成！</div>
                                                                        <div class="wr_tip" id="message"><span id="message_inner"></span></div>
								</div>
							</td>
						</tr>
						<tr>
							<td height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">用户密码：</div>
							</td>
							<td>
								<input type="password" name="password" onkeydown="javascript:KeyDown();" class="t_w217_c99" />
								<div class="ts_block_box">
									<div id="r_password_error" class="ts_block" style="display:none;">密码错误，请重新输入！</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="no_wr">
									<label for="rememberMe">
										<input name="remember" type="checkbox" value="1" id="rememberMe" />
										记住我的登录状态
									</label>
									<a href="#" onclick="get_password();return false;" class="fgreen"><strong>忘记密码?</strong></a>
								</div>
							</td>
						</tr>
						<tr>
							<td height="40">&nbsp;</td>
							<td>
								<input type="button" name="submitlogin" onclick="check_login_form();" class="btn_login" value="登录" />
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="registerMainRight">
				<h2 class="has_account">还不是妈咪树会员？</h2>
				<a href="/user/register" class="register_now" title="10秒快速注册" hidefocus="">10秒快速注册</a>
				<ul class="four_info">
						<li><a href="javascript:void(0)" class="zhengpin_login" title="100%正品保证"></a></li>
						<li class="mr0"><a href="javascript:void(0)" class="mian_login" title="品牌商直发"></a></li>
						<li><a href="javascript:void(0)" class="seven_login" title="7天无条件退款"></a></li>
						<li class="mr0"><a href="javascript:void(0)" class="huo_login" title="闪电发货"></a></li>
				</ul>
			</div>

			<div class="otherLogin">
				<h3 class="other_way">使用其他方式登录<?php print SITE_NAME;?></h3>
				<ul class="union_login">
					<li><a href="/user/xinlang_login" title="新浪微博" class="sina_login"></a></li>
					<li><a href="/user/alipay_login" title="支付宝" class="alipay_login"></a></li>
					<li><a href="#" onclick="toQzoneLogin();return false;" title="QQ登录" class="qq_login"></a></li>
                                        <li><a href="/user/weixin_login" title="微信" class="alipay_login">微信登陆</a></li>
				</ul>
			</div>

		</div>
			
		<div class="<?php if (!$is_register): ?>hide<?php else: ?>registerMain<?php endif; ?>">
			<div class="<?php if (!$is_register): ?>reg r hide<?php else: ?>login l<?php endif; ?>">
				<h2>
					<s class="s_l"></s>
					注册<?php print SITE_NAME;?>
					<s class="sr"></s>
				</h2>
				<div class="reg_c">
					<table width="370" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="80" height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">注册账号：</div>
							</td>
							<td valign="top">
								<input type="text" class="t_w217_c99 gray" name="r_email" id="r_email" onFocus="javascript:register_focus(this);_onFocus(this); $('#r_email_error_r').css('display', 'none');" onBlur="javascript:register_blur(this);_onBlur(this); _valid_user(this.value); check_real_name();" onkeyup="javascript:referToUsername(this);" value="Email地址或手机号码" maxlength="50" />
								<span id="r_email_success" class="exactness" style="display:none;"></span>
								<div class="ts_block_box">
									<div id="r_email_error" class="ts_block" style="display:none;">请输入有效的Email地址或手机号码！</div>
									<div id="r_email_error_r" class="ts_block" style="display:none;">此用户已注册！</div>
									<div id="r_email_s_error" class="ts_block" style="display:none;">您的昵称过短，不能少于3个字符！</div>
									<div id="r_email_l_error" class="ts_block" style="display:none;">您的昵称过长，不能超过12个字符！</div>
								</div>
							</td>
						</tr>
						<tr>
							<td height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">设置密码：</div>
							</td>
							<td valign="top">
								<div style="position:relative; z-index: 1">
									<input type="text" id="passwordText" class="t_w217_c99 gray" maxlength="16" value="6-16位字母或者数字" />
									<input type="password" name="r_password" id="r_password" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99 black" maxlength="16" value="" />
								</div>
								<span id="r_password_success" class="exactness" style="display:none;"></span>
								<div class="ts_block_box">
									<div id="r_password_error" style="display:none;" class="ts_block">无效的密码,只允许6-16位的字母或数字！</div>
								</div>
							</td>
						</tr>
						<tr>
							<td height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">重复密码：</div></td>
							<td valign="top">
								<input type="password" name="r_cpassword" id="r_cpassword" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99" maxlength="16" />
								<span id="r_cpassword_success" class="exactness" style="display:none;"></span>
								<div class="ts_block_box">
									<div id="r_cpassword_error" style="display:none;" class="ts_block">两次密码输入不一致！</div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="80" height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">验证码：</div>
							</td>
							<td valign="top">
								<input type="text" class="t_w90_c99" name="r_captcha" id="r_captcha" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" onkeyup="javascript:referToUsername(this);" value="" maxlength="50" />
								<img id="verify_code" src="/user/show_verify/" style="cursor:hand;" onclick="this.src=img_src + Math.random();" alt="点击更换图片" />
								<span id="r_captcha_success" class="exactness" style="display:none;"></span>
								<div class="ts_block_box">
									<div id="r_captcha_error" class="ts_block" style="display:none;">验证码错误，请重新输入！</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="no_wr">
									<label for="tongyi">
										<input name="agreement" type="checkbox" value = "1" checked="checked" id="tongyi" />
										同意服务条款
									</label>
									(<a href="/help-80.html" target="_blank">查看详细服务条款</a>)</div>
								<div id="r_message" class="wr_tip"><span id="r_message_inner"></span></div>
							</td>
						</tr>
						<tr>
							<td height="48">&nbsp;</td>
							<td valign="middle">
								<div type="button" name="submitregister" onclick="check_register_form();" class="registerBtn" value="注册">注册并登录</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="loginMainRight">
				<h2 class="has_account">已有<?php print SITE_NAME;?>帐号</h2>
				<a href="user/login" class="btn_login" title="立即登录" hidefocus="">立即登录</a>
                
				<h3 class="other_way">使用其他方式登录<?php print SITE_NAME;?></h3>
				<ul class="union_login">
					<li><a href="/user/xinlang_login" title="新浪微博" class="sina_login"></a></li>
					<li><a href="/user/alipay_login" title="支付宝" class="alipay_login"></a></li>
					<li><a href="#" onclick="toQzoneLogin();return false;" title="QQ登录" class="qq_login"></a></li>
				</ul>
                <ul class="four_info">
						<li><a href="javascript:void(0)" class="zhengpin_login" title="100%正品保证"></a></li>
						<li class="mr0"><a href="javascript:void(0)" class="mian_login" title="品牌商直发"></a></li>
						<li><a href="javascript:void(0)" class="seven_login" title="7天无条件退款"></a></li>
						<li class="mr0"><a href="javascript:void(0)" class="huo_login" title="闪电发货"></a></li>
				</ul>
			</div>
		</div>
			
	</div>
		
	<div class="cl"></div>
</div>
<div id="mobile_area" style="display:none">
<div style="margin-top:90px;margin-left:50px;" >
<font color="red">您今天的短信密码找回已经发送三次!</font><br /><br />
输入右边的验证码：<input type="text" id="yanzhen" style="width:60px;" value="" />&nbsp;&nbsp;
<img id="validation" src="/user/show_verify/" style="cursor:hand;" onclick="this.src=img_src + Math.random();" alt="点击更换图片" />
</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<script>
function check_login_form()
{
	var remember = $('input[type=checkbox][name=remember]:checked').val();
	if(remember != 1){
		remember = 0;
	}
	var user_name = $('input[type=text][name=user_name]').val();
	var password = $('input[type=password][name=password]').val();

	$('input[type=text][name=user_name]').css("border-color","");
	$('input[type=password][name=password]').css("border-color","");

	var err_msg = '';
	if($.trim(user_name) =='' || $.trim(user_name) == 'Email地址或手机号码'){
		err_msg = "请输入用户账号";
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && $.trim(password) ==''){
		err_msg = "请输入用户密码";
		obj = $('input[type=password][name=password]');
	}

	if(err_msg != ''){
		if(obj)
		{
		obj.css("border-color","red");
		}
		$('#message').show();
		$('#message_inner').html(err_msg);
		return false;
	}
	else
	{
		$('#message').hide();
		$('#message_inner').html('');
	}

	$('input[type=button][name=submitlogin]').attr("disabled", "disabled");


		$.ajax({
		url:'/user/proc_login',
		data:{is_ajax:true,user_name:user_name,password:password,
			remember:remember,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
			if(result.back_url != '')
			{
				window.location.href = result.back_url;
			}else
			{
				window.location.href = '/user';
			}

			}else
			{
			$('#message').show();
			$('#message').html(result.message);
			$('input[type=button][name=submitlogin]').removeAttr("disabled");
			}
		},
                error: function(err){
                    try {
                        if (null != document.referrer) {
                            location.href = document.referrer
                        }
                    }catch (e){
                        location.reload();
                    }
                    
                    
                }
		});

	return false;
}
    function check_real_name() {
    $("#r_email_l_error").css("display", "none");
    $("#r_email_s_error").css("display", "none");
        var email = $('input[type=text][name=r_email]').val();
        var user_name = isMobile(email) ? email : email.substr(0, email.indexOf("@") > 12 ? 12 : email.indexOf("@")); // $('input[type=text][name=r_user_name]').val();
	var user_name_len = user_name.replace(/[^\x00-\xff]/g, "**").length;
        var err_msg = '';
        if(err_msg == '' && user_name_len < 3)
	{
		err_msg = '您的昵称过短，不能少于3个字符';
		obj = $('input[type=text][name=r_email]');
                _error("r_email_s");
                $("#r_email_success").css("display", "none");
                $("#r_email_error").css("display", "");
	}

	if(err_msg == '' && user_name_len > 12)
	{
		err_msg = '您的昵称过长，不能超过12个字符';
		obj = $('input[type=text][name=r_email]');
                _error("r_email_l");
                $("#r_email_success").css("display", "none");
                $("#r_email_error").css("display", "");
	}
    }
function check_register_form()
{
	var agreement = $('input[type=checkbox][name=agreement]:checked').val();
	if(agreement != 1)
	{
		agreement = 0;
	}
	var email = $('input[type=text][name=r_email]').val();
	var user_name = isMobile(email) ? email : email.substr(0, email.indexOf("@") > 12 ? 12 : email.indexOf("@")); // $('input[type=text][name=r_user_name]').val();
	var password = $('input[type=password][name=r_password]').val();
	var c_password = $('input[type=password][name=r_cpassword]').val();
        var captcha = $("#r_captcha").val();
	$('input[type=text][name=r_email]').css("border-color","");
	$('input[type=text][name=r_user_name]').css("border-color","");
	$('input[type=password][name=r_password]').css("border-color","");
	$('input[type=password][name=r_cpassword]').css("border-color","");

	var user_name_len = user_name.replace(/[^\x00-\xff]/g, "**").length;
	var obj = null;
	var regu = "^[0-9a-zA-Z]+$";
	var re = new RegExp(regu);
	var err_msg = '';

	if($.trim(email) =='' || $.trim(email) == 'Email地址或手机号码')
	{
		err_msg = "请输入注册账号";
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
	}

	if(err_msg == '' && isEmail(email) == false)
	{
		if( /^ *$/.test(email) || !/^ *(\d){11} *$/.test(email) )
		{
		err_msg = '请正确输入Email地址或者手机号码';
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
		}
	}

	if(err_msg == '' && $.trim(user_name) =='')
	{
		err_msg = "请输入昵称";
		obj = $('input[type=text][name=r_user_name]');
	}

	if(err_msg == '' && user_name_len < 3)
	{
		err_msg = '您的昵称过短，不能少于3个字符';
		obj = $('input[type=text][name=r_email]');
                _error("r_email_s");
                $("#r_email_success").css("display", "none");
                $("#r_email_error").css("display", "");
	}

	if(err_msg == '' && user_name_len > 12)
	{
		err_msg = '您的昵称过长，不能超过12个字符';
		obj = $('input[type=text][name=r_email]');
                _error("r_email_l");
                $("#r_email_success").css("display", "none");
                $("#r_email_error").css("display", "");
	}

	if(err_msg == '' && $.trim(password) =='')
	{
		err_msg = "请输入密码";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
	}

	if(err_msg == '')
	{
		if (!re.test(password)){
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
		if(password.length < 6 || password.length > 16)
		{
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
	}

	if(err_msg == '' && $.trim(c_password) =='')
	{
		err_msg = "请输入确认密码";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

	if(err_msg == '' && $.trim(password) != $.trim(c_password))
	{
		err_msg = "两次输入密码不一致";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

        if (err_msg == '' && (captcha.length != 4 || !captcha.match(/^[0-9a-zA-Z]+$/))) {
            err_msg = "请输入验证码";
            obj = $('input[type=password][name=r_captcha]');
            _error("r_captcha");
        }
        
	if(err_msg == '' && agreement != 1)
	{
		err_msg = "您没有接受协议";
	}

	if(err_msg != '')
	{
		if(obj)
		{
		obj.css("border-color","red");
		}
		$('#r_message').show();
		$('#r_message_inner').html(err_msg);
		return false;
	}
	else
	{
		$('#r_message').hide();
		$('#r_message_inner').html('');
	}

	$('input[type=button][name=submitregister]').attr("disabled", "disabled");


		$.ajax({
		url:'/user/proc_register',
		data:{is_ajax:true,email:email,user_name:user_name,password:password,rnd:new Date().getTime(),captcha:captcha},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
			if(result.back_url != '')
			{
				window.location.href = result.back_url;
			}else
			{
				window.location.href = '/index';
			}

			}else{
			$('#r_message').show();
			$('#r_message').html(result.message);
			$('input[type=button][name=submitregister]').removeAttr("disabled");
			}
		},
                error: function(err){
                    location.reload();
                }
		});

	return false;
}
function _valid_user(user_name) {
    $.ajax({
        url: "/user/valid_user",
        type: "POST",
        dataType: "json",
        data:{user:user_name},
        success: function(json) {
            if (json.error > 0) {
                $("#r_email_success").css("display", "none");
                $('#r_email_error_r').css('display', '');
            }
        }
    });
}
</script>