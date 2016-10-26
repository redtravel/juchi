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
		listTable.url = 'support/log_list';
		function search(){
			listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
			listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
			listTable.filter['closed'] = $.trim($('select[name=closed]').val());
			listTable.filter['issue_id'] = $.trim($(':input[name=issue_id]').val());
			listTable.filter['admin_name'] = $.trim($(':input[name=admin_name]').val());
			listTable.loadList();
		}

		function close_log(log_id) {
			if(!confirm('确定关闭该备注?')) return false;
			$.ajax({
				url:'support/close_log',
				data:{log_id:log_id,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.msg) alert(result.msg);
					if(result.err) return false;
					var container=$('tr[rel='+log_id+']');
					$('td:eq(4)',container).html('已关闭');
					$('td:eq(5)',container).html(result.close_date);
				}
			});
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">在线客服备注记录</span><span class="r"><a href="support/issue_list" class="return r">会话列表</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			会话ID：<input type="text" class="ts" name="issue_id" value="<?php print $filter['issue_id']?$filter['issue_id']:''?>" style="width:100px;">
			时间从：<input type="text" class="ts" name="start_date" value="" style="width:100px;" />
			到：<input type="text" class="ts" name="end_date" value="" style="width:100px;" />
			<?php print form_dropdown('closed',array(-1=>'状态','0'=>'未关闭',1=>'已关闭'))?>
			备注人：<input type="text" class="ts" name="admin_name" value="" style="width:100px;" />
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
						<a href="javascript:listTable.sort('l.log_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'l.log_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="100px">会话ID</th>
					<th width="150px">备注时间</th>
					<th width="150px">备注人</th>
					<th width="100px">状态</th>
					<th width="150px">关闭时间</th>
					<th>内容</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row" rel="<?php print $row->log_id?>">					
					<td><?php print $row->log_id; ?></td>
					<td><a href="support/message_list/<?php print $row->rec_id; ?>"><?php print $row->rec_id; ?></a></td>
					<td><?php print $row->create_date ?></td>
					<td><?php print $row->admin_name ?></td>
					<td><?php print $row->closed?'已关闭':('<a href="javascript:void(0)" onclick="close_log('.$row->log_id.')">[点击关闭]</a>') ?></td>
					<td><?php if($row->closed) print $row->close_date; ?></td>
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