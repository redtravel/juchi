<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/exchange/exchange_list';
		function search(){
			listTable.filter['exchange_code'] = $.trim($('input[type=text][name=exchange_code]').val());
			listTable.filter['exchange_status'] = $.trim($('select[name=exchange_status]').val());
			listTable.filter['out_depot_id'] = $.trim($('select[name=out_depot_id]').val());
			listTable.filter['in_depot_id'] = $.trim($('select[name=in_depot_id]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.loadList();
		}

		function check_confirm_del(exchange_id)
		{
			$.ajax({
	            url: '/exchange/check_delete',
	            data: {is_ajax:1,exchange_id:exchange_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(exchange_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(exchange_id)
		{
			var exchange_code = $.trim($('input[type=text][name=exchange_code]').val());
			var out_depot_id = $.trim($('select[name=out_depot_id]').val());
			var in_depot_id = $.trim($('select[name=in_depot_id]').val());
			var exchange_status = $.trim($('select[name=exchange_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/exchange/proc_delete',
	            data: {is_ajax:1,exchange_id:exchange_id,exchange_code:exchange_code,
	            		out_depot_id:out_depot_id,in_depot_id:in_depot_id,
	            		exchange_status:exchange_status,provider_goods:provider_goods,
	            		 rnd : new Date().getTime()},
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

		function check_confirm_lock(exchange_id)
		{
			$.ajax({
	            url: '/exchange/check_lock',
	            data: {is_ajax:1,exchange_id:exchange_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定锁定？'))
	                	{
							confirm_lock(exchange_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_lock(exchange_id)
		{
			var exchange_code = $.trim($('input[type=text][name=exchange_code]').val());
			var out_depot_id = $.trim($('select[name=out_depot_id]').val());
			var in_depot_id = $.trim($('select[name=in_depot_id]').val());
			var exchange_status = $.trim($('select[name=exchange_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/exchange/proc_lock',
	            data: {is_ajax:1,exchange_id:exchange_id,exchange_code:exchange_code,
	            		out_depot_id:out_depot_id,in_depot_id:in_depot_id,
	            		exchange_status:exchange_status,provider_goods:provider_goods,
	            		 rnd : new Date().getTime()},
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

		function check_confirm_unlock(exchange_id)
		{
			$.ajax({
	            url: '/exchange/check_unlock',
	            data: {is_ajax:1,exchange_id:exchange_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定解锁？'))
	                	{
							confirm_unlock(exchange_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_unlock(exchange_id)
		{
			var exchange_code = $.trim($('input[type=text][name=exchange_code]').val());
			var out_depot_id = $.trim($('select[name=out_depot_id]').val());
			var in_depot_id = $.trim($('select[name=in_depot_id]').val());
			var exchange_status = $.trim($('select[name=exchange_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/exchange/proc_unlock',
	            data: {is_ajax:1,exchange_id:exchange_id,exchange_code:exchange_code,
	            		out_depot_id:out_depot_id,in_depot_id:in_depot_id,
	            		exchange_status:exchange_status,provider_goods:provider_goods,
	            		 rnd : new Date().getTime()},
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

		function check_confirm_check_out(exchange_id)
		{
			$.ajax({
	            url: '/exchange/check_check_out',
	            data: {is_ajax:1,exchange_id:exchange_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定审核？'))
	                	{
							confirm_check_out(exchange_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_check_out(exchange_id)
		{
			var exchange_code = $.trim($('input[type=text][name=exchange_code]').val());
			var out_depot_id = $.trim($('select[name=out_depot_id]').val());
			var in_depot_id = $.trim($('select[name=in_depot_id]').val());
			var exchange_status = $.trim($('select[name=exchange_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/exchange/proc_check_out',
	            data: {is_ajax:1,exchange_id:exchange_id,exchange_code:exchange_code,
	            		out_depot_id:out_depot_id,in_depot_id:in_depot_id,
	            		exchange_status:exchange_status,provider_goods:provider_goods,
	            		 rnd : new Date().getTime()},
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

		function check_confirm_check_in(exchange_id)
		{
			$.ajax({
	            url: '/exchange/check_check_in',
	            data: {is_ajax:1,exchange_id:exchange_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	var pre_msg = '';
	                	if (result.msgr)
	                	{
	                		pre_msg = result.msgr+',';
	                	}
	                	if(confirm(pre_msg+'确定审核？'))
	                	{
							confirm_check_in(exchange_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_check_in(exchange_id)
		{
			var exchange_code = $.trim($('input[type=text][name=exchange_code]').val());
			var out_depot_id = $.trim($('select[name=out_depot_id]').val());
			var in_depot_id = $.trim($('select[name=in_depot_id]').val());
			var exchange_status = $.trim($('select[name=exchange_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/exchange/proc_check_in',
	            data: {is_ajax:1,exchange_id:exchange_id,exchange_code:exchange_code,
	            		out_depot_id:out_depot_id,in_depot_id:in_depot_id,
	            		exchange_status:exchange_status,provider_goods:provider_goods,
	            		 rnd : new Date().getTime()},
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
		<span class="l">调仓管理 &gt;&gt; 调仓单列表</span>
		<?php if (check_perm('exchange_add')): ?>
		<span class="r">
			<a href="exchange/add_exchange" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			调仓单编号：<input type="text" class="ts" name="exchange_code" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" /><br />
			调仓单状态：<?php print form_dropdown('exchange_status',$status_list);?>
			出库仓库：<?php print form_dropdown('out_depot_id',$out_depot_list);?>
			入库仓库：<?php print form_dropdown('in_depot_id',$in_depot_list);?>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="11" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="120px">调仓单编号</th>
					<th>出库仓库</th>
					<th>出库数量</th>
					<th>出库审核人</th>
					<th>入库仓库</th>
					<th>入库数量</th>
					<th>入库审核人</th>
					<th>日期</th>
					<th>状态</th>
					<th>锁定人</th>
					<th width="210px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td>&nbsp;<?php print $row->exchange_code; ?></td>
					<td>&nbsp;<?php print $row->out_depot_name; ?></td>
					<td>&nbsp;<?php print $row->exchange_out_number; ?></td>
					<td>&nbsp;<?php print $row->out_audit_name; ?></td>
					<td>&nbsp;<?php print $row->in_depot_name; ?></td>
					<td>&nbsp;<?php print $row->exchange_in_number; ?></td>
					<td>&nbsp;<?php print $row->in_audit_name; ?></td>
					<td>&nbsp;<?php print date('Y-m-d',strtotime($row->out_date)); ?></td>
					<td>&nbsp;<?php print $row->exchange_status_name; ?></td>
					<td>&nbsp;<?php print $row->lock_name; ?></td>
					<td>
						<a href="exchange/edit_exchange/<?php print $row->exchange_id; ?>" title="编辑" class="edit"></a>
						<?php if ($row->lock_admin == $my_id && empty($row->out_audit_admin) && check_perm('exchange_del')): ?>
						<a onclick="check_confirm_del('<?php print $row->exchange_id; ?>');return false;" href="#" title="删除" class="del"></a>
						<?php endif; ?>

						<?php if ($row->lock_admin == 0 && empty($row->in_audit_admin) && check_perm(array('exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'))): ?>
						<a onclick="check_confirm_lock('<?php print $row->exchange_id; ?>');return false;" href="#" title="锁定" >锁定</a>
						<?php elseif ($row->lock_admin == $my_id && empty($row->in_audit_admin)): ?>
						<a onclick="check_confirm_unlock('<?php print $row->exchange_id; ?>');return false;" href="#" title="解锁" >解锁</a>
						<?php endif; ?>
						<?php if ($row->lock_admin == $my_id): ?>
						<?php if (empty($row->out_audit_admin)): ?>
						<a onclick="check_confirm_check_out('<?php print $row->exchange_id; ?>');return false;" href="#" title="出库审核" >出库审核</a>
						<?php endif; ?>
						<?php if ($row->out_audit_admin > 0 && empty($row->in_audit_admin)): ?>
						<a onclick="check_confirm_check_in('<?php print $row->exchange_id; ?>');return false;" href="#" title="入库审核" >入库审核</a>
						<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="11" class="bottomTd"> </td>
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