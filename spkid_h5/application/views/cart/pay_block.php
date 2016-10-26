<script type="text/javascript">
$(function () {
	/*支付信息标签切换*/
	$('#payContents .payfor_bank_online').eq(0).show();
	$('#payTypes li').click(function () {
		var num=$(this).index();
		$('#payTypes li').removeClass('sel');
		$(this).addClass('sel');
		$('#payContents .payfor_bank_online').hide();
		$('#payContents .payfor_bank_online').eq(num).show();
	});
});
</script>

<table width="880" border="0" cellspacing="0" cellpadding="0">
    <!-- 使用账户余额 开始 -->
    <tr>
	<td style="padding-left:15px">
    <li>
	<label>
	    <?php print form_checkbox('use_balance', 1, $cart_summary['balance'], 'onclick="check_use_balance();"'); ?>
	    <span class="paymen_pic ablack">余额支付</span>
	</label>
	<input name="balance" type="text" class="t_w150" value="<?php print number_format($cart_summary['balance'], 2, '.', ''); ?>" onblur="check_balance();" />
	&nbsp;&nbsp;( 可用余额 <font id="user_money" class="cred_b"><?php print number_format($user->user_money, 2, '.', ''); ?></font> )
    </li>
</td>
</tr>
	<!-- 使用账户余额 结束 -->

	<!-- 支付方式 开始 -->
	<tr>
		<td>
			<div class="fenge_line"></div>
			<span style="color:red">选择在线支付，享受更低运费！</span>
			<div id="div_pay_bank" class="fContainerBox">
				<!-- 支付方式标签 开始 -->
				<ul id="payTypes" class="payTypes">
					<li class="sel">
						<a hidefocus="" href="javascript:void(0)">银行支付</a>
					</li>
					<li>
						<a hidefocus="" href="javascript:void(0)">支付宝</a>
					</li>
					<li>
						<a hidefocus="" href="javascript:void(0)">货到付款</a>
					</li>
				</ul>
				<!-- 支付方式标签 结束 -->

				<!-- 支付方式标签相应内容 开始 -->
				<div id="payContents" class="payContents">
				    <input type="hidden" name="is_pay_id" id="is_pay_id" value="1">
					<div class="payfor_bank_online">
						<ul>
						    <?php foreach ($alipay_bank_list as $pay): ?>
							<li>
								<input type="radio" name="pay_id" value="<?php print PAY_ID_ALIPAY.'_'.$pay['pay_code'];?>" onclick="set_shipping_fee();" />
							    <img src="<?php print static_style_url($pay['pay_logo']); ?>" alt="<?php print $pay['pay_name']; ?>" />
							</li>
						    <?php endforeach ?>
						</ul>
					</div>
					<div class="payfor_bank_online">
						 <ul>
							<?php foreach ($pay_list as $pay): ?>
							<?php if( $pay->pay_id == PAY_ID_ALIPAY ): ?>
							<li>
								<input type="radio" name="pay_id" value="<?php print $pay->pay_id.'_';?>" onclick="set_shipping_fee();" />
								<img src="<?php print img_url($pay->pay_logo); ?>" alt="<?php print $pay->pay_name; ?>" width="99" height="31"/><span style="position:relative;top:-10px;font-size:12px"><?php print $pay->pay_desc ?></span>
							</li>
							<?php endif; ?>
							<?php endforeach ?>
						</ul>
					</div>
					<div class="payfor_bank_online">
					    <ul>
						<?php foreach ($pay_list as $pay): ?>
						<?php if($pay->pay_id == PAY_ID_COD ){ ?>
						<li>
							<input type="radio" name="pay_id" value="<?php print $pay->pay_id.'_';?>" onclick="set_shipping_fee();" />
							<img src="<?php print img_url($pay->pay_logo); ?>" alt="<?php print $pay->pay_name; ?>" width="99" height="31"/><span style="position:relative;top:-10px;font-size:12px"><?php print $pay->pay_desc ?></span>
						</li>
						<?php } ?>
						<?php endforeach ?>
					    </ul>
					</div>
				</div>
				<!-- 支付方式标签相应内容 结束 -->
			</div>
		</td>
	</tr>
	<!-- 支付方式 结束 -->
</table>