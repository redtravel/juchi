<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('pag_dis_name', '请填写礼包名称');
			validator.required('start_date', '请填写开始时间');
			validator.required('end_date', '请填写结束时间');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">礼包管理 >> 新增 </span><a href="package_discount/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('package_discount/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">礼包类型</td>
				<td class="item_input">
					<?php print form_dropdown('pag_dis_type', $all_type);?>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包名称</td>
				<td class="item_input">
					<?php print form_input('pag_dis_name', '', 'class="textbox require"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">购买数量</td>
				<td class="item_input">
					<?php print form_input('pag_dis_max_num', '', 'class="textbox require"');?>
					(礼包主产品对应折扣商品购买数量比例)
				</td>
			</tr>
			<tr>
				<td class="item_title">开始时间</td>
				<td class="item_input">
					<?php print form_input('start_date', '', 'class="textbox require"');?>
					<?php print form_input('start_time', '00:00:00', 'class="textbox require"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">结束时间</td>
				<td class="item_input">
					<?php print form_input('end_date', '', 'class="textbox require"');?>
					<?php print form_input('end_time', '23:59:59', 'class="textbox require"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包图片</td>
				<td class="item_input">
					<?php print form_upload('pag_dis_image', '', 'class="textbox require"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">首页图片</td>
				<td class="item_input">
					<?php print form_upload('pag_dis_homepage_image', '', 'class="textbox"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">分享图片</td>
				<td class="item_input">
					<?php print form_upload('pag_dis_wechat_image', '', 'class="textbox"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">排序</td>
				<td class="item_input">
					<?php print form_input('sort_order', '0', 'class="textbox" size=3');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包简介</td>
				<td class="item_input">
					<?php print form_textarea('pag_dis_desc', '');?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>