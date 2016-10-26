<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/depotio/show_sel_win';
		function search(){
			listTable.filter['type'] = 1;
			listTable.filter['purchase_code'] = $.trim($('input[type=text][name=purchase_code]').val());
			listTable.filter['purchase_provider'] = $.trim($('select[name=purchase_provider]').val());
			listTable.filter['purchase_type'] = $.trim($('select[name=purchase_type]').val());
			listTable.filter['purchase_status'] = $.trim($('select[name=purchase_status]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.loadList();
		}

		//var   moOBJ   =   dialogArguments;
		function gotoLogin()
		{
			var temp=document.getElementsByName("is_choose");
			var check_id=0;
			for (i=0;i<temp.length;i++){
		        if(temp[i].checked)
		        {
		           check_id=temp[i].value;
		           break;
		        }
			}
			if(check_id == 0)
			{
				alert("请选择要入库的来源单号");
				return false;
			}
			var check_arr = check_id.split("|||");
		    //moOBJ.pass=true;
			//moOBJ.order_id=check_arr[0];
			//moOBJ.order_sn=check_arr[1];
            window.opener.document.getElementById('order_id').value = check_arr[0];
            window.opener.document.getElementById('order_sn').value = check_arr[1];
		    window.close();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">采购单列表</div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			采购单编号：<input type="text" class="ts" name="purchase_code" value="" style="width:100px;" />
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			采购类型：<?php print form_dropdown('purchase_type',$type_list);?>
			供应商：<?php print form_dropdown('purchase_provider',$provider_list);?>
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
					<th width="140px">采购单编号</th>
					<th>采购类型</th>
					<th>采购发起时间</th>
					<th>供应商</th>
					<th>采购总金额</th>
					<th>采购总数量</th>
					<th>采购完工数量</th>
					<th>状态</th>
					<th>锁定人</th>
					<th>审核人</th>
					<th>终止人</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print form_radio(array('name'=>'is_choose','value'=>$row->purchase_id."|||".$row->purchase_code));?><?php print $row->purchase_code; ?></td>
					<td><?php print $row->purchase_type_name; ?></td>
					<td><?php print date('Y-m-d',strtotime($row->purchase_order_date)); ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->purchase_amount; ?></td>
					<td><?php print $row->purchase_number; ?></td>
					<td><?php print $row->purchase_finished_number; ?></td>
					<td><?php print $row->purchase_status_name; ?></td>
					<td><?php print $row->lock_name; ?></td>
					<td><?php print $row->purchase_check_name; ?></td>
					<td><?php print $row->purchase_break_name; ?></td>
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
			<div class="blank5"></div>
			<BR><BR><BR>
			  <div   align=center>
			  <input type="button"  value="确定"  class="am-btn am-btn-secondary" onclick="gotoLogin();" />
			  <input type="button"  value="取消"  class="am-btn am-btn-secondary" onclick="window.close();" />

			  </div>
<?php if($full_page): ?>
		</div>
	</div>
</body>
</html>
<?php endif; ?>
