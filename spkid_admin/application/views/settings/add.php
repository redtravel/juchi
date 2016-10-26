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
	<div class="main_title"><span class="l">参数配置管理 >> 新增 </span><a href="system_settings/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('system_settings/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">参数名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'config_name','class'=> 'textbox require','style'=> 'width:230px;'));?></td>
			</tr>
			<tr>
				<td class="item_title">参数代码:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'config_code','class'=> 'textbox require','style'=> 'width:230px;'));?></td>
			</tr>

			<tr>
				<td class="item_title">显示类型:</td>
				<td class="item_input">
					<?php foreach ($display_types as $key => $value) { ?>
						<label><?php print form_radio('type', $key, true)?><?php echo $value;?></label>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">存储类型:</td>
				<td class="item_input">
					<?php foreach ($store_types as $key => $value) { ?>
						<label><?php print form_radio('storage_type', $key, true)?><?php echo $value;?></label>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">数值:</td>
				<td class="item_input">
					<?php print form_textarea(array('name'=>'config_value','rows'=>4, 'cols'=>30));?> <small><span style="color:red;"> 注：</span> 当存储类型选择 <span style="color:red;">数组 </span> 时，请填写数组，如：array ('yes' => array ( 0 => 1, 1 => '是' ), 'no' => array ( 0 => 2,1 => '否',))</small>
				</td>
			</tr>
			<tr>
				<td class="item_title">备注:</td>
				<td class="item_input">
					<?php print form_textarea(array('name'=>'comment','rows'=>4, 'cols'=>30));?><small>  <span style="color:red;">	注： </span>当显示类型选择 <span style="color:red;">单选框 </span> 时，请填写数组，如：array ('yes' => array ( 0 => 1, 1 => '是' ), 'no' => array ( 0 => 2,1 => '否',))</small>
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
