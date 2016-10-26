<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	});
	function check_form(){
		var validator = new Validator('mainForm');
			//validator.required('location_name', '请填写储位名称');
			validator.required('location_code1', '请填写储位编码1');
			validator.required('location_code2', '请填写储位编码2');
			validator.required('location_code3', '请填写储位编码3');
			validator.required('location_code4', '请填写储位编码4');
                        validator.required('location_code5', '请填写储位编码5');
			document.forms['mainForm'].elements['location_name'].value = document.forms['mainForm'].elements['location_code1'].value + '-' +document.forms['mainForm'].elements['location_code2'].value + '-' + document.forms['mainForm'].elements['location_code3'].value + '-' + document.forms['mainForm'].elements['location_code4'].value + '-' + document.forms['mainForm'].elements['location_code5'].value;
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
	<span class="l">储位管理 &gt;&gt; 储位编辑</span>
	 <span class="r">[ <a href="/depot/location_list">返回列表 </a>]</span>
	 <?php if (check_perm('location_edit')): ?>
	<span class="r"><a href="depot/add_location" class="add">新增</a></span>
	<?php endif; ?> </div>
	<div class="blank5"></div>
	<?php print form_open('/depot/proc_edit_location',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('location_id'=>$row->location_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">储位编码:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
				<?php print form_input(array('name'=> 'location_code1','class'=> 'textbox require','size'=>'2','value' => $row->location_code1));?>-
				<?php print form_input(array('name'=> 'location_code2','class'=> 'textbox require','size'=>'2','value' => $row->location_code2));?>-
				<?php print form_input(array('name'=> 'location_code3','class'=> 'textbox require','size'=>'2','value' => $row->location_code3));?>-
				<?php print form_input(array('name'=> 'location_code4','class'=> 'textbox require','size'=>'2','value' => $row->location_code4));?>-
				<?php print form_input(array('name'=> 'location_code5','class'=> 'textbox require','size'=>'2','value' => $row->location_code5));?>
				<?php else: print $row->location_code1.'-'.$row->location_code2.'-'.$row->location_code3.'-'.$row->location_code4.'-'.$row->location_code5; ?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">储位名称:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
				<?php print form_input(array('name'=> 'location_name','class'=> 'textbox require','size'=>'16','value' => $row->location_name,'disabled' =>'disabled'));?>
				<?php else: print $row->location_name; ?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">所属仓库:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
				<?php print form_dropdown('depot_id',$depot_list,$row->depot_id);?>
				<?php else: ?>
				<?php print form_dropdown('depot_id',$depot_list,$row->depot_id,'disabled="disabled"');?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
					<label>可用<?php print form_radio(array('name'=>'is_use','value'=>1,'checked'=>$row->is_use==1));?></label>
					<label>停用<?php print form_radio(array('name'=>'is_use','value'=>0,'checked'=>$row->is_use!=1));?></label>
				<?php else: ?>
					<label>可用<?php print form_radio(array('name'=>'is_use','value'=>1,'checked'=>$row->is_use==1,'disabled'=>'disabled'));?></label>
					<label>停用<?php print form_radio(array('name'=>'is_use','value'=>0,'checked'=>$row->is_use!=1,'disabled'=>'disabled'));?></label>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
				<?php if ($can_edit): ?>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>