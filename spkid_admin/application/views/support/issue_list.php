<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		$(function(){
			$(':input[name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'support/issue_list';
		function search(){
			listTable.filter['user_name'] = $.trim($('input[type=text][name=user_name]').val());
			listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
			listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
			listTable.filter['status'] = $.trim($('select[name=status]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">在线客服概览</span><span class="r"><a href="support/log_list" class="return r">备注记录</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			邮箱|手机：<input type="text" class="ts" name="user_name" value="" style="width:100px;" />
			时间从：<input type="text" class="ts" name="start_date" value="" style="width:100px;" />
			到：<input type="text" class="ts" name="end_date" value="" style="width:100px;" />
			<?php print form_dropdown('status',array('-1'=>'状态')+$status); ?>
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
					<th width="50px">
						<a href="javascript:listTable.sort('m.rec_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'm.rec_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>来访时间</th>
					<th>会员</th>
					<th>管理员</th>
					<th>当前状态</th>	
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->rec_id; ?></td>
					<td><?php print $row->create_date ?></td>
					<td><?php print $row->user_id?$row->user_name:'[访客]' ?></td>
					<td><?php print $row->admin_name ?></td>
					<td><?php print $status[$row->status] ?></td>
					<td>
						<a href="support/message_list/<?php print $row->rec_id ?>">历史记录</a>
						<a href="support/log_list/<?php print $row->rec_id ?>">备注记录</a>
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