<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		$(function(){
        	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'order_return/index';
		function search(){
			listTable.filter['return_sn'] = $.trim($('input[name=return_sn]').val());
	        listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
	        listTable.filter['consignee'] = $.trim($('input[name=consignee]').val());
	        listTable.filter['provider_goods'] = $.trim($('input[name=provider_goods]').val());

	        listTable.filter['composite_status'] = $.trim($('select[name=status]').val());
	        listTable.filter['pay_status'] = $.trim($('select[name=pay_status]').val());
	        listTable.filter['shipping_status'] = $.trim($('select[name=shipping_status]').val());
	        listTable.filter['return_status'] = $.trim($('select[name=return_status]').val());
	        listTable.filter['shipping_id'] = $.trim($('select[name=shipping_id]').val());
	        listTable.filter['is_ok'] = $.trim($('select[name=is_ok]').val());
	        listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
	        listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}

		function swap_more_search(){
	        var more_search = document.getElementById('more_search');

	        if (more_search.style.display == ''){
	            more_search.style.display='none';
	        }else{
	            more_search.style.display='';
	        }
	    }
		//]]>
	</script>
<div class="main">
		<div class="main_title"><span class="l">退货单管理 >> 退货单列表</span>
		<?php if (check_perm('order_return_edit')): ?>
		<span class="r"><a href="order_return/add" class="add">新增</a></span>
		<?php endif; ?>
		</div>
        <div class="blank5"></div>
        <div class="search_row">
			<form name="search" action="javascript:search(); ">
			订单编号：<input type="text" class="ts" name="order_sn" id="order_sn" value="" style="width:100px;" />
			退货单编号：<input type="text" class="ts" name="return_sn" id="return_sn" value="" style="width:100px;" />
			退货人：<input type="text" class="ts" name="consignee" id="consignee" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			退单状态：<?php print form_dropdown('status',$status_list);?>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			<span style="cursor: hand; color:red;" onclick ="swap_more_search()">高级搜索</span>
		    <div id="more_search" style="display:none;margin-top:8px;">
		        <select name="return_status" id="select9">
		          <option value="-1" selected>退货单状态</option>
		          <option value="0">未客审</option>
		          <option value="1">已客审</option>
		          <option value="4">已作废</option>
		        </select>

		        <select name="shipping_status" id="select10">
		          <option value="-1" selected>发货状态</option>
		          <option value="0">未入库</option>
		          <option value="1">已入库</option>
		        </select>

			<select name="pay_status" id="select11">
		          <option value="-1" selected>付款状态</option>
		          <option value="0">未付款</option>
		          <option value="1">已付款</option>
		        </select>

		        <select name="is_ok" id="select101">
		          <option value="-1" selected>完结状态</option>
		          <option value="0">未完结</option>
		          <option value="1">已完结</option>
		        </select>
		        <?php print form_dropdown('shipping_id',get_pair($all_shipping,'shipping_id','shipping_name', array(''=>'配送方式'))); ?>
		        下单时间<input type="text" name="start_time" id="start_time" />~<input type="text" name="end_time" id="end_time" />
			    </div>
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="150">退货单号</th>
			      <th>下单时间</th>
			      <th>退货人</th>
			      <th>退货金额</th>
				  <th>配送方式</th>
				  <th>退单状态</th>
				  <th>锁定状态</th>
				  <th width="50">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->return_sn; ?><br/> &nbsp; 原订单<a href ="order/info/<?php print $row->order_id; ?>" target="_blank"><?php print $row->order_sn; ?></a></td>
					<td><?php print $row->buyer; ?><br /><?php print $row->short_return_time; ?></td>
					<td><?php print $row->consignee; ?><?php print !empty($row->tel)?'TEL: '.$row->tel:''; ?><br /><?php print $row->address; ?></td>
					<td><?php print $row->formated_return_price; ?></td>
					<td><?php print $row->shipping_name; ?></td>
					<td><?php print $row->return_status_name; ?>,<?php print $row->pay_status_name; ?>,<?php print $row->shipping_status_name; ?></td>
					<td><?php if ($row->is_ok == 1): ?>
						<font color="red">已完结</font>
						<?php else: ?>
							<?php if ($row->lock_admin > 0): ?><!--<img src="public/images/lock.gif" />换样式显示 By Rock--><span class="lockForGif"></span><?php print $row->lock_name; ?><?php endif; ?>
						<?php endif; ?>
					</td>

					<td>
						<a href="order_return/edit/<?php print $row->return_id; ?>" title="编辑" class="edit"></a>
						<?php if (empty($row->confirm_admin) && check_perm('order_return_edit') && $row->lock_admin == $my_id): ?>
                    	<!--<a onclick="check_confirm_del('<?php print $row->return_id; ?>');return false;" href="#" title="删除" class="del"></a>-->
			      		<?php endif; ?>
                                                <?php if (!empty($row->apply_id) && check_perm('order_return_edit')): ?>
                                                    <br/>
                                                    <a href="apply_return/info/<?php print $row->apply_id; ?>" title="查看关联申请退货单" target="_blank">查看申请单</a>
                                                <?php endif; ?>
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
