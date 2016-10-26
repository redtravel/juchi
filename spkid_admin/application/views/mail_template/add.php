<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('template_code', '请填写模板TAG');
			validator.required('template_name', '请填写模板名称');
			validator.required('template_subject', '请填写模板标题');
			validator.isInt('template_priority', '请填写优先级',true);
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">邮件短信模板管理 >> 新增</span><a href="mail_template/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('mail_template/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">模板TAG:</td>
				<td class="item_input">
                <input name="template_code" type="text" class="textbox require" id="template_code" />
                </td>
			</tr>
			<tr>
			  <td class="item_title">模板名称:</td>
			  <td class="item_input"><input name="template_name" type="text" class="textbox require" id="template_name" /></td>
		  </tr>
			<tr>
			  <td class="item_title">HTML:</td>
			  <td class="item_input"><input name="is_html" type="radio" value="0" checked="checked" />
			    否
                  <input type="radio" name="is_html" value="1" />
              是</td>
		  </tr>
			<tr>
			  <td class="item_title">模板标题:</td>
			  <td class="item_input"><input name="template_subject" type="text" class="textbox require" id="template_subject" /></td>
		  </tr>
			<tr>
			  <td class="item_title">优先级:</td>
			  <td class="item_input"><input name="template_priority" type="text" class="textbox require" id="template_priority" /></td>
		  </tr>
			<tr>
				<td class="item_title">邮件内容:</td>
				<td class="item_input"><?php print $this->ckeditor->editor('template_content');?></td>
			</tr>
                        <tr>
				<td class="item_title">短信内容:</td>
                                <td class="item_input"><textarea name="sms_content" id="sms_content" style="width: 100%;"></textarea></td>
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