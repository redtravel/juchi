<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/colorselector.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('type_name', '请填写建议类型名称');
			validator.required('type_code', '请填写建议类型CODE');
			validator.required('type_color', '请填写建议类型颜色');
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">建议类型管理 >> 编辑 </span><a href="order_advice_type/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('order_advice_type/proc_edit/'.$type->type_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">建议类型名称:</td>
				<td class="item_input">
                <input name="type_name" class="textbox require" value="<?php echo $type->type_name?>" id="type_name" /></td>
			</tr>
			<tr>
				<td class="item_title">建议类型CODE:</td>
				<td class="item_input"><input name="type_code" value="<?php echo $type->type_code?>" class="textbox require" id="type_code" /></td>
			</tr>
            <tr>
			  <td class="item_title">建议类型颜色:</td>
			  <td class="item_input" id="color_add">
              <input type="text" name="type_color" id="type_color" value="<?php echo $type->type_color?>" class="textbox require" onclick="ColorSelecter.Show(this);" /><div style=" <?php echo $type->type_color?"background-color:{$type->type_color};":''?>;height:15px; width:15px; border:1px solid #000; display:inline-block; margin-left:2px;"; id="type_color_show"></div>
              </td>
		  </tr>

			<tr>
			  <td class="item_title">是否使用:</td>
			  <td class="item_input"><input name="is_use" type="radio" id="RadioGroup1_1" value="0" checked="checked" <?php echo $type->is_use == 0 ? ' checked="checked"' : '';?> />
否
  <input type="radio" name="is_use" value="1" id="RadioGroup1_0" <?php echo $type->is_use == 1 ? ' checked="checked"' : '';?> />
是 </td>
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