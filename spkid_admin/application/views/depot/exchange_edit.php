<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	<?php if ($is_edit): ?>
		//$('input[type=text][name=depot_out_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
	<?php endif; ?>
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('source_depot_id', '请选择出库仓库');
			validator.selected('dest_depot_id', '请选择入库仓库');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">调仓管理 &gt;&gt; 调仓单基础信息</span> <span class="r">[ <a href="/exchange/exchange_list">返回列表 </a>]</span></div>
	<div class="produce">
		<ul>
	         <li class="p_sel conf_btn"><span>基础信息</span></li>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_out_exchange/<?php print $row->exchange_id; ?>'"><span>出库商品</span></li>
	         <?php if ($row->out_audit_admin > 0): ?>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_in_exchange/<?php print $row->exchange_id; ?>'"><span>入库商品</span></li>
	         <?php endif; ?>
	     </ul>

	<div class="pc base">
	<?php print form_open('exchange/proc_edit_exchange',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('exchange_id'=>$row->exchange_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">调仓单编号:</td>
				<td class="item_input"><?php print $row->exchange_code;?></td>
			</tr>
			<tr>
				<td class="item_title">出库仓库:</td>
				<td class="item_input">
				<?php if ($is_edit_out): ?>
					<?php if ($row->has_product_out): ?>
					<?php print form_dropdown('source_depot_id',$depot_list,$row->source_depot_id,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('source_depot_id',$depot_list,$row->source_depot_id);?>
					<?php endif; ?>
				<?php else: ?>
					<?php print form_dropdown('source_depot_id',$depot_list,$row->source_depot_id,' disabled="disabled"');?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">入库仓库:</td>
				<td class="item_input">
				<?php if ($is_edit_in): ?>
					<?php if ($row->has_product_in): ?>
					<?php print form_dropdown('dest_depot_id',$depot_list,$row->dest_depot_id,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('dest_depot_id',$depot_list,$row->dest_depot_id);?>
					<?php endif; ?>
				<?php else: ?>
					<?php print form_dropdown('dest_depot_id',$depot_list,$row->dest_depot_id,' disabled="disabled"');?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">出库总数量:</td>
				<td class="item_input"><?php print $row->exchange_out_number;?></td>
			</tr>
			<tr>
				<td class="item_title">入库总数量:</td>
				<td class="item_input"><?php print $row->exchange_in_number;?></td>
			</tr>
			<tr>
				<td class="item_title">锁定/锁定人:</td>
				<td class="item_input"><?php print $row->lock_status_name." / ".$row->lock_name;?></td>
			</tr>
			<tr>
				<td class="item_title">状态/操作人:</td>
				<td class="item_input">
				<?php print $row->exchange_status_name." / ".$row->oper_name;?>
				</td>
			</tr>
			<tr>
				<td class="item_title">调仓备注:</td>
				<td class="item_input">
				<?php if ($is_edit_in): ?>
				<?php print form_input(array('name'=>'exchange_reason', 'class'=>'textbox','value'=>$row->exchange_reason));?>
				<?php else: ?>
				<?php print $row->exchange_reason;?>
				<?php endif; ?>
				</td>
			</tr>
			<?php if ($is_edit_in): ?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
	</div>
	</div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>