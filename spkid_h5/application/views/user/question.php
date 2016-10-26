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

		var obj = null;
		var err_msg = '';

		if($.trim(old_password) =='')
		{
            err_msg = "请输入原密码";
            obj = $('input[type=password][name=old_password]');
        }

        if(err_msg == '' && $.trim(new_password) =='')
       	{
       		err_msg = "请输入新密码";
       		obj = $('input[type=password][name=new_password]');
	    }

	    if(err_msg == '')
	    {
	    	if (!re.test(new_password)){
	    		err_msg = "无效的密码,只允许6-16位的字母或数字";
	    		obj = $('input[type=password][name=new_password]');
	    	}
	    	if(new_password.length < 6 || new_password.length > 16)
	    	{
	    		err_msg = "无效的密码,只允许6-16位的字母或数字";
	    		obj = $('input[type=password][name=new_password]');
	    	}
	    }

	    if(err_msg == '' && $.trim(re_password) =='')
	    {
       		err_msg = "请输入确认密码";
       		obj = $('input[type=password][name=re_password]');
	    }

	    if(err_msg == '' && $.trim(new_password) != $.trim(re_password))
	    {
       		err_msg = "确认密码和新密码不一致";
       		obj = $('input[type=password][name=re_password]');
	    }

		if(err_msg != '')
		{
			if(obj)
			{
				obj.css("border-color","red");
			}
			alert(err_msg);
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
		<a class="now">常见问题</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">

		<!--常见问题-->
		<div class="list_block" id="listdiv">
			<h2>常见问题</h2>
			<div class="list_block_content problemList">
				<ul>
					<li><a href="/help-13.html">商品的配送费用是怎么收取的?</a></li>
					<li><a href="/help-26.html">如何办理退换货及退款?</a></li>
					<li><a href="/help-24.html">签收商品时需要注意哪些问题？</a></li>
					<li><a href="/help-16.html">我可以货到付款吗？</a></li>
				</ul>
			</div>
		</div>

	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>