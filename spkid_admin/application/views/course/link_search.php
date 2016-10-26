<table class="dataTable" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=8 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">
			<label><input type="checkbox" name="ck_check_all" onclick="check_all();">商品编号</label>
		</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>售价</th>
		<th>库存</th>
	</tr>
	
	<?php foreach($list as $r): ?>
	<tr class="row">
		<td>
			<label><input type="checkbox" name="link_product_id" value="<?php print $r->product_id; ?>" /><?php print $r->product_id; ?></label>
		</td>
		<td><?php print $r->product_sn; ?></td>
		<td><?php print $r->product_name; ?></td>
		<td><?php print $r->provider_productcode; ?></td>
		<td><?php print $r->shop_price; ?></td>
		<td><?php print $r->sub_total==-2?'无限':$r->sub_total; ?></td>
	</tr>
	<?php endforeach; ?>
	
	<tr class="row">
		<td colspan=6>
			<label><input type="radio" name="is_bothway" value="0" checked>单向关联</label>
			<label><input type="radio" name="is_bothway" value="1">双向关联</label>
			<input type="button" name="mysubmit" value="关联" onclick="link()" />
		</td>	
	</tr>
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>
<div class="page">
	<?php include(APPPATH.'views/common/page.php') ?>
</div>