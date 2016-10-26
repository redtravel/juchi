<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">规格详情图管理 >> 添加 </span><a href="size/image_index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('size/proc_image_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">品牌:</td>
				<td class="item_input">
					<select name="brand_id">
						<?php 
						foreach ($all_brand as $key => $value) {
							echo "<option value='{$value->brand_id}'>{$value->brand_name}</option>";
						}
						;?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">分类:</td>
				<td class="item_input">
					<select name="category_id">
						<?php 
						foreach ($all_category as $key => $value) {
							echo "<option value='{$value->category_id}'>{$value->level_space} {$value->category_name}</option>";
						}
						;?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">性别:</td>
				<td class="item_input">
					<label>男<?php print form_radio(array('name'=>'sex', 'value'=>1,'checked'=>TRUE)); ?></label>
					<label>女<?php print form_radio(array('name'=>'sex', 'value'=>2)); ?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">详情图:</td>
				<td class="item_input">
					<?php print form_upload('image_url');?>
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