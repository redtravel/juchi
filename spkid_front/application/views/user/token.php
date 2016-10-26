<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
//现金券输入框的鼠标触发事件
$(function () {
	$('#token_add').focus(function () {
		if ($(this).attr('value')=='请输入现金券号码') {
			$(this).attr('value','');
			$(this).css({'color':'#000'});
		};
	});
	$('#token_add').blur(function () {
		if ($(this).attr('value')=='') {
			$(this).attr('value','请输入现金券号码');
			$(this).removeAttr('style');
		};
	});
});

var order_status = '<?php echo $order_status ?>';
var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';
function filter_result(status,page)
{
	if (status == 0)
	{
		status = order_status;
	}
	page_count = order_page_count;

	if (page == 0)
	{
		page = order_page;
	}
	if(page < 1)
	{
		page = 1;
	}
	if(page > page_count)
	{
		page = page_count;
	}

			$.ajax({
				url:'/user/token',
				data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#listdiv').html(result.content);
						order_status = result.order_status;
                                                order_page_count = result.page_count
					}
				}
			});

		return false;
}

function userAddToken()
{
	var msg = '';
	var token_new = document.getElementById('token_add').value;
	if (token_new.length == 0)
	{
		msg += '请输入有效的现金券号码' + '\n';
	}

	 if (msg.length > 0)
	 {
		 alert(msg);
		 return false;
	 }
	 else
	 {
		$.ajax({
				url:'/user/token_add',
				data:{is_ajax:true,voucher_sn:token_new,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
						filter_result(1,1);
					} else
					{
						$('.tip_top .errorInfo').show();
						//alert(result.msg);
					}
				}
			});
		return true;
	 }
}
</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
                >
                <a href="/user">会员中心</a>
                >
		<a class="now">我的现金劵</a>
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="tip_top">
			<div class="bold">激活新的现金券:</div>
			<div>
				<div><input class="inputText c99 w200" type="text" name="token_add" id="token_add" value="请输入现金券号码" /></div>
				<div><input class="btn_g_75 inputBtn marginR10" type="button" onclick="userAddToken();" value="激活" class="u_sbtn" /></div>
				<!-- <font class="c99"></font> -->
			</div>
			<div class="errorInfo" style="display:none;margin-left:108px;">请输入正确的现金券号码!</div>
		</div>
		<div class="switch_block" id="listdiv">
			<?php endif; ?>
			<div class="switch_block_title">
				<ul>
					<li <?php if ($order_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>可使用</li>
					<li <?php if ($order_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>已使用</li>
					<li <?php if ($order_status == 3): ?>class="sel"<?php else: ?>onclick="filter_result(3,1);return false;"<?php endif;?>>已过期</li>
				</ul>
			</div>
			<div class="switch_block_content">
				<table width="748" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th width="18%">现金券号码</th>
						<th width="8%">金额</th>
						<th width="20%">有效期</th>
						<th width="30%">使用范围</th>
						<th width="8%">状态</th>
					</tr>
					<?php if (!empty($token_list)):
					foreach ($token_list as $token): ?>
					<tr>
						<td><?php echo $token->voucher_sn ?></td>
						<td class="red"><?php echo $token->voucher_amount ?>元</td>
						<td><?php echo $token->start_end ?></td>
						<td><?php echo $token->display_name ?></td>
						<td><?php echo $token->voucher_status ?></td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="6">
							<div class="switch_block_page ablack">
							<?php include(APPPATH.'views/user/page.php') ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<?php if($full_page): ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>