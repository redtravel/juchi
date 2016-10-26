<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js') ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
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
				url:'/user/order',
				data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#listdiv').html(result.content);
						order_status = result.order_status;
						$(".scroll").jCarouselLite({
							btnPrev: ".prev",//下翻按钮
							btnNext: ".next",//上翻按钮
							auto:6000,//多少毫秒移动一次
							speed:800,//移动速度
							visible:3,//显示多少个li，可以是小数
							scroll:1//每次移动多少个li，可以设置为1.5等小数
						});
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
		<a class="now">订单管理</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="switch_block" id="listdiv">
			<?php endif; ?>
			<div class="order_point" style="display: none">抱歉，该订单不能作废，请联系客服！</div>
			<div class="switch_block_title">
				<ul>
					<li <?php if ($order_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>所有订单</li>
					<li <?php if ($order_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>待审核</li>
					<li <?php if ($order_status == 3): ?>class="sel"<?php else: ?>onclick="filter_result(3,1);return false;"<?php endif;?>>处理中</li>
					<li <?php if ($order_status == 4): ?>class="sel"<?php else: ?>onclick="filter_result(4,1);return false;"<?php endif;?>>已成交</li>
					<li <?php if ($order_status == 5): ?>class="sel"<?php else: ?>onclick="filter_result(5,1);return false;"<?php endif;?>>已作废</li>
				</ul>
			</div>
			<div class="switch_block_content">
				<table width="748" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th width="10%">时间</th>
						<th width="16%">订单编号</th>
						<th width="14%">订单状态</th>
						<th width="14%">支付状态</th>
						<th width="13%">总价</th>
						<th width="13%">待付金额</th>
						<th width="20%">操作</th>
					</tr>
					<?php if (!empty($order_list)):
					 foreach ($order_list as $order): ?>
					<tr>
						<td><?php echo $order->format_create_date ?></td>
						<td><?php echo $order->order_sn ?></td>
						<td>
                                                    <?php if (isset($order->return_number) && $order->return_number > 0) {
                                                        echo "<font style=\"color:red\" >退货</font>";
                                                    } else if (isset($order->change_number) && $order->change_number > 0) {
                                                        echo "<font style=\"color:red\" >换货</font>";
                                                    } else {
                                                        echo order_status($order);
                                                    } ?>
                                                </td>
						<td><?php echo pay_status($order)  ?></td>
						<td class="red"><?php echo $order->total_fee ?></td>
						<td class="red"><?php echo ($order->order_amount > 0)?  ''.$order->order_amount:'0.00' ?></td>
						<td>
							<a class="btn_g_52" href="/order/info/<?php print $order->order_sn ?>" target="_blank">查看</a>
							<?php if ($order->can_pay): ?>
							<a class="btn_r_56 external" href="/order/pay/<?php print $order->order_id ?>" target="_blank">付款</a>
							<?php endif ?>
							<?php if ( (empty($order->lock_admin) && $order->lock_admin == 0) && $order->order_status == 0 ): ?>
							<!--<a class="btn_no" href="/order/invalid/<?php print $order->order_sn ?>" >作废</a>-->
							<?php endif ?>
                                                        <?php if ($order->apply_return): ?>
							<a  class="tuidan" hidefocus href="/user/apply_return/<?php print $order->order_id ?>" target="_blank">在线申请退货</a>
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="7">
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