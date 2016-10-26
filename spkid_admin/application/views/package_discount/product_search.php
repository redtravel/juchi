<table class="dataTable" cellpadding=0 cellspacing=0 rel="3">
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
	</tr>
	
	<?php foreach($list as $r): ?>
	<tr class="row">
		<td>
			<label><input type="checkbox" name="product_id" value="<?php print $r->product_id; ?>" /><?php print $r->product_id; ?></label>
		</td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print "[{$r->sub_gl}][{$r->sub_consign}]"; ?></td>
		<td><?php print $r->size_name; ?></td>
	</tr>
	<?php endforeach; ?>
	
	<tr class="row">
		<td colspan=8>
			<input type="button" name="mysubmit" value="添加" onclick="add_discount_product(<?php print $pag_dis_id;?>,<?php print $dis_pro_type;?>)" />
		</td>	
	</tr>
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>
<div class="page">
	<?php include(APPPATH.'views/common/page.php') ?>
</div>