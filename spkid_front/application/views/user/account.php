<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
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
				url:'/user/account',
				data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#listdiv').html(result.content);
						order_status = result.order_status;
					}
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
		<a class="now">帐户查询</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="tip_top">
			<div class="bold">当前账户余额:</div>
			<div class="cred_b"><?php echo $user->user_money ?>元</div>
			<!-- come soon <a href="/" class="btn_g_75">我要充值</a> -->
		</div>
		<div class="tipList">
			<dl>
				<dt>充值会员帐号：</dt>
				<dd><?php echo $user->short_user_name ?></dd>
			</dl>
			<dl>
				<dt>充值金额：</dt>
				<dd>
					<input type="text" id="recharge_amount" class="h23" />
                                        <a href="javascript:void(0);" onclick="if($('#recharge_amount').val().replace(/(^\s*)|(\s*$)/g, '') == '' || isNaN($('#recharge_amount').val()) || parseFloat($('#recharge_amount').val()) < 1) {alert('请输入有效的金额！'); return false;} window.open('/user/recharge/' + $('#recharge_amount').val().replace(/(^\s*)|(\s*$)/g, ''));" class="btn_g_122">支付宝在线充值</a>
				</dd>
			</dl>
		</div>
		<div class="switch_block" id="listdiv">
			<?php endif; ?>
			<div class="switch_block_title">
				<ul>
					<li <?php if ($order_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>最近交易</li>
					<li <?php if ($order_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>充值明细</li>
					<li <?php if ($order_status == 3): ?>class="sel"<?php else: ?>onclick="filter_result(3,1);return false;"<?php endif;?>>消费明细</li>
					<li <?php if ($order_status == 4): ?>class="sel"<?php else: ?>onclick="filter_result(4,1);return false;"<?php endif;?>>余额调节</li>
				</ul>
			</div>
			<div class="switch_block_content">
				<table width="748" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th width="14%">时间</th>
						<th width="18%">类型</th>
						<th width="18%">金额</th>
						<th width="18%">账户余额</th>
						<th width="32%">备注</th>
						<?php if ($order_status == 1): ?>
						<?php endif;?>
					</tr>
					<?php if (!empty($account_list)):
					 foreach ($account_list as $account): ?>
					<tr>
						<td><?php echo $account->format_create_date ?></td>
						<td><?php echo $account->change_code_value ?></td>
						<td><?php echo $account->user_money ?>元</td>
						<td class="red"><?php echo $account->cur_surplus ?>元</td>
						<?php if ($order_status == 1): ?>
						<?php endif;?>
						<td><?php echo $account->change_desc ?></td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="5">
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