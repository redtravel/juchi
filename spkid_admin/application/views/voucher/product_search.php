<table class="dataTable" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=9 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">
			<label><input type="checkbox" name="check_all_box" onclick="check_all();">商品编号</label>
		</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>分类</th>
		<th>品牌</th>
		<th>售价</th>
		<th>库存</th>
	</tr>
	<?php foreach($list as $r): ?>
	<tr class="row">
		<td>
			<label><input type="checkbox" name="product_id" value="<?php print $r->product_id; ?>" /><?php print $r->product_id; ?></label>
		</td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->category_name; ?></td>
		<td><?php print $r->brand_name; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print "[{$r->sub_gl}][{$r->sub_consign}]"; ?></td>
	</tr>
	<?php endforeach; ?>

	<?php if(!$list):?>
	<tr class="row">
		<td colspan=8>
			无记录
		</td>
	</tr>
	<?php else:?>
	<tr class="row">
		<td colspan=8 style="text-align: left;">
			<input type="button" name="mysubmit" value="添加" onclick="add_product()" />
		</td>
	</tr>
	<?php endif;?>
	
	<tr>
		<td colspan=9 class="bottomTd"></td>
	</tr>
</table>
<div class="page">
	<?php include(APPPATH.'views/common/page.php') ?>
</div>