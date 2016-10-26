<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
$(function(){
	//点击验证激活倒计时
	$('#mobileV').click(function () {
		$(this).hide();
		$('#countdown').css({'display':'inline-block'});
		var time = 20;	//倒计时秒数
		var text = '重新获取'+'('+time+')';
			$('#countdown a').text(text);
		setTimeout(countdown(time-1),0);
	});
});

//倒计时js
function countdown(time) {
	var time = time;
	var start = setInterval(function () {
			if (time<1) {
				$('#mobileV').css({'display':'inline-block'});
				$('#countdown').hide();
				clearInterval(start);
			};
			var text = '重新获取'+'('+time+')';
			time -= 1;
			$('#countdown a').text(text);
		},1000);
}

function clear_input()
{
	$('input[type=password][name=old_password]').val('');
	$('input[type=password][name=new_password]').val('');
	$('input[type=password][name=re_password]').val('');
}

function check_add_form()
{
		var old_password = $('input[type=password][name=old_password]').val();
        var new_password = $('input[type=password][name=new_password]').val();
        var re_password = $('input[type=password][name=re_password]').val();

        var regu = "^[0-9a-zA-Z]+$";
		var re = new RegExp(regu);

		$('input[type=password][name=old_password]').css("border-color","");
        $('input[type=password][name=new_password]').css("border-color","");
        $('input[type=password][name=re_password]').css("border-color","");
        $("#old_password_err").css("display", "none");
        $("#new_password_err").css("display", "none");
        $("#re_password_err").css("display", "none");

		var obj = null;
		var err_msg = '';

		if($.trim(old_password) =='')
		{
            err_msg = "请输入原密码";
            obj = $('#old_password_err');
        }

        if(err_msg == '' && $.trim(new_password) =='')
       	{
       		err_msg = "请输入新密码";
       		obj = $('#new_password_err');
	    }

	    if(err_msg == '')
	    {
	    	if (!re.test(new_password)){
	    		err_msg = "无效的密码,只允许6-16位的字母或数字";
	    		obj = $('#new_password_err');
	    	}
	    	if(new_password.length < 6 || new_password.length > 16)
	    	{
	    		err_msg = "无效的密码,只允许6-16位的字母或数字";
	    		obj = $('#new_password_err');
	    	}
	    }

	    if(err_msg == '' && $.trim(re_password) =='')
	    {
       		err_msg = "请输入确认密码";
       		obj = $('#re_password_err');
	    }

	    if(err_msg == '' && $.trim(new_password) != $.trim(re_password))
	    {
       		err_msg = "确认密码和新密码不一致";
       		obj = $('#re_password_err');
	    }

		if(err_msg != '')
		{
			if(obj)
			{
				obj.css("border-color","red");
                                obj.html(err_msg);
                                obj.css("display", "");
			} else {
                            alert(err_msg);
                        }
			return false;
		}

		$('input[type=button][name=add_submit]').attr("disabled", "disabled");

			$.ajax({
				url:'/user/password_edit',
				data:{is_ajax:true,old_password:old_password,new_password:new_password,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
						window.location.href = '/user';
					}else{
						alert(result.msg);
					}
					$('input[type=button][name=add_submit]').attr("disabled", "");
				}
			});

		return false;
}

</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
		>
		<a href="/user">会员中心</a>
		>
		<a class="now">修改密码</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<!--安全中心-->
		<div class="list_block" id="listdiv">
			<?php endif; ?>
			<h2>修改密码</h2>
			<div class="list_block_content passwordList">
				<table width="748" border="0" cellspacing="3" cellpadding="0" id="passwordEdit">
					<tr>
						<td>&nbsp;</td>
						<td align="right" style="vertical-align:top;height:40px;"><span class="font14b">会员账号</span></td>
						<td align="left" style="vertical-align:top;height:40px;"><span class="font14b">:&nbsp;<?php echo isset($user->email) ? $user->email : $user->mobile; ?></span></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th width="7%">
							<div class="exactness_big_icon"></div>
							<!-- <div class="warning_big_icon"></div> -->
						</th>
						<th width="13%" align="right"><span class="font14b">密码修改&nbsp;</span></th>
						<th width="61%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<span class="font12"><? if($is_union == 1){echo "由于您是联合登陆用户，故不支持密码修改。";}else{?>经常的更换您的账号密码，并且不要和其他账号共用同一个密码<? }?></span></th>
						<th width="18%" align="left"><!-- <a name="add_submit" onclick="check_add_form(0);" class="btn_g_75">密码修改</a> --></th>
					</tr>
					<? if($is_union == 0){?>
					<tr>
						<td>&nbsp;</td>
						<td align="right">
							<span class="red l marginL10">*</span>原密码 ：
						</td>
						<td>
							<input type="password" name="old_password" id="old_password" maxlength="16" />
                                                        <span class="red" id="old_password_err" style="display: none;">新密码不能为空!</span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right">
							<span class="red l marginL10">*</span>新密码 ：
						</td>
						<td>
							<input type="password" name="new_password" id="new_password" maxlength="16" />
							<span class="red" id="new_password_err" style="display: none;">新密码不能为空!</span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right">
							<span class="red l marginL10">*</span>确认密码 ：
						</td>
						<td>
							<input type="password" name="re_password" id="re_password" maxlength="16" />
							<span class="red" id="re_password_err" style="display: none;">新密码不能为空!</span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td>
							<div>
								<!-- <a type="button" onclick="clear_input();" class="btn_g_75">清空</a> -->
								<a name="add_submit" onclick="check_add_form(0);" class="btn_g_75">提交</a>
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
					<? } ?>
				</table>
<!-- come soon
				<table width="748" border="0" cellspacing="3" cellpadding="0" id="mailBind">
					<tr>
						<th width="7%">
							<div class="warning_big_icon"></div>
						</th>
						<th width="13%" align="right"><span class="font14b">绑定邮箱&nbsp;</span></th>
						<th width="61%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<span class="font12">经常的更换您的账号密码，并且不要和其他账号共用同一个密码</span></th>
						<th width="18%" align="left"><a name="add_submit" onclick="check_add_form(0);" class="btn_g_75">立即绑定</a></th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right">
							<span class="red l marginL10">*</span>绑定邮箱 ：
						</td>
						<td>
							<input type="text" name="mail_bind" id="email" />
							<a onclick="valid_email();return false;" class="btn_g_122">去邮箱验证</a>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
				</table>

				<table width="748" border="0" cellspacing="3" cellpadding="0" id="mobileBind">
					<tr>
						<th width="7%">
							<div class="warning_big_icon"></div>
						</th>
						<th width="13%" align="right"><span class="font14b">绑定手机&nbsp;</span></th>
						<th width="61%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<span class="font12">经常的更换您的账号密码，并且不要和其他账号共用同一个密码</span></th>
						<th width="18%" align="left"><a name="add_submit" onclick="check_add_form(0);" class="btn_g_75">立即绑定</a></th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right">
							<span class="red l marginL10">*</span>绑定手机 ：
						</td>
						<td colspan="2">
							<input type="text" name="mail_bind" id="" />
							<a onclick="" class="btn_g_122" id="mobileV">免费获取短信验证码</a>
							<div class="inblock" id="countdown">
								<a class="btn_gray_93">重新获取</a>
								<font>已发送,1分钟后可重新获取</font>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
				</table>
 -->
			</div>
			<?php if($full_page): ?>
		</div>


	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>