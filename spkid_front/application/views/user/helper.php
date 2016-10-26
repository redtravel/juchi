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
		<a class="now">帮助中心</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<!--帮助中心-->
		<div class="list_block" id="listdiv">
			<h2>帮助中心</h2>
			<div class="helpList">
				<div id="helpMain">
					<dl class="helpMain">
						<dt>购物指南</dt>
						<dd><a href="/article/info/2">正品保证</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/3">积分说明</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/4">发票制度</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/5">会员制度</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/7">购买流程</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/10">积分换券及使用说明</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/11">订单查询</a></dd>
					</dl>
					<dl class="helpMain">
						<dt>配送方式</dt>
						<dd><a href="/article/info/12">送货快递</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/13">配送费用</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/14">配送规范</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/15">商品签收</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/65">免运费标准</a></dd>
					</dl>
					<dl class="helpMain">
						<dt>支付方式</dt>
						<dd><a href="/article/info/16">货到付款</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/17">网上支付</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/21">账户余额支付</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/22">发票制度</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/66">银行转账</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/67">现金券支付</a></dd>
					</dl>
					<dl class="helpMain">
						<dt>售后服务</dt>
						<dd><a href="/article/info/25">退换政策</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/26">退换流程</a></dd>
					</dl>
					<dl class="helpMain">
						<dt>自助服务</dt>
						<dd><a href="/article/info/45">常见问题</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/68">补开发票</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/69">绑定手机/邮箱</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/70">联系客服</a></dd>
						<dd>|</dd>
						<dd><a href="/article/info/71">订单查询</a></dd>
					</dl>
				</div>
			</div>
		</div>

	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>