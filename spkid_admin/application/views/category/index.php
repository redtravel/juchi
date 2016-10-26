<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
	<div class="main">
		<div class="main_title"><span class="l">分类列表</span><span class="r"><a href="category/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">编号</th>
					<th width="150px">分类代号</th>
					<th>分类名称</th>
					<th>商品大类</th>
					<th>排序号</th>
					<th>启用</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->category_id; ?></td>
					<td><?php echo $row->cate_code;?></td>
					<td style="text-align:left;"><?php echo  $row->level_space, $row->category_name?></td>
					<td><?php echo $row->name?></td>
					<td>
						<?php print edit_link('category/edit_field','sort_order',$row->category_id,$row->sort_order); ?>
					</td>
					<td width="50px" align="center">
						<?php print toggle_link('category/toggle','is_use',$row->category_id,$row->is_use);?>
					</td>
					<td>
						<a class="edit" href="category/edit/<?php print $row->category_id; ?>" title="编辑"></a>
						<?php if($perm_delete):?>
						<a class="del" href="javascript:void(0)" rel="category/delete/<?php print $row->category_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>