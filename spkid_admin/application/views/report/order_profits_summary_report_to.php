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
			// if ((eles['start_time'].value=='' || eles['end_time'].value=='') && eles['is_end_time'].value=='' || eles['is_start_time'].value==''  ){
			// 	alert('错误：请输入报表期间!');
			// 	return false;
			// }
			if((eles['end_time'].value < eles['start_time'].value) || (eles['is_end_time'].value < eles['is_start_time'].value)){
				alert('错误：期间的结束时间早于或等于开始时间!');
				return false;
			}
		}
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 订单利润汇总表（已完结，对应已完结退货）</span> </div>
    <div class="blank5"></div>
	  	<div class="search_row">
			<form method="post" action="report/order_profits_summary_report_to" name="theForm"  onsubmit = "return checkForm()">				
				<span style="color: #FF0000;font: 12px verdana;"></span>财审期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
				<span style="color: #FF0000;font: 12px verdana;"></span>完结期间：<input type="text" name="is_start_time" id="is_start_time" value="<?php echo $is_start_time;?>" /><input type="text" name="is_end_time" id="is_end_time" value="<?php echo $is_end_time;?>" />
				订单号: <input type="text" name="order_sn" value="<?php echo $order_sn;?>" size="20" />
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
					<td colspan="25" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>单号</th>
				    <th>财审日期</th>
					<th>销售员</th>
					<th>订单状态</th>
					<th>订单类型</th>
					<th>客户名称</th>
					<th>省--市</th>
					<th>运单号</th>
					<th>发货时间</th>					
					<th>赠品成本</th>
					<th>含税销售金额</th>
					<th>含税销售成本</th>
					<th>退货金额</th>
					<th>退货成本</th>
					<th>不含税毛利</th>
					<th>不含税毛利率</th>
					<th>是否开票</th>
					<th>收款日期</th>
					<th>收款方式</th>
                                        
                                        <th>实际运费</th>
                                        <th>包裹重量</th>
                                        <th>理论运费</th>
                                        <th>订单毛利</th>
                                        <th>订单毛利率</th>
                                        <th>销售员</th>
				</tr>
				<?php foreach ($order_profits as $op_val): 
					$profit = (($op_val->shop_price - $op_val->cost_price - $op_val->tui_price + $op_val->return_cost_price - $op_val->zenpin_cost) / 1.17) - (($op_val->zenpin_cost * 1.05 / 1.17) * 0.17);
					$profit_margin = round(($profit * 1.17 / $op_val->shop_price),4) * 100 ;
					$profit1 = (($op_val->shop_price - $op_val->cost_price - $op_val->tui_price + $op_val->return_cost_price - $op_val->zenpin_cost) / 1.17) - (($op_val->zenpin_cost * 1.05 / 1.17) * 0.17)-$op_val->real_shipping_fee;
					$profit_margin2 = round(($profit1 * 1.17 / $op_val->shop_price),4) * 100 ;                                        
				?>
					<tr>
						<td><?php print $op_val->trans_sn; ?></td>
						<td><?php print $op_val->finance_check_date; ?></td>
						<td><?php print $op_val->admin_name; ?></td>
						<td>
							<?php if ($op_val->is_ok) print '已完结'; ?>
							<?php if ($op_val->has_return): ?>
								<a style="color:red;" href="order_return/index/order_sn/<?php print $op_val->trans_sn ?>">有退货</a>
							<?php endif ?>
						</td>
						<td><?php print $op_val->source_name; ?></td>
						<td><?php print $op_val->consignee; ?></td>
						<td><?php print $op_val->province_name.'--'.$op_val->city_name; ?></td>
						<td><?php print $op_val->invoice_no; ?></td>
						<td><?php print $op_val->shipping_date; ?></td>
						<td><?php print $op_val->zenpin_cost; ?></td>
						<td><?php print $op_val->shop_price; ?></td>
						<td><?php print $op_val->cost_price; ?></td>
						<td><?php print $op_val->tui_price; ?></td>
						<td><?php print $op_val->return_cost_price; ?></td>
						<td><?php print round($profit,2);?></td>
						<td><?php print ($profit_margin) ? $profit_margin.'%' : ''; ?></td>						
						<td><?php print $op_val->invoice_title; ?></td>
						<td><?php print $op_val->payment_date; ?></td>
						<td><?php print $op_val->pay_name; ?></td>
                                                
                                                <td><?php print $op_val->real_shipping_fee; ?></td>
                                                <td><?php print $op_val->recheck_weight_unreal; ?></td>
                                                <td><?php print $op_val->recheck_shipping_fee; ?></td>
                                                <td><?php print round($profit1,2); ?></td>
                                                <td><?php print ($profit_margin2) ? $profit_margin2.'%' : ''; ?></td>
                                                <td><?php print $op_val->saler; ?></td>
					</tr>
				<?php endforeach; ?>
                                <tr>
					<td colspan="25" class="bottomTd"> </td>
				</tr>
			</table>
			<?php endif; ?>
  			<div class="blank5"></div>
	  	</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
