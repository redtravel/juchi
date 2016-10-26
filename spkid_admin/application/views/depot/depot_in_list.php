<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depotio/in';
		function search(){
			listTable.filter['depot_in_code'] = $.trim($('input[type=text][name=depot_in_code]').val());
			listTable.filter['depot_in_type'] = $.trim($('select[name=depot_in_type]').val());
			listTable.filter['depot_in_status'] = $.trim($('select[name=depot_in_status]').val());
			listTable.filter['depot_depot_id'] = $.trim($('select[name=depot_depot_id]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]').val());
			listTable.filter['provider_barcode'] = $.trim($('input[type=text][name=provider_barcode]').val());
			listTable.filter['box_code'] = $.trim($('input[type=text][name=box_code]').val());
			listTable.loadList();
		}

		function check_confirm_del(depot_in_id)
		{
			$.ajax({
	            url: '/depotio/check_delete_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(depot_in_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(depot_in_id)
		{
			var depot_in_code = $.trim($('input[type=text][name=depot_in_code]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_in_type = $.trim($('select[name=depot_in_type]').val());
			var depot_in_status = $.trim($('select[name=depot_in_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_delete_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_in_code:depot_in_code,
	            		depot_depot_id:depot_depot_id,depot_in_type:depot_in_type,
	            		depot_in_status:depot_in_status,provider_goods:provider_goods,
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

		function check_confirm_lock(depot_in_id)
		{
			$.ajax({
	            url: '/depotio/check_lock_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定锁定？'))
	                	{
							confirm_lock(depot_in_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_lock(depot_in_id)
		{
			var depot_in_code = $.trim($('input[type=text][name=depot_in_code]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_in_type = $.trim($('select[name=depot_in_type]').val());
			var depot_in_status = $.trim($('select[name=depot_in_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_lock_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_in_code:depot_in_code,
	            		depot_depot_id:depot_depot_id,depot_in_type:depot_in_type,
	            		depot_in_status:depot_in_status,provider_goods:provider_goods,
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

		function check_confirm_unlock(depot_in_id)
		{
			$.ajax({
	            url: '/depotio/check_unlock_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定解锁？'))
	                	{
							confirm_unlock(depot_in_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_unlock(depot_in_id)
		{
			var depot_in_code = $.trim($('input[type=text][name=depot_in_code]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_in_type = $.trim($('select[name=depot_in_type]').val());
			var depot_in_status = $.trim($('select[name=depot_in_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_unlock_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_in_code:depot_in_code,
	            		depot_depot_id:depot_depot_id,depot_in_type:depot_in_type,
	            		depot_in_status:depot_in_status,provider_goods:provider_goods,
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

		function check_confirm_check(depot_in_id)
		{
			$.ajax({
	            url: '/depotio/check_check_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定审核？'))
	                	{
							confirm_check(depot_in_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_check(depot_in_id)
		{
			var depot_in_code = $.trim($('input[type=text][name=depot_in_code]').val());
			var depot_depot_id = $.trim($('select[name=depot_depot_id]').val());
			var depot_in_type = $.trim($('select[name=depot_in_type]').val());
			var depot_in_status = $.trim($('select[name=depot_in_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());

			$.ajax({
	            url: '/depotio/proc_check_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_in_code:depot_in_code,
	            		depot_depot_id:depot_depot_id,depot_in_type:depot_in_type,
	            		depot_in_status:depot_in_status,provider_goods:provider_goods,
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
		<span class="l">入库管理 &gt;&gt; 入库单列表</span>
		<?php if (check_perm('depotin_add')): ?>
		<span class="r">
			<a href="depotio/add_in" class="add">新增</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			入库单编号：<input type="text" class="ts" name="depot_in_code" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
			商品条码：<input type="text" class="ts" name="provider_barcode" value="" style="width:100px;" />
			收货箱号：<input type="text" class="ts" name="box_code" value="" style="width:100px;" />
			<br />
			入库单状态：<?php print form_dropdown('depot_in_status',$status_list);?>
			入库类型：<?php print form_dropdown('depot_in_type',$type_list);?>
			入库仓库：<?php print form_dropdown('depot_depot_id',$depot_list);?>
			<?php print form_dropdown('brand_id',get_pair($brand_list,'brand_id','brand_name', array(''=>'品牌'))); ?>
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
					<th width="120px">入库单编号</th>
					<th>入库类型</th>
					<th>关联单号</th>
					<th>入库时间</th>
					<th>入库仓库</th>
					<th>入库数量</th>
					<th>实际入库数量</th>
					<th>入库金额</th>
					<th>状态</th>
					<th>锁定人</th>
					<th>审核人</th>
					<th width="210px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
				    <td>&nbsp;<?php print $row->depot_in_code; ?><?php if($row->is_deleted): echo '<span style="color:red">[删]</span>'; endif;?></td>
					<td>&nbsp;<?php print $row->depot_type_name; ?></td>
					<td>&nbsp;<?php print $row->order_sn; ?><?php if($row->box_code): ?><?php print ' / '.$row->box_code; ?><?php endif; ?></td>
					<td>&nbsp;<?php print date('Y-m-d',strtotime($row->depot_in_date)); ?></td>
					<td>&nbsp;<?php print $row->depot_name; ?></td>
					<td>&nbsp;<?php print $row->depot_in_number; ?></td>
					<td style="color:red;font-weight:bold;">&nbsp;<?php print $row->depot_in_finished_number; ?></td>
					<td>&nbsp;<?php print $row->depot_in_amount; ?></td>
					<td>&nbsp;<?php print $row->depot_status_name; ?></td>
					<td>&nbsp;<?php print $row->lock_name; ?></td>
					<td>&nbsp;<?php print $row->audit_name; ?></td>
					<td>
						<a href="depotio/edit_in/<?php print $row->depot_in_id; ?>" title="编辑" class="edit"></a>
						<?php if ($row->lock_admin == $my_id && empty($row->audit_admin) && check_perm('depotin_del')): ?>
						<a onclick="check_confirm_del('<?php print $row->depot_in_id; ?>');return false;" href="#" title="删除" class="del"></a>
						<?php endif; ?>
						<?php if(empty($row->inventory_id)): ?>
						<?php if ($row->lock_admin == 0 && empty($row->audit_admin) && check_perm(array('depotin_add','depotin_del','depotin_audit'))): ?>
						<a onclick="check_confirm_lock('<?php print $row->depot_in_id; ?>');return false;" href="#" title="锁定" >锁定</a>
						<?php elseif ($row->lock_admin == $my_id && empty($row->audit_admin)): ?>
						<a onclick="check_confirm_unlock('<?php print $row->depot_in_id; ?>');return false;" href="#" title="解锁" >解锁</a>
						<?php endif; ?>
						<?php endif; ?>
						<?php $show_audit = FALSE; if ($row->lock_admin == $my_id && empty($row->audit_admin) && check_perm('depotin_audit')) $show_audit=TRUE; ?>
						<?php if($show_audit && $row->depot_in_type == 13 &&$row->depot_in_number != $row->depot_in_finished_number){$show_audit=FALSE;}?>
						<?php if($show_audit):?><a onclick="check_confirm_check('<?php print $row->depot_in_id; ?>');return false;" href="#" title="审核" >审核</a><?php endif;?>
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