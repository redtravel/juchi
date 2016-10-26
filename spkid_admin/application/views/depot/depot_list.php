<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depot/depot_list';
		function search(){
			listTable.filter['depot_name'] = $.trim($('input[type=text][name=depot_name]').val());
			listTable.loadList();
		}

		function check_confirm_del(depot_id)
		{
			$.ajax({
	            url: '/depot/check_delete_depot',
	            data: {is_ajax:1,depot_id:depot_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(depot_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(depot_id)
		{
		    var depot_name = $.trim($('input[type=text][name=depot_name]').val());
			$.ajax({
	            url: '/depot/proc_delete_depot',
	            data: {is_ajax:1,depot_id:depot_id, depot_name:depot_name, rnd : new Date().getTime()},
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
		<span class="l">仓库管理 &gt;&gt; 仓库列表</span>
		<?php if (check_perm('depot_edit')): ?>
		<span class="r">
			<a href="depot/add_depot" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			仓库名称：<input type="text" class="ts" name="depot_name" value="" style="width:100px;" />
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
				  <th width="150px">仓库名称</th>
			      <th>地点</th>
				  <th>启用</th>
				  <th>可售</th>
				  <th>合作方式</th>
				  <th>退货仓</th>
				  <th>优先级</th>
			      <th>创建时间</th>
				  <th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
				    <td><?php print $row->depot_name; ?></td>
				    <td><?php print $row->depot_position; ?></td>
				    <td><?php print $row->is_use == 1 ? '可用' : '停用'; ?></td>
				    <td><?php print $row->depot_type == 1 ? '可售' : '不可售'; ?></td>
				    <td><?php print $row->cooperation_name; ?></td>
				    <td><span class="<?php echo $row->is_return == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
				    <td><?php print $row->depot_priority; ?></td>
				    <td><?php print $row->create_date; ?></td>
				    <td>
					<a href="depot/edit_depot/<?php print $row->depot_id; ?>" title="编辑" class="edit"></a>
				    <?php if (check_perm('depot_edit')): ?>
					<a onclick="check_confirm_del('<?php print $row->depot_id; ?>');return false;" href="#" title="删除" class="del"></a>
				    <?php endif; ?>
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