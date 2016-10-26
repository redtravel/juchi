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
	<div class="main_title"><span class="l">注册号管理 >> 编辑 </span><a href="register_code/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('register_code/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('id'=>$row->id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">注册号:</td>
				<td class="item_input"><?php print form_input('register_no',$row->register_no,'class="textbox require" style="width:230px;" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">MDC_1:</td>
				<td class="item_input">
					<select name="medical_1">
						<?php foreach($mdc_1 as $val) { ?>
							<option value='<?php echo $val->field_id; ?>' <?php if($row->medical1 == $val->field_id) echo 'selected'; ?> ><?php echo $val->field_value1; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">MDC_2:</td>
				<td class="item_input">
					<select name="medical_2">
						<?php foreach($mdc_2 as $val) { ?>
							<option value='<?php echo $val->field_id; ?>' <?php if($row->medical2 == $val->field_id) echo 'selected'; ?> ><?php echo $val->field_value1.'--'.$val->field_value2; ?></option>
						<?php } ?>
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