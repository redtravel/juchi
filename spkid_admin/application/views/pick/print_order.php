<?php include(APPPATH.'views/common/header.php'); ?>
<style type="text/css" media="all">
	.notice{display:none;}
	.main_title{display:none;}
	.printer_title{font-size:16px;font-weight:bold;}
	.body{background-color:#FFF;}
	table.dingdan_nr td{padding:5px;};
</style>
<script type="text/javascript">
	$(function(){window.print();});
</script>
	<div class="main">
		<?php $i=0;foreach($order_info as $order):$i+=1; ?>
		<div class="blank5"></div>
			<div align="center" class="full_width" id="printer_order">
				<table cellspacing="0" cellpadding="0" border="0" align="center">
					<tbody>
						<tr height="19">
							<br/>
							<td width="15%" valign="top" height="19" align="left"><img src="public/images/logo2.0.png" style="width: 147px; height: 69px" alt=""></td>
							<!--<td width="10%" valign="top" height="19" align="left"><span style="font-size:14px;line-height:18px; "><br/><b>http://www.baobeigou.com</b><br><br><b>电话：4008-320-235</b></span></td>-->
							<td width="55%" valign="bottom" align="center" class="printer_title">订单装箱单</td>
							<!--<td width="30%" valign="top" height="19" align="right"><img src="index/barcode/<?php //print str_replace(' ','-',$order->invoice_no);?>.html"></td>-->
                            <td width="30%" valign="top" height="19" align="center"><img src="index/barcode/<?php print $order->order_sn;?>.html"><br/><b><?php echo $pick_sn; ?></b></td>
							<td></td>
						</tr>
					</tbody>
				</table>
				<table cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td align="left" colspan="2">公司名称：上海欧思蔚奥医疗器材有限公司</td>
						</tr>
						<tr>
							<td width="1%" colspan="2" align="left">订单序列号：<?php print $order->pick_cell;?></td>
						</tr>
						<tr>
							<td align="left" colspan="2">送货方式：<?php print $order->shipping_name;?></td>
							<!--<td align="right">销售单号：<?php //print $order->order_sn;?></td>-->
						</tr>
						<tr>
							<td align="left" colspan="2">发货日期：<?php print $order->shipping_date;?></td>
						</tr>
						
					</tbody>
				</table>
				
				<!--<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">-->
				<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">
					<tbody>
						<tr height="20">
							<td height="15" colspan="2">
								<p style="font-size: 12px">订单号：<?php print $order->order_sn;?></p>
							</td>
							<td colspan="7">
								<p style="font-size: 12px">是否开发票：<?php if ($order->invoice_title) print '是'.' '.$order->invoice_title;?><?php if (!$order->invoice_title) print '否';?></p>
							</td>           
						</tr>
						<tr height="20">
							<td height="70" colspan="2">
								<p style="font-size: 12px">订购人</p>
								<p><?php print $order->user_name;?><br/>
								<?php print $order->email;?><br/>
							</td>
							<td colspan="7">
								<p style="font-size: 12px">收货人</p>
								<p><?php print $order->consignee.' '.$order->mobile.' '.$order->tel.' '.$order->zipcode;?> <br>
								<?php print $order->province_name.' '.$order->city_name.' '.$order->district_name.' '.$order->address;?></p>
	<?php if ( $pick_sn == 'PK2016011531620') :?>
								<p style="font-size: 12px"><b>赠品4支牙刷将于下周(2016-01-18起)内全部发出.</b></p>
<?php endif;?>
							</td>           
						</tr>
						<tr height="20">
							<td height="15" colspan="9">
								<p style="font-size: 12px">已付 ￥<?php print $order->paid_price;?>元 待付 ￥<?php print $order->unpay_price;?>元</p>
							</td>          
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td width="10%" height="35" align="center">商品款号</br>条码</td>
							<td width="20%" align="center">生产单位</br>注册/备案号</td>
							<td width="20%" align="center">品牌</br>商品名称</td>
							<td width="10%" align="center">数量</br>单位</td>
							<td width="10%" align="center">单价</td>
                            <td width="10%" align="center">金额</td>
							<td width="10%" align="center">入库批号</br>储位</td>
							<td width="10%" align="center">生产批号</br>有效期</td>
						</tr>
						<?php foreach($order->product_list as $product):?>
						<tr>
							<td width="10%" height="35" align="center"><?php print $product->sku.'<br> '.$product->provider_barcode?></td>
							<td width="20%" align="center"><?php print $product->unit.'<br> '. $product->register_no?></td>
							<td width="20%" align="center"><?php print $product->brand_name.'<br> '. $product->product_name.'【'.$product->size_name.'】'; ?></td>
							<td width="10%" align="center"><?php print $product->product_num .'<br> '. $product->unit_name; ?></td>
							<td width="10%" align="center"><?php print $product->product_price; ?></td>
                            <td width="10%" align="center"><?php print $product->total_price; ?></td>
							<td width="10%" align="center"><?php print $product->batch_code.'<br> '.$product->location_name; ?></td>
							<td width="10%" align="center"><?php print $product->production_batch;?> <br> <?php print ($product->expire_date == '0000-00-00' || $product->expire_date == '0000-00-00 00:00:00' || $product->expire_date == '')?'无':$product->expire_date; ?></td>
						</tr>
						<?php endforeach;?>
						</tbody>
				</table>
				<div align="right">合计：<?php print $order->product_num;?>件 ￥<?php print $order->order_price;?></div>
				
                                <p style="text-align: left; font-size: 14px" class="dingdan_nr full_width"><i>非常感谢您在 http://pc.redtravel.cn 购物，我们期待您的再次光临！</i></p>
			</div>
		<?php if($i<count($order_info)) print '<P style="page-break-after:always">&nbsp;</P>';  ?>
		<?php endforeach;?>
		<div class="blank5"></div>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
