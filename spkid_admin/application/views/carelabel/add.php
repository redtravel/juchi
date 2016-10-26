<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('carelabel_name', '请填写洗标名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">洗标管理 >> 新增 </span><span class="r"><a href="carelabel/index" class="return">返回列表</a></span></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('carelabel/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">洗标名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'carelabel_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">图片地址:</td>
				<td class="item_input">
					<?php print form_upload(array('name'=> 'carelabel_url','class'=> 'textbox'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'sort_order','class' => 'textbox')); ?>
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