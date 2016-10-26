<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('user_id', '请选择用户');
			var amount = parseFloat($('input[name=amount]').val());
			if(isNaN(amount) || amount<0.01) validator.addErrorMsg('请填写充值金额');
			validator.selected('pay_id', '请选择支付方式');
			validator.required('admin_note', '请填写管理员备注');
			return validator.passed();
	}
	
	function select_user_name(valu){
		if(valu == '')return;
		$('select[name=user_id]')[0].options.length  = 1;
		$.ajax({
		   type: "POST",
		   url: "user_recharge/select_user_name",
		   data: "user_phone_email="+valu,
		   dataType: "json",
		   success: function(msg){
			 if(msg.type == 0){
			 	alert('用户不存在');
				return false;
			 }
			 for(i in msg.res){
				$('select[name=user_id]')[0].options.add(new Option('用户名：'+msg.res[i].user_name+' 手机号:'+msg.res[i].mobile+' 邮箱：'+msg.res[i].email, msg.res[i].user_id));
			 }
		   }
		});	
	}
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">充值管理 >> 新增 </span><a href="user_recharge/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('user_recharge/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">注册邮箱/手机:</td>
				<td class="item_input">
                <input name="user_phone_email" onblur="return select_user_name(this.value)" class="textbox require" id="user_phone_email" />
                <select name="user_id" id="user_id">
                  <option value="">--请选择--</option>
                
                </select>
                </td>
			</tr>
			<tr>
				<td class="item_title">充值金额:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'amount','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
			  <td class="item_title">支付方式:</td>
			  <td class="item_input">
			    <select name="pay_id" id="pay_id">
			      <option value="">--请选择--</option>
                  <?php
                  foreach($all_payment as $item):
				  ?>
			      <option value="<?php echo $item->pay_id?>"><?php echo $item->pay_name?></option>
		      	  <?php
                  endforeach;
				  ?>
                </select>
		      
              </td>
		  </tr>
			<tr>
				<td class="item_title">管理员备注:</td>
				<td class="item_input">
                <textarea name="admin_note" cols="60" rows="4"></textarea>
					
				</td>
			</tr>
			<tr>
			  <td class="item_title">审核：</td>
			  <td class="item_input">
               <input name="is_audit" type="radio" value="0" checked="checked" />否
			       
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