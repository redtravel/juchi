<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/shipping_fcheck/index';
		function search() {
			listTable.filter['batch_sn'] = $.trim($('input[type=text][name=batch_sn]').val());
			listTable.filter['invoice_no'] = $.trim($('input[type=text][name=invoice_no]').val());
			listTable.filter['lock_status'] = $.trim($('select[name=lock_status]').val());
			listTable.filter['shipping_check'] = $.trim($('select[name=shipping_check]').val());
			listTable.filter['finance_check'] = $.trim($('select[name=finance_check]').val());
			listTable.loadList();
		}
		
		function check_upload() {
			if($("select[name='shipping_id']").val() == 0) {
				alert('请选择快递公司');
            	return false;
			}
			if($("#batch_type").val() == '') {
				alert('请选择导入类型');
            	return false;
			}
			if($("#file").val() == '') {
				alert('请上传对帐文件');
            	return false;
			}
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">物流对帐管理</span>
		</div>
		
		<div class="search_row">
			<form name="upload" action="shipping_fcheck/upload" method="post" enctype="multipart/form-data" onsubmit="return check_upload();">
			新建对帐批次：
			<?php print form_dropdown('shipping_id',get_pair($all_shipping,'shipping_id','shipping_name', array('0'=>'请选择快递公司'))); ?>
			
			<select id="batch_type" name="batch_type">
				<option value="">选择导入类型</option>
				<option value="1">运费+COD</option>
				<option value="2">COD</option>
				<option value="3">运费</option>
				<option value="4">拒收</option>
			</select>
			
			<input type="file" id="file" name="file" value="" />
			
			<input type="submit" class="am-btn am-btn-primary" value="导入新批次" />
			 &nbsp;&nbsp;&nbsp;&nbsp;代收款、运费： 
		    <a target="_blank" style="color:red;" href="public/import/_template/shipping_fcheck.xml">模板下载（右键另存）</a>
			&nbsp;&nbsp;&nbsp;&nbsp;拒收： 
		    <a target="_blank" style="color:red;" href="public/import/_template/shipping_fcheck_deny.xml">模板下载（右键另存）</a>
		    <div style="text-align:left; padding-right:20px; color:red; ">注意：如要增量导入修正后的数据，请进行相应的批次操作。</div>
			</form>
		</div>
		<div style="height:5px;"></div>
		<div class="blank5"></div>
		<div class="search_row">
		<form name="search" action="javascript:search(); ">
			批次号：<input name="batch_sn" type="text" id="batch_sn" >
    		运单号：<input name="invoice_no" type="text" id="invoice_no" >
			<select name="lock_status">
				<option value="">锁定状态</option>
				<option value="0">未锁定</option>
				<option value="1">已锁定</option>
			</select>
		     <select name="shipping_check" id="shipping_check">
		          <option value="">物流确认</option>
		          <option value="1">已确认</option>
		          <option value="0">未确认</option>
		     </select>
		     <select name="finance_check" id="finance_check">
		          <option value="">财审状态</option>
		          <option value="1">已财审</option>
		          <option value="0">未财审</option>
		     </select>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
		</div>
		<div class="blank5"></div>
		
		<div id="listDiv">
<?php endif; ?>

			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
	                <th>对帐单批次号</th>
	                <th>导入类型</th>
	                <th>导入日期</th>
	                <th>物流审核</th>
	                <th>财务审核</th>
	                <th>锁定状态</th>
	                <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td align="center">
					<a href="shipping_fcheck/info/<?php print $row->batch_id; ?>"><?php print $row->batch_sn; ?></a>
					</td>
					<td align="center">
					<?php if($row->batch_type == 1): ?>
					运费+COD
					<?php elseif($row->batch_type == 2): ?>
					COD
					<?php elseif($row->batch_type == 3): ?>
					运费
					<?php elseif($row->batch_type == 4): ?>
					拒收
					<?php endif; ?>
					</td>
					<td align="center"><?php print $row->create_date; ?></td>
					<td align="center">
						<?php if($row->shipping_check == 0): ?>
						未审核
						<?php else: ?>
						已审核  <?php print $row->shipping_check_user; ?>  <?php print $row->shipping_check_date; ?>
						<?php endif; ?>
					</td>
					<td align="center">
						<?php if($row->batch_type != 4): ?>
						<?php if($row->finance_check == 0): ?>
						未审核
						<?php else: ?>
						已审核  <?php print $row->finance_check_user; ?>  <?php print $row->finance_check_date; ?>
						<?php endif; ?>
						<?php endif; ?>
					</td>
					<td align="center">
						<?php if(!empty($row->lock_user)): ?>
							<?php print $row->lock_user; ?>
						<?php endif; ?>
					</td>
					<td align="center"><a href="shipping_fcheck/info/<?php print $row->batch_id; ?>" >查看</a></td>
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