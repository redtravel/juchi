<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('change_code', '请填写变动CODE');
		validator.required('change_name', '请填写变动名称');
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">积分变动类型管理 >> 编辑</span> <span class="r"><a href="user_account_log_kind/index" class="return">返回列表</a></span></div>
	<?php print form_open_multipart('user_account_log_kind/proc_edit/'.$arr->change_code,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">变动CODE:</td>
				<td class="item_input">
                <input type="text" name="change_code" <?php echo $perms['uaccount_k_edit'] == 1 ? '' : 'disabled="disabled"';?> <?php echo ($use_type == 1) ? '' : 'readonly="readonly"';?>  class="textbox require" value="<?php echo $arr->change_code;?>" />
                </td>
			</tr>
			<tr>
				<td class="item_title">变动名称:</td>
				<td class="item_input">
				<input type="text" name="change_name" <?php echo $perms['uaccount_k_edit'] == 1 ? '' : 'disabled="disabled"';?>  class="textbox require" value="<?php echo $arr->change_name;?>" />
				</td>
			</tr>
			<tr>
				<td class="item_title">是否使用:</td>
				<td class="item_input">
                
                	    <label><input <?php echo $perms['uaccount_k_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="0" <?php echo $arr->is_use==0 ? 'checked="checked"' : '';?>  />
					      禁用</label>
					      <label><input <?php echo $perms['uaccount_k_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="1" <?php echo $arr->is_use==1 ? 'checked="checked"' : '';?>  />
					      启用</label>

				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <?php if($perms['uaccount_k_edit'] == 1):?>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    <?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>