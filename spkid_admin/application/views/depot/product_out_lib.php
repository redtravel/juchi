			<table id="tb_product_out" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="10" class="topTd"> </td>
				</tr>
				<tr class="row head">
					<th width="280px"><input type="checkbox" id="depot_all_check" onclick="check_all_sel_depot(this)" />商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>条码</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>批次</th>
					<th>仓库</th>
					<th>储位</th>
					<th>完成数</th>
					<th>数量</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row <?php if($row->product_number > $row->product_finished_number): ?>tr_bg_yellow<?php elseif($row->product_number < $row->product_finished_number):?>tr_bg_green<?php endif;?> " >
					<td><input type="checkbox" id="depot_check_<?php print $row->depot_out_sub_id; ?>" /><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print /*$row->provider_price.' | '.*/$row->shop_price; ?></td>
					<td><?php print $row->provider_barcode; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print $row->batch_code; ?></td>
					<td><?php print $row->depot_name; ?></td>
					<td><?php print $row->location_name; ?></td>
					<td><?php print $row->product_finished_number; ?></td>
					<td><span style="display:-moz-inline-box; display:inline-block; width:100px;">
                                                <!--可出库数：
                                                <?php //print $row->real_num+$row->product_number; ?>
                                                -->
                                        <?php print $row->product_number; ?>&nbsp;</span>
                                            <!--
                                            <input type="text" size="4" id="depot_num_<?php //print $row->depot_out_sub_id; ?>" value="<?php //print $row->product_number; ?>" /></td>
                                            -->
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="10" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="purchase_btn">
			<input type="button" class="r" name="del_p" id="del_p" value="删除勾选商品" onclick="del_sel_depot()" <?php echo !empty($goods_list)?'':'disabled' ?> />
			<!--
			<input type="button" class="r" name="edit_p" id="edit_p" value="提交数量更改" onclick="update_sel_depot()" <?php echo !empty($goods_list)?'':'disabled' ?> />
			-->
			</div>
			<div class="page">
				<?php include(APPPATH.'views/common/page_depot.php') ?>
			</div>