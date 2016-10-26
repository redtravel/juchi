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
			if($.trim($('input[type=password][name=admin_password]').val())!=''){
				validator.equal('admin_password','admin_password_repeat', '两次输入的密码不相符');
			}
			validator.isEmail('admin_email', 'Email格式不正确', false);
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">管理员管理 >> 编辑 </span><a href="admin/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open('admin/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('admin_id'=>$row->admin_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">管理员帐号:</td>
				<td class="item_input"><?php print $row->admin_name; ?></td>
			</tr>
			<tr>
				<td class="item_title">真实姓名:</td>
				<td class="item_input">
					<?php print form_input('realname',$row->realname,'class="textbox" '.($perm_edit?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">Email:</td>
				<td class="item_input">
					<?php print form_input('admin_email',$row->admin_email,'class="textbox" '.($perm_edit?'':'disabled')); ?>
				</td>
			</tr>
			<?php if ($perm_edit): ?>
				<tr>
					<td class="item_title">新密码:</td>
					<td class="item_input">
						<?php print form_password(array('name' => 'admin_password','class' => 'textbox','value' => '')); ?>
					 	如不更改密码，不需要填写。
					 </td>
				</tr>
				<tr>
					<td class="item_title">重复新密码:</td>
					<td class="item_input">
						<?php print form_password(array('name' => 'admin_password_repeat','class' => 'textbox','value' => '')); ?>
					</td>
				</tr>
			<?php endif ?>
			
			<tr>
				<td class="item_title">性别:</td>
				<td class="item_input">
					<label><?php print form_radio('sex',1,$row->sex==1,$perm_edit?'':'disabled'); ?>男</label>
					<label><?php print form_radio('sex',2,$row->sex==2,$perm_edit?'':'disabled'); ?>女</label>
					
				</td>
			</tr>
			<tr>
				<td class="item_title">生日:</td>
				<td class="item_input"><?php print form_input('birthday',$row->birthday,'class="textbox" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">入职时间:</td>
				<td class="item_input"><?php print form_input('join_date',$row->join_date,'class="textbox" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio('user_status',1,$row->user_status,$perm_edit?'':'disabled');?>可用</label>
					<label><?php print form_radio('user_status',0,!$row->user_status,$perm_edit?'':'disabled');?>停用</label>
					
				</td>
			</tr>
			<tr>
				<td class="item_title">部门名称:</td>
				<td class="item_input"><?php print form_input('dept_name',$row->dept_name,'class="textbox" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<?php if ($perm_edit): ?>
				<tr>
					<td class="item_title"></td>
					<td class="item_input">
						<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
					</td>
				</tr>
			<?php endif ?>
			
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>