<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
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
		<th>关联方式</th>
		<th width="120px;">操作</th>
	</tr>
	<?php if($link_product):?>
	<tr class="row">
		<td colspan=8>该商品关联以下商品</td>
	</tr>
	<?php foreach($link_product as $r): ?>
	<tr class="row link_<?php print $r->link_id?>">
		<td>
		<?php print $r->product_id; ?>
		</td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print $r->sub_total==-2?'无限':$r->sub_total; ?></td>
		<td><?php print toggle_link('product_api/toggle_link','is_bothway', $r->link_id, $r->is_bothway, $yes_exp='双向关联', $no_exp='单向关联');?></td>
		<td>
			<a href="javascript:remove_link(<?php print $r->link_id;?>);" title="移除">移除</a>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php endif;?>
	<?php if($link_by_product):?>
	<tr class="row">
		<td colspan=8>该商品被以下商品关联</td>
	</tr>
	<?php foreach($link_by_product as $r): ?>
	<tr class="row link_<?php print $r->link_id?>">
		<td><?php print $r->product_id; ?></td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print $r->sub_total==-2?'无限':$r->sub_total; ?></td>
		<td><?php print toggle_link('product_api/toggle_link','is_bothway', $r->link_id, $r->is_bothway, $yes_exp='双向关联', $no_exp='单向关联');?></td>
		<td>
			<a href="javascript:remove_link(<?php print $r->link_id;?>); return false;" title="移除">移除</a>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php endif;?>
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>