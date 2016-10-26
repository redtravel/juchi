<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
	<div class="main">
	    <div class="main_title"><span class="l">扫描出库</span></div>
	    <div class="blank5"></div>
	    <div class="search_row">
			<table style="width:60%">
			    <tr>
				<td align="right">出库单编号:</td>
				<td>&nbsp;&nbsp;<?=$depot_content->depot_out_code?></td>
				<td align="right">预计出库数量:</td>
				<td>&nbsp;&nbsp;<?=$depot_content->depot_out_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align="right">箱子数量:</td>
				<td>
				    &nbsp;&nbsp;<span id="scan_box"><?=$box_count?></span>
				</td>
				<td align="right">扫描数量：</td>
				<td>&nbsp;&nbsp;<?=$scan_num?></td>
			    </tr>
			</table>
			</div>
			    <div class="blank5"></div>
			    <div id="listDiv">
				    <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
					    <tr>
						    <td colspan="9" class="topTd"> </td>
					    </tr>
					    <tr class="row">
						<th>条码</th>
						<th>货号</th>
						<th>商品名称</th>
						<th>品牌</th>
						<th>商品款号</th>
						<th>颜色【编码】</th>
						<th>尺码【编码】</th>
						<th>扫描数量</th>
						<th width="192px">操作</th>
					    </tr>
					    <?php foreach($content as $rows): ?>
					    <tr>
						<td colspan="8">
						    箱号：<?php print $rows->box_code; ?>
						    &nbsp;&nbsp;总件数：<?php print $rows->product_number; ?>
						    &nbsp;&nbsp;扫描人：<?php print $rows->qc_name; ?>
						    &nbsp;&nbsp;扫描时间：<?php print $rows->qc_starttime; ?>
						</td>
						<td rowspan="<?=count($rows->detail_list)+1?>">
						    <input type="button" class="am-btn am-btn-primary" onclick="redirect('outbound/print_box_order/<?php print $rows->box_id; ?>');" value="打印装箱单" style="margin: 2px;"/>
						    <?php if(check_perm('cancel_depot_out_box_scan')):?><input type="button" class="am-btn am-btn-primary" onclick="cancel_box(<?php print $rows->box_id; ?>);" value="取消此箱扫描" style="margin: 2px;"/><? endif;?>
						</td>
					    </tr>
					    
					    <?php if(!empty($rows->detail_list)):foreach($rows->detail_list as $row): ?>
					    <tr class="row">
						<td><?php print $row->provider_barcode; ?></td>
						<td><?php print $row->provider_productcode; ?></td>
						<td><?php print $row->product_name; ?></td>
						<td><?php print $row->brand_name; ?></td>
						<td><?php print $row->product_sn; ?></td>
						<td><?php print $row->color_name; ?>【<?php print $row->color_id; ?>】</td>
						<td><?php print $row->size_name; ?>【<?php print $row->size_id; ?>】</td>
						<td><?php print $row->finished_scan_number; ?></td>
					    </tr>
					     <?php endforeach;endif;?>
					    <?php endforeach;?>
					    <tr>
						<td colspan="9" class="bottomTd"> </td>
					    </tr>
				    </table>
			    <div class="blank5"></div>
			</div>
	</div>
    <script type="text/javascript">
	function cancel_box(box_id){
	if(!confirm('确定取消此箱子的出库扫描？'))return;
	    $.ajax({
	            url: '/outbound/cancel_depot_out_box_scan/'+box_id,
	            data: {is_ajax:1,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err == 0)
	                {
			    location.href=location.href;
	                }
	            }
	        });
	}
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
