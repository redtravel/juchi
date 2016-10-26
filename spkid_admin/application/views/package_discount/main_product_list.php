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
		<th>规格</th>
		<th width="120px;">操作</th>
	</tr>
	<?php if(!empty($main_product)){ foreach ($main_product as $main) { ?>
		<tr class="row rec_<?php print $main->dis_pro_id;?>" >
			<td><?php print $main->product_id; ?></td>
			<td><?php print $main->product_sn; ?></td>
			<td><?php print $main->product_name; ?></td>
			<td><?php print $main->provider_productcode; ?></td>
			<td><?php print $main->shop_price; ?></td>
			<td><?php print "[{$main->sub_gl}][{$main->sub_consign}]"; ?></td>
			<td><?php print $main->size_name; ?></td>
			<td>
				<?php if($perms['edit']): ?>
				<a href="javascript:remove_discount_product(<?php print $main->pag_dis_id; ?>,<?php print $main->dis_pro_id; ?>);" title="移除">移除</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php } }?>	
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>