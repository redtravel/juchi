<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
	    	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
			listTable.filter.page = '<?php echo $filter['page']; ?>';
			listTable.url = 'manual_sms/sms_list';
    	});

		function search(){
			listTable.filter['mobile'] = $.trim($('input[name=mobile_phone]').val());
			listTable.filter['page'] = 1;
			listTable.loadList();
		}

		function confirmDo(url){
		    if( confirm( '您取定要执行此操作？' )){
		        window.location.href=url;
		    }
		}

	</script>
	<div class="main">
    <div class="main_title"><span class="l">促销管理 >> 手机号码列表</span> <span class="r"><a href="manual_sms/add" class="add">添加手机号</a></span></div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
手机号：<input type='text' value='' name='mobile_phone' id='mobile_phone'/>
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
				    <th>记录ID</th>
				    <th>手机号</th>
				    <th>来源</th>
				    <th>添加时间</th>
				    <th>添加人</th>
				    <th>状态</th>
				    <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row['rec_id']; ?></td>
				    <td>&nbsp;<?php print $row['sms_to']; ?></td>
				    <td>&nbsp;<?php print $row['source_name']; ?></td>
				    <td>&nbsp;<?php print $row['create_date']; ?></td>
				    <td>&nbsp;<?php print $row['admin_name']; ?></td>
				    <td>&nbsp;<?php print $row['status_name']; ?></td>
				    <td>
				        <?php if ($row['status'] == 0):?>
				            <a href="javascript:confirmDo('manual_sms/oper/page/<?php echo $filter['page']; ?>/refer_id/<?php print $row['rec_id']; ?>/op/delete');">删除</a>
				        <?php endif; ?>
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