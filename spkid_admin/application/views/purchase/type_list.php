<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'purchase/type';
		function search(){
			listTable.filter['purchase_type_name'] = $.trim($('input[type=text][name=purchase_type_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">采购类型列表</div>
		<div class="blank5"></div>
		<div class="button_row">
			<input type="button" class="am-btn am-btn-primary" value="新增" onclick="location.href='purchase/add_type';"; />
		</div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			采购类型名称：<input type="text" class="ts" name="purchase_type_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="450px">采购类型名称</th>
				  <th>启用</th>
			      <th>创建时间</th>
				  <th width="180px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td><?php print $row->purchase_type_name; ?></td>
					<td><?php print $row->is_use == 1 ? '可用' : '停用'; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
			      		<?php if ($row->in_use == 1): ?>编辑 | 删除
			      		<?php else: ?>
						<a href="purchase/edit_type/<?php print $row->purchase_type_id; ?>" title="编辑">编辑</a> |
			        	<a href="purchase/delete_type/<?php print $row->purchase_type_id; ?>" onclick="return confirm('确定删除？')" title="删除">删除</a>
			        	<?php endif; ?>
			      </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="8" class="bottomTd"> </td>
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