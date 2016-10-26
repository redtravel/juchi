<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/voucher.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
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
	});
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">现金券发放管理 >> 添加 </span><a href="voucher/edit/<?php print $campaign->campaign_id;?>" class="return r">返回活动</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('voucher/proc_add_release',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('campaign_id'=>$campaign->campaign_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">现金券描述:</td>
				<td class="item_input"><?php print form_input('voucher_name', '', 'class="require textbox"');?></td>
			</tr>
			<tr>
				<td class="item_title">现金券面值:</td>
				<td class="item_input"><?php print form_input('voucher_amount', '', 'class="require textbox"');?> </td>
			</tr>
			<tr>
				<td class="item_title">最小订单金额:</td>
				<td class="item_input"><?php print form_input('min_order', '', 'class="require textbox"');?> </td>
			</tr>
			<?php if ($config['repeat']): ?>
				<tr>
					<td class="item_title">可复用次数:</td>
					<td class="item_input"><?php print form_input('repeat_number', '', 'class="require textbox" size="3"');?> </td>
				</tr>
			<?php endif ?>
			<tr>
				<td class="item_title">有效期:</td>
				<td class="item_input">
				<?php if ($config['sys']): ?>
					<?php print form_input('expire_days', '', 'class="require textbox" size="3"');?> 天
				<?php else: ?>
					<?php print form_input('start_date', '', 'class="require textbox" size="10"');?> 
					<?php print form_input('start_time', '00:00:00', 'class="require textbox" size="8"');?> 
					至
					<?php print form_input('end_date', '', 'class="require textbox" size="10"');?> 
					<?php print form_input('end_time', '23:59:59', 'class="require textbox" size="8"');?> 
				<?php endif ?>
				</td>
			</tr>
			<?php if ($config['worth']): ?>
				<tr>
					<td class="item_title">兑换价值:</td>
					<td class="item_input"><?php print form_input('worth', '', 'class="require textbox" size="3"');?> </td>
				</tr>
			<?php endif ?>
			<?php if ($config['logo']): ?>
				<tr>
					<td class="item_title">现金券LOGO:</td>
					<td class="item_input"><?php print form_upload('logo', '');?> </td>
				</tr>
			<?php endif ?>
			<?php if ($config['rules']): ?>
				<tr>
					<td class="item_title">发放规则:</td>
					<td class="item_input">
						<select name="rule" onchange="change_release_rule();">
						<?php foreach ($config['rules'] as $rule) print "<option value='{$rule}'>{$release_rules[$rule]}</option>" ?>
						</select>
					</td>
				</tr>
				<?php if (in_array('number', $config['rules'])): ?>
					<tr class="rule_number">
						<td class="item_title">发放数量:</td>
						<td class="item_input">
							<?php print form_input('rule_number', '', 'class="require textbox" size="5"');?>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('list', $config['rules'])): ?>
					<tr class="rule_list">
						<td class="item_title">用户ID:</td>
						<td class="item_input">
							<textarea name="rule_list" rows="4" cols="60"></textarea>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('sn', $config['rules'])): ?>
					<tr class="rule_sn">
						<td class="item_title">现金券号:</td>
						<td class="item_input">
							多个券号请以逗号分隔<br/>
							<textarea name="rule_sn" rows="4" cols="60"></textarea>
						</td>
					</tr>
				<?php endif ?>
				<?php if (in_array('rule', $config['rules'])): ?>
					<tr class="rule_rule">
						<td class="item_title">用户筛选规则:</td>
						<td class="item_input">
							注册时间：
							<?php print form_input('rule_reg_date_min', '', 'class="textbox"');?>
							至
							<?php print form_input('rule_reg_date_max', '', 'class="textbox"');?>
						</td>
					</tr>
				<?php endif ?>
				
			<?php endif ?>
			<tr>
				<td class="item_title">发放备注:</td>
				<td class="item_input">
					<input type="text" name="release_note" size="80" />
				</td>
			</tr>
			
			<tr>
				<td class="item_title">&nbsp;</td>
				<td class="item_input">
					<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"');?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>		
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>