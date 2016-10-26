<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('page_name', '请填写单页名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">单页专题管理 >> 新增</span><a href="single_page/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('single_page/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">单页名称:</td>
				<td class="item_input">
                <input name="page_name" type="text" class="textbox require" id="page_name" />
                </td>
			</tr>
			<tr>
				<td class="item_title">单页内容:</td>
				<td class="item_input"><?php print $this->ckeditor->editor('page_content');?></td>
			</tr>
			<tr>
			  <td class="item_title">启用:</td>
			  <td class="item_input">
               <input name="is_use" type="radio" value="0" checked="checked" />未启用
			   <input type="radio" name="is_use" value="1" />启用
			       
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