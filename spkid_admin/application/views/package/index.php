<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		$(function(){
			$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'package/index';
		function search(){
			listTable.filter['package_name'] = $.trim($('input[type=text][name=package_name]').val());
			listTable.filter['package_type'] = $.trim($('select[name=package_type]').val());
			listTable.filter['package_status'] = $.trim($('select[name=package_status]').val());
			listTable.filter['is_recommend'] = $.trim($('select[name=is_recommend]').val());
			listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
			listTable.filter['start_time'] = $.trim($('input[type=text][name=start_time]').val());
			listTable.filter['index_time'] = $.trim($('input[type=text][name=index_time]').val());
			
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">礼包列表</span><span class="r"><a href="package/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			<select name='package_type'>
				<option value='-1'>礼包类型</option>
				<?php foreach($all_type as $type_id=>$type_name) print "<option value='{$type_id}'>{$type_name}</option>"; ?>
			</select>
			<select name='package_status'>
				<option value="-1">礼包状态</option>
				<option value="0">未启用</option>
				<option value="1">已启用</option>
				<option value="2">已停用</option>
			</select>
			<select name='is_recommend'>
				<option value="-1">是否推荐</option>
				<option value="0">不推荐</option>
				<option value="1">已推荐</option>
			</select>
			礼包名称：<input type="text" class="ts" name="package_name" value="" style="width:60px;" />
			商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:80px;" />
			礼包期间：<input type="text" class="ts" name="start_time" value="" style="width:80px;" />
			至<input type="text" class="ts" name="end_time" value="" style="width:80px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">
						<a href="javascript:listTable.sort('p.package_id', 'DESC'); ">ID<?php echo ($filter['sort_by'] == 'p.product_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>礼包名称</th>
					<th width="80px">礼包类型</th>
					<th width="160px">期间</th>
					<th width="80px">创建时间</th>
					<th width="80px">启用时间</th>
					<th width="80px">停用时间</th>
					<th width="50px">状态</th>
					<th width="50px">推荐</th>
					<th width="50px">排序</th>
					<th width="80px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->package_id; ?></td>
					<td><?php print $row->package_name; ?></td>
					<td><?php print $all_type[$row->package_type];?></td>
					<td><?php print substr($row->start_time, 0, 10).'至'.substr($row->end_time, 0, 10);?></td>
					<td><?php print substr($row->create_date, 0, 10);?></td>
					<td><?php print $row->package_status>0?substr($row->check_date, 0, 10):'未启用'?></td>
					<td><?php print $row->package_status>1?substr($row->over_date, 0, 10):'未停用'?></td>
					<td><?php print $all_status[$row->package_status];?></td>
					<td><?php print toggle_link('package/toggle','is_recommend',$row->package_id, $row->is_recommend);?></td>
					<td><?php print edit_link('package/edit_field', 'sort_order', $row->package_id, $row->sort_order);?></td>
					<td>
						<a class="edit" href="package/edit/<?php print $row->package_id; ?>" title="编辑"></a>
						<?php if($row->package_status==0 && $perm_delete):?>
						<a class="del" href="javascript:void(0)" rel="package/delete/<?php print $row->package_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="12" class="bottomTd"> </td>
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