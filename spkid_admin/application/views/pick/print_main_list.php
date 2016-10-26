
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan="6" class="topTd"> </td>
	</tr>
	<tr class="row">
		<th width="160px"><input type="checkbox" name="check_all" value="1" checked onclick="switch_check();" />单号</th>
		<th>快递方式</th>
		<th>代收金额</th>
		<th>收货人</th>
		<th>收货地址</th>
	</tr>
	<?php foreach($list as $row): ?>
	<tr class="row" id="row_<?php print $row->sn;?>">
		<td>
			<input type="checkbox" name="sn" value="<?php print $row->sn;?>" checked />
			<?php print $row->sn; ?>
			<input type="hidden" name="id" value="<?php print $row->id;?>" />
			<input type="hidden" name="pick_cell" value="<?php print $row->pick_cell;?>" />
			<input type="hidden" name="code" value="<?php print $row->shipping_code; ?>" />
			<input type="hidden" name="codAmount" value="<?php print number_format($row->codAmount,2,'.',''); ?>" />
			<input type="hidden" name="rcvPerson" value="<?php print $row->consignee; ?>" />
			<input type="hidden" name="rcvAddress" value="<?php print $row->address; ?>" />
			<input type="hidden" name="rcvMobile" value="<?php print trim($row->mobile); ?>" />
			<input type="hidden" name="rcvTel" value="<?php print trim($row->tel); ?>" />
			<input type="hidden" name="bestTime" value="<?php print $row->best_time; ?>" />
			<input type="hidden" name="goods_num" value="<?php print $row->goods_num; ?>" />
			<input type="hidden" name="weight" value="<?php print $row->weight; ?>" />
			<input type="hidden" name="city" value="<?php print $row->city; ?>" />
		</td>
		<td><?php print $row->shipping_name; ?></td>
		<td><?php print $row->codAmount; ?></td>
		<td><?php print $row->consignee; ?></td>
		<td><?php print $row->address; ?></td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<td colspan="6" class="bottomTd"> </td>
	</tr>
</table>
