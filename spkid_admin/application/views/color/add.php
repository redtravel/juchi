<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/colorselector.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('color_name', '请填写颜色名称');
			//validator.reg('color_sn',/^.{4}$/,'请正确填写颜色编码');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">颜色管理 >> 新增 </span><a href="color/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('color/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">颜色组:</td>
				<td class="item_input"><?php print form_dropdown('group_id', get_pair($all_group,'group_id','group_name'));?></td>
			</tr>
			<tr>
				<td class="item_title">颜色名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'color_name','class'=> 'textbox require'));?></td>
			</tr>
<!--			<tr>
				<td class="item_title">颜色编码:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'color_sn','class'=> 'textbox require'));?> 比如0062，用4位表示</td>
			</tr>-->
			<tr>
				<td class="item_title">颜色码:</td>
				<td class="item_input" id="color_add">
                <input name="color_color" type="text" class="textbox" id="color_color" onclick="ColorSelecter.Show(this);" value="" /><div style="height:15px; width:15px; border:1px solid #000; display:inline-block; margin-left:2px;"; id="color_color_show"></div>
                </td>
			</tr>
			<tr>
				<td class="item_title">颜色图片:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'color_img','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'sort_order','class' => 'textbox')); ?>
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