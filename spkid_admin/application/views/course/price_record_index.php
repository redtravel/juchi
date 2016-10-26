<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		$(function(){
			$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$('input[type=text][name=end_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:''});
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'product/price_record_index';
		function search(){
			listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
			listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
			listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
			listTable.filter['create_admin'] = $.trim($('input[type=text][name=create_admin]').val());
			listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['sr_onsale'] = $.trim($('select[name=sr_onsale]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">商品管理 >> 调价记录表</span> </div>

		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
			<select name="category_id">
				<option value="">分类</option>
				<?php foreach($all_category as $category) print "<option value='{$category->category_id}'>{$category->level_space}{$category->category_name}</option>"?>
			</select>
			<?php print form_dropdown('brand_id', get_pair($all_brand,'brand_id','brand_name',array(''=>'品牌')));?>
			开始时间：<input type="text" name="start_date" class="ts" value="" style="width:100px;">
			结束时间：<input type="text" name="end_date" class="ts" value="" style="width:100px;">
			操作人：<input type="text" name="create_admin" class="ts" value="" style="width:100px;">
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
					<th>分类</th>
					<th>品牌</th>
					<th>市场价</th>
					<th>售价</th>				
					<th>操作</th>
					<th>操作时间</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?></td>
					<td><?php print $row->product_name; ?></td>
					<td><?php print $row->category_name; ?></td>
					<td><?php print $row->brand_name; ?></td>
                    <td><?php print $row->market_price; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->admin_name; ?></td>
					<td><?php print $row->create_date; ?></td>
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