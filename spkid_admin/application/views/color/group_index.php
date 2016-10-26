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
		listTable.url = 'color/group_index';
		
		function search(){
			listTable.filter['group_name'] = $.trim($('input[type=text][name=group_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">颜色组列表</span><span class="r"><a href="color/group_add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			颜色组名称：<input type="text" class="ts" name="group_name" value="" style="width:100px;" />
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
					<th width="50px">
						<a href="javascript:listTable.sort('g.group_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'g.group_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>颜色组名称</th>
					<th>颜色组图片</th>
					<th>颜色码</th>
					<th>组内颜色</th>
					<th>
						<a href="javascript:listTable.sort('g.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'g.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>启用</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->group_id; ?></td>
					<td><?php print $row->group_name; ?></td>
					<td>
                        <img src="<?php print PUBLIC_DATA_IMAGES . $row->group_img; ?>" />
					</td>
					<td>
						<?php if($row->group_color):?>
						<span style="display:inline-block;width:15px; height:15px; margin-right:10px; background-color:<?php print $row->group_color?>"> </span><?php print $row->group_color?>
						<?php else:?>
						未设定
						<?php endif;?>
					</td>
					<td>
						<?php print isset($row->color_list) ? form_dropdown('color_list', get_pair($row->color_list,'color_id','color_name')):'无';?>
					</td>
					<td>
						<?php print edit_link('color/edit_group_field', 'sort_order', $row->group_id, $row->sort_order);?>
					</td>
					<td>
						<?php print toggle_link('color/toggle_group','is_use',$row->group_id, $row->is_use);?>
					</td>
					<td>
						<a class="edit" href="color/group_edit/<?php print $row->group_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="color/group_delete/<?php print $row->group_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						
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