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
				url:'/user/points',
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
		<a class="now">积分查询</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="tip_top">
			<div class="bold">当前积分:</div>
			<div class="cred_b"><?php echo $user->pay_points ?>分</div>
			<a class="btn_g_147" href="/user/exchange_voucher.html" target="_blank">使用积分兑换现金券</a>
		</div>
		<div class="switch_block" id="listdiv" style="margin-top:15px;">
			<?php endif; ?>
			<div class="switch_block_title">
				<ul>
					<li <?php if ($order_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>最近动态</li>
					<li <?php if ($order_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>已获积分</li>
					<li <?php if ($order_status == 3): ?>class="sel"<?php else: ?>onclick="filter_result(3,1);return false;"<?php endif;?>>已用积分</li>
				</ul>
			</div>
			<div class="switch_block_content">
				<table width="748" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th width="15%">时间</th>
						<th width="20%">类型</th>
						<th width="15%">数量</th>
						<th width="20%">积分余额</th>
						<th width="30%">备注</th>
					</tr>
					<?php if (!empty($points_list)):
					foreach ($points_list as $points): ?>
					<tr>
						<td><?php echo $points->format_create_date ?></td>
						<td><?php echo $points->change_code_value ?></td>
						<td class="red"><?php echo $points->pay_points ?>分</td>
						<td><?php echo $points->cur_surplus ?>分</td>
						<td><?php echo $points->change_desc ?></td>
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