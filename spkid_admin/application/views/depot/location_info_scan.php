<?php if($full_page): ?>
<?php include(APPPATH.'views/common/rf_header.php'); ?>
<style type="text/css">
	table.dataTable {
		border-bottom:1px solid #d0d0d0;
		border-right:1px solid #d0d0d0;
		width:100%;
		margin-top:2px;
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
	listTable.url = '/depot/location_info_scan';
	function search() {
		listTable.filter['location_name'] = $.trim($('#location_name').val());
		$('#location_name').val('');
		listTable.loadList();
	}
	//扫描储位
	$("#location_name").keydown(function(event){
		if(event.which == 13){
			$("#search").submit();
		}
	});

	$(function(){
		$("#location_name").focus();
	});
	//]]>
</script>

<div class="main">
	<div class="blank5"></div>
	<form id="search" name="search" action="javascript:search(); ">
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title">储位：</td>
			<td class="item_input">
				<input name="location_name" class="textbox" id="location_name" />
			</td>
		</tr>
		<tr>
			<td colspan=2 align="left" class="bottomTd"></td>
		</tr>
	</table>
	</form>
<?php endif; ?>
	<div id="listDiv">
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan="2">
					储位：<?php print empty($location_name)?'':$location_name; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					待出：<?php print empty($filter)?'':abs($filter['sum_daichu']); ?> &nbsp;&nbsp;
					待入：<?php print empty($filter)?'':$filter['sum_dairu']; ?> &nbsp;&nbsp;
					实际：<?php print empty($filter)?'':$filter['sum_shiji']; ?> &nbsp;&nbsp;
				</td>
			</tr>
		</table>

		<?php if (!empty($list)): ?>
		<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			<?php foreach($list as $row): ?>
			<?php if ($row->num_daichu!=0 || $row->num_dairu!=0 || $row->num_shiji!=0): ?>
		    <tr class="row">
				<td align="right">商品&nbsp;</td>
				<td>&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->color_name; ?>&nbsp;|&nbsp;<?php print $row->size_name; ?></td>
		    </tr>
		    <tr class="row">
				<td align="right">品牌&nbsp;</td>
				<td>&nbsp;<?php print $row->brand_name; ?></td>
		    </tr>
		    <tr class="row">
				<td align="right">货号&nbsp;</td>
				<td>&nbsp;<?php print $row->provider_productcode; ?></td>
		    </tr>
		    <tr class="row">
		    	<td align="right">数量&nbsp;</td>
				<td>&nbsp;待出：<?php print $row->num_daichu; ?>&nbsp;&nbsp;待入：<?php print $row->num_dairu; ?>&nbsp;&nbsp;实际：<?php print $row->num_shiji; ?></td>
		    </tr>
		    <tr class="row">
				<td align="right">条形码&nbsp;</td>
				<td>&nbsp;<?php print $row->provider_barcode; ?></td>
		    </tr>
		    <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>

		<!--
		<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan="10" class="topTd"> </td>
			</tr>
			<tr class="row">
		      <th>货号</th>
		      <th>待出</th>
		      <th>待入</th>
		      <th>实际</th>
			  <th>商品名称</th>
		      <th>款号</th>
		      <th>颜色</th>
			  <th>尺寸</th>
		      <th>品牌</th>
		      <th>条形码</th>

			</tr>
			<?php foreach($list as $row): ?>
		    <tr class="row">
				<td>&nbsp;<?php print $row->provider_productcode; ?></td>
				<td>&nbsp;<?php print $row->num_daichu; ?></td>
				<td>&nbsp;<?php print $row->num_dairu; ?></td>
				<td>&nbsp;<?php print $row->num_shiji; ?></td>
		    	<td>&nbsp;<?php print $row->product_name; ?></td>
				<td>&nbsp;<?php print $row->product_sn; ?></td>
				<td>&nbsp;<?php print $row->color_name; ?></td>
				<td>&nbsp;<?php print $row->size_name; ?></td>
				<td>&nbsp;<?php print $row->brand_name; ?></td>
				<td>&nbsp;<?php print $row->provider_barcode; ?></td>
		    </tr>
			<?php endforeach; ?>
		    <tr>
				<td colspan="10" class="bottomTd"> </td>
			</tr>
		</table>
		-->
		<div class="blank5"></div>
		<?php if($full_page): ?>
	</div>
</div>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>
<?php endif; ?>