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
		<div class="main_title"><span class="l">销售单打印</span><span class="r"><a href="pick" class="add">拣货单列表</a></span></div>
		
		<?php $i=0;foreach($list as $item):$i+=1; ?>
		<div class="blank5"></div>
		<?php if($item->type=='order'): //销售单 ?>
			<div align="center" class="full_width" id="printer_order">
				<table cellspacing="0" cellpadding="0" border="0" align="center">
					<tbody>
						<tr height="19">
							<td width="15%" valign="top" height="19" align="left"><img src="public/images/logo_print.jpg" style="width: 147px; height: 69px" alt=""></td>
							<td width="20%" valign="top" height="19" align="left"><span style="font-size:14px;line-height:18px; "><b>http://www.baobeigou.com</b><br><br><b>电话：4008-320-235</b></span></td>
							<td width="40%" valign="bottom" align="center" class="printer_title">销售订单</td>
							<td><img src="index/barcode/<?php print $item->order_sn;?>.html"></td>
						</tr>
						<tr height="19">
							<td align="right" colspan="4"><?php if($item->pick_sn) print '导入批次号：'.$item->pick_sn;  ?></td>
						</tr>
					</tbody>
				</table>
				<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">
					<tbody>
						<tr height="20">
							<td height="96" colspan="3">
							<p style="font-size: 12px">订购人</p>
							<p><?php print $item->user_name;  ?><br/>
							<?php print $item->email;  ?><br/>
							<?php print $item->mobile;  ?></p>
							</td>
							<td colspan="7">
							<p style="font-size: 12px">收货人</p>
							<p><?php print $item->consignee.' '.$item->mobile.' '.$item->email;?> <br>
							<?php print $item->province_name.' '.$item->city_name.' '.$item->district_name.' '.$item->address;  ?></p>
							</td>           
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td width="10%" height="33">商品款号</td>
							<td width="10%">货号</td>
							<td width="17%">商品名称</td>
							<td width="9%">颜色</td>
							<td width="9%">尺码</td>
							<td width="10%">单价</td>
							<td width="8%">数量</td>
							<td width="10%">小计</td>
							<td width="15%">储位</td>
						</tr>
						<?php foreach($item->products as $p):  ?>
						<tr>
							<td width="10%" height="33"><?php print $p->product_sn; ?></td>
							<td width="10%"><?php print $p->provider_productcode; ?></td>
							<td width="17%"><?php print $p->product_name; ?></td>
							<td width="9%"><?php print $p->color_name; ?></td>
							<td width="9%"><?php print $p->size_name; ?></td>
							<td width="10%"><?php print $p->product_price; ?></td>
							<td width="5%"><?php print $p->product_num.' '.$p->unit_name; ?></td>
							<td width="10%"><?php print $p->total_price; ?></td>
							<td width="15%"><?php print implode('<br/>',$p->locations); ?></td>
						</tr>
						<?php endforeach;?>
						<tr height="19">
							<td height="19" align="right" colspan="6">合计</td>            
							<td><?php print $item->total_num;?></td>
							<td><?php print $item->order_price;?></td>
							<td>&nbsp;</td>
						</tr>
						<tr height="19">
							<td height="19" align="right" colspan="9">
							运费：<?php print $item->shipping_fee  ?>&nbsp;&nbsp;&nbsp;&nbsp;
							折扣：<?php print number_format($item->discount,2); ?>&nbsp;&nbsp;&nbsp;&nbsp;
							
							<?php 
							if($item->pay_id==PAY_ID_COD && $item->order_amount>0) print '待付：'.number_format($item->order_amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;';
							if($item->invoice_title){
								print '开票金额：'.number_format($item->order_price-$item->discount+$item->shipping_fee,2);
								print '&nbsp;&nbsp;&nbsp;&nbsp;发票抬头：'.$item->invoice_title;
							}else{
								print '是否开票：否';
							}
							?>
							</td>  
						</tr>
						</tbody>
						
				</table>
				<p style="text-align: left; font-size: 14px" class="dingdan_nr full_width"><i>非常感谢您在 http://www.52kid.cn 购物，我们期待您的再次光临！</i></p>
				
			</div>
		<?php else://换货单?>
			<div align="center" class="full_width" id="printer_order">
				<table cellspacing="0" cellpadding="0" border="0" align="center">
					<tbody>
						<tr height="19">
							<td width="15%" valign="top" height="19" align="left"><img src="public/images/logo_print.jpg" style="width: 147px; height: 69px" alt=""></td>
							<td width="20%" valign="top" height="19" align="left"><span style="font-size:14px;line-height:18px; "><b>http://www.baobeigou.com</b><br><br><b>电话：4008-320-235</b></span></td>
							<td width="40%" valign="bottom" align="center" class="printer_title">换货单</td>
							<td><img src="index/barcode/<?php print $item->change_sn;?>.html"></td>
						</tr>
						<tr height="19">
							<td align="right" colspan="4"><?php if($item->pick_sn) print '导入批次号：'.$item->pick_sn;  ?></td>
						</tr>
					</tbody>
				</table>
				
				<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">
					<tbody>
						<tr>
							<td width="10%" height="33">商品款号</td>
							<td width="10%">货号</td>
							<td width="17%">商品名称</td>
							<td width="9%">原色</td>
							<td width="9%">原码</td>
							<td width="9%">颜色</td>
							<td width="9%">尺码</td>
							<td width="8%">数量</td>
							<td width="15%">储位</td>
						</tr>
						<?php foreach($item->products as $p):  ?>
						<tr>
							<td width="10%" height="33"><?php print $p->product_sn; ?></td>
							<td width="10%"><?php print $p->provider_productcode; ?></td>
							<td width="17%"><?php print $p->product_name; ?></td>
							<td width="9%"><?php print $p->src_color_name; ?></td>
							<td width="9%"><?php print $p->src_size_name; ?></td>
							<td width="9%"><?php print $p->color_name; ?></td>
							<td width="9%"><?php print $p->size_name; ?></td>
							<td width="5%"><?php print $p->change_num.' '.$p->unit_name; ?></td>
							<td width="15%"><?php print implode('<br/>',$p->locations); ?></td>
						</tr>
						<?php endforeach;?>
						<tr>
							<td height="19" align="right">换货原因</td>
							<td align="left" colspan="5"><?php print $item->change_reason; ?></td>
							<td align="right">合计</td>            
							<td><?php print $item->total_num;?></td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<p style="text-align: left; font-size: 14px" class="dingdan_nr full_width"><i>非常感谢您在 http://www.52kid.cn 购物，我们期待您的再次光临！</i></p>
				
		</div>
		<?php endif;  ?>
		<?php if($i<count($list)) print '<P style="page-break-after:always">&nbsp;</P>';  ?>
		<?php endforeach;?>
		<div class="blank5"></div>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>