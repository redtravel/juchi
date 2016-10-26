<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<script type="text/javascript" src="public/js/colorselector.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('color_name', '请填写颜色名称');
//			validator.reg('color_sn',/^.{4}$/,'请正确填写颜色编码');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">颜色管理 >> 编辑 </span><a href="color/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('color/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('color_id'=>$row->color_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">颜色组:</td>
				<td class="item_input"><?php print form_dropdown('group_id', get_pair($all_group,'group_id','group_name'),$row->group_id, $perm_edit?'':'disabled');?></td>
			</tr>
			<tr>
				<td class="item_title">颜色名称:</td>
				<td class="item_input"><?php print form_input('color_name',$row->color_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">颜色编码:</td>
				<td class="item_input"><?php print form_input('color_sn',$row->color_sn,'class="textbox require" disabled');?> 比如0062，用4位表示</td>
			</tr>
			<tr>
				<td class="item_title">颜色码:</td>
				<td class="item_input" id="color_add">
                <input name="color_color" type="text" class="textbox" id="color_color" onclick="ColorSelecter.Show(this);" value="<?php echo $row->color_color?>" <?php print $perm_edit?'':'disabled';?> /><div style="height:15px; <?php print $row->color_color?"background-color:{$row->color_color};":''?> width:15px; border:1px solid #000; display:inline-block; margin-left:2px;"; id="color_color_show"></div>
                </td>
			</tr>
			<tr>
				<td class="item_title">颜色图片:</td>
				<td class="item_input">
					<?php print form_upload('color_img','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES, $row->color_img); ?>
				</td>
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