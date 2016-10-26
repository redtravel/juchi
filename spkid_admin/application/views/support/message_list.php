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
		listTable.url = 'support/message_list/<?php print $filter['issue_id'] ?>';
		function search(){
			listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
			listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">在线客服会话记录</span><span class="r"><a href="support/issue_list" class="return r">会话列表</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			时间从：<input type="text" class="ts" name="start_date" value="" style="width:100px;" />
			到：<input type="text" class="ts" name="end_date" value="" style="width:100px;" />
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
						<a href="javascript:listTable.sort('s.message_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 's.message_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="150px">时间</th>
					<th width="150px">角色</th>
					<th>内容</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->message_id; ?></td>
					<td><?php print $row->create_date ?></td>
					<td style="text-align:left;">
						<?php if ($row->qora): ?>
							<span style="color:#FF6600;">[答]</span>
							<?php print $row->admin_name; ?>
						<?php else: ?>
							<span style="color:#1C94C4;">[问]</span>
							<?php print $row->user_name?$row->user_name:'[访客]' ?>
						<?php endif ?>
					</td>
					<td style="text-align:left;"><?php print htmlspecialchars($row->content); ?></td>
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