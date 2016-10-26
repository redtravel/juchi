<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('pay_code', '请正确填写支付编码');
		validator.required('pay_name', '请正确填写支付名称');
		return validator.passed();
	}

	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">支付方式管理 >> 安装</span> <span class="r"><a href="payment/index" class="return r">返回列表</a></span></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('payment/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">支付编码:</td>
				<td class="item_input"><input name="pay_code"  class="textbox require" id="pay_code" />
               </td>
			</tr>
			<tr>
				<td class="item_title">支付名称:</td>
				<td class="item_input"><input name="pay_name" class="textbox require" id="pay_name" /></td>
			</tr>
			<tr>
			  <td class="item_title">支付说明:</td>
			  <td class="item_input"><textarea name="pay_desc" cols="80" rows="4" id="pay_desc"></textarea></td>
		  </tr>
          
			<tr>
			  <td class="item_title">在线支付:</td>
			  <td class="item_input">
			  <label>
				<input name="is_online"  type="radio" id="RadioGroup1_6" value="0" checked="checked"  />
			    否
			  </label>
			  <label>
			  	<input type="radio" name="is_online" value="1" id="RadioGroup1_7"  />
			    是 </td>
			  </label>
			    
		  </tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input"><input name="sort_order" class="textbox" id="sort_order" value="0" /></td>
			</tr>			<tr>
			  <td class="item_title">LOGO:</td>
			  <td class="item_input"><input name="pay_logo" type="file" id="pay_logo" /></td>
		  </tr>
			<tr>
			  <td class="item_title">折扣:</td>
			  <td class="item_input">
			  	<?php print form_dropdown('is_discount',array('支付','折扣'),0); ?>
		      </td>
		  </tr>
			<tr>
			  <td class="item_title">退还方式:</td>
			  <td class="item_input">
			  <?php print form_dropdown('back_type',$this->back_type); ?>
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">启用:</td>
			  <td class="item_input">
			  <label>
				<input name="enabled" type="radio" id="RadioGroup1_1" value="0" checked="checked" />
				否
			  </label>
			  <label>
				<input type="radio" name="enabled" value="1" id="RadioGroup1_0" />
				是 
			  </label>
  				
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