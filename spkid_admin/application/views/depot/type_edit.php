<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('depot_type_code', '请填写出入库类型编号');
			validator.required('depot_type_name', '请填写出入库类型名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
	<span class="l">出入库类型管理 &gt;&gt; 出入库类型编辑</span>
	 <span class="r">[ <a href="/depotio/type">返回列表 </a>]</span>
	 <?php if (check_perm('dt_edit')): ?>
	 <span class="r"><a href="depotio/add_type" class="add">新增</a></span>
	<?php endif; ?>
	 </div>
	<div class="blank5"></div>
	<?php print form_open('/depotio/proc_edit_type',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('depot_type_id'=>$row->depot_type_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">出入库类型编号:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
				<?php print form_input(array('name'=> 'depot_type_code','class'=> 'textbox require','value' => $row->depot_type_code));?>
				<?php else: print $row->depot_type_code; ?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">出入库类型名称:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
				<?php print form_input(array('name'=> 'depot_type_name','class'=> 'textbox require','value' => $row->depot_type_name));?>
				<?php else: print $row->depot_type_name; ?>
				<?php endif; ?>
				</td>
			</tr>

			<tr>
				<td class="item_title">出库/入库</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
					<label>入库<?php print form_radio(array('name'=>'depot_type_out','value'=>0,'checked'=>$row->depot_type_out!=1));?></label>
					<label>出库<?php print form_radio(array('name'=>'depot_type_out','value'=>1,'checked'=>$row->depot_type_out==1));?></label>
				<?php else: ?>
					<label>入库<?php print form_radio(array('name'=>'depot_type_out','value'=>0,'checked'=>$row->depot_type_out!=1,'disabled'=>'disabled'));?></label>
					<label>出库<?php print form_radio(array('name'=>'depot_type_out','value'=>1,'checked'=>$row->depot_type_out==1,'disabled'=>'disabled'));?></label>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">系统定制设定:</td>
				<td class="item_input">
				<?php if ($can_edit): ?>
					<label>通用<?php print form_radio(array('name'=>'depot_type_special','value'=>0,'checked'=>empty($row->depot_type_special)));?></label>
					<label>从采购单入库<?php print form_radio(array('name'=>'depot_type_special','value'=>1,'checked'=>$row->depot_type_special==1));?></label>
					<label>从出库单入库<?php print form_radio(array('name'=>'depot_type_special','value'=>2,'checked'=>$row->depot_type_special==2));?></label>
				<?php else: ?>
					<label>通用<?php print form_radio(array('name'=>'depot_type_special','value'=>0,'checked'=>empty($row->depot_type_special),'disabled'=>'disabled'));?></label>
					<label>从采购单入库<?php print form_radio(array('name'=>'depot_type_special','value'=>1,'checked'=>$row->depot_type_special==1,'disabled'=>'disabled'));?></label>
					<label>从出库单入库<?php print form_radio(array('name'=>'depot_type_special','value'=>2,'checked'=>$row->depot_type_special==2,'disabled'=>'disabled'));?></label>
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