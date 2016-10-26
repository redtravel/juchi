<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/brand.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
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
	<div class="main_title"><span class="l">品牌管理 >> 编辑 </span><a href="brand/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('brand/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('brand_id'=>$row->brand_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">品牌名称:</td>
				<td class="item_input"><?php print form_input('brand_name',$row->brand_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">品牌Logo:</td>
				<td class="item_input">
					<?php print form_upload('brand_logo','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES, $row->brand_logo);?>(图片必须为jpg格式)
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌Banner:</td>
				<td class="item_input">
					<?php print form_upload('brand_banner','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES, $row->brand_banner); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌视频:</td>
				<td class="item_input">
					<?php print form_upload('brand_video','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php if($row->brand_video):?>
					<a href="<?php print PUBLIC_DATA_IMAGES . $row->brand_video?>" height="30" target="_blank" >
						查看
					</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
			  <td class="item_title">品牌简介:</td>
			  <td class="item_input"><textarea <?php echo $perm_edit?'':'disabled="disabled"';?> name="brand_info" id="brand_info" cols="90" rows="3"><?php echo $row->brand_info?></textarea></td>
		  </tr>
			<tr>
				<td class="item_title">品牌故事:</td>
				<td class="item_input">
					<?php print $this->ckeditor->editor('brand_story', $row->brand_story); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌首字母:</td>
				<td class="item_input">
					<?php print form_input('brand_initial',strtoupper($row->brand_initial),'class="textbox" style="width:18px;" '.($perm_edit?'':'disabled')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input('sort_order',$row->sort_order,'class="textbox" style="width:25px" '.($perm_edit?'':'disabled')); ?>
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
				<td class="item_title">产地:</td>
				<td class="item_input">
					<select name="flag_id" onChange="show_flag()" <?php print $perm_edit?'':'disabled'?>>
					<?php foreach ($all_flag as $flag): ?>
						<option value="<?php print $flag->flag_id?>" rel="<?php print $flag->flag_url?>" <?php print $row->flag_id==$flag->flag_id?'selected':''?>><?php print $flag->flag_name?></option>
					<?php endforeach ?>
					</select>
					<span id="flag_span"></span>
				</td>
			</tr>
			<?php if($perm_edit):?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>