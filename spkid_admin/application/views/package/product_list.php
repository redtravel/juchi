<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top: 0">
	<tr>
		<td colspan=9 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">商品编号</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>售价</th>
		<th>库存</th>
		<th>默认颜色</th>
		<th>排序</th>
		<th width="120px;">操作</th>
	</tr>
	<?php foreach($package_product as $area): ?>
	<tr class="row">
		<td colspan=9><?php print $area['area_name'];?></td>
	</tr>
	<?php foreach($area['product_list'] as $p): ?>
	<tr class="row rec_<?php print $p->rec_id?>">
		<td>
		<?php print $p->product_id; ?>
		</td>
		<td><?php print $p->product_sn; ?></td>
		<td><?php print $p->product_name; ?></td>
		<td><?php print $p->provider_productcode; ?></td>
		<td><?php print $p->shop_price; ?></td>
		<td><?php print "[{$p->sub_gl}][{$p->sub_consign}]";?></td>
		<td><?php print $p->default_color_name; ?></td>
		<td><?php print $perms['edit'] ? edit_link('package/edit_product_field', 'sort_order', $p->rec_id, $p->sort_order) : $p->sort_order;?></td>
		<td>
			<?php if($perms['edit']):?>
			<a href="javascript:remove_product(<?php print $p->rec_id;?>);" title="移除">移除</a>
			<?php endif;?>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php endforeach;?>
	
	<tr>
		<td colspan=9 class="bottomTd"></td>
	</tr>
</table>