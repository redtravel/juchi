<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('config_code', '请填写英文名称');
			validator.required('config_name', '请填写中文名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">参数配置管理 >> 编辑 </span><a href="system_settings/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('system_settings/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('id'=>$row->id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">参数名称:</td>
				<td class="item_input"><?php echo $row->config_name;?></td>
			</tr>
			<tr>
				<td class="item_title">参数代码:</td>
				<td class="item_input"><?php print form_input('config_code',$row->config_code,'class="textbox require" style="width:230px;" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">数值:</td>
				<td class="item_input"><?php print form_input('congif_value',$row->congif_value,'class="textbox require" style="width:230px;" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">备注:</td>
				<td class="item_input"><?php print form_input('comment',$row->comment,'class="textbox require" style="width:230px;" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					config_value
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>