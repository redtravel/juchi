<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depot/location_list';
		function search(){
			listTable.filter['location_name'] = $.trim($('input[type=text][name=location_name]').val());
			listTable.filter['depot_id'] = $.trim($('select[name=depot_id]').val());
			listTable.loadList();
		}

		function check_confirm_del(location_id)
		{
			$.ajax({
	            url: '/depot/check_delete_location',
	            data: {is_ajax:1,location_id:location_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(location_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(location_id)
		{
			var location_name = $.trim($('input[type=text][name=location_name]').val());
			var depot_id = $.trim($('input[type=text][name=depot_id]').val());
			$.ajax({
	            url: '/depot/proc_delete_location',
	            data: {is_ajax:1,location_id:location_id,depot_id:depot_id,location_name:location_name,rnd : new Date().getTime()},
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
		<span class="l">储位管理 &gt;&gt; 储位列表</span>
		<?php if (check_perm('location_edit')): ?>
		<span class="r">
			<a href="depot/add_location" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			储位名称或编码：<input type="text" class="ts" name="location_name" value="" style="width:100px;" />
			<?php print form_dropdown('depot_id',$depot_list);?>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="150px">储位编码</th>
			      <th>储位名称</th>
			      <th>仓库名称</th>
				  <th>启用</th>
			      <th>创建时间</th>
				  <th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td>&nbsp;<?php print $row->location_code1."-".$row->location_code2."-".$row->location_code3."-".$row->location_code4."-".$row->location_code5; ?></td>
					<td>&nbsp;<?php print $row->location_name; ?></td>
					<td>&nbsp;<?php print $row->depot_name; ?></td>
					<td>&nbsp;<?php print $row->is_use == 1 ? '可用' : '停用'; ?></td>
					<td>&nbsp;<?php print $row->create_date; ?></td>
					<td>
						<a href="depot/edit_location/<?php print $row->location_id; ?>" title="编辑" class="edit"></a>
						<?php if (check_perm('location_edit')): ?>
                    	<a onclick="check_confirm_del('<?php print $row->location_id; ?>');return false;" href="#" title="删除" class="del"></a>
			      		<?php endif; ?>
			      </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="6" class="bottomTd"> </td>
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