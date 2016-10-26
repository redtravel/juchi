<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'admin/index';
		function search(){
			listTable.filter['admin_name'] = $.trim($('input[type=text][name=admin_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">管理员列表</span><span class="r"><a href="admin/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			管理员帐号：<input type="text" class="ts" name="admin_name" value="" style="width:100px;" />
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
					<th width="50px">编号</th>
					<th>管理员帐号</th>
					<th>真实姓名</th>
					<th>Email</th>
					<th>性别</th>
					<th>部门</th>
					<th>启用</th>
					<th>最后登录时间</th>
					<th>最后登录IP</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->admin_id; ?></td>
					<td><?php print $row->admin_name; ?></td>
					<td><?php print $row->realname; ?></td>
					<td><?php print $row->admin_email; ?></td>
					<td><?php print $row->sex==1?'男':($row->sex==2?'女':''); ?></td>
					<td><?php print $row->dept_name; ?></td>
					<td><?php print toggle_link('admin/toggle','user_status',$row->admin_id, $row->user_status);?></td>
					<td><?php print $row->last_login; ?></td>
					<td>&nbsp;<?php print $row->last_ip; ?></td>
					<td>
						<a class="edit" href="admin/edit/<?php print $row->admin_id; ?>" title="编辑"></a>
						<?php if($row->admin_id != 1 && $perm_perm): ?>
						<a class="priv" href="admin/perm/<?php print $row->admin_id; ?>" title="权限"></a>
						
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