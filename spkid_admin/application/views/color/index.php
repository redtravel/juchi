<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'color/index';
		function search(){
			listTable.filter['color_name'] = $.trim($('input[type=text][name=color_name]').val());
			listTable.filter['group_id'] = $.trim($('select[name=group_id]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">颜色列表</span><span class="r"><a href="color/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			颜色名称：<input type="text" class="ts" name="color_name" value="" style="width:100px;" />
			颜色组：<?php print form_dropdown('group_id', get_pair($all_group,'group_id','group_name'));?>
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
						<a href="javascript:listTable.sort('c.color_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'c.color_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>颜色名称</th>
					<th>颜色编码</th>
					<th>颜色码</th>
					<th>颜色组</th>
					<th>颜色图片</th>
					<th>
						<a href="javascript:listTable.sort('c.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'c.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>启用</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->color_id; ?></td>
					<td><?php print $row->color_name; ?></td>
					<td><?php print $row->color_sn; ?></td>
					<td>	
						<?php if($row->color_color):?>
						<span style="display:inline-block;width:15px; height:15px; margin-right:10px; background-color:<?php print $row->color_color?>"> </span><?php print $row->color_color?>
						<?php else:?>
						未设定
						<?php endif;?>
					</td>
					<td><?php print $row->group_name; ?></td>
					<td>
                        <img src="<?php print PUBLIC_DATA_IMAGES . $row->color_img; ?>" />
					</td>
					<td>
						<?php print edit_link('color/edit_field', 'sort_order', $row->color_id, $row->sort_order);?>
					</td>
					<td>
						<?php print toggle_link('color/toggle','is_use',$row->color_id, $row->is_use);?>
					</td>
					<td>
						<a class="edit" href="color/edit/<?php print $row->color_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="color/delete/<?php print $row->color_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						
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