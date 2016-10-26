<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('hotword_name', '热门关键字名称');
			validator.required('hotword_url', '请填写热门关键字URL');
			validator.isInt('click_count', '请正确填写点击量');
			validator.isInt('sort_order', '请正确填写排序号');
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l"><span class="l">热门关键字管理 >> 编辑</span></span> <span class="r"><a href="hotword/index" class="return r">返回列表</a></span></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('hotword/proc_edit/'.$arr->hotword_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">热门关键字名称:</td>
				<td class="item_input">
                <input name="hotword_name" class="textbox require" value="<?php echo $arr->hotword_name?>" id="hotword_name" type="text" /></td>
			</tr>
			<tr>
				<td class="item_title">热门关键字URL:</td>
				<td class="item_input"><input name="hotword_url"  value="<?php echo $arr->hotword_url?>" class="textbox require" type="text" id="hotword_url" /></td>
			</tr>
			<tr>
			  <td class="item_title">点击量:</td>
			  <td class="item_input"><input name="click_count"  class="textbox require" id="click_count" value="<?php echo $arr->click_count?>" /></td>
		  </tr>
		  <tr>
			  <td class="item_title">类别:</td>
			  <td class="item_input">
			  	<select name="hotword_type" id="hotword_type">
			  		<option value="0" <?php if($arr->hotword_type == 0 ) print "selected"; ?> >商品</option>
			  		<option value="1" <?php if($arr->hotword_type == 1 ) print "selected"; ?> >课程</option>
			  	</select>
			  </td>
		  </tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input"><input name="sort_order" class="textbox require" id="sort_order" value="<?php echo $arr->sort_order?>" /></td>
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