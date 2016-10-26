<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('style_name', '请填写风格名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">风格管理 >> 编辑 </span><a href="style/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('style/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('style_id'=>$row->style_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">风格名称:</td>
				<td class="item_input"><?php print form_input('style_name',$row->style_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input('sort_order',$row->sort_order,'class="textbox" '.($perm_edit?'':'disabled')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio('is_use',0,!$row->is_use,$perm_edit?'':'disabled'); ?>禁用</label>
					<label><?php print form_radio('is_use',1,$row->is_use,$perm_edit?'':'disabled'); ?>启用</label>
					
				</td>
			</tr>
			<?php if ($perm_edit): ?>
				<tr>
					<td class="item_title"></td>
					<td class="item_input">
						<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
					</td>
				</tr>
			<?php endif ?>
			
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>