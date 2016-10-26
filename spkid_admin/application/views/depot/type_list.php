<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depotio/type';
		function search(){
			listTable.filter['depot_type_name'] = $.trim($('input[type=text][name=depot_type_name]').val());
			listTable.loadList();
		}

		function check_confirm_del(depot_type_id)
		{
			$.ajax({
	            url: '/depotio/check_delete_type',
	            data: {is_ajax:1,depot_type_id:depot_type_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(depot_type_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(depot_type_id)
		{
			var depot_type_name = $.trim($('input[type=text][name=depot_type_name]').val());
			$.ajax({
	            url: '/depotio/proc_delete_type',
	            data: {is_ajax:1,depot_type_id:depot_type_id,depot_type_name:depot_type_name, rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	document.getElementById('listDiv').innerHTML = result.content;
	                }
	            }
	        });
		    return false;
		}

		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">出入库类型管理 &gt;&gt; 出入库类型列表</span>
		<?php if (check_perm('dt_edit')): ?>
		<span class="r">
			<a href="depotio/add_type" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			出入库类型名称或编号：<input type="text" class="ts" name="depot_type_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="250px">出入库类型编号</th>
				  <th>出入库类型名称</th>
				  <th>出入库方向</th>
				  <th>系统定制</th>
				  <th>启用</th>
			      <th>创建时间</th>
				  <th width="180px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td><?php print $row->depot_type_code; ?></td>
			    	<td><?php print $row->depot_type_name; ?></td>
			    	<td><?php print $row->depot_type_out == 1 ? '出库' : '入库'; ?></td>
			    	<td><?php print $row->depot_type_special == 1 ? '从采购单入库' : ($row->depot_type_special == 2 ? '从出库单入库' : '通用'); ?></td>
					<td><?php print $row->is_use == 1 ? '可用' : '停用'; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
						<a href="depotio/edit_type/<?php print $row->depot_type_id; ?>" title="编辑" class="edit"></a>
						<?php if (check_perm('dt_edit')): ?>
                    	<a onclick="check_confirm_del('<?php print $row->depot_type_id; ?>');return false;" href="#" title="删除" class="del"></a>
			      		<?php endif; ?>
			      </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="8" class="bottomTd"> </td>
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