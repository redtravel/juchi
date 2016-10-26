<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>

	<script type="text/javascript">
		//<![CDATA[

		$(function(){
        	//$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			//$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'order_change/index';
		function search(){
			listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
	        listTable.filter['change_sn'] = $.trim($('input[name=change_sn]').val());
	        listTable.filter['consignee'] = $.trim($('input[name=consignee]').val());
	        listTable.filter['provider_goods'] = $.trim($('input[name=provider_goods]').val());

	        listTable.filter['composite_status'] = $.trim($('select[name=status]').val());
	        listTable.filter['invoice_status'] = $.trim($('select[name=invoice_status]').val());
	        //listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
	       //listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
                listTable.filter['odd']=$(':checkbox:checked[name=odd]').length;
                listTable.filter['pick']=$(':checkbox:checked[name=pick]').length;
                listTable.loadList();
		}

		//]]>
	</script>
<div class="main">
		<div class="main_title"><span class="l">换货单管理 >> 换货单列表</span>
		<?php if (check_perm('order_change_edit')): ?>
		<span class="r"><a href="order_change/add" class="add">新增</a></span>
		<?php endif; ?>
		</div>
        <div class="blank5"></div>
        <div class="search_row">
			<form name="search" action="javascript:search(); ">
			订单编号：<input type="text" class="ts" name="order_sn" id="order_sn" value="" style="width:100px;" />
			换货单编号：<input type="text" class="ts" name="change_sn" id="change_sn" value="" style="width:100px;" />
			换货人：<input type="text" class="ts" name="consignee" id="consignee" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			换货单状态：<?php print form_dropdown('status',$status_list);?>
			<!--<select name="invoice_status" id="invoice_status">
		        <option value="-1" selected>快递状态</option>
		        <option value="0">未签收</option>
		        <option value="1">已签收</option>
		        <option value="2">疑难件</option>
		        <option value="3">疑难解决</option>
		    </select>-->
                        <label><input type="checkbox" name="odd" value="1" />问题单</label>
                        <label><input type="checkbox" name="pick" value="1" />拣货中</label>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="100">换货单号</th>
				  <th>订单单号</th>
			      <th>下单时间</th>
			      <th>换货人</th>
			      <th>换货单状态</th>
				  <th>锁定状态</th>
				  <th width="128">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->change_sn; ?></td>
					<td><a href ="order/info/<?php print $row->order_id; ?>" target="_blank"><?php print $row->order_sn; ?></a></td>
					<td><?php print $row->buyer; ?><br /><?php print substr($row->create_date,0,10); ?></td>
					<td><?php print $row->consignee; ?><?php print !empty($row->tel)?'TEL: '.$row->tel:''; ?><br /><?php print $row->address; ?></td>
                                        <td><?php print implode('&nbsp;',  format_change_status($row,TRUE));?></td>
					<td><?php if ($row->is_ok == 1): ?>
						<font color="red">已完结</font>
						<?php else: ?>
							<?php if ($row->lock_admin > 0): ?><!--<img src="public/images/lock.gif" />换样式显示 By Rock--><span class="lockForGif"></span><?php print $row->lock_name; ?><?php endif; ?>
                            
						<?php endif; ?>
					</td>

					<td>
						<a href="order_change/edit/<?php print $row->change_id; ?>" title="编辑" class="edit"></a>
						<?php if (($row->change_status == 3 || $row->change_status == 4) && empty($row->confirm_admin) && check_perm('order_change_edit') && $row->lock_admin == $my_id): ?>
                    	<!--<a onclick="check_confirm_del('<?php print $row->change_id; ?>');return false;" href="#" title="删除" class="del"></a>-->
			      		<?php endif; ?>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="7" class="bottomTd"> </td>
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
