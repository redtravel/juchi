<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'product_cost_price/search';
		function search(){
			listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
			listTable.filter['batch_code'] = $.trim($('input[type=text][name=batch_code]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
            listTable.filter.page = '1';
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">商品管理 >> 商品成本价格</span> </div>

		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			商品款号：<input type="text" class="ts" name="product_sn"   value="" style="width:100px;" />
			批次号：  <input type="text" class="ts" name="batch_code"   value="" style="width:100px;" />
			<?php print form_dropdown('brand_id',get_pair($all_brand,'brand_id','brand_name', array(''=>'品牌'))); ?>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr class="row">
					<th>商品款号</th>
					<th>商品名称</th>
					<th>批次号</th>
					<th>货号</th>
					<th>供应商名称(合作方式)</th>
					<th>baby价/市场价</th>
					<th>成本价</th>				
					<th>代销价</th>
					<th>代销率</th>
					<th>税率</th>
					<th>录入人</th>
					<th>录入时间</th>
					<th>更新人</th>
					<th>更新时间</th>				
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?></td>
					<td><?php print $row->product_name; ?></td>
					<td><?php print $row->batch_code; ?></td>
					<td><?php print $row->provider_productcode; ?></td>
                    <td><?php print $row->provider_name; ?>(<?php print $row->cooperation_name; ?>)</td>
		    <td><?php print $row->product_price." / ".$row->market_price; ?></td>
                    <td>
                        <?php if ($row->cost_price==0 || !$perm_edit): ?>
                            <?php print $row->cost_price; ?>
                        <?php else: ?>
                            [<?php print edit_link('product_cost_price/edit_cost_price', 'cost_price', $row->product_cost_id, $row->cost_price);?>]
                        <?php endif; ?>
					</td>
                    <td>
                      <?php if ($row->consign_price==0 || !$perm_edit): ?>
                            <?php print $row->consign_price; ?>
                        <?php else: ?>
                            [<?php print edit_link('product_cost_price/edit_cost_price', 'consign_price', $row->product_cost_id, $row->consign_price);?>]
                        <?php endif; ?>
					</td>
                    <td>
                      <?php if ($row->consign_rate==0 || !$perm_edit): ?>
                            <?php print $row->consign_rate; ?>
                        <?php else: ?>
                            [<?php print edit_link('product_cost_price/edit_cost_price', 'consign_rate', $row->product_cost_id, $row->consign_rate);?>]
                        <?php endif; ?>
					</td>
                    <td>
                      <?php if (!$perm_edit): ?>
                            <?php print $row->product_cess; ?>
                        <?php else: ?>
                            [<?php print edit_link('product_cost_price/edit_cost_price', 'product_cess', $row->product_cost_id, $row->product_cess);?>]
                        <?php endif; ?>
					</td>
                    <td><?php print $row->creat_name; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td><?php print $row->update_name; ?></td>
					<td><?php print $row->update_time; ?></td>
					
				</tr>
				<?php endforeach; ?>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>