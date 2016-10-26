<?php include(APPPATH.'views/common/header.php'); ?>
<style type="text/css" media="all">
	.notice{display:none;}
	.main_title{display:none;}
	.printer_title{font-size:16px;font-weight:bold;}
	.body{background-color:#FFF;}
	table.dingdan_nr td{padding:5px;}
</style>
<script type="text/javascript">
	$(function(){window.print();});
</script>
	<div class="main">
		<div class="blank5"></div>
		<div align="center" class="full_width" id="printer_order">
			<table cellspacing="0" cellpadding="0" border="0" align="center">
				<tbody>
					<tr height="50">
						<td valign="bottom" valign="top" height="50" align="center" class="printer_title" colspan="2">小车拣货单</td>
					</tr>
					<tr height="20">
						<td width="1%" valign="top" height="20" align="left" colspan="2"><img src="index/barcode/<?php print $pick_sn;?>.html"></td>
					</tr>
					<tr>
						<td align="center" valign="top" >打印时间：<?php print date("Y-m-d H:i:s"); ?></td>
                        <td align="center" valign="top" ><b>物流公司：<?php print $pick->shipping_name; ?></b></td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">
				<tbody>
					<tr>
						<td align="center" style="font-weight:bold">行号</td>
						<td align="center" style="font-weight:bold">产品代码</td>
						<td align="center" style="font-weight:bold">品牌</td>
						<td align="center" style="font-weight:bold">品名</td>
						<td align="center" style="font-weight:bold">实际库位</td>
					</tr>
					<?php foreach($pick_info as $key=>$row):  ?>
					<tr>
						<td align="center"><?php print $key+1; ?></td>
						<td align="center" style="font-weight:bold"><?php print $row->sku; ?></p><?php print $row->provider_barcode; ?></td>
						<td align="center"><?php print $row->brand_name; ?></td>
						<td align="center"><?php print $row->product_name; ?> <?php print $row->color_name; ?> <?php print $row->size_name; ?></p><img src="index/barcode/<?php print urlencode($row->provider_barcode);?>.html"></td>
						<td align="center">
						    <span style="font-weight:bold">
							<?php print $row->location_name; ?>
						    </span>
						    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数量&nbsp;&nbsp;|&nbsp;&nbsp;订单格编号
						    </p>
						    <span style="font-weight:bold;<?php if($row->product_number >1) print 'font-size: 12pt';?>">
							<?php print $row->product_number; ?>
						    </span>
						    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						    <span style="font-weight:bold"><?php print $row->pick_cell; ?></span>
						</td>
					</tr>
					<?php endforeach;?>
					</tbody>
			</table>
		</div>
		<div class="blank5"></div>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
