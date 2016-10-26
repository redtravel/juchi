			<table class="dataTable" cellpadding=0 cellspacing=0 >
				<tr>
					<td colspan="10" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="150px"><input type="checkbox" id="purchase_all_check" onclick="check_all_sel_purchase(this)" />商品款号</th>
					<th>商品名称</th>
					<th>供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>颜色</th>
					<th>尺码</th>
					<th width="60px;">数量</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><input type="checkbox" id="purchase_check_<?php print $row->purchase_sub_id; ?>" /><?php print $row->product_sn; ?></td>
					<td><?php print $row->product_name; ?></td>
					<td><?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><input type="text" size="4" id="purchase_num_<?php print $row->purchase_sub_id; ?>" value="<?php print $row->product_number; ?>" /></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="10" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="purchase_btn">
			<input type="button" class="r" name="del_p" id="del_p" value="删除勾选商品" onclick="del_sel_purchase()" <?php echo !empty($goods_list)?'':'disabled' ?> />
			<input type="button" class="r" name="edit_p" id="edit_p" value="提交数量更改" onclick="update_sel_purchase()" <?php echo !empty($goods_list)?'':'disabled' ?> />
			</div>
