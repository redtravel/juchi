<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('user_id', '请搜索并选择会员');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> 新增订单 </span><span class="r"><a href="order/index" class="return">返回列表</a></span></div>
	<div class="blank5"></div>
	<?php print form_open('order/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="200">订单来源:</td>
				<td class="item_input"><?php print form_dropdown('source_id', get_pair($all_source,'source_id','source_name'), $order_source_tel_id);?></td>
			</tr>
			<tr>
				<td class="item_title" width="200">订单类型:</td>
				<td class="item_input">牙科产品 <input type="hidden" name="genre_id" value="1"></td>
			</tr>
			<tr>
				<td class="item_title">按会员名或Email或手机搜索:</td>
				<td class="item_input">
					<?php print form_input('user_name') ?>
					<?php print form_button('user_search','搜索','onclick="search_user();"') ?>
					<?php print form_dropdown('user_id', array()); ?>
					<a href="user/add" target="_blank"><span style="color:red">新增用户</span></a>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit('mysubmit','下一步','class="am-btn am-btn-primary"') ?>
					<?php print form_button('mycancel','取消','class="am-btn am-btn-primary" onclick="location.href=\'order/index\';"'); ?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
