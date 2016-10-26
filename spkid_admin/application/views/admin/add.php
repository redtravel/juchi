<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=birthday]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
		$('input[type=text][name=join_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('admin_name', '请填写管理员帐号');
			validator.required('admin_password', '请设置密码');
			validator.equal('admin_password','admin_password_repeat', '两次输入的密码不相符');
			validator.isEmail('admin_email', 'Email格式不正确', false);
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">管理员管理 >> 新增 </span><a href="admin/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open('admin/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">管理员帐号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'admin_name','class'=> 'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title">真实姓名:</td>
				<td class="item_input">
					<?php print form_input(array('name'=> 'realname','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">Email:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'admin_email','class' => 'textbox')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">新密码:</td>
				<td class="item_input">
					<?php print form_password(array('name' => 'admin_password','class' => 'textbox')); ?>
				 	
				 </td>
			</tr>
			<tr>
				<td class="item_title">重复新密码:</td>
				<td class="item_input">
					<?php print form_password(array('name' => 'admin_password_repeat','class' => 'textbox')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">性别:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'sex', 'value'=>1,'checked'=>TRUE)); ?>男</label>
					<label><?php print form_radio(array('name'=>'sex', 'value'=>2)); ?>女</label>
				</td>
			</tr>
			<tr>
				<td class="item_title">生日:</td>
				<td class="item_input"><?php print form_input(array('name'=>'birthday', 'class'=>'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title">入职时间:</td>
				<td class="item_input"><?php print form_input(array('name'=>'join_date','class'=>'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label>可用<?php print form_radio(array('name'=>'user_status','value'=>1,'checked'=>TRUE));?></label>
					<label>停用<?php print form_radio(array('name'=>'user_status','value'=>0));?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">部门名称:</td>
				<td class="item_input"><?php print form_input(array('name'=>'dept_name', 'class'=>'textbox'));?></td>
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