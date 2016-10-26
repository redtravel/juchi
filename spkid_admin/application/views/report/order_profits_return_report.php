<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=is_start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=is_end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});

		function checkForm(){
			var eles = document.forms['theForm'];
			// if (eles['start_time'].value=='' || eles['end_time'].value==''){
			// 	alert('错误：请输入报表期间!');
			// 	return false;
			// }
			if(eles['end_time'].value < eles['start_time'].value ){
				alert('错误：期间的结束时间早于或等于开始时间!');
				return false;
			}
		}
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 订单退货表（已完结）</span> </div>
    <div class="blank5"></div>
	  	<div class="search_row">
			<form method="post" action="report/order_profits_return_report" name="theForm"  onsubmit = "return checkForm()">				
				<span style="color: #FF0000;font: 12px verdana;"></span>财审期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
				<span style="color: #FF0000;font: 12px verdana;"></span>完结期间：<input type="text" name="is_start_time" id="is_start_time" value="<?php echo $is_start_time;?>" /><input type="text" name="is_end_time" id="is_end_time" value="<?php echo $is_end_time;?>" />
				订单号 <input type="text" name="order_sn" value="<?php echo $order_sn;?>" size="20" />
				销售员：<input type="text" name="admin_name" value="<?php echo $admin_name;?>" size="20" />
				<input type="submit" class="am-btn am-btn-primary" value="搜索" />
                                <input type="submit" name="export" class="am-btn am-btn-primary" value="导出" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if ((isset($order_profits) && !empty($order_profits))): ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>单号</th>
				    <th>退货日期</th>
					<th>财审日期</th>
					<th>原订单销售人员</th>
					<th>退货录入员</th>
					<th>含税销售金额</th>
					<th>含税销售成本</th>
					<th>不含税商品毛利</th>
					<th>订单收款日期</th>
					<th>收款方式</th>
				</tr>
				<?php foreach ($order_profits as $key => $op_val): 
					$profit = -(($op_val->paid_price - $op_val->cost_price) / 1.17);
				?>
					<tr>
						<td><?php print $op_val->trans_sn; ?></td>
						<td><?php print $op_val->create_date; ?></td>
						<td><?php print $op_val->finance_date; ?></td>
						<td><?php print $op_val->order_name; ?></td>
						<td><?php print $op_val->return_name; ?></td>
						<td><?php print ($op_val->paid_price == 0.00) ? $op_val->paid_price : '-'.$op_val->paid_price; ?></td>
						<td><?php print ($op_val->paid_price = 0.00) ? $op_val->cost_price : '-'.$op_val->cost_price; ?></td>
						<td><?php print round($profit,2); ?></td>
						<td><?php print $op_val->payment_date; ?></td>
						<td><?php print $op_val->pay_name; ?></td>
					</tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="12" class="bottomTd"> </td>
				</tr>
			</table>
			<?php endif; ?>
  			<div class="blank5"></div>
	  	</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
