<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('shipping_code', '请填写快递方式编码');
			validator.required('shipping_name', '请填写快递方式名称',true);
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">配送方式管理 >> 编辑 </span><a href="shipping/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('shipping/proc_edit_shipping_info/'.$shipping->shipping_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">编码:</td>
				<td class="item_input">
                <input name="shipping_code"  <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox require" id="shipping_code" value="<?php echo $shipping->shipping_code?>" /></td>
			</tr>
			<tr>
				<td class="item_title">名称:</td>
				<td class="item_input"><input <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> name="shipping_name" class="textbox require" id="shipping_name"  value="<?php echo $shipping->shipping_name?>"/></td>
			</tr>
			<tr>
			  <td class="item_title">启用:</td>
			  <td class="item_input">
			  <label>
              <input name="is_use" type="radio" <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> id="RadioGroup1_1" value="0" <?php echo $shipping->is_use == 0 ? 'checked="checked"' : '';?>/>
	          否
	          </label>
	          <label>
              <input type="radio" name="is_use" <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> value="1" id="RadioGroup1_0" <?php echo $shipping->is_use == 1 ? 'checked="checked"' : '';?> />
			  是
			  </label>
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">描述:</td>
			  <td class="item_input"><textarea <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> name="shipping_desc" cols="80" rows="4" id="shipping_desc"><?php echo $shipping->shipping_desc?></textarea></td>
		  </tr>
			<tr>
			  <td class="item_title">跟踪接口用名称:</td>
			  <td class="item_input"><input <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> name="track_name" value="<?php echo $shipping->track_name?>" class="textbox" id="track_name" /></td>
		  </tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input"><input <?php echo $perms['shipping_edit'] == 2 ? 'disabled="disabled"' : '';?> name="sort_order" class="textbox" value="<?php echo $shipping->sort_order?>" id="sort_order" />小的优先级高</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <?php if($perms['shipping_edit'] == 1):?>
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