<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'season/index';
		function search(){
			listTable.filter['season_name'] = $.trim($('input[type=text][name=season_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">季节列表</span><span class="r"><a href="season/add" class="add">新增</a></span></div>		
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			季节名称：<input type="text" class="ts" name="season_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
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
					<th width="50px">
						<a href="javascript:listTable.sort('c.season_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'c.season_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>季节名称</th>
					<th>
						<a href="javascript:listTable.sort('c.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'c.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>启用</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->season_id; ?></td>
					<td><?php print $row->season_name?></td>
					<td>
						<?php print edit_link('season/edit_field', 'sort_order', $row->season_id, $row->sort_order);?>
					</td>
					<td>
						<?php print toggle_link('season/toggle','is_use',$row->season_id, $row->is_use);?>
					</td>
					<td>
						<a class="edit" href="season/edit/<?php print $row->season_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="season/delete/<?php print $row->season_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						
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