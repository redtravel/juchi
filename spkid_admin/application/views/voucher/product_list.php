<table class="dataTable" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=10 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">
			商品编号
		</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>分类</th>
		<th>品牌</th>
		<th>售价</th>
		<th>库存</th>
		<th>操作</th>
	</tr>
	<?php foreach($product_list as $r): ?>
	<tr class="row pro_<?php print $r->product_id?>">
		<td>
			<?php print form_hidden('product_ids[]', $r->product_id);?>
			<?php print $r->product_id; ?>
		</td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->category_name; ?></td>
		<td><?php print $r->brand_name; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print "[{$r->sub_gl}][{$r->sub_consign}]"; ?></td>
		<td>
		<?php if(!isset($perms) || $perms['edit']): ?>
		<a href="javascript:remove_product('<?php print $r->product_id?>');">删除</a>
		<?php endif;?>
		</td>
	</tr>
	<?php endforeach; ?>

	<?php if(!$product_list):?>
	<tr class="row">
		<td colspan=10>
			无记录
		</td>
	</tr>
	<?php endif;?>
	
	<tr>
		<td colspan=10 class="bottomTd"></td>
	</tr>
</table>