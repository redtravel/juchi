<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
<script type="text/javascript">
function login_focus(el)
{
	var username = el;

	if(username.value=='Email地址或手机号码' )
	{
		username.value='';
	}
	//username.className='normal_focus';

}

function login_blur(el)
{
	var username = el;
	if(username.value=='')
	{
		//username.value=defaultStr;
		//username.className='normal';
	}
}

function KeyDown(is_float)
{
	if(window.ActiveXObject)
	{
		if (event.keyCode == 13)
	    {
        	check_login_form();
	    }
	}
}

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
						$('input[type=button][name=submitlogin]').attr("disabled", "");
					}
				}
			});

		return false;
}

function check_register_form()
{
		var agreement = $('input[type=checkbox][name=agreement]:checked').val();
		if(agreement != 1)
		{
			agreement = 0;
		}
		var email = $('input[type=text][name=r_email]').val();
        var user_name = $('input[type=text][name=r_user_name]').val();
        var password = $('input[type=password][name=r_password]').val();
        var c_password = $('input[type=password][name=r_cpassword]').val();

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
        }

        if(err_msg == '' && isEmail(email) == false)
        {
        	if( /^ *$/.test(email) || !/^ *(\d){11} *$/.test(email) )
        	{
            	err_msg = '请正确输入Email地址或者手机号码';
            	obj = $('input[type=text][name=r_email]');
        	}
        }

		if(err_msg == '' && $.trim(user_name) =='')
		{
           	err_msg = "请输入昵称";
           	obj = $('input[type=text][name=r_user_name]');
       	}

       	if(err_msg == '' && user_name_len > 20)
        {
           	err_msg = '您的昵称过长，不能超过20个字符';
        	obj = $('input[type=text][name=user_name]');
        }

       	if(err_msg == '' && $.trim(password) =='')
       	{
       		err_msg = "请输入密码";
       		obj = $('input[type=password][name=r_password]');
	    }

	    if(err_msg == '')
	    {
	    	if (!re.test(password)){
	    		err_msg = "无效的密码,只允许6-12位的字母或者数字组成的密码";
	    		obj = $('input[type=password][name=r_password]');
	    	}
	    	if(password.length < 6 || password.length > 12)
	    	{
	    		err_msg = "无效的密码,只允许6-12位的字母或者数字组成的密码";
	    		obj = $('input[type=password][name=r_password]');
	    	}
	    }

	    if(err_msg == '' && $.trim(c_password) =='')
	    {
       		err_msg = "请输入确认密码";
       		obj = $('input[type=password][name=r_cpassword]');
	    }

	    if(err_msg == '' && $.trim(password) != $.trim(c_password))
	    {
       		err_msg = "两次输入密码不一致";
       		obj = $('input[type=password][name=r_cpassword]');
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
				data:{is_ajax:true,email:email,user_name:user_name,password:password,rnd:new Date().getTime()},
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

					}else{
						$('#r_message').show();
						$('#r_message').html(result.message);
						$('input[type=button][name=submitregister]').attr("disabled", "");
					}
				}
			});

		return false;
}

function referToUsername(el)
{
	$('input[type=text][name=r_user_name]').val(el.value.split('@')[0]);
}

function isEmail(email)
{
  var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
  return reg1.test( email );
}

function get_password()
{
	var user_name = $('input[type=text][name=user_name]').val();
	if($.trim(user_name) == '' || user_name == 'Email地址或手机号码')
	{
		alert('请填写你注册的邮箱或者手机');
		return false;
	}
	$.ajax({
				url:'/user/find_password',
				data:{is_ajax:true,user_name:user_name,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
					}else{
						alert(result.msg);
					}
				}
			});
	return false;
}
</script>
<div id="content">
   <div class="login_line">您所在的位置：<a href="/" class="c33">首 页</a> > 用户登录</div>

   <div class="songjin_top"></div>

   <div class="<?php if (!$is_register): ?>login l<?php else: ?>reg r<?php endif; ?>">
     <h2 class="f16b"><s class="s_l"></s>用户登录<s class="sr"></s></h2>
     <div class="login_c">
     <table width="320" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="80" height="40" align="center" class="f14">用户账号：</td>
    <td width="240"><input type="text" name="user_name" class="t_w223_c99" onkeydown="javascript:KeyDown();" onFocus="javascript:login_focus(this)" onBlur="javascript:login_blur(this);" value="Email地址或手机号码" /></td>
  </tr>
  <tr>
    <td height="40" align="center" class="f14">用户密码：</td>
    <td><input type="password" name="password" onkeydown="javascript:KeyDown();" class="t_w223_c99" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
    <div class="no_wr"><input name="remember" type="checkbox" value="1" /> 记住我的登录状态&nbsp;&nbsp;<a href="#" onclick="get_password();return false;" class="fgreen"><strong>忘记密码?</strong></a></div>
    <div class="wr_tip" id="message"><span id="message_inner"></span></div>
</td>
    </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td><input type="button" name="submitlogin" onclick="check_login_form();" class="btn_login" value="登录" /><a href="#this" class="btn_alipay">支付宝登录</a></td>
  </tr>
</table>
<div class="oth_login">
    你还可以通过以下合作网站登录：
      <div class="oth_login_c">
      <s class="s_ocl"></s>
      <ul>
        <li class="login_alipay"><a href="#this" class="fgreen">支付宝登录</a></li>
        <li class="login_happy"><a href="#this" class="fgreen">QQ登录</a></li>
        <li class="login_sina"><a href="#this" class="fgreen">新浪微博登录</a></li>
      </ul>
      <s class="s_ocr"></s>
    </div>
</div>
     </div>
   </div>

   <div class="<?php if (!$is_register): ?>reg r<?php else: ?>login l<?php endif; ?>">
    <h2 class="f16b"><s class="s_l"></s>用户注册<s class="sr"></s></h2>
    <div class="reg_c">
    <table width="320" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="80" height="40" align="center" class="f14">注册账号：</td>
    <td><input type="text" class="t_w223_c99" name="r_email" onFocus="javascript:login_focus(this)" onBlur="javascript:login_blur(this);" onkeyup="javascript:referToUsername(this);" value="Email地址或手机号码" /></td>
  </tr>
  <tr>
    <td height="40" align="center" class="f14">昵 称：</td>
    <td><input type="text" name="r_user_name" class="t_w223_c99" /></td>
  </tr>
    <tr>
    <td height="40" align="center" class="f14">设置密码：</td>
    <td><input type="password" name="r_password" class="t_w223_c99" /></td>
  </tr>
    <tr>
    <td height="40" align="center" class="f14">重复密码：</td>
    <td><input type="password" name="r_cpassword" class="t_w223_c99" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    
    <td>
    <div class="no_wr"><input name="agreement" type="checkbox" value = "1" checked="checked" /> 同意服务条款&nbsp;(<a href="article-42.html" class="fgreen" target="_blank"> 查看详细服务条款 </a>)</div>
    <div id="r_message" class="wr_tip"><span id="r_message_inner"></span></div>    
</td>
    </tr>
  <tr>
    <td height="48">&nbsp;</td>
    <td valign="middle"><input type="button" name="submitregister" onclick="check_register_form();" class="btn_reg" value="注册" /></td>
  </tr>
</table>
    </div>
   </div>

  <div class="cl"></div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>