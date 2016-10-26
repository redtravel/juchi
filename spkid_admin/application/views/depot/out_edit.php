<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	<?php if ($is_edit): ?>
		$('input[type=text][name=depot_out_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
	<?php endif; ?>
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('depot_out_type', '请选择出库类型');
			validator.selected('depot_depot_id', '请选择出库仓库');
			validator.required('depot_out_date', '请填写实际出库时间');
			return validator.passed();
	}
	function change_provider(dom) {
		var url = '/purchase_batch/get_provider_batch/'+dom.value;
		$("#batch_id option").remove();
		var emptyStr = '<option value="">请选择</option>';
		$("#batch_id").append(emptyStr);
		$. get(url, function(result) {
			$.each($.parseJSON(result), function() {
		        var htmlStr = '<option value="'+this.batch_id+'">'+this.batch_code+'</option>';
		        $("#batch_id").append(htmlStr);
		    });
        });
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">出库管理 &gt;&gt; 出库单基础信息</span> <span class="r">[ <a href="/depotio/out">返回列表 </a>]</span></div>
	<div class="produce">
		<ul>
	         <li class="p_sel conf_btn"><span>基础信息</span></li>
	         <li class="p_nosel conf_btn" onclick="location.href='/depotio/edit_out_product/<?php print $row->depot_out_id; ?>'"><span>出库商品</span></li>
	     </ul>

	<div class="pc base">
	<?php print form_open('depotio/proc_edit_out',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('depot_out_id'=>$row->depot_out_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">出库单编号:</td>
				<td class="item_input"><?php print $row->depot_out_code;?></td>
			</tr>
			<tr>
				<td class="item_title">出库类型:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
					<?php if ($row->has_product): ?>
					<?php print form_dropdown('depot_out_type',$type_list,$row->depot_out_type,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('depot_out_type',$type_list,$row->depot_out_type);?>
					<?php endif; ?>
				<?php else: ?>
					<?php print $type_list[$row->depot_out_type];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">出库仓库:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
					<?php if ($row->has_product): ?>
					<?php print form_dropdown('depot_depot_id',$depot_list,$row->depot_depot_id,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('depot_depot_id',$depot_list,$row->depot_depot_id);?>
					<?php endif; ?>
				<?php else: ?>
					<?php print $depot_list[$row->depot_depot_id];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">实际出库时间:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'depot_out_date', 'class'=>'textbox require','value'=>date('Y-m-d',strtotime($row->depot_out_date))));?>
				<?php else: ?>
				<?php print date('Y-m-d',strtotime($row->depot_out_date));?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">供应商:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
					<?php if ($row->has_product): ?>
					<?php print form_dropdown('provider_id',$provider_list,$row->provider_id,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('provider_id',$provider_list,$row->provider_id,'onchange=change_provider(this);');?>
					<?php endif; ?>
				<?php else: ?>
					<?php print $provider_list[$row->provider_id];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">批次:</td>
				<td class="item_input">
				<?php if ($is_edit && !$row->has_product): ?>
					<select id="batch_id" name="batch_id">
						<?php foreach($batch_list as $key=>$val): ?>
						<option value="<?php print $key; ?>" <?php if($key==$row->batch_id):?>selected="selected"<?php endif;?>><?php print $val; ?></option>
						<?php endforeach; ?>
                    </select>
				<?php else: ?>
					<?php print $batch_list[$row->batch_id];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">出库单总数量:</td>
				<td class="item_input"><?php print $row->depot_out_number;?></td>
			</tr>
			<tr>
				<td class="item_title">出库单总金额:</td>
				<td class="item_input"><?php print $row->depot_out_amount;?></td>
			</tr>
			<tr>
				<td class="item_title">锁定/锁定人:</td>
				<td class="item_input"><?php print $row->lock_status_name." / ".$row->lock_name;?></td>
			</tr>
			<tr>
				<td class="item_title">状态/操作人:</td>
				<td class="item_input">
				<?php print $row->depot_out_status_name." / ".$row->oper_name;?>
				</td>
			</tr>
			<tr>
				<td class="item_title">出库备注:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'depot_out_reason', 'class'=>'textbox','value'=>$row->depot_out_reason));?>
				<?php else: ?>
				<?php print $row->depot_out_reason;?>
				<?php endif; ?>
				</td>
			</tr>
			<?php if ($is_edit): ?>
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
</div></div></div>
<?php include(APPPATH.'views/common/footer.php');?>