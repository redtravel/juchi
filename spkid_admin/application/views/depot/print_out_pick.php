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
						<td valign="bottom" valign="top" height="50" align="center" class="printer_title">出库拣货单</td>
					</tr>
					<tr height="20">
						<td width="1%" valign="top" height="20" align="left"><!-- 出库单号 --><img src="index/barcode/<?php print $depot_out_info->depot_out_code;?>.html"></td>
					</tr>
					<tr>
						<td align="center" valign="top" >打印时间：<?php print date("Y-m-d H:i:s"); ?></td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr">
				<tbody>
					<tr>
						<td align="center" style="font-weight:bold">行号</td>
						<td align="center" style="font-weight:bold">储位号</td>
						<td align="center" style="font-weight:bold">产品条码</td>
						<td align="center" style="font-weight:bold">货号</td>
						<td align="center" style="font-weight:bold">商品名称</td>
						<td align="center" style="font-weight:bold">颜色编码</td>
						<td align="center" style="font-weight:bold">规格编码</td>
						<td align="center" style="font-weight:bold">数量</td>
						<td align="center" style="font-weight:bold">生产批号</td>
						<td align="center" style="font-weight:bold">有效期</td>
					</tr>
					<?php foreach($goods_list as $key=>$row): ?>
					<tr style="line-height:10px">
						<td align="center"><?php print $key+1; ?></td>
                                                <td align="center"><?php print $row->location_name; ?></td>
						<td align="center" style="font-weight:bold;white-space:nowrap;"><?php print $row->provider_barcode;?></td>
						<td align="center"><?php print $row->provider_productcode; ?></td>
						<td align="center"><?php print $row->product_name; ?></td>
						<td align="center"><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
						<td align="center"><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
						<td align="center"><span style="display:-moz-inline-box; display:inline-block;"><?php print $row->product_number; ?>&nbsp;</span></td>
						<td align="center"><?php print $row->production_batch; ?></td>
						<td align="center"><?php print ($row->expire_date == '0000-00-00' || $row->expire_date == '0000-00-00 00:00:00' || $row->expire_date == '')?'无':$row->expire_date; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="blank5"></div>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>