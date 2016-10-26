<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript">
	<!--
	function show_diff_only(dom) {
		if(dom.checked) {
			$("#tb_product_out tr").not(".head").not(".tr_bg_yellow").not("tr_bg_green").hide();
		} else {
			$("#tb_product_out tr").show();
		}
	}
	//-->
	</script>
	<div class="main">
		<div class="main_title"><span class="l">出库管理 &gt;&gt; 出库商品详细</span> &nbsp;单号：<?php print $depot_out_info->depot_out_code; ?><span class="r">[ <a href="/depotio/out">返回列表 </a>]</span></div>
		<div class="produce">
		
		<input type="checkbox" onclick="show_diff_only(this);">只显示差异
		
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/depotio/edit_out/<?php print $depot_out_info->depot_out_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/depotio/edit_out_product/<?php print $depot_out_info->depot_out_id; ?>'"><span>出库商品</span></li>
	     </ul>

		<div class="pc base">
		<div id="goodsDiv">
			<table id="tb_product_out" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row head">
					<th width="280px">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>条码</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>有效期</th>
					<th>生产批号</th>
					<th>仓库</th>
					<th>储位</th>
					<th>完成数</th>
					<th>数量</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row <?php if($row->product_number > $row->product_finished_number): ?>tr_bg_yellow<?php elseif($row->product_number < $row->product_finished_number):?>tr_bg_green<?php endif;?> " >
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print /*$row->provider_price.' | '.*/$row->shop_price; ?></td>
					<td><?php print $row->provider_barcode; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print ($row->expire_date == '0000-00-00' || $row->expire_date == '0000-00-00 00:00:00' || $row->expire_date == '')?'无':$row->expire_date; ?></td>
					<td><?php print $row->production_batch; ?></td>
					<td><?php print $row->depot_name; ?></td>
					<td><?php print $row->location_name; ?></td>
					<td><?php print $row->product_finished_number; ?></td>
					<td><span style="display:-moz-inline-box; display:inline-block; "><?php print $row->product_number; ?>&nbsp;</span></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="12" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div></div></div>
		<div style="height: 5px;"></div>
	</div>
	<input type="hidden" name="depot_out_id" id="depot_out_id" value="<?php print $depot_out_info->depot_out_id; ?>" />
<?php include_once(APPPATH.'views/common/footer.php'); ?>
