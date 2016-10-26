	<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
	var depot_url = '/exchange/flash_exchange_in';
		var depot_page = '';
		var depot_name = '<?php print $depot_name; ?>'
	</script>
	<div class="main">
		<div class="main_title"><span class="l">调仓管理 &gt;&gt; 调仓入库商品详细</span> &nbsp;单号：<?php print $exchange_info->exchange_code; ?><span class="r">[ <a href="/exchange/exchange_list">返回列表 </a>]</span></div>
		<div class="produce">
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>基础信息</span></li>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_out_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>出库商品</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/exchange/edit_in_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>入库商品</span></li>
	     </ul>

		<div class="blank5"></div>
		<div class="pc base">
		<div id="goodsDiv">
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
					<th width="200px;">出库</th>
					<th width="300px;">入库</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td>
					<?php foreach ($row->out_item as $out_item): ?>
					<p><span style="display:-moz-inline-box; display:inline-block; width:200px;"><?php print $out_item->depot_name; ?>&nbsp;|&nbsp;<?php print $out_item->location_name;?>&nbsp;出库数:<?php print $out_item->product_number; ?>&nbsp;</span></p>
					<?php endforeach; ?>
					</td>
					<td>
					<div id="proddiv_<?php print $row->sub_id; ?>">
					<?php $n = 0; foreach($row->in_item as $in_item): ?>
					<p style="margin-top:3px;" id="product_p_<?php print $in_item->exchange_leaf_id; ?>"><?php print $in_item->depot_name; ?>--<?php print $in_item->location_name; ?>&nbsp;入库数：<?php print $in_item->product_number; ?>
					</p>
					<?php endforeach; ?>
					</div>
					<input type="hidden" name="addprodnum_<?php print $row->sub_id; ?>" id="addprodnum_<?php print $row->sub_id; ?>" value="1" />
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div></div>
	</div>
	<input type="hidden" name="exchange_id" id="exchange_id" value="<?php print $exchange_info->exchange_id; ?>" />
	<input type="hidden" name="dest_depot_id" id="dest_depot_id" value="<?php print $exchange_info->dest_depot_id; ?>" />
<?php include_once(APPPATH.'views/common/footer.php'); ?>
