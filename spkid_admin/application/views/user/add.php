<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>



<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=birthday]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-80:-3'});
		});

	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			if($('input[name=email]').val() == '' && $('input[name=mobile]').val() == ''){
				validator.addErrorMsg('手机或者邮箱必填写一个');
			}
			if($('input[name=email]').val() != ''){
				validator.isEmail('email', '请填写邮箱' , true);
			}
			if($('input[name=mobile]').val() != ''){
				validator.reg('mobile' , /^1[0-9]{10}$/ , '请正确填写手机号码');
			}
			validator.required('user_name', '请填写用户名');
			validator.required('password', '请填写密码');
			validator.equal('password', 'password_check' , '两次密码输入不一致');
			validator.selected('user_type', '请选择会员类型');
			if(/^0\.[0-9]{1,2}$/.test($('input[name=discount_percent]').val()) == false && /^1\.0{1,2}$/.test($('input[name=discount_percent]').val()) == false && $('input[name=discount_percent]').val() != 1){
				validator.addErrorMsg('请填写会员折扣率');
			}
			if(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/.test($('input[name=birthday]').val()) == false && $('input[name=birthday]').val() != ''){
				validator.addErrorMsg('请正确填写生日');
			}
			if(/^[0-9a-zA-Z]{15,18}$/.test($('input[name=identity_code]').val()) == false && $('input[name=identity_code]').val() != ''){
				validator.addErrorMsg('请正确填写身份证号');
			}
			if(/^[0-9a-zA-Z]{1,30}$/.test($('input[name=passport_code]').val()) == false && $('input[name=passport_code]').val() != ''){
				validator.addErrorMsg('请正确填写护照号');
			}
			if(/^[0-9]{6}$/.test($('input[name=zipcode]').val()) == false && $('input[name=zipcode]').val() != ''){
				validator.addErrorMsg('请正确填写邮编');
			}
			if(/^[0-9]{2,3}-[0-9]{7,8}$/.test($('input[name=tel]').val()) == false && $('input[name=tel]').val() != '' && /^[0-9]{6,15}$/.test($('input[name=tel]').val()) == false){
				validator.addErrorMsg('请正确填写固定电话');
			}
			if($('input[name=mobile_address]').val() != ''){
				validator.reg('mobile_address' , /^1[0-9]{10}$/ , '请正确填写手机号码');
			}
			return validator.passed();
	}
	
	function substr_user(){
		var user_na = $('input[name=email]').val().split('@');
		var user_mob = $('input[name=mobile]').val();
		if(user_na == '' ){
			$('input[name=user_name]').val(user_mob);
		}else{
			$('input[name=user_name]').val(user_na[0]);
		}
	}
	
	function change_region(type,value,are){
		if(type == 0){
			$('select[name=province]')[0].options.length = 1;
			$('select[name=city]')[0].options.length = 1;
			$('select[name=district]')[0].options.length = 1;
		}
		if(type == 1){
			$('select[name=city]')[0].options.length = 1;
			$('select[name=district]')[0].options.length = 1;
		}
		if(type == 2){
			$('select[name=district]')[0].options.length = 1;
		}
		$.ajax({
		   type: "POST",
		   url: "user/ajax_region",
		   dataType: "JSON",
		   data: "type="+type+"&parent_id="+value,
		   success: function(msg){
			 for(i in msg.list){
				$('select[name='+are+']')[0].options.add(new Option(msg.list[i].region_name, msg.list[i].region_id));
			 }
		   }
		});
	}
	function chang_mobile(){
		$("input[name=mobile_address]").val($("input[name=mobile]").val());	
	}
	
	function discount_value(val){
		if(val == 2){
			$('input[name=discount_percent]').val('1');
			$('input[name=discount_percent]').attr('readonly','readonly');
			return false;
		}
		$('input[name=discount_percent]').val('1');
		$('input[name=discount_percent]').removeAttr('readonly','readonly');
	}
	
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">会员管理 >> 新增 </span><a href="user/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('user/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input"><span class="item_title">*必填信息</span></td>
		  </tr>
			<tr>
				<td class="item_title">EMAIL:</td>
				<td class="item_input"><input name="email" class="textbox require" type="text" onblur="return substr_user();" /></td>
			</tr>
			<tr>
			  <td class="item_title">手机:</td>
			  <td class="item_input"><input name="mobile" class="textbox require" type="text" onblur="return chang_mobile()" /></td>
		  </tr>
			<tr>
				<td class="item_title">用户名:</td>
				<td class="item_input"><input name="user_name" class="textbox require" type="text" /></td>
			</tr>
			<tr>
			  <td class="item_title">密码:</td>
			  <td class="item_input"><?php print form_password(array('name'=> 'password','class'=> 'textbox require'));?></td>
		  </tr>
          <tr>
			  <td class="item_title">确认密码:</td>
			  <td class="item_input"><?php print form_password(array('name'=> 'password_check','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">会员类型:</td>
			  <td class="item_input">
			        <select name="user_type" id="user_type" onchange="return discount_value(this.value)">
			          <option <?php echo $perms['user_type'] == 1 ? '' : 'disabled="disabled"';?> value="3">代销商</option>
			          <option value="2"  selected="selected">普通会员</option>
		            </select>
	          </td>
		  </tr>
			<tr>
			  <td class="item_title">会员折扣率：</td>
			  <td class="item_input">
			  <input readonly="readonly" name="discount_percent" type="text" class="textbox require" id="discount_percent" maxlength="15" value="1" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input"><span class="item_title">*选填信息</span></td>
		  </tr>
			<tr>
			  <td class="item_title">真实姓名:</td>
			  <td class="item_input">
			        <input name="real_name" type="text" class="textbox" id="real_name" maxlength="15" />
		      </td>
		  </tr>
			<tr>
			  <td class="item_title">性别:</td>
			  <td class="item_input">
			          <input type="radio" name="sex" value="1" id="sex" checked="checked" />
			          男
			          <input type="radio" name="sex" value="2" id="sex" />
			          女
				</td>
		  </tr>
			<tr>
			  <td class="item_title">生日:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'birthday','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">身份证号:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'identity_code','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">护照号:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'passport_code','class' => 'textbox')); ?></td>
		  </tr>
		  	<tr>
		  		<td class="item_title">单位名称:</td>
			  	<td class="item_input"><?php print form_input(array('name' => 'company_name','class' => 'textbox')); ?></td>
			</tr>
			<tr>
		  		<td class="item_title">单位性质:</td>
			  	<td class="item_input">
					<select name="company_type">
		                <?php foreach($company_type_list as $key => $type):?>
		                <option value="<?php echo $key;?>"><?php echo $type;?></option>
						<?php endforeach;?>
				     </select>
			  	</td>
			</tr>
			<tr>
		  		<td class="item_title">单位职务:</td>
			  	<td class="item_input"><?php print form_input(array('name' => 'company_position','class' => 'textbox')); ?></td>
			</tr>
			<tr>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input"><span class="item_title">*联系地址（请填写完整，如果不完整将不记录数据）</span></td>
		  </tr>
			<tr>
			  <td class="item_title">联系人:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'consignee','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">省:</td>
			  <td class="item_input"><select name="province" id="province" onchange="return change_region(1,this.value,'city')">
			    <option value="">--请选择--</option>
                <?php foreach($province as $item):?>
                <option value="<?php echo $item->region_id?>"><?php echo $item->region_name?></option>
				<?php endforeach;?>
		      </select></td>
		  </tr>
			<tr>
			  <td class="item_title">市:</td>
			  <td class="item_input"><select name="city" id="city" onchange="return change_region(2,this.value,'district')">
			    <option value="">--请选择--</option>
		      </select></td>
		  </tr>
			<tr>
			  <td class="item_title">县/区:</td>
			  <td class="item_input"><select name="district" id="district">
			    <option value="">--请选择--</option>
		      </select></td>
		  </tr>
			<tr>
			  <td class="item_title">详细地址:</td>
			  <td class="item_input"><input name="address" type="text" style="width:350px;" class="textbox require" /></td>
		  </tr>
			<tr>
			  <td class="item_title">邮编:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'zipcode','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">固定电话:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'tel','class'=> 'textbox require'));?></td>
		  </tr>

			<tr>
			  <td class="item_title">手机:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'mobile_address','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
				<td class="item_title">&nbsp;</td>
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