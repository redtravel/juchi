<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'voucher/query';
		function search(){
			listTable.filter['campaign_name'] = $.trim($('input[type=text][name=campaign_name]').val());
			listTable.filter['campaign_type'] = $.trim($(':input[name=campaign_type]').val());
			listTable.filter['voucher_status'] = $.trim($(':input[name=voucher_status]').val());
			listTable.filter['user_name'] = $.trim($(':input[name=user_name]').val());
			listTable.filter['voucher_name'] = $.trim($(':input[name=voucher_name]').val());
			listTable.filter['voucher_sn'] = $.trim($(':input[name=voucher_sn]').val());
			listTable.filter['release_id'] = $.trim($(':input[name=release_id]').val());
			listTable.loadList();
		}
	
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
			<span class="l">现金券查询</span>
			<div class="blank5"></div>
		</div>
		<div class="search_row">	
			<form name="search" action="javascript:search(); ">		
			活动名称：<input type="text" class="ts" name="campaign_name" value="" style="width:100px;" />
			<?php print form_dropdown('campaign_type', array(''=>'活动类型')+$all_type);?>
			<select name="voucher_status">
				<option value="">现金券状态</option>
				<option value="1">未使用</option>
				<option value="2">使用中</option>
				<option value="3">已用完</option>
			</select>
			现金券名称：<input type="text" class="ts" name="voucher_name" value="" style="width:100px;" />
			券号：<input type="text" class="ts" name="voucher_sn" value="" style="width:100px;" />
			Email/手机：<input type="text" class="ts" name="user_name" value="" style="width:100px;" />
			发放ID：
			<?php print form_input('release_id', isset($filter['release_id'])?$filter['release_id']:'', 'class="checkbox" style="width:30px;"');?>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">
						<a href="javascript:listTable.sort('v.voucher_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'v.voucher_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>现金券编号</th>
					<th>现金券金额</th>
					<th>现金券描述</th>
					<th>生成时间</th>
					<th>使用情况</th>
					<th>活动名称</th>
					<th>活动类型</th>
					<th>Email/手机</th>
					<th>最小订单金额</th>
					<th>有效期</th>
				</tr>
				<?php if (!$list): ?>
					<tr class="row"><td colspan="11">没有记录</td></tr>
				<?php endif ?>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->voucher_id; ?></td>
					<td><?php print $row->voucher_sn; ?></td>
					<td><?php print $row->voucher_amount; ?></td>
					<td><?php print $row->voucher_name; ?></td>
					<td><?php print substr($row->create_date,0,10); ?></td>
					<td><?php print $row->used_number.' / '.$row->repeat_number; ?></td>
					<td><?php print $row->campaign_name; ?></td>
					<td><?php print $all_type[$row->campaign_type]; ?></td>
					<td>
					<?php 
					if ($row->user_id>0 && isset($all_user[$row->user_id])) {
						print $all_user[$row->user_id]->email.' / '.$all_user[$row->user_id]->mobile;
					}					
					?>
					</td>
					<td><?php print $row->min_order; ?></td>
					<td><?php print $row->start_date.'至'.$row->end_date; ?></td>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="12" class="bottomTd"> </td>
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