<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/My97DatePicker/WdatePicker.js"></script>
	<script type="text/javascript">
    	
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/purchase_log';
		function search() {
			listTable.filter['start_time'] = $.trim($('#start_time').val());
			listTable.filter['end_time'] = $.trim($('#end_time').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">采购入库日志</span>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
				时间段：
				<input type="text" name="start_time" id="start_time" class="Wdate" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')}'})" /> - 
				<input type="text" name="end_time" id="end_time" class="Wdate" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}'})" />
				<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		
		<div id="listDiv">
<?php endif; ?>

			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="5" class="topTd"> </td>
				</tr>
				<tr class="row">
	                <th>箱号</th>
	                <th>类型</th>
	                <th>内容</th>
	                <th>创建人</th>
	                <th>创建时间</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td align="center"><?php print $row->box_code; ?></td>
					<td align="center">
					<?php if($row->related_type == 0): ?>修改收货箱商品数量
					<?php elseif($row->related_type == 1): ?>取消收货箱
					<?php else: ?>
					<?php endif; ?>
					</td>
					<td align="center"><?php print $row->desc_content; ?></td>
					<td align="center"><?php print $row->create_user; ?></td>
					<td align="center"><?php print $row->create_date; ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="5" class="bottomTd"> </td>
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