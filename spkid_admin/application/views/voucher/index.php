<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'voucher/index';
		function search(){
			listTable.filter['campaign_name'] = $.trim($('input[type=text][name=campaign_name]').val());
			listTable.filter['campaign_type'] = $.trim($(':input[name=campaign_type]').val());
			listTable.filter['campaign_status'] = $.trim($(':input[name=campaign_status]').val());
			listTable.loadList();
		}
		$(function(){
			$(':input[name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		});
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">现金券活动列表</span><span class="r"><a href="voucher/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			审核时间：
			<input type="text" class="ts" name="start_time" value="" style="width:100px;" />
			至
			<input type="text" class="ts" name="end_time" value="" style="width:100px;" />
			活动名称：<input type="text" class="ts" name="campaign_name" value="" style="width:100px;" />
			<?php print form_dropdown('campaign_type', array(''=>'活动类型')+$all_type);?>
			<?php print form_dropdown('campaign_status', array('-1'=>'活动状态')+$all_status);?>


			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">
						<a href="javascript:listTable.sort('c.campaign_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'c.campaign_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>活动名称</th>
					<th>活动类型</th>
					<th>活动期间</th>
					<th>添加时间</th>
					<th>启用时间</th>
					<th>停用时间</th>
					<th>活动状态</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->campaign_id; ?></td>
					<td><?php print $row->campaign_name; ?></td>
					<td><?php print $all_type[$row->campaign_type]; ?></td>
					<td><?php print substr($row->start_date,0,10).' 至 '.substr($row->end_date,0,10); ?></td>
					<td><?php print substr($row->create_date,0,10); ?></td>
					<td><?php print $row->campaign_status>0?substr($row->audit_date,0,10):'未启用'; ?></td>
					<td><?php print $row->campaign_status==2?substr($row->stop_date,0,10):'未停用'; ?></td>
					<td><?php print $all_status[$row->campaign_status]; ?></td>
					<td>
						<a class="edit" href="voucher/edit/<?php print $row->campaign_id; ?>" title="编辑"></a>
						<?php if($row->campaign_status==0 && $perm_delete):?>
						<a class="del" href="javascript:void(0)" rel="voucher/delete/<?php print $row->campaign_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
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