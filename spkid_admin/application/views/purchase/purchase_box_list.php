<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/purchase_box/index';
		function search(){
			listTable.filter['purchase_code'] = $.trim($('input[type=text][name=purchase_code]').val());
			listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
			listTable.filter['start_time'] = $.trim($('input[type=text][name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[type=text][name=end_time]').val());
			listTable.filter['user_name'] = $.trim($('input[type=text][name=user_name]').val());
			listTable.loadList();
		}
	    $(function(){
            $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
            $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
      });
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">仓库管理 &gt;&gt; 收货箱列表</span>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			采购单号：<input type="text" class="ts" name="purchase_code" value="<?=$purchase_code?>" style="width:120px;" />
            商品款号：<input type="text" class="ts" name="product_sn" style="width:90px;">
            收件开始时间：<input type="text" class="ts" name="start_time" style="width:80px;">
            收件结束时间：<input type="text" class="ts" name="end_time" style="width:80px;">
            收件人：<input type="text" class="ts" name="user_name" style="width:80px;">

			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="150px">采购单号</th>
				  <th>箱号</th>
				  <th>总件数</th>
				  <th>已上架件数</th>
				  <th>收件人</th>
				  <th>收件开始时间</th>
				  <th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
                    <td><?=$row->purchase_code?></td>
					<td><?=$row->box_code?></td>
					<td><?=$row->product_number?></td>
					<td><?=$row->product_shelve_num?></td>
					<td><?=$row->real_name?></td>
					<td><?=$row->scan_start_time?></td>
					<td>
					    <a href="/purchase_box/box_product/<?=$row->box_id?>">查看商品</a>
					</td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="7" class="bottomTd"> </td>
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
