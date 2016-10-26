<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('register_no', '请填写注册号');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">注册号管理 >> 新增 </span><a href="register_code/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('register_code/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">注册号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'register_no','class'=> 'textbox require','style'=> 'width:230px;'));?></td>
			</tr>
			<tr>
				<td class="item_title">MDC_1:</td>
				<td class="item_input">
					<select name="medical_1">
						<?php foreach($mdc_1 as $val) print "<option value='{$val->field_id}'>{$val->field_value1}--{$val->field_value2}</option>"?>
					</select>
				</td>	
			</tr>

			<tr>
				<td class="item_title">MDC_2:</td>
				<td class="item_input">
					<select name="medical_2">
						<?php foreach($mdc_2 as $v) print "<option value='{$v->field_id}'>{$v->field_value1}--{$v->field_value2}</option>"?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>