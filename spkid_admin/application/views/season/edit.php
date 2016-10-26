<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('season_name', '请填写季节名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">季节管理 >> 编辑 </span><a href="season/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('season/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('season_id'=>$row->season_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">季节名称:</td>
				<td class="item_input"><?php print form_input('season_name',$row->season_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
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