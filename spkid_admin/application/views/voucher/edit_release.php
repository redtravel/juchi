<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/voucher.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	//<![CDATA[
	//listTable.url = 'voucher/search_product';
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('voucher_name', '请填写现金券描述');
		validator.isPrice('voucher_amount', '请填写现金券面值',true);
		validator.required('min_order', '请填写最小订单金额');
		
		if($(':input[name=start_date]').length>0){
			
			validator.reg('start_date',/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/,'请填写有效期开始日期');
			validator.reg('start_time',/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/,'请填写有效期开始时间');
		}
			
		if($(':input[name=end_date]').length>0){
			validator.reg('end_date',/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/,'请填写有效期结束日期');
			validator.reg('end_time',/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/,'请填写有效期结束时间');
		}
			
		if($(':input[name=expire_days]').length>0)
			validator.isInt('expire_days', '请填写有效期天数');
		if($(':input[name=repeat_number]').length>0) 
			validator.isInt('repeat_number', '请填写可复用次数');
		return validator.passed();
	}
	
	$(function(){
		$(':input[name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$(':input[name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$(':input[name=rule_reg_date_min]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$(':input[name=rule_reg_date_max]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		change_release_rule();
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false,width:250});
	});
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">现金券发放管理 >> 编辑 </span><a href="voucher/edit/<?php print $campaign->campaign_id;?>" class="return r">返回活动</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('voucher/proc_edit_release',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('campaign_id'=>$campaign->campaign_id, 'release_id'=>$release->release_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="item_title" style="text-align:left">
					<?php print form_button('audit_release','审核','onclick="operate_release(\'audit\');" '.($perms['audit']?'':'disabled')); ?>
					<?php print form_button('back_release','撤销','onclick="operate_release(\'back\');" '.($perms['back']?'':'disabled')); ?>
					<?php if($perms['back']) print '撤销理由：'.form_input('back_note',$release->back_note,'class="require textbox" size="60"').'(撤销时填写)'?>
				</td>
			</tr>
			<tr>
				<td class="item_title">现金券描述:</td>
				<td class="item_input" colspan="3"><?php print form_input('voucher_name', $release->voucher_name, 'class="require textbox" '.($perms['edit']?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">现金券面值:</td>
				<td class="item_input" colspan="3"><?php print form_input('voucher_amount', $release->voucher_amount, 'class="require textbox" '.($perms['edit']?'':'disabled'));?> </td>
			</tr>
			<tr>
				<td class="item_title">最小订单金额:</td>
				<td class="item_input" colspan="3"><?php print form_input('min_order', $release->min_order, 'class="require textbox" '.($perms['edit']?'':'disabled'));?> </td>
			</tr>
			<?php if ($config['repeat']): ?>
				<tr>
					<td class="item_title">可复用次数:</td>
					<td class="item_input" colspan="3"><?php print form_input('repeat_number', $release->repeat_number, 'class="require textbox" size="3" '.($perms['edit']?'':'disabled'));?> </td>
				</tr>
			<?php endif ?>
			<tr>
				<td class="item_title">有效期:</td>
				<td class="item_input" colspan="3">
				<?php if ($config['sys']): ?>
					<?php print form_input('expire_days', $release->expire_days, 'class="require textbox" size="3" '.($perms['edit']?'':'disabled'));?> 天
				<?php else: ?>
					<?php print form_input('start_date', substr($release->start_date,0,10), 'class="require textbox" size="10" '.($perms['edit']?'':'disabled'));?> 
					<?php print form_input('start_time', substr($release->start_date,11,8), 'class="require textbox" size="8" '.($perms['edit']?'':'disabled'));?> 
					至
					<?php print form_input('end_date', substr($release->end_date,0,10), 'class="require textbox" size="10" '.($perms['edit']?'':'disabled'));?> 
					<?php print form_input('end_time', substr($release->end_date,11,8), 'class="require textbox" size="8" '.($perms['edit']?'':'disabled'));?> 
				<?php endif ?>
				</td>
			</tr>
			<?php if ($config['worth']): ?>
				<tr>
					<td class="item_title">兑换价值:</td>
					<td class="item_input" colspan="3"><?php print form_input('worth', $release->worth, 'class="require textbox" size="3" '.($perms['edit']?'':'disabled'));?> </td>
				</tr>
			<?php endif ?>
			<?php if ($config['logo']): ?>
				<tr>
					<td class="item_title">现金券LOGO:</td>
					<td class="item_input" colspan="3">
						<?php print form_upload('logo', '');?> 
						<?php print img_tip(PUBLIC_DATA_IMAGES,$release->logo);?>
						<label><?php if ($release->logo) print form_checkbox('delete_logo',1, FALSE,$perms['edit']?'':'disabled') . '删除原图'?></label>
					</td>
				</tr>
			<?php endif ?>
			<?php if ($config['rules']): ?>
				<tr>
					<td class="item_title">发放规则:</td>
					<td class="item_input" colspan="3">
						<select name="rule" onchange="change_release_rule();" <?php print $perms['edit']?'':'disabled'; ?>>
						<?php foreach ($config['rules'] as $rule) print "<option value='{$rule}' ".($release->release_rules['rule']==$rule?'selected':'').">{$release_rules[$rule]}</option>" ?>
						</select>
					</td>
				</tr>
				<?php if (in_array('number', $config['rules'])): ?>
					<tr class="rule_number">
						<td class="item_title">发放数量:</td>
						<td class="item_input" colspan="3">
							<?php print form_input('rule_number', isset($release->release_rules['rule_number'])?$release->release_rules['rule_number']:'', 'class="require textbox" size="5" '.($perms['edit']?'':'disabled'));?>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('list', $config['rules'])): ?>
					<tr class="rule_list">
						<td class="item_title">用户ID:</td>
						<td class="item_input" colspan="3">
							<textarea name="rule_list" rows="4" cols="60" <?php print $perms['edit']?'':'disabled';?>><?php print isset($release->release_rules['rule_list'])?$release->release_rules['rule_list']:''?></textarea>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('sn', $config['rules'])): ?>
					<tr class="rule_sn">
						<td class="item_title">现金券号:</td>
						<td class="item_input" colspan="3">
							多个券号请以逗号分隔<br/>
							<textarea name="rule_sn" rows="4" cols="60" <?php print $perms['edit']?'':'disabled';?>><?php print isset($release->release_rules['rule_sn'])?$release->release_rules['rule_sn']:''?></textarea>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('rule', $config['rules'])): ?>
					<tr class="rule_rule">
						<td class="item_title">用户筛选规则:</td>
						<td class="item_input" colspan="3">
							注册时间：
							<?php print form_input('rule_reg_date_min', isset($release->release_rules['rule_reg_date_min'])?$release->release_rules['rule_reg_date_min']:'', 'class="textbox" '.($perms['edit']?'':'disabled'));?>
							至
							<?php print form_input('rule_reg_date_max', isset($release->release_rules['rule_reg_date_max'])?$release->release_rules['rule_reg_date_max']:'', 'class="textbox" '.($perms['edit']?'':'disabled'));?>
						</td>
					</tr>
				<?php endif ?>
				
			<?php endif ?>
			<tr>
				<td class="item_title">发放备注:</td>
				<td class="item_input" colspan="3">
					<?php print form_input('release_note', $release->release_note, 'size="80" '.($perms['edit']?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">添加时间:</td>
				<td class="item_input"><?php print $release->create_date; ?></td>
				<td class="item_title">添加人:</td>
				<td class="item_input">
				<?php print $release->create_admin ? $all_admin[$release->create_admin]->admin_name : ''; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">审核时间:</td>
				<td class="item_input">
				<?php if ($release->release_status>0): ?>
					<?php print $release->audit_date;?>
					<a href="voucher/query/release_id/<?php print $release->release_id;?>" target="_blank">查看报表</a>
				<?php else: ?>
					未审核
				<?php endif ?>
				</td>
				<td class="item_title">审核人:</td>
				<td class="item_input"><?php print $release->audit_admin ? $all_admin[$release->audit_admin]->admin_name:''; ?></td>
			</tr>
			<tr>
				<td class="item_title">撤销时间:</td>
				<td class="item_input"><?php print $release->release_status==2 ? $campaign->stop_date:'未撤销 '; ?></td>
				<td class="item_title">撤销人:</td>
				<td class="item_input"><?php print $release->back_admin ? $all_admin[$release->back_admin]->admin_name:''; ?></td>
			</tr>
			<?php if ($release->release_status==2): ?>
				<tr>
					<td class="item_title">撤销备注:</td>
					<td class="item_input" colspan="3"><?php print $release->back_note;?></td>
				</tr>
			<?php endif ?>
			
			<?php if ($perms['edit']): ?>			
			<tr>
				<td class="item_title">&nbsp;</td>
				<td class="item_input" colspan="3">
					<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"');?>
				</td>
			</tr>
			<?php endif ?>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>		
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>