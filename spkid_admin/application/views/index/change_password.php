<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('admin_password', '请填写密码');
			validator.equal('admin_password','repeat_password', '两次输入的密码不一致');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">更改密码 </span></div>
	<div class="blank5"></div>
	<?php print form_open('index/proc_change_password',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">密码:</td>
				<td class="item_input"><?php print form_password('admin_password','','class="require"');?></td>
			</tr>
			<tr>
				<td class="item_title">重复密码:</td>
				<td class="item_input"><?php print form_password('repeat_password','','class="require"');?></td>
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