<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top: 0">
	<tr>
		<td colspan=8 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">商品编号</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>售价</th>
		<th>库存</th>
		<th>商品折扣('-1'或者'50%')</th>
		<th>规格</th>
		<th>排序</th>
		<th width="120px;">操作</th>
	</tr>
	<?php if(!empty($discount_product)) { foreach($discount_product as $p): ?>
	<tr class="row rec_<?php print $p->dis_pro_id?>">
		<td><?php print $p->product_id; ?></td>
		<td><?php print $p->product_sn; ?></td>
		<td><?php print $p->product_name; ?></td>
		<td><?php print $p->provider_productcode; ?></td>
		<td><?php print $p->shop_price; ?></td>
		<td><?php print "[{$p->sub_gl}][{$p->sub_consign}]";?></td>
		<td><?php print $perms['edit'] ? edit_link('package_discount/edit_product_field', 'discount_price', $p->dis_pro_id, $p->discount_price) : $p->discount_price;?></td>
		<td><?php print $p->size_name; ?></td>
		<td><?php print $perms['edit'] ? edit_link('package_discount/edit_product_field', 'sort_order', $p->dis_pro_id, $p->sort_order) : $p->sort_order;?></td>
		<td>
			<?php if($perms['edit']):?>
			<a href="javascript:remove_discount_product(<?php print $p->pag_dis_id; ?>,<?php print $p->dis_pro_id;?>);" title="移除">移除</a>
			<?php endif;?>
		</td>
	</tr>
	<?php endforeach; }?>
	
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>