<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'data_alert/index';
		function search(){
			listTable.filter['status'] = $.trim($('select[name=status]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}

		//]]>
	</script>
	<div class="main">
    <div class="main_title"><span class="l">系统设置 >> 数据检查报警</span> <span class="r"><a href="data_alert/check" class="add">新的检查</a></span></div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
状态：
<select name="is_use" id="is_use">
	    <option value="-1">--请选择--</option>
			    <option value="9">无错误</option>
			    <option value="1">有错误</option>
			  </select>
			  创建时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="5" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>ID</th>
				  <th>创建时间</th>
				  <th>状态</th>
				  <th>检查人</th>
				  <th width="10%">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->sys_log_id; ?></td>
					<td>&nbsp;<?php print $row->date_insert; ?></td>
					<td>&nbsp;<?php print ($row->status == 1)?'有错误':'无错误'; ?></td>
					<td>&nbsp;<?php print $row->admin_name; ?></td>
					<td>
						<a href="data_alert/read/<?php print $row->sys_log_id; ?>" title="查看" class="edit"></a>
			      </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="5" class="bottomTd"> </td>
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