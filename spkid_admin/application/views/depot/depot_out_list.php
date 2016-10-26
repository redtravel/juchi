<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depotio/out';
		function search(){
			listTable.filter['depot_out_code'] = $.trim($('input[type=text][name=depot_out_code]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['depot_out_type'] = $.trim($('select[name=depot_out_type]').val());
			listTable.filter['depot_out_status'] = $.trim($('select[name=depot_out_status]').val());
			listTable.filter['depot_depot_id'] = $.trim($('select[name=depot_depot_id]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.loadList();
		}

		function check_confirm_del(depot_out_id)
		{
			$.ajax({
	            url: '/depotio/check_delete_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(depot_out_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(depot_out_id)
		{
			var depot_out_code = $.trim($('input[type=text][name=depot_out_code]').val());
			var provider_id = $.trim($('select[name=provider_id]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_out_type = $.trim($('select[name=depot_out_type]').val());
			var depot_out_status = $.trim($('select[name=depot_out_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_delete_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,depot_out_code:depot_out_code,
	            		depot_depot_id:depot_depot_id,depot_out_type:depot_out_type,provider_id:provider_id,
	            		depot_out_status:depot_out_status,provider_goods:provider_goods,
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

		function check_confirm_lock(depot_out_id)
		{
			$.ajax({
	            url: '/depotio/check_lock_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定锁定？'))
	                	{
							confirm_lock(depot_out_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_lock(depot_out_id)
		{
			var depot_out_code = $.trim($('input[type=text][name=depot_out_code]').val());
			var provider_id = $.trim($('select[name=provider_id]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_out_type = $.trim($('select[name=depot_out_type]').val());
			var depot_out_status = $.trim($('select[name=depot_out_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_lock_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,depot_out_code:depot_out_code,
	            		depot_depot_id:depot_depot_id,depot_out_type:depot_out_type,provider_id:provider_id,
	            		depot_out_status:depot_out_status,provider_goods:provider_goods,
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

		function check_confirm_unlock(depot_out_id)
		{
			$.ajax({
	            url: '/depotio/check_unlock_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定解锁？'))
	                	{
							confirm_unlock(depot_out_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_unlock(depot_out_id)
		{
			var depot_out_code = $.trim($('input[type=text][name=depot_out_code]').val());
			var provider_id = $.trim($('select[name=provider_id]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_out_type = $.trim($('select[name=depot_out_type]').val());
			var depot_out_status = $.trim($('select[name=depot_out_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_unlock_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,depot_out_code:depot_out_code,
	            		depot_depot_id:depot_depot_id,depot_out_type:depot_out_type,provider_id:provider_id,
	            		depot_out_status:depot_out_status,provider_goods:provider_goods,
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

		function check_confirm_check(depot_out_id)
		{
			$.ajax({
	            url: '/depotio/check_check_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定审核？'))
	                	{
							confirm_check(depot_out_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_check(depot_out_id)
		{
			var depot_out_code = $.trim($('input[type=text][name=depot_out_code]').val());
			var provider_id = $.trim($('select[name=provider_id]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_out_type = $.trim($('select[name=depot_out_type]').val());
			var depot_out_status = $.trim($('select[name=depot_out_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_check_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,depot_out_code:depot_out_code,
	            		depot_depot_id:depot_depot_id,depot_out_type:depot_out_type,provider_id:provider_id,
	            		depot_out_status:depot_out_status,provider_goods:provider_goods,
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
		<span class="l">出库管理 &gt;&gt; 出库单列表</span>
		<?php if (check_perm('depotout_add')): ?>
		<span class="r">
			<a href="depotio/add_out" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			出库单编号：<input type="text" class="ts" name="depot_out_code" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" /><br />
			出库单状态：<?php print form_dropdown('depot_out_status',$status_list);?>
			出库类型：<?php print form_dropdown('depot_out_type',$type_list);?>
			供应商：<?php print form_dropdown('provider_id',$provider_list);?>
			出库仓库：<?php print form_dropdown('depot_depot_id',$depot_list);?>
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
					<th width="120px">出库单编号</th>
					<th>出库类型</th>
					<th>出库时间</th>
					<th>供应商</th>
					<th>出库仓库</th>
					<th>出库数量</th>
					<th>实际出货数量</th>
					<th>出库金额</th>
					<th>状态</th>
					<th>锁定人</th>
					<th>审核人</th>
					<th width="210px;">操作</th>
				</tr>
				<?php foreach($list as $row):
				    if($row->depot_type_code == 'ck001'){
					$biz_type = 1;
				    }else if($row->depot_type_code == 'ck007'){
					$biz_type = 2;
				    }else if($row->depot_type_code == 'ck003'){
					$biz_type = 3;
				    }else{
					$biz_type = 0;
				    }
				    ?>
				<tr class="row">
					<td>&nbsp;<?php print $row->depot_out_code; ?><?php if($row->is_deleted): echo '<span style="color:red">[删]</span>'; endif;?></td>
					<td>&nbsp;<?php print $row->depot_type_name; ?></td>
					<td>&nbsp;<?php print date('Y-m-d',strtotime($row->depot_out_date)); ?></td>
					<td>&nbsp;<?php print $row->provider_name; ?></td>
					<td>&nbsp;<?php print $row->depot_name; ?></td>
					<td>&nbsp;<?php print $row->depot_out_number; ?></td>
					<td style="color:red;font-weight:bold;">&nbsp;<?php print $row->depot_out_finished_number; ?></td>
					<td>&nbsp;<?php print $row->depot_out_amount; ?></td>
					<td>&nbsp;<?php print $row->depot_status_name; ?></td>
					<td>&nbsp;<?php print $row->lock_name; ?></td>
					<td>&nbsp;<?php print $row->audit_name; ?></td>
					<td>
						<a href="depotio/edit_out/<?php print $row->depot_out_id; ?>" title="编辑" class="edit"></a>
						<?php if (empty($row->audit_admin)): ?>
							<?php if(empty($row->inventory_id)): ?>
							    <?php if ($row->lock_admin == 0 && check_perm(array('depotout_add','depotout_del','depotout_audit'))): ?>
							    <a onclick="check_confirm_lock('<?php print $row->depot_out_id; ?>');return false;" href="#" title="锁定" >锁定</a>
							    <?php elseif ($row->lock_admin == $my_id): ?>
							    <a onclick="check_confirm_unlock('<?php print $row->depot_out_id; ?>');return false;" href="#" title="解锁" >解锁</a>
							    <?php endif; ?>
							<?php endif; ?>
                                                    <?php if ($row->depot_out_number !=0): ?>
                                                        <?php if ($row->depot_type_code!='ck004'): ?>
							<a href="depotio/print_out_pick/<?php print $row->depot_out_id; ?>" title="打印拣货单" target="_blank">打印拣货单</a>
							 <?php if ($biz_type == 1 && check_perm(array('box_check'))): ?><a href="box_check/index/<?php print $biz_type ."/".$row->depot_out_code; ?>" target="_BLANK" title="出库复核" >出库复核</a><?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
						    <?php if ($row->lock_admin == $my_id): ?>
							<?php if (check_perm('depotout_del')): ?><a onclick="check_confirm_del('<?php print $row->depot_out_id; ?>');return false;" href="#" title="删除" class="del"></a><?php endif; ?>
							<?php if (check_perm('depotout_audit') && $row->depot_out_finished_number == $row->depot_out_number ): ?>
							    <a onclick="check_confirm_check('<?php print $row->depot_out_id; ?>');return false;" href="#" title="审核" >审核</a>
							<?php endif; ?>
						    <?php endif; ?>
						<?php endif; ?>
						<?php if ($row->depot_out_number !=0 && $biz_type != 0 && check_perm(array('depot_out_view_pick'))): ?>
							<a href="pick_out/pick_details/<?php print $biz_type ."/". $row->depot_out_code; ?>" target="_BLANK" title="拣货记录" >拣货记录</a>
						<?php endif; ?>
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