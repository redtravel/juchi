<table class="dataTable" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=8 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="90px">
			<label><input type="checkbox" name="check_all_box" onclick="check_all();">商品编号</label>
		</th>
		<th>款号</th>
		<th>名称</th>
		<th>供应商货号</th>
		<th>售价</th>
		<th>库存</th>
		<th>默认颜色</th>
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
		<td>
			<?php if($r->cs_list):?>
			<select name="cs_<?php print $r->product_id?>">
			<?php foreach($r->cs_list as $cs): ?>
				<option value="<?php print $cs['color_id']?>"><?php print $cs['color_name']?></option>	
			<?php endforeach;?>
			</select>
			<?php endif;?>
		</td>
	</tr>
	<?php endforeach; ?>
	
	<tr class="row">
		<td colspan=8>
			区域
			<select name="area_id">
			<?php 
			foreach($area_list as $area){
				if($area->area_type==2) continue;
				print "<option value='{$area->area_id}'>{$area->area_name}</option>";	
			} 
			?>

			</select>
			<input type="button" name="mysubmit" value="添加" onclick="add_product()" />
		</td>	
	</tr>
	<tr>
		<td colspan=8 class="bottomTd"></td>
	</tr>
</table>
<div class="page">
	<?php include(APPPATH.'views/common/page.php') ?>
</div>