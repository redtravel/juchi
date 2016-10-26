<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('flag_name', '请填写产地名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">产地管理 >> 新增 </span><a href="flag/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('flag/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">产地名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'flag_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">所属大洲:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'continent','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">图片地址:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'flag_url','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'sort_order','class' => 'textbox')); ?>数值大的在前
				</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?>禁用</label>
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?>启用</label>
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