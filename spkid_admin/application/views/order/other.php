<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript" src="public/js/region.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		return true;
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> <?php print $act=='add'?'新增订单':'编辑订单' ?> >> 其它信息 </span><span class="r"><a href="order/info/<?php print $order->order_id ?>" class="return">返回订单</a></span></div>
	<div class="blank5"></div>
	<div id="product_list">
		<?php include 'order_product.php' ?>
	</div>
	<?php print form_open('order/proc_other',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('order_id'=>$order->order_id,'act'=>$act));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td class="item_title">发票抬头:</td>
				<td class="item_input">
					<?php print form_input('invoice_title',$order->invoice_title,'class="textbox"') ?>
					不填写表示不需要发票
				</td>
			</tr>
			<tr>
				<td class="item_title">发票内容:</td>
				<td class="item_input">
					<?php print form_input('invoice_content',$order->invoice_content,'class="textbox" size="80"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">客户留言:</td>
				<td class="item_input">
					<?php print form_input('user_notice',$order->user_notice,'class="textbox" size="80" readonly') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">客服对客户的留言:</td>
				<td class="item_input">
					<?php print form_input('to_buyer',$order->to_buyer,'class="textbox" size="80"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php if ($act=='add'): ?>
						<input type="button" name="myprev" value="上一步" onclick="location.href=base_url+'order/payment/<?php print $order->order_id;?>?act=add'">
						<?php print form_submit('mysubmit','下一步','class="am-btn am-btn-primary"') ?>					
					<?php else: ?>
						<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"') ?>
					<?php endif ?>
					<input type="button" class="am-btn am-btn-primary" name="mycancel" value="取消" onclick="location.href=base_url+'order/info/<?php print $order->order_id;?>'">
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>