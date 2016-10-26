<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
    
	<script type="text/javascript">
		//<![CDATA[
		$(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});
		
		function img_tip()
		{
			$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
		}
		$(function(){
			img_tip();
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'campaign/index';
		listTable.func = img_tip;
		function search(){
			listTable.filter['campaign_name'] = $.trim($('input[type=text][name=campaign_name]').val());
			listTable.filter['start_time'] = $.trim($('input[type=text][name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[type=text][name=end_time]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
			<span class="l">活动列表</span>
			<span class="r"><a href="campaign/add/3" class="add">抢购</a></span>
			<span class="r"><a href="campaign/add/2" class="add">免邮</a></span>
			<span class="r"><a href="campaign/add" class="add">新增</a></span>
		</div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			活动名称：<input type="text" class="ts" name="campaign_name" value="" style="width:100px;" />
            开始时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
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
					<th width="50">
                    编号
				  </th>
					<th width="68">活动名称</th>
					<th width="68">活动类型</th>
					<th width="68">最小金额</th>
					<th width="85">赠送/免邮/抢购</th>
					<th width="69">开始时间</th>
					<th>结束时间</th>
					<th width="45">启用</th>
					<th width="90">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php echo $row->campaign_id?></td>
					<td><?php echo $row->campaign_name?></td>
					<td><?php echo $campaign_type[$row->campaign_type]?></td>
					<td><?php echo $row->limit_price?></td>
					<td><?php echo $row->product_name .' '. $row->product_sn?></td>
					<td><?php echo $row->start_date?></td>
					<td width="90" align="center"><?php echo $row->end_date?></td>
					<td><?php if($row->is_use == 0){echo '未启用';}elseif($row->is_use == 1){echo '启用';}else{echo '停止';}?></td>
					<td>
						<a class="edit" href="campaign/edit/<?php echo $row->campaign_id?>" title="编辑"></a>
						
						<a class="del" href="javascript:void(0);" rel="campaign/delete/<?php echo $row->campaign_id?>" title="删除" onclick="do_delete(this)"></a>
						
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