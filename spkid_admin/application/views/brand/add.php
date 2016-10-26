<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/brand.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		show_flag();
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('brand_name', '请填写品牌名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">品牌管理 >> 新增 </span><a href="brand/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('brand/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="100px">品牌名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'brand_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">品牌Logo:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'brand_logo','class'=> 'textbox'));?>(图片必须为jpg格式)
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌Banner:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'brand_banner','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌视频:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'brand_video','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
			  <td class="item_title">品牌简介:</td>
			  <td class="item_input"><textarea name="brand_info" id="brand_info" cols="90" rows="3"></textarea></td>
		  </tr>
			<tr>
				<td class="item_title">品牌故事:</td>
				<td class="item_input">
					<?php print $this->ckeditor->editor('brand_story');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌首字母:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'brand_initial','class' => 'textbox','style'=>'width:24px;')); ?>
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
				<td class="item_title">产地:</td>
				<td class="item_input">
					<select name="flag_id" onChange="show_flag()" >
					<?php foreach ($all_flag as $flag): ?>
						<option value="<?php print $flag->flag_id?>" rel="<?php print $flag->flag_url?>" ><?php print $flag->flag_name?></option>
					<?php endforeach ?>
					</select>
					<span id="flag_span"></span>
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