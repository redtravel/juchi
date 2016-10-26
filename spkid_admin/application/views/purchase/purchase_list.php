<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
        <script type="text/javascript" src="public/js/depot.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
                listTable.url = '/purchase/index';
		function search(){
			listTable.filter['purchase_code'] = $.trim($('input[type=text][name=purchase_code]').val());
			listTable.filter['purchase_provider'] = $.trim($('select[name=purchase_provider]').val());
			listTable.filter['purchase_type'] = $.trim($('select[name=purchase_type]').val());
			listTable.filter['purchase_status'] = $.trim($('select[name=purchase_status]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['purchase_batch'] = $.trim($('select[name=purchase_batch]').val());
			listTable.filter['is_consign'] = $.trim($('select[name=is_consign]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]').val());
                        if ($('input[type=checkbox][name=overtime5]').is(':checked')) {
                            listTable.url = '/purchase/index/1';
                        } else {
                            listTable.url = '/purchase/index';
                        }
			listTable.loadList();
		}
                
		function check_confirm_del(purchase_id)
		{
			$.ajax({
	            url: '/purchase/check_delete',
	            data: {is_ajax:1,purchase_id:purchase_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定删除？'))
	                	{
							confirm_del(purchase_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_del(purchase_id)
		{
			var purchase_code = $.trim($('input[type=text][name=purchase_code]').val());
			var purchase_provider = $.trim($('select[name=purchase_provider]').val());
			var purchase_type = $.trim($('select[name=purchase_type]').val());
			var purchase_status = $.trim($('select[name=purchase_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());
			var purchase_batch = $.trim($('select[name=purchase_batch]').val());

			$.ajax({
	            url: '/purchase/proc_delete',
	            data: {is_ajax:1,purchase_id:purchase_id,purchase_code:purchase_code,
	            		purchase_provider:purchase_provider,purchase_type:purchase_type,
	            		purchase_status:purchase_status,provider_goods:provider_goods,
				purchase_batch:purchase_batch,rnd : new Date().getTime()},
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

		function check_confirm_lock(purchase_id)
		{
			$.ajax({
	            url: '/purchase/check_lock',
	            data: {is_ajax:1,purchase_id:purchase_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定锁定？'))
	                	{
							confirm_lock(purchase_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_lock(purchase_id)
		{
			var purchase_code = $.trim($('input[type=text][name=purchase_code]').val());
			var purchase_provider = $.trim($('select[name=purchase_provider]').val());
			var purchase_type = $.trim($('select[name=purchase_type]').val());
			var purchase_status = $.trim($('select[name=purchase_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());
			var purchase_batch = $.trim($('select[name=purchase_batch]').val());

			$.ajax({
	            url: '/purchase/proc_lock',
	            data: {is_ajax:1,purchase_id:purchase_id,purchase_code:purchase_code,
	            		purchase_provider:purchase_provider,purchase_type:purchase_type,
	            		purchase_status:purchase_status,provider_goods:provider_goods,
				purchase_batch:purchase_batch,rnd : new Date().getTime()},
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

		function check_confirm_unlock(purchase_id)
		{
			$.ajax({
	            url: '/purchase/check_unlock',
	            data: {is_ajax:1,purchase_id:purchase_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定解锁？'))
	                	{
							confirm_unlock(purchase_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_unlock(purchase_id)
		{
			var purchase_code = $.trim($('input[type=text][name=purchase_code]').val());
			var purchase_provider = $.trim($('select[name=purchase_provider]').val());
			var purchase_type = $.trim($('select[name=purchase_type]').val());
			var purchase_status = $.trim($('select[name=purchase_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());
			var purchase_batch = $.trim($('select[name=purchase_batch]').val());

			$.ajax({
	            url: '/purchase/proc_unlock',
	            data: {is_ajax:1,purchase_id:purchase_id,purchase_code:purchase_code,
	            		purchase_provider:purchase_provider,purchase_type:purchase_type,
	            		purchase_status:purchase_status,provider_goods:provider_goods,
				purchase_batch:purchase_batch,rnd : new Date().getTime()},
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

		function check_confirm_check(purchase_id)
		{
			$.ajax({
	            url: '/purchase/check_check',
	            data: {is_ajax:1,purchase_id:purchase_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(confirm('确定审核？'))
	                	{
							confirm_check(purchase_id);
	                	}
	                }
	            }
	        });
		    return false;
		}

		function confirm_check(purchase_id)
		{
			var purchase_code = $.trim($('input[type=text][name=purchase_code]').val());
			var purchase_provider = $.trim($('select[name=purchase_provider]').val());
			var purchase_type = $.trim($('select[name=purchase_type]').val());
			var purchase_status = $.trim($('select[name=purchase_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());
			var purchase_batch = $.trim($('select[name=purchase_batch]').val());

			$.ajax({
	            url: '/purchase/proc_check',
	            data: {is_ajax:1,purchase_id:purchase_id,purchase_code:purchase_code,
	            		purchase_provider:purchase_provider,purchase_type:purchase_type,
	            		purchase_status:purchase_status,provider_goods:provider_goods,
	            		purchase_batch:purchase_batch,rnd : new Date().getTime()},
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

		function check_confirm_break(purchase_id, is_consign, purchase_number, purchase_finished_number)
		{
			$.ajax({
	            url: '/purchase/check_break',
	            data: {is_ajax:1,purchase_id:purchase_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	var confirmMsg = '';
                                if (is_consign==1 && purchase_number > purchase_finished_number)
                                    confirmMsg += '虚库销售采购单欠收，采购总数量：' + purchase_number + ', 实际收货数量：' + purchase_finished_number + '。';
                                confirmMsg += '确定终止？';
                                
                                if(confirm(confirmMsg))
                                {
                                                        confirm_break(purchase_id);
                                }
	                }
	            }
	        });
		    return false;
		}

		function confirm_break(purchase_id)
		{
			var purchase_code = $.trim($('input[type=text][name=purchase_code]').val());
			var purchase_provider = $.trim($('select[name=purchase_provider]').val());
			var purchase_type = $.trim($('select[name=purchase_type]').val());
			var purchase_status = $.trim($('select[name=purchase_status]').val());
			var provider_goods = $.trim($('input[type=text][name=provider_goods]').val());
			var purchase_batch = $.trim($('select[name=purchase_batch]').val());

			$.ajax({
	            url: '/purchase/proc_break',
	            data: {is_ajax:1,purchase_id:purchase_id,purchase_code:purchase_code,
	            		purchase_provider:purchase_provider,purchase_type:purchase_type,
	            		purchase_status:purchase_status,provider_goods:provider_goods,
	            		purchase_batch:purchase_batch,rnd : new Date().getTime()},
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
		<span class="l">采购管理 &gt;&gt; 采购单列表</span>
		<?php if (check_perm('purchase_add')): ?>
		<span class="r">
			<a href="purchase/add" class="add">新增</a>
		</span>
                <span class="r">
			<a href="purchase/product_import" class="add">导入产品有效期</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			采购单编号：<input type="text" class="ts" name="purchase_code" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
                        <input type="checkbox" name="overtime5" value="1" <?php if($filter['overtime5'] == 1): ?>checked="true"<?php endif; ?> />审核超过5天未终止
			<br>
			采购单状态：<?php print form_dropdown('purchase_status',$status_list);?>
			采购类型：<?php print form_dropdown('purchase_type',$type_list);?>
			供应商：<?php print form_dropdown('purchase_provider',$provider_list,"","onchange=get_purchase_batch() data-am-selected='{searchBox: 1,maxHeight: 300}'");?>
			批次号：<?php print form_dropdown('purchase_batch',array("请选择"));?>
			销售类型：
				<select name="is_consign">
				<option value="">请选择</option>
				<option value="0">实库销售</option>
				<option value="1">虚库销售</option>
				</select>
			<?php print form_dropdown('brand_id',get_pair($brand_list,'brand_id','brand_name', array(''=>'品牌')),'','data-am-selected="{searchBox: 1,maxHeight: 300}"'); ?>
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
					<th width="120px">采购单编号</th>
					<th>采购类型</th>
					<th>采购发起时间</th>
					<th>供应商</th>
					<th>品牌</th>
					<th>采购总金额</th>
					<th>采购总数量</th>
					<th>实际收货数量</th>
					<th>上架数量</th>
					<th>状态</th>
					<th>锁定人</th>
					<th>审核人</th>
					<th>终止人</th>
					<th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td>
                                        <?php if (date('Y-m-d',strtotime($row->purchase_check_date))!=date('Y-m-d',strtotime('0000-00-00 00:00:00'))): ?>
                                            <?php if (date('Y-m-d',strtotime($row->purchase_break_date))==date('Y-m-d',strtotime('0000-00-00 00:00:00'))): ?>
                                                <?php if (date('Y-m-d',strtotime($row->purchase_check_date)) < date('Y-m-d',strtotime(' -5 day'))): ?>
                                                    <span style="color:red;">
                                                <?php endif; ?>
                                            <?php endif; ?>
			      		<?php endif; ?>
					&nbsp;<?php print $row->purchase_code; ?>
					<?php if ($row->is_consign ==1):?><br>(虚库销售)<?php endif; ?>
                                        <?php if (date('Y-m-d',strtotime($row->purchase_check_date))!=date('Y-m-d',strtotime('0000-00-00 00:00:00'))): ?>
                                            <?php if (date('Y-m-d',strtotime($row->purchase_break_date))==date('Y-m-d',strtotime('0000-00-00 00:00:00'))): ?>
                                                <?php if (date('Y-m-d',strtotime($row->purchase_check_date)) < date('Y-m-d',strtotime(' -5 day'))): ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
			      		<?php endif; ?>
					</td>
					<td>&nbsp;<?php print $row->purchase_type_name; ?></td>
					<td>&nbsp;<?php print date('Y-m-d',strtotime($row->purchase_order_date)); ?></td>
					<td>&nbsp;<?php print $row->provider_name."[".$row->provider_code."]"; ?></td>
					<td>&nbsp;<?php print $row->brand_name; ?></td>
					<td>&nbsp;<?php print $row->purchase_amount; ?></td>
					<td>&nbsp;<?php print $row->purchase_number; ?></td>
					<td style="color:red;font-weight:bold;">&nbsp;<?php print intval($row->purchase_finished_number); ?></td>
					<td style="color:red;font-weight:bold;">&nbsp;<?php print intval($row->purchase_shelved_number); ?></td>
					<td>&nbsp;<?php print $row->purchase_status_name; ?></td>
					<td>&nbsp;<?php print $row->lock_name; ?></td>
					<td>&nbsp;<?php print $row->purchase_check_name; ?></td>
					<td>&nbsp;<?php print $row->purchase_break_name; ?></td>
					<td>
						<a href="purchase/edit/<?php print $row->purchase_id; ?>" title="编辑" class="edit"></a>
						<?php if (empty($row->purchase_check_admin) && empty($row->purchase_break_admin) && check_perm('purchase_del') && $row->lock_admin == $my_id): ?>
                    	<a onclick="check_confirm_del('<?php print $row->purchase_id; ?>');return false;" href="#" title="删除" class="del"></a>
			      		<?php endif; ?>

						<?php if ($row->lock_admin == 0 && empty($row->purchase_break_admin) && empty($row->purchase_finished) && check_perm(array('purchase_add','purchase_del','purchase_stop','purchase_audit'))): ?>
						<a class="lockForGif" onclick="check_confirm_lock('<?php print $row->purchase_id; ?>');return false;" href="#" title="锁定" ></a>
						<?php elseif ($row->lock_admin == $my_id && empty($row->purchase_break_admin)): ?>
						<a class="lockoffForGif" onclick="check_confirm_unlock('<?php print $row->purchase_id; ?>');return false;" href="#" title="解锁" ></a><span> 
						<?php endif; ?>
						<?php if ($row->lock_admin == $my_id): ?>
						<?php if (empty($row->purchase_check_admin) && empty($row->purchase_break_admin) && check_perm('purchase_audit')): ?>
						<a class="checkForGif" onclick="check_confirm_check('<?php print $row->purchase_id; ?>');return false;" href="#" title="审核" ></a>
						<?php endif; ?>
						<?php if (empty($row->purchase_break_admin) && !empty($row->purchase_check_admin) && check_perm('purchase_stop')): ?>
						<a onclick="check_confirm_break('<?php print $row->purchase_id; ?>', '<?php print $row->is_consign; ?>', '<?php print $row->purchase_number; ?>', '<?php print $row->purchase_finished_number; ?>');return false;" href="#" title="终止" >终止</a>
						<?php endif; ?>
						<?php endif; ?>
						<?php if ( !empty($row->purchase_check_admin) && empty($row->purchase_break) ): ?>
						<?php if(check_perm('menu_purchasebox') ){?>
						<a class="list1_icon" href="/purchase_box/index/<?= $row->purchase_code ?>" title="收货箱列表" ></a>
						<?php } ?>
						<?php if(check_perm('purchase_box_scanning') ){?>
						<a class="scan_icon" href="/purchase_box/scan/<?php print $row->purchase_id; ?>" title="扫描收货" ></a>
						<?php } ?>
						<?php endif; ?>
						<?php if($row->purchase_finished_number >0 && check_perm('pruchase_box_scan_list')): ?>
						<a class="icon_xiang" href="/purchase_box/pruchase_box_scan_list/<?php print $row->purchase_code; ?>" target="_blank" title="扫描记录" ></a>
						 <?php endif; ?>
                                                <a href="purchase/export/<?php print $row->purchase_id; ?>">导出</a>
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