<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		//$('input[type=text][name=depot_out_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});

	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('source_depot_id', '请选择出库仓库');
			validator.selected('dest_depot_id', '请选择出库仓库');
			return validator.passed();
	}


	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">调仓管理 &gt;&gt; 新增调仓单</span> <span class="r">[ <a href="/exchange/exchange_list">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/exchange/proc_add_exchange',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">出库仓库:</td>
				<td class="item_input"><?php print form_dropdown('source_depot_id',$depot_list);?></td>
			</tr>
			<tr>
				<td class="item_title">入库仓库:</td>
				<td class="item_input"><?php print form_dropdown('dest_depot_id',$depot_list);?></td>
			</tr>
			<tr>
				<td class="item_title">调仓备注:</td>
				<td class="item_input"><?php print form_input(array('name'=>'exchange_reason', 'class'=>'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'添加'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>