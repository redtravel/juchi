<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('region_name', '请填写地区名称');
		return validator.passed();
	}

	//]]>
</script>
<div class="main">
  <div class="main_title"><span class="l">地区管理 >> 新增<?php if($region_type == 0){echo '一';}elseif($region_type == 1){echo '二';}elseif($region_type == 2){echo '三';}else{echo '四';}?>级地区</span><a href="region/index/<?php echo $region_type?>/<?php echo $parent_id?>" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('region/proc_add/'.$region_type.'/'.$parent_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">地区名称:</td>
				<td class="item_input">
                <input name="region_name" class="textbox require" id="region_name" /></td>
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