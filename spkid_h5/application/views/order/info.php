<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js') ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />

<div id="content">

	<div class="now_pos">
		<a href="/">首 页</a>
		>
		<a href="/user">会员中心</a>
		>
		<a href="/user/order">我的订单</a>
		>
		<a class="now">订单详情</a>
                <!-- come soon 
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>

	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="topInfo">
			<font class="bold" style="margin-right:10px;">订单详情</font>
			<font class="bold red" style="margin-right:15px;">编号：<?php print $order->order_sn ?></font>
			订单状态：<font class="red" style="margin-right:30px;"><?php print order_status($order); ?></font>
			支付状态：<font class="red" style="margin-right:40px;"><?php print pay_status($order); ?></font>
			
			<?php if ( (empty($order->lock_admin) && $order->lock_admin == 0) && $order->order_status == 0 ): ?>
				<a class="btn_g_52" href="/order/invalid/<?php print $order->order_id ?>" >作废</a>
			<?php endif ?>
		</div>
		<div class="mid_block bgWiter">
			<font class="bold" style="margin-left:20px;line-height:34px;">订单信息</font>
			<div class="order_address">
				<dl>
					<dt>收货人：</dt>
					<dd><?php print $order->consignee ?></dd>
				</dl>
				<dl>
					<dt>配送方式：</dt>
					<dd>
					<?php print $order->shipping_name?$order->shipping_name:'快递'; ?>
					<?php if ($order->shipping_status): ?>
					【
					已发货 
					<?php print $order->invoice_no?"运单号：{$order->invoice_no}":'' ?>
					<?php print $order->shipping_desc; ?>
					】
					<?php endif ?>
					</dd>
				</dl>
				<dl>
					<dt>配送地址：</dt>
					<dd>
					<?php if ($order->shipping_id==SHIPPING_ID_CAC): ?>
					自提
					<?php else: ?>
					<?php print "{$order->province_name} {$order->city_name} {$order->district_name} {$order->address} 邮编：{$order->zipcode} 电话：{$order->mobile} {$order->tel}" ?>
					<?php endif ?>
					</dd>
				</dl>
				<?php if ($order->unpay_price>0): ?>
				<dl>
					<dt>支付方式：</dt>
					<dd>
					<img class="l" src="<?php print $pay_logo; ?>"/>
					<?php if ($order->unpay_price>0 && $order->is_online && !$order->order_status): ?>
					<a href="/order/pay/<?php print $order->order_id ?>.html" class="btn_impay external" target="_blank">立即支付</a>
					<?php endif ?> 
					</dd>
				</dl>
				<?php endif ?>
				<dl>
					<dt>送货时间：</dt>
					<dd>
					<?php print $order->best_time ?$order->best_time:''; ?>
					</dd>
				</dl>
				<!--dl>
					<dt>索要发票：</dt>
					<dd>
					<?php print $order->invoice_title?$order->invoice_title:'不开具发票'; ?>
					</dd>
				</dl-->
				<!-- 
				<?php if ($order->user_notice): ?>
				<dl>
					<td height="30" align="right">客户留言：</td>
					<td colspan="3">
					<?php print $order->user_notice; ?>
					</td>
				</dl>
				<?php endif ?>
				<?php if ($order->to_buyer): ?>
				<dl>
					<td height="30">客服回复：</td>
					<td colspan="3" style="color:red;">
					<?php print $order->to_buyer; ?>
					</td>
				</dl>
				<?php endif ?>
				-->
			</div>
			<font class="bold" style="margin-left:20px;line-height:34px;">未出库商品</font>
			<div class="orderList switch_block_content">
				
				<table width="748" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFfff";>
					<tr>
						<th width="12%" height="35">商品图</th>
						<th width="24%">商品名称</th>
						<th width="12%">商品款号</th>
						<th width="8%">颜色</th>
						<th width="13%">尺码 </th>
						<th width="13%">数量</th>
						<th width="8%">单价</th>
						<th width="10%">小计</th>
					</tr>
					<?php foreach ($product_list as $p): ?>
					<tr>
						<td>
							<a href="<?php print "/product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank"><img src="<?php print img_url($p->img_url); ?>.85x85.jpg" /></a>
						</td>
						<td>
							<a href="<?php print "/product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank"><?php print $p->product_name ?><?php if($p->discount_type==4) print '<font style="color:red">[赠品]</font>'; ?>
							</a></td>
						<td><?php print $p->product_sn ?></td>
						<td><?php print $p->color_name ?></td>
						<td><?php print $p->size_name ?></td>
						<td><?php print $p->product_num ?></td>
						<td><?php print $p->product_price ?></td>
						<td><?php print $p->total_price ?></td>
					</tr>
					<?php endforeach ?>
					<?php foreach ($package_list as $pkg): ?>
					<tr>
						<td rowspan="<?php print count($pkg->product_list) ?>"><a href="/<?php print "package-{$pkg->package_id}.html" ?>" target="_blank"><img src="<?php print static_style_url('data/package/'.$pkg->package_image); ?>" width="30" height="40" /></a></td>
						<td rowspan="<?php print count($pkg->product_list) ?>"><a href="/<?php print "package-{$pkg->package_id}.html" ?>" target="_blank"><?php print $pkg->package_name ?></a></td>
						<td><a href="/<?php print "product-{$pkg->product_list[0]->product_id}-{$pkg->product_list[0]->color_id}.html" ?>" target="_blank"><?php print $pkg->product_list[0]->product_sn ?></a></td>
						<td><?php print $pkg->product_list[0]->color_name ?></td>
						<td><?php print $pkg->product_list[0]->size_name ?></td>
						<td rowspan="<?php print count($pkg->product_list) ?>">1</td>
						<td class="fred" rowspan="<?php print count($pkg->product_list) ?>"><?php print number_format($pkg->package_real_amount,2,'.',''); ?></td>
						<td class="fred" rowspan="<?php print count($pkg->product_list) ?>"><?php print number_format($pkg->package_real_amount,2,'.',''); ?></td>
					</tr>
					<?php $i=0; foreach ($pkg->product_list as $p): ?>
					<?php if ($i): ?>
					<tr>
						<td>
							<a href="<?php print "package-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank"><?php print $p->product_sn ?></a>
						</td>
						<td><?php print $p->color_name ?></td>
						<td><?php print $p->size_name ?></td>
					</tr>
					<?php endif ?>
					<?php $i++; endforeach ?>

					<?php endforeach ?>
				</table>
			</div>
			<font class="bold" style="margin-left:20px;line-height:34px;">商品小计</font>
			<div class="order_add">
				<dl>
					<dt>商品总数：</dt>
					<dd><?php print $order->product_num ?> 件</dd>
				</dl>
				<dl>
					<dt>商品总价：</dt>
					<dd><?php print $order->order_price ?>元</dd>
				</dl>
				<dl>
					<dt>运费：</dt>
					<dd>
						<?php print $order->shipping_fee; ?>元
					</dd>
					<dd>
						<a class="cred" href="/help-14.html" target="_blank">运费规则</a>
					</dd>
				</dl>
				<dl>
					<dt>可获积分：</dt>
					<dd>
						<?php print $order->point_amount ?>个
					</dd>
					<dd>
						<a class="cred" href="/help-4.html" target="_blank">积分规则</a>
					</dd>
				</dl>
				<dl>
					<dt>订单总价：</dt>
					<dd>
						<font class="cred und"><?php print number_format($order->order_price+$order->shipping_fee,2,'.',''); ?>元</font>
					</dd>
				</dl>
			</div>
			
			<font class="bold" style="margin-left:20px;line-height:34px;">付款明细</font>
			<div class="pay_detail switch_block_content">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<th width="20%" height="30">付款日期</th>
						<th width="20%">支付金额</th>
						<th width="20%">支付方式</th>
						<th width="20%">付款账号</th>
						<th width="20%">交易流水号</th>
					</tr>
					<?php foreach ($order_payment as $p): ?>
					<tr>
						<td height="30" align="center"><?php print substr($p->payment_date,0,10); ?></td>
						<td align="center"><?php print $p->payment_money; ?></td>
						<td align="center"><?php print $p->pay_name; ?></td>
						<td align="center"><?php print $p->payment_account?$p->payment_account:'--'; ?></td>
						<td align="center"><?php print $p->trade_no?$p->trade_no:'--'; ?></td>
					</tr>
					<?php endforeach ?>
				</table>
			</div>
			<div class="order_total">
				<a href="https://jf.alipay.com/exchange/exchange.htm?signIn=https://hi.alipay.com/campaign/normal_campaign.htm?campInfo=f8TFC%2B0iCwsVvnEWRBz5qLzMoy1VtWKh&from=jfb" target="_blank" class="<?php if($order->pay_id==PAY_ID_ALIPAY) echo 'jifenbao';?>"></a>已付金额：<span class="cred_b">￥<?php print $order->paid_price ?>元</span>，待付金额：<span class="cred_b">￥<?php print number_format($order->unpay_price,2,'.',''); ?>元</span>
			</div>
		</div>
		<div class="car_b_b"><s class="cb_tsl"></s>&nbsp;<s class="cb_tsr"></s></div>
	</div>
	<div class="cl"></div>  

</div>
<?php include(APPPATH.'views/common/footer.php'); ?>
