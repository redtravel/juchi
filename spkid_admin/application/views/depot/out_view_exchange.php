<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>

	<div class="main">
		<div class="main_title"><span class="l">调仓管理 &gt;&gt; 调仓出库商品详细</span> &nbsp;单号：<?php print $exchange_info->exchange_code; ?> <span class="r">[ <a href="/exchange/exchange_list">返回列表 </a>]</span></div>
		<div class="produce">
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/exchange/edit_out_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>出库商品</span></li>
	         <?php if ($exchange_info->out_audit_admin > 0): ?>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_in_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>入库商品</span></li>
	         <?php endif; ?>
	     </ul>

		<div class="blank5"></div>
		<div class="pc base">
		<div id="goodsDiv">
			<table class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="10" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="280px">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>进价|售价</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>仓库</th>
					<th>储位</th>
					<th width="260px;">数量</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->provider_price.' | '.$row->shop_price; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print $row->depot_name; ?></td>
					<td><?php print $row->location_name; ?></td>
					<td><?php print $row->product_number; ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="10" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
		</div>
	</div>
	<input type="hidden" name="exchange_id" id="exchange_id" value="<?php print $exchange_info->exchange_id; ?>" />
	<input type="hidden" name="source_depot_id" id="source_depot_id" value="<?php print $exchange_info->source_depot_id; ?>" />
<?php include_once(APPPATH.'views/common/footer.php'); ?>
