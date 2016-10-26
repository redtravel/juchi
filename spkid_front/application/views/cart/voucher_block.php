<?php if (empty($payment['voucher'])): ?>
<table width="880" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100">使用现金劵抵扣</td>
		<td width="780"><a href="/user/exchange_voucher.html" target="_blank"><img src="<?php print static_style_url('img/shop_process/coupons.png'); ?>" width="220" height="27" /></a></td>
	</tr>
	<tr>
		<td>使用新的现金劵</td>
		<td><input type="text" name="voucher_sn" class="t_w150" value="请输入您的现金劵号码" onclick="this.select()" onblur="if(!this.value) this.value='请输入您的现金劵号码'" />&nbsp;<input onclick="pay_voucher(null);" type="submit" value="使用" class="btn_sub" /></td>
	</tr>
</table>
<?php if ($voucher_list): ?>
<div class="coupon_block">
	<table width="700" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th width="90">劵号</th>
			<th width="90">金额</th>
			<th width="300">说明</th>
			<th width="150">有效期</th>
			<th width="90">操作</th>
		</tr>
		<?php foreach ($voucher_list as $v): ?>
		<tr>
			<td align="center"><?php print $v->voucher_sn ?></td>
			<td align="center" class="cred"><?php print $v->voucher_amount; ?></td>
			<td align="center"><?php print $v->voucher_name; ?></td>
			<td align="center"><?php print $v->end_date; ?></td>
			<td align="center"><input onclick="pay_voucher('<?php print $v->voucher_sn ?>')" type="submit" value="使用" class="btn_sub" /></td>
		</tr>
		<?php endforeach ?>
	</table>
</div>
<?php endif ?>
<?php else: ?>
<table width="880" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="880">
			您已使用现金券 <?php print $payment['voucher']->voucher_sn ?> 抵扣 <?php print $payment['voucher']->payment_amount ?> 元 <input onclick="unpay_voucher();" type="submit" value="取消" class="btn_sub" />
		</td>
	</tr>
</table>
<?php endif ?>