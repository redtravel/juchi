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
			validator.required('carelabel_name', '请填写洗标名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">洗标管理 >> 编辑 </span><a href="carelabel/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('carelabel/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('carelabel_id'=>$row->carelabel_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">洗标名称:</td>
				<td class="item_input"><?php print form_input('carelabel_name',$row->carelabel_name,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">图片地址:</td>
				<td class="item_input">
					<?php print form_upload('carelabel_url','','class="textbox" '.($perm_edit?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES , $row->carelabel_url); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input('sort_order',$row->sort_order,'class="textbox" '.($perm_edit?'':'disabled')); ?>
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