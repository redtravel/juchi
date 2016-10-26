<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('user_money','请填写变动金额');
		validator.required('change_desc', '请填写变动原因',true);

		return validator.passed();
	}
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">会员管理 >> 调节账户</span> <a href="user_account_log/index/<?php echo $check->user_id;?>" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('user_account_log/proc_add/'.$check->user_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">用户名:</td>
				<td class="item_input"><?php echo $check->user_name;?></td>
			</tr>
			<tr>
				<td class="item_title">账户金额:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'user_money','class'=> 'textbox require'));?> 当前值：<?php echo $check->user_money;?>元  <span style="color:red">(填写正值为增加金额，负值为减少金额)</span></td>
			</tr>
			
			<tr>
				<td class="item_title">变动原因:</td>
				<td class="item_input">
					<textarea name="change_desc" cols="50" rows="6"></textarea>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>