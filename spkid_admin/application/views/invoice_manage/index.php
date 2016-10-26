<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">

		$(function(){
        	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'invoice_manage/index';

		function search_invoice(){
			listTable.filter['start_time'] = $.trim($('input[type=text][name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[type=text][name=end_time]').val());
			listTable.filter['order_sn'] = $.trim($('input[type=text][name=order_sn]').val());
			listTable.filter['invoice_status'] = $.trim($('select[name=invoice_status]').val());
			listTable.loadList();
		}

		//]]>
	</script>
	<style type="text/css">

	</style>
	<div class="main">
		<div class="main_title">
			<span class="l">发票管理</span>
			<span class="r" style="height:60px;" >
				<form method="post" action="invoice_manage/invoice_data_import" enctype="multipart/form-data" class="am-form-inline">
					<a class="add" href="javascript:void(0);">导入Excel表：</a>
					<input  type="file" name="file_invoice" class="am-form-field" style="border:0px;line-height:36px;height:36px;"/><input type="submit"  value="导入" class="am-form-field" style="border:0px;height:36px;" />
				</form>
			</span>
		</div>		
		<div class="blank5"></div>
		<div class="search_row">
			<form method="post" action="invoice_manage/invoice_data_export" class="am-form-inline">
				财审时间：	<input type="text" name="start_time" id="start_time" value="<?=$filter['start_time']?>" class="am-form-field" placeholder="开始时间" />
							<input type="text" name="end_time" id="end_time" value="<?=$filter['end_time']?>" class="am-form-field" placeholder="结束时间" />
				订单号：	<input type="text" name="order_sn" id="order_sn" value="<?=$filter['order_sn']?>" class="am-form-field" />
				打印状态：	
							<?php print form_dropdown('invoice_status', get_pair($filter['all_invoice_status'],'invoice_status','invoice_value'),array(),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>		
							<input type="button" class="am-btn am-btn-primary" value="搜索" onclick="search_invoice();" />
							<input type="submit" class="am-btn am-btn-secondary" value="导出">
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px"><a href="javascript:listTable.sort('o.order_id', 'ASC'); ">编号<?php echo ($filter['order_id'] == 'o.order_id') ? $filter['sort_flag'] : '' ?></th>
					<th>订单号</th>
					<th>商品名称</th>
					<th>规格型号</th>
					<th>单价</th>
					<th>数量</th>
					<th>单位</th>
					<th>商品金额</th>
					<th>支付金额</th>
					<th>发票抬头</th>
					<th width="300px;">发票内容</th>
					<th>财审时间</th>
					<th>打印状态</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->order_id; ?></td>
					<td><?php print $row->order_sn; ?></td>
					<td><?php print $row->product_name; ?></td>
					<td>
						<?php 
							if(!empty($row->product_desc_additional)){
								$dimensions = json_decode($row->product_desc_additional);
								$dimensions->desc_dimensions == false ? '': $dimensions->desc_dimensions;
							}
						?>
					</td>
					<td><?php print $row->product_price; ?></td>
					<td><?php print $row->product_num; ?></td>
					<td><?php print $row->unit_name; ?></td>
					<td><?php print $row->total_price; ?></td>
					<td><?php print $row->paid_price; ?></td>
					<td><?php print $row->invoice_title; ?></td>
					<td><?php print $row->invoice_content; ?></td>
					<td><?php print $row->finance_date; ?></td>
					<td>
					<?php 
						foreach ($filter['all_invoice_status'] as $val) {
							if ($row->invoice_status == $val['invoice_status']) echo $val['invoice_value'];
						}
					?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>