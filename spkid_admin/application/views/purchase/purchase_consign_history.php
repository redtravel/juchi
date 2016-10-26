<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		$(function(){			
			$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
    	});
    	
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/purchase_consign/history';
		function search() {
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
                        listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">代销采购操作记录</span>
		<?php if (check_perm('purchase_consign_view')): ?>
		<span class="r">
			<a href="purchase_consign/autio" class="add">代销采购</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			<?php print form_dropdown('provider_id',get_pair($all_provider,'provider_id','provider_name', array(''=>'供应商'))); ?>
                            订单号:<input type="text" class="textbox" name="order_sn"/>
                            <input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		
		<div id="listDiv">
<?php endif; ?>

			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="5" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>序列</th>
				    <th>批次号</th>
				    <th>采购单编码</th>
				    <th>供应商名称</th>
				    <th>品牌名称</th>
				    <th>开始时间</th>
				    <th>结束时间</th>
				    <th>创建人</th>
				    <th>创建时间</th>
                                    <th>详情</th>
				    <!--<th>操作</th>-->
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td align="center"><?php print $row->id; ?></td>
					<td align="center"><?php print $row->batch_code; ?></td>
					<td align="center">
					<?php if(!empty($row->purchase_id) && $row->purchase_id>0): ?>
					<a href="/purchase/edit_product/<?php print $row->purchase_id?>" target="_blank"><?php print $row->purchase_code; ?></a>
					<?php else: ?>
					<?php print $row->purchase_code; ?>
					<?php endif; ?>
					</td>
					<td align="center"><?php print $row->provider_name; ?></td>
					<td align="center"><?php print $row->brand_name; ?></td>
					<td align="center"><?php print $row->start_time; ?></td>
					<td align="center"><?php print $row->end_time; ?></td>
					<td align="center"><?php print $row->create_name; ?></td>
					<td align="center"><?php print $row->create_date; ?></td>
					<td align="center"><a href="/purchase_consign/show_consign_detail/<?php print $row->purchase_code?>" target="_blank">显示详情</a></td>
					<!--<td align="center"><a href="public/import/consign_purchase/consign_<?php print $row->id; ?>.xml">导出_右键另存</a></td>-->
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="5" class="bottomTd"> </td>
				</tr>
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