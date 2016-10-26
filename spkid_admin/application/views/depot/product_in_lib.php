			<table class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="280px">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>条码</th>
					<th>颜色|尺码</th>
					<th>包装名称</th>
					<th>批次</th>
					<th>最大入库数</th>
					<th>完成数</th>
					<th>入库详细</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->provider_barcode; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?>|<?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td class="editable"><?php print $row->package_name; ?></td>
					<td><?php print $row->batch_code; ?></td>
					<td><?php print $row->max_num; ?></td>
					<td><?php print $row->product_finished_number; ?></td>
					<td>
					<div id="proddiv_<?php print $row->sub_id; ?>">
					<?php $n = 0; foreach($row->item as $item): ?>
					<p style="margin-top:3px;" id="product_p_<?php print $item->depot_in_sub_id; ?>"><?php print $item->depot_name; ?>--<?php print $item->location_name; ?>:<input style="margin-left:6px;margin-right:6px;" type="text" size="4" id="depot_num_<?php print $item->depot_in_sub_id; ?>" value="<?php print $item->product_number; ?>" /><a href="#" onclick="del_sel_product('<?php print $item->depot_in_sub_id; ?>');return false;" title="删除">删除</a>
					<?php if ($n ==0): $n++; ?>&nbsp;<a href="#" onclick="add_prod_p('<?php print $row->sub_id ?>');return false;" value="add" /><!--<img src="<?php /*echo $imagedomain*/ ?>/add.gif" border="0" />--><span class="addForGif"></span></a><?php endif; ?>
					</p>
					<?php endforeach; ?>
					</div>
					<input type="hidden" name="addprodnum_<?php print $row->sub_id; ?>" id="addprodnum_<?php print $row->sub_id; ?>" value="0" />
					</td>

				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="8" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="purchase_btn">
			<input type="button" class="r" name="edit_p" id="edit_p" value="提交数量更改" onclick="update_product_in()" <?php echo (!empty($goods_list))?'':'disabled' ?> />
			</div>
			
			<?php if(!$revisable): ?>
			<script type="text/javascript">
			<!--
			$(function(){
				$("#edit_p").hide();
				$(".addForGif").hide();
				$("input[id^='depot_num_']").attr("readOnly","readOnly");
			});
			//-->
			</script>
			<?php endif; ?>
			
			<div class="page">
				<?php include(APPPATH.'views/common/page_depot.php') ?>
			</div>
