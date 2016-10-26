<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('depot_name', '请填写仓库名称');
			//validator.required('depot_code', '请填写仓库编码');
			validator.required('depot_position', '请填写仓库地点');
			validator.isInt('depot_priority', '请填写正确的仓库优先级');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">仓库管理 &gt;&gt; 新增仓库</span> <span class="r">[ <a href="/depot/depot_list">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/depot/proc_add_depot',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">仓库名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'depot_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">仓库地点:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'depot_position','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">优先级:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'depot_priority','class'=> 'textbox'), 1);?></td>
			</tr>
			<tr>
				<td class="item_title">仓库类型:</td>
				<td class="item_input">
					<label>可售<?php print form_radio(array('name'=>'depot_type', 'value'=>1,'checked'=>TRUE)); ?></label>
					<label>不可售<?php print form_radio(array('name'=>'depot_type', 'value'=>0)); ?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">退货仓:</td>
				<td class="item_input">
					<label>是<?php print form_radio(array('name'=>'is_return', 'value'=>1,'checked'=>TRUE)); ?></label>
					<label>否<?php print form_radio(array('name'=>'is_return', 'value'=>0)); ?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label>可用<?php print form_radio(array('name'=>'is_use','value'=>1,'checked'=>TRUE));?></label>
					<label>停用<?php print form_radio(array('name'=>'is_use','value'=>0));?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">合作方式:</td>
				<td class="item_input">
                                        <?php foreach ($all_cooperations as $coop): ?>
                                            <label><?=$coop->cooperation_name;?><?php print form_radio(array('name'=>'cooperation_id','value'=>$coop->cooperation_id));?></label>
                                        <?php endforeach; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>