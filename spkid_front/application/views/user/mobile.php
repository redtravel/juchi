<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<style type="text/css">
.ucenter_main input{height:22px;line-height:22px;margin-right:8px;*display:block;*float:left}
.ucenter_main .btn_g_75{height:26px;}
</style>
<script type="text/javascript">
$(function(){

});

function send_mobile_code(el)
{
	var mobile = document.getElementById('mobile').value;
	if ($.trim(mobile) =='')
	{
		alert('请输入正确的手机号');
		return false;
	}
	if( /^ *(\d){11} *$/.test(mobile) == false)
	{
		alert('请输入正确的手机号');
		return false;
	}
	<?php if(!empty($user->mobile)):  ?>
	$('#mobile').attr('readonly',true);
	<?php endif; ?>
	el.blur();
	$('#btn_send_code').attr('disabled',"disabled");
	$('#btn_send_code').attr('class',"");
	$.ajax({
				url:'/user/send_mobile_code',
				data:{is_ajax:true,mobile:mobile,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
					}else{
					    alert(result.msg);
					    return false;
					}
					document.getElementById('btn_send_code').disabled=false;
					document.getElementById('btn_send_code').value="获取验证码";
					<?php if(!empty($user->mobile)):  ?>
					document.getElementById('mobile').readOnly=false;
					<?php endif; ?>
					time_send(el, 20);
				}
			});
	return false;
}

function time_send(el, s, str)
{
	if ( typeof el == 'string' ) el = document.getElementById(el);
	if ( typeof str == 'undefined' ) str='再发一次';
	if( parseInt(s) -1 >0)
	{
		el.value=s+'秒后重新发送';
		el.disabled=true;
		//el.style.fontWeight='bold';
		hl_timeout=setTimeout("time_send('"+el.id+"',"+(s-1)+",'"+str+"');", 1000 );
	}else{
		el.value = str;
		el.disabled=false;
	}
}

function bind_mobile()
{
	var mobile = document.getElementById('mobile').value;
	var mobile_code = document.getElementById('mobile_code').value;
	if ($.trim(mobile) =='')
	{
		alert('请输入正确的手机号');
		return false;
	}
	if( /^ *(\d){11} *$/.test(mobile) == false)
	{
		alert('请输入正确的手机号');
		return false;
	}
	if ($.trim(mobile_code) =='')
	{
		alert('请输入正确的验证码');
		return false;
	}
	$.ajax({
				url:'/user/bind_mobile',
				data:{is_ajax:true,mobile:mobile,mobile_code:mobile_code,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
						window.location.href = '/user/profile';
					}else{
						alert(result.msg);
					}
				}
			});
}
</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
		>
		<a href="/user">会员中心</a>
		>
		<a href="/user/profile">编辑个人资料</a>
		>
		<a class="now">绑定手机</a>
	</div>
	<div class="ucenter_left">
	 <?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="list_block" id="listdiv">
		<?php endif; ?>
		<h2>绑定手机</h2>
		<div class="list_block_content">
			<table width="738" border="0" cellspacing="3" cellpadding="0">
				<tr>
					<td width="40%" align="right">手机号码：</td>
					<td colspan="2"><?php if (!empty($user->mobile)): ?><span  class="c_o"><?php echo $user->mobile ?></span><input type="hidden" name="mobile" id="mobile" value="<?php echo $user->mobile ?>" /><?php else: ?><input type="text" name="mobile" id="mobile" style="width:161px;" /><?php endif; ?></td>
				</tr>
				<tr>
					<td align="right">验证码：</td>
					<td width="12%"><input type="text" name="mobile_code" id="mobile_code" style="width:80px;" /> </td>
					<td width="48%"><input id="btn_send_code" type="button" onclick="send_mobile_code(this);" value="获取验证码" class="btn_g_75"></td>
				</tr>
				<tr>
					<td align="right">&nbsp;</td>
					<td><input class="btn_g_75" type="button" style="border: 0px solid;" onclick="bind_mobile();" name="submit" value="绑定手机"></td>
					<td><input class="btn_g_75" type="button" style="border: 0px solid;" onclick="location.href='/user/profile'" name="fanhui" value="返回"></td>
				</tr>
			</table>
		</div>
		<?php if($full_page): ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>