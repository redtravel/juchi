<?php if($full_page): ?>
<?php include(APPPATH.'views/common/rf_header.php'); ?>
<style type="text/css">
	table.dataTable {
		border-bottom:1px solid #d0d0d0;
		border-right:1px solid #d0d0d0;
		width:100%;
		margin-top:2px;
		//table-layout:fixed;
	}
	table.dataTable th, .dataTable td {
		border-top:1px solid #d0d0d0;
		border-left:1px solid #d0d0d0;
		padding:0 2px;
		font-size: 12px;
	}
</style>
<script type="text/javascript" src="/public/js/listtable.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.url = '/depot/barcode_scan';
	function search() {
		listTable.filter['provider_barcode'] = $.trim($('#provider_barcode').val());
		$('#provider_barcode').val('');
		listTable.loadList();
	}
	//扫描条形码
	$("#provider_barcode").keydown(function(event){
		if(event.which == 13){
			$("#search").submit();
		}
	});

	$(function(){
		$("#provider_barcode").focus();
	});
	//]]>
</script>

<div class="main">
	<div class="blank5"></div>
	<form id="search" name="search" action="javascript:search(); ">
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td class="item_title">条形码：</td>
			<td class="item_input">
				<input name="provider_barcode" class="textbox" id="provider_barcode" />
			</td>
		</tr>
	</table>
	</form>
<?php endif; ?>
	<div id="listDiv">
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan="2">
					条形码：
					<?php if(!empty($filter)): ?>
						<?php print $filter['provider_barcode']; ?>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<?php if(!empty($list)): ?>
		<?php foreach($list as $key => $row): ?>
		<table width="100%" style="zoom:0;" id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan="5">
					<?php print $row['product_name']; ?>&nbsp;|&nbsp;
					<?php print $row['color_name']; ?>&nbsp;|&nbsp;
					<?php print $row['size_name']; ?>&nbsp;|&nbsp;
					<?php print $row['brand_name']; ?>&nbsp;|&nbsp;
					<?php print $row['provider_productcode']; ?>
				</td>
			</tr>
                </table>
		<table width="100%" style="zoom:0;" id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			<tr class="row">
                            <th width="44%">储位</th>
                            <th width="14%">待出</th>
                            <th width="14%">待入</th>
                            <th width="14%">实际</th>
                            <th width="14%">批次</th>
			</tr>
			<?php foreach($row['location'] as $location): ?>
			<?php if($location->num_daicu != 0 || $location->num_dairu != 0 || $location->num_shiji != 0): ?>
		    <tr class="row">
				<td>&nbsp;<?php print $location->location_name; ?></td>
				<td>&nbsp;<?php print abs($location->num_daicu); ?></td>
				<td>&nbsp;<?php print $location->num_dairu; ?></td>
				<td>&nbsp;<?php print $location->num_shiji; ?></td>
				<td>&nbsp;<?php print $location->batch_id; ?></td>
		    </tr>
		    <?php endif; ?>
		    <?php endforeach; ?>
		    <tr>
		    	<td colspan="5">关联单据：</td>
		    </tr>
    		<?php foreach($row['trans_log'] as $trans): ?>
		    <tr>
				<td colspan="2">
					<?php print $trans->trans_sn; ?>
				</td>
				<td colspan="3">
				<?php if ($trans->trans_status == 1): ?>待出
				<?php elseif($trans->trans_status == 2): ?>已出
				<?php elseif($trans->trans_status == 3): ?>待入
				<?php elseif($trans->trans_status == 4): ?>已入
				<?php endif; ?>
				：<?php print abs($trans->product_number); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endforeach; ?>
		<?php endif; ?>
		
		<div class="blank5"></div>
		<?php if($full_page): ?>
	</div>
</div>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>
<?php endif; ?>