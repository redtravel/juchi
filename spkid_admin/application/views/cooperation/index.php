<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'cooperation/index';
		function search(){
			listTable.filter['cooperation_name'] = $.trim($('input[type=text][name=cooperation_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">商品管理 >> 合作方式列表</span>  <span class="r"> <a class="add" href="cooperation/add">新增</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			合作方式名称：<input type="text" class="ts" name="cooperation_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr class="row">
					<th width="50px">
						<a href="javascript:listTable.sort('c.cooperation_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'c.cooperation_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>合作方式名称</th>
					<th>
						<a href="javascript:listTable.sort('c.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'c.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>状态</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->cooperation_id; ?></td>
					<td><?php print $row->cooperation_name?></td>
					<td>
						<?php print edit_link('cooperation/edit_field', 'sort_order', $row->cooperation_id, $row->sort_order);?>
					</td>
					<td>
						<?php print toggle_link('cooperation/toggle','is_use',$row->cooperation_id, $row->is_use);?>
					</td>
					<td>
						<a class="edit" href="cooperation/edit/<?php print $row->cooperation_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="cooperation/delete/<?php print $row->cooperation_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						
					</td>
				</tr>
				<?php endforeach; ?>
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