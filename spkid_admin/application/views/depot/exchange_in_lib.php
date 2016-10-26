			<table class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="230px">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>批次</th>
					<th width="240px;">出库</th>
					<th width="280px;">入库（<span style="color:red">双击输入框选择储位</span>）</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print $row->batch_code; ?></td>
					<td>&nbsp;
					<?php foreach ($row->out_item as $out_item): ?>
					<p><span style="display:-moz-inline-box; display:inline-block; width:240px;"><?php print $out_item->depot_name; ?>&nbsp;|&nbsp;<?php print $out_item->location_name;?>&nbsp;出库数:<?php print $out_item->product_number; ?>&nbsp;</span></p>
					<?php endforeach; ?>
					</td>
					<td>
					<div id="proddiv_<?php print $row->sub_id; ?>">
					<?php $n = 0; foreach($row->in_item as $in_item): ?>
					<p style="margin-top:3px;" id="product_p_<?php print $in_item->exchange_leaf_id; ?>"><?php print $in_item->depot_name; ?>--<?php print $in_item->location_name; ?>:<input style="margin-left:6px;margin-right:6px;" type="text" size="4" id="depot_num_<?php print $in_item->exchange_leaf_id; ?>" value="<?php print $in_item->product_number; ?>" /><a href="#" onclick="del_sel_product('<?php print $in_item->exchange_leaf_id; ?>');return false;" title="删除">删除</a>
					<?php if ($n ==0): $n++; ?>&nbsp;<a href="#" onclick="add_prod_p('<?php print $row->sub_id ?>','<?php print $row->batch_id; ?>');return false;" value="add" /><!--<img src="<?php /*echo $imagedomain*/ ?>/add.gif" border="0" />用样式显示By Rock--><span class="addForGif"></span></a><?php endif; ?>
					</p>
					<?php endforeach; ?>
					<?php if (empty($row->in_item)): ?>
					<p id="prodp_<?php print $row->sub_id; ?>_0" style="margin-top:3px;">
					    储位:<input type="text" class="textbox" name="prodlocation_<?php print $row->sub_id; ?>_0" id="prodlocation_<?php print $row->sub_id; ?>_0" value="" ondblclick="showLoactionWin(this,'<?php print $exchange_info->dest_depot_id; ?>');" style="width:75px;" />
					    &nbsp;&nbsp;数量:<input type="text" class="textbox" name="prodinnum_<?php print $row->sub_id; ?>_0" id="prodinnum_<?php print $row->sub_id; ?>_0" value="" style="width:35px;" />
					    &nbsp;<a href="#" onclick="insert_sel_product('<?php print $row->sub_id; ?>','0','<?php print $row->batch_id; ?>');return false;" value="add" ><span class="yesForGif"></span></a>&nbsp;
					    <a href="#" onclick="add_prod_p('<?php print $row->sub_id ?>','<?php print $row->batch_id; ?>');return false;" value="add"><span class="addForGif"></span></a>
					</p>
					<?php endif; ?>
					</div>
					<input type="hidden" name="addprodnum_<?php print $row->sub_id; ?>" id="addprodnum_<?php print $row->sub_id; ?>" value="1" />
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="purchase_btn">
			<input type="button" class="r" name="edit_p" id="edit_p" value="提交数量更改" onclick="update_product_in()" <?php echo !empty($goods_list)?'':'disabled' ?> />
			</div>
			<div class="page">
				<?php include(APPPATH.'views/common/page_depot.php') ?>
			</div>