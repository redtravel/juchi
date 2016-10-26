<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.isInt('sort_order', '请正确填写排序号',true);
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">支付方式管理 >> 编辑</span> <span class="r"><a href="payment/index" class="return r">返回列表</a></span></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('payment/proc_edit/'.$payment->pay_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">支付编码:</td>
			  <td class="item_input"><input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="pay_code" value="<?php echo $payment->pay_code?>"  class="textbox require" id="pay_code" /></td>
		  </tr>
			<tr>
			  <td class="item_title">支付名称:</td>
			  <td class="item_input"><input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="pay_name" value="<?php echo $payment->pay_name?>"  class="textbox require" id="pay_name" /></td>
		  </tr>
			<tr>
			  <td class="item_title">支付说明:</td>
			  <td class="item_input"><textarea <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="pay_desc" cols="80" rows="4" id="pay_desc"><?php echo $payment->pay_desc?></textarea></td>
		  </tr>
			<tr>
			  <td class="item_title">在线支付:</td>
			  <td class="item_input">
			  <label>
			  <input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="is_online"  type="radio" id="" value="0"  <?php echo $payment->is_online == 0 ? ' checked="checked"' : '';?> />
			    否
			  </label>
			  <label>
			    <input type="radio" <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="is_online" value="1" id="" <?php echo $payment->is_online == 1 ? ' checked="checked"' : '';?> />
			    是 
			    </label>
			</td>
		  </tr>
			<tr>
			  <td class="item_title">排序号:</td>
			  <td class="item_input"><input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="sort_order" value="<?php echo $payment->sort_order;?>"  class="textbox" id="sort_order"  /></td>
		  </tr>
			<tr>
			  <td class="item_title">LOGO:</td>
			  <td class="item_input"><input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="pay_logo" type="file" id="pay_logo" />
              <?php echo empty($payment->pay_logo) ? '' : '<img src="'.PUBLIC_DATA_IMAGES.$payment->pay_logo.'" />';?></td>
		  </tr>
			<tr>
			  <td class="item_title">类型:</td>
			  <td class="item_input">
			  	<?php print form_dropdown('is_discount',array('支付','折扣'),$payment->is_discount,$perms['payment_edit']?'':'disabled'); ?>
		      </td>
		  </tr>
			<tr>
			  <td class="item_title">退还方式:</td>
			  <td class="item_input">
			  <?php print form_dropdown('back_type',$this->back_type,$payment->back_type,$perms['payment_edit']?'':'disabled'); ?>
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">启用:</td>
			  <td class="item_input">
			  <label>
			  <input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> name="enabled"  type="radio" id="RadioGroup1_6" value="0" checked="checked" <?php echo $payment->enabled == 0 ? ' checked="checked"' : '';?> />
			    否
			    </label>
			    <label>
			    <input <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="enabled" value="1" id="RadioGroup1_7" <?php echo $payment->enabled == 1 ? ' checked="checked"' : '';?> />
			    是 
			    </label>
			 </td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <input name="mysubmit"  type="submit"  class="am-btn am-btn-secondary" value="提交" <?php echo $perms['payment_edit'] == 1 ? '' : 'disabled="disabled"';?>/>
					
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>