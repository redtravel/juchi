<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/shipping_fcheck/info/<?php print $shipping_fcheck->batch_id; ?>';
		function search() {
			listTable.loadList();
		}
		
		function check_upload() {
			if($("#file").val() == '') {
				alert('请上传对帐文件');
            	return false;
			}
			return true;
		}
		
		function do_confirm(action,batch_id){
	        if(!confirm('确定执行该操作？')) return false;
	        location.href="/shipping_fcheck/"+action+"/"+batch_id;
	    }
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">物流对帐详情</span>
		<span class="r">
		<a class="add" href="shipping_fcheck">物流对帐管理</a>
		</span>
		</div>

		<!-- 状态 -->
		<div class="search_row">
		    <input type="button" style="width:100px;" value="锁定" <?php if(!$operation_list['lock']): ?>disabled="true"<?php endif; ?> onclick="location.href='/shipping_fcheck/lock/<?php print $shipping_fcheck->batch_id; ?>'" />
		    <input type="button" style="width:100px;" value="解锁" <?php if(!$operation_list['unlock']): ?>disabled="true"<?php endif; ?> onclick="location.href='/shipping_fcheck/unlock/<?php print $shipping_fcheck->batch_id; ?>'" />
		    <?php if ($shipping_fcheck->batch_type == 4): ?>
		    <input type="button" style="width:100px;" value="审核" <?php if(!$operation_list['shipping_check']): ?>disabled="true"<?php endif; ?> onclick="do_confirm('deny_check','<?php print $shipping_fcheck->batch_id; ?>')" />
		    <?php else: ?>
		    <input type="button" style="width:100px;" value="物流审核" <?php if(!$operation_list['shipping_check']): ?>disabled="true"<?php endif; ?> onclick="do_confirm('shipping_check','<?php print $shipping_fcheck->batch_id; ?>')" />
		    <input type="button" style="width:100px;" value="物流反审核" <?php if(!$operation_list['shipping_uncheck']): ?>disabled="true"<?php endif; ?> onclick="do_confirm('shipping_uncheck','<?php print $shipping_fcheck->batch_id; ?>')" />
		    <input type="button" style="width:100px;" value="财务审核" <?php if(!$operation_list['finance_check']): ?>disabled="true"<?php endif; ?> onclick="do_confirm('finance_check','<?php print $shipping_fcheck->batch_id; ?>')" />
		    <?php endif; ?>
		    <input type="button" style="width:100px;" value="删除对帐单" <?php if(!$operation_list['del']): ?>disabled="true"<?php endif; ?> onclick="do_confirm('del','<?php print $shipping_fcheck->batch_id; ?>')" /><br/>
		</div>
		
		<div style="height:5px;"></div>
		<div class="blank5"></div>
		
		<div class="search_row">
		<form action="/shipping_fcheck/upload_more/<?php print $shipping_fcheck->batch_id; ?>" method="post" onsubmit="javascript:return check_upload();" name="uploadForm" enctype="multipart/form-data">
			对帐单批次号：<?php print $shipping_fcheck->batch_sn; ?> &nbsp;&nbsp; 快递公司：<?php print $shipping_fcheck->shipping_name; ?> &nbsp;&nbsp;
			物流审核：<?php if($shipping_fcheck->shipping_check): ?> 已审核 <?php print $shipping_fcheck->shipping_check_user; $shipping_fcheck->shipping_check_date; ?> <?php else: ?> 未审核 <?php endif; ?> &nbsp;&nbsp;
			财务审核：<?php if($shipping_fcheck->finance_check): ?> 已审核 <?php print $shipping_fcheck->finance_check_user; $shipping_fcheck->finance_check_date; ?> <?php else: ?> 未审核 <?php endif; ?> &nbsp;&nbsp;
		    <?php if($shipping_fcheck->lock_user): ?>当前由 <?php print $shipping_fcheck->lock_user; ?> 锁定 <?php endif; ?><br/>
			修正数据增量导入：
		    <input type="file" id="file" name="file" />
		    <input type="hidden" id="batch_id" name="batch_id" value="<?php print $shipping_fcheck->batch_id; ?>" />
		    <input type="submit" name="submit_bt" value="修正数据导入" <?php if(!$operation_list['upload']): ?>disabled="true"<?php endif; ?> />
		    <?php if(!empty($file_path)): ?>
			该对帐单有不匹配数据请  <a href=" <?php print $file_path; ?>" style="color:red" target="_blank">右键另存下载</a>
		    <?php endif; ?>
		</form>
		</div>
		
		<div style="height:5px;"></div>
		<div class="blank5"></div>
		
		<div ><!-- class="search_row" -->
		<form name="search" action="javascript:search(); ">
			<!--
			<select name="finance_check" id="finance_check">
				<option value="">财审状态</option>
				<option value="1">已财审</option>
				<option value="0">未财审</option>
			</select>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			-->
		</form>
		</div>
		<div class="blank5"></div>
		
		<div id="listDiv">
<?php endif; ?>

			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="11" class="topTd"> </td>
				</tr>
				<tr class="row">
	                <th>订单号</th>
	                <th>运单号</th>
	                <?php if ($shipping_fcheck->batch_type != 4): ?>
	                <th>到达地</th>
	                <th>重量</th>
	                <th>件数</th>
	                <th>应收货款</th>
	                <th>实收货款</th>
	                <th>运费</th>
	                <th>手续费</th>
	                <th>签收时间</th>
	                <?php endif; ?>
	                <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td align="center"><a target="_blank" href="order/info/<?php print $row->order_id; ?>"><?php print $row->order_sn; ?></a></td>
					<td align="center"><?php print $row->invoice_no; ?></td>
					<?php if ($shipping_fcheck->batch_type != 4): ?>
					<td align="center"><?php print $row->destination; ?></td>
					<td align="center"><?php print $row->weight; ?></td>
					<td align="center"><?php print $row->goods_number; ?></td>
					<td align="center"><?php print $row->order_amount; ?></td>
					<td align="center"><?php print $row->cod_amount; ?></td>
					<td align="center"><?php print $row->express_fee; ?></td>
					<td align="center"><?php print $row->cod_fee; ?></td>
					<td align="center"><?php print $row->sign_date; ?></td>
					<?php endif; ?>
					<td align="center">
					<?php if($operation_list['del_sub']): ?>
					<a href="/shipping_fcheck/del_sub/<?php print $row->batch_id; ?>?sub_id=<?php print $row->id; ?>" onclick="return confirm('确定要删除？')">删除</a>
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
			
			<div class="blank5"></div>
			
			<div class="search_row">
				代收合计：  <?php print $batch_summery->cod_amount; ?><br/>
				扣除运费：  <?php print $batch_summery->express_fee; ?><br/>
				扣除手续费：<?php print $batch_summery->cod_fee; ?><br/>
				实返代收款：<?php print $batch_summery->total; ?><br/>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>