<?php include APPPATH."views/common/header.php"; ?>
	<link href="<?php print static_style_url('css/header.css'); ?>" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
	<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
	<script type="text/javascript" src="<?php print static_style_url('js/forBasic.js'); ?>"></script>
	<script type="text/javascript" src="<?php print static_style_url('js/jquery.lazyload.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php print static_style_url('js/login.js'); ?>"></script>
	<script type="text/javascript">
	    var static_host = '<?php print static_style_url(); ?>';
	    var img_host = '<?php print static_style_url(); ?>';
	    var base_url = '/';
	</script>
    <body>
	<!-- 联合登录开始 -->
	<div id="content">
	    <div id="login_union">
		<p class="title1 font25">欢迎访问<?php print SITE_NAME;?>!</p>
		<p class="title2 font16">为了给您提供更好的服务，保证您购物过程中的权益，我们强烈建议您完善个人账户信息。</p>
		<div class="user_info">
		    <h2>完善个人资料</h2>
		    <div class="reg_c">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="0">
			    <tr>
				<td width="135" height="45" align="center" valign="top" class="f14">
				    <div class="tdTitle">输入手机号/邮箱：</div>
				</td>
				<td valign="top">
				    <input type="text" class="t_w217_c99 gray" name="r_email" id="r_email" onFocus="javascript:register_focus(this,'此账号可直接登录<?php print SITE_NAME;?>');_onFocus(this); $('#r_email_error_r').css('display', 'none');" onBlur="javascript:register_blur(this,'此账号可直接登录<?php print SITE_NAME;?>');_onBlur(this); _valid_user(this.value); check_real_name();" onkeyup="javascript:referToUsername(this);" value="此账号可直接登录<?php print SITE_NAME;?>" maxlength="50" />
				    <span id="r_email_success" class="exactness" style="display:none;"></span>
				    <div class="ts_block_box">
<!--					<div id="r_message_" class="ts_block" style="display:none;"></div>-->
					<div id="r_email_error" class="ts_block" style="display:none;">请输入有效的Email地址或手机号码！</div>
					<div id="r_email_error_r" class="ts_block" style="display:none;">此用户已注册！</div>
					<div id="r_email_s_error" class="ts_block" style="display:none;">您的昵称过短，不能少于3个字符！</div>
					<div id="r_email_l_error" class="ts_block" style="display:none;">您的昵称过长，不能超过12个字符！</div>
				    </div>
				</td>
			    </tr>
			    <tr>
				<td height="45" align="center" valign="top" class="f14">
				    <div class="tdTitle">输入密码：</div>
				</td>
				<td valign="top">
				    <div style="position:relative;">
					<input type="text" id="passwordText" class="t_w217_c99 gray" maxlength="16" value="6-16位字母或者数字" />
					<input style="color: black;" type="password" name="r_password" id="r_password" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99 black" maxlength="16" value="" />
				    </div>
				    <span id="r_password_success" class="exactness" style="display:none;"></span>
				    <div class="ts_block_box">
					<div id="r_password_error" style="display:none;" class="ts_block">无效密码,只允许6-16位的字母或数字！</div>
				    </div>
				</td>
			    </tr>
			    <tr>
				<td height="45" align="center" valign="top" class="f14">
				    <div class="tdTitle">再次输入密码：</div></td>
				<td valign="top">
				    <input type="password" name="r_cpassword" id="r_cpassword" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99" maxlength="16" />
				    <span id="r_cpassword_success" class="exactness" style="display:none;"></span>
				    <div class="ts_block_box">
					<div id="r_cpassword_error" style="display:none;" class="ts_block">两次密码输入不一致！</div>
				    </div>
				</td>
			    </tr>
			    <tr>
				<td height="48">&nbsp;</td>
				<td valign="middle">
				    <div type="button" name="submitregister" onclick="check_comp_info();" class="btn_union_reg" value="注册">确定</div>
				    <div type="button" name="" onclick="javascript:window.location.href='<?= $back_url?>';" class="btn_union_jump" value="跳过">跳过</div>
				</td>
			    </tr>
			</table>
		    </div>
		</div>
	    </div>
	    <div class="cl"></div>
	</div>
	<!-- 联合登录结束 -->
<?php include APPPATH.'views/common/footer.php'; ?>