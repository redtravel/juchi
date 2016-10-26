<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript" src="public/js/region.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		if($(':radio[name=pay_id]').length>0){
			validator.requiredRadio('pay_id', '请选择支付方式');
		}
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> 新增订单 >> 支付方式 </span><span class="r"><a href="order/index" class="return">返回列表</a></span></div>
	<div class="blank5"></div>
	<div id="product_list">
		<?php include 'order_product.php' ?>
	</div>
	<?php print form_open('order/proc_payment',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('order_id'=>$order->order_id,'act'=>$act));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td class="item_title">使用余额:</td>
				<td class="item_input">
					<?php print form_input('balance_amount','','class="textbox"'); ?>
					<?php print form_button('balance_submit','支付','onclick="pay_balance();"'); ?>
					可用余额： <?php print fix_price($user->user_money); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">使用现金券:</td>
				<td class="item_input">
					<?php if ($voucher_payment): ?>
						<?php printf('已支付现金券 %s ,抵用 %.2f 元', $voucher_payment->payment_account, $voucher_payment->payment_money) ?>
						<a href="javascript:remove_voucher()">[ 移除 ]</a>
					<?php else: ?>
						<select name="available_voucher" onchange="choice_voucher();">
							<option value="">可用现金券</option>
							<?php foreach ($voucher_list as $voucher): ?>
								<option value="<?php print $voucher->voucher_sn ?>"><?php print $voucher->voucher_sn.' '.$voucher->voucher_name ?></option>
							<?php endforeach ?>
						</select>
						<?php print form_input('voucher_sn','','class="textbox"'); ?>
						<?php print form_button('voucher_submit','支付','onclick="pay_voucher();"'); ?>
					<?php endif ?>
				</td>
			</tr>			
			<?php if ($order->order_amount): ?>
			<tr>
				<td class="item_title">支付方式:</td>
				<td class="item_input">
					<?php foreach ($pay_list as $pay): ?>
						<label>
						<?php print form_radio('pay_id', $pay->pay_id, $order->pay_id == $pay->pay_id); ?>
						<?php print $pay->pay_name; ?>
						</label>
					<?php endforeach ?>
				</td>
			</tr>
			<?php else: ?>
                            <?php if ($voucher_payment): ?>
                            <input type="hidden" name="pay_id" value="1">
                            <?php else: ?>
                            <input type="hidden" name="pay_id" value="5">
                            <?php endif ?>
			<?php endif ?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit('mysubmit','下一步','class="am-btn am-btn-primary"') ?>
					<?php print form_button('mycancel','取消','class="am-btn am-btn-primary" onclick="location.href=base_url+\'order/info/'.$order->order_id.'\';"'); ?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>