<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('flag_name', '请填写产地名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">产地管理 >> 编辑 </span><a href="flag/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('flag/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('flag_id'=>$row->flag_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">产地名称:</td>
				<td class="item_input"><?php print form_input('flag_name',$row->flag_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">所属大洲:</td>
				<td class="item_input"><?php print form_input('continent',$row->continent,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">图片地址:</td>
				<td class="item_input">
					<?php print form_upload('flag_url','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES, $row->flag_url);?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input('sort_order',$row->sort_order,'class="textbox" '.($perm_edit?'':'disabled')); ?>数值大的在前
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