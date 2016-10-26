<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('source_code', '请填写订单来源CODE');
			validator.required('source_name', '请填写订单来源名称');
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">订单来源管理 >> 新增</span>  <a href="order_source/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('order_source/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">订单来源CODE:</td>
				<td class="item_input">
                <input name="source_code" class="textbox require" id="source_code" /></td>
			</tr>
			<tr>
				<td class="item_title">订单来源名称:</td>
				<td class="item_input"><input name="source_name" class="textbox require" id="source_name" /></td>
			</tr>
			<tr>
			  <td class="item_title">是否使用:</td>
			  <td class="item_input"><input name="is_use" type="radio" id="RadioGroup1_1" value="0" checked="checked" />
否
  <input type="radio" name="is_use" value="1" id="RadioGroup1_0" />
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