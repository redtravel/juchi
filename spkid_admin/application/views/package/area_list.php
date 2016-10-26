<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top: 0">
	<tr>
		<td colspan=8 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="50px">ID</th>
		<th width="120px">区域名称</th>
		<th>区域类型</th>
		<th>最小购买数量</th>
		<th>排序</th>
		<th width="200px;">操作</th>
	</tr>
	<?php foreach($area_list as $area): ?>
	<tr class="row area_<?php print $area->area_id;?>">
		<td>
		<?php print $area->area_id; ?>
		</td>
		<td><?php print $perms['edit']? edit_link('package/edit_area_field', 'area_name', $area->area_id, $area->area_name) : $area->area_name; ?></td>
		<td><?php print $area->area_type==1?'商品区域':'自定义区域'; ?></td>
		<td><?php print $perms['edit']? edit_link('package/edit_area_field', 'min_number', $area->area_id, $area->min_number) : $area->min_number; ?></td>
		<td><?php print $perms['edit']? edit_link('package/edit_area_field', 'sort_order', $area->area_id, $area->sort_order) : $area->sort_order; ?></td>
		<td>
			<?php if($perms['edit']):?>
			<a href="javascript:remove_area(<?php print $area->area_id;?>);" title="删除">删除</a>
			<?php if($area->area_type==2):?>
			<a href="javascript:edit_area(<?php print $area->area_id;?>);" title="编辑">编辑自定义区域</a>
			<?php endif;?>
			<?php endif;?>
		</td>
	</tr>
	<?php if($area->area_type==2):?>
	<tr class="row area_<?php print $area->area_id;?>">
		<td colspan="8" class="area_text_<?php print $area->area_id;?>"><?php print $area->area_text;?></td>
	</tr>
	<?php endif;?>
	<?php endforeach; ?>
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>