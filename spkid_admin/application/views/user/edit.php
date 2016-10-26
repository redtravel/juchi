<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>



<script type="text/javascript">

	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.isEmail('email', '请填写邮箱');
			validator.required('user_name', '请填写用户名');
			if($('input[name=password]').val() != '' || $('input[name=password_check]').val() != ''){
				validator.equal('password', 'password_check' , '两次密码输入不一致');
			}
			// if(/^0\.[0-9]{1,2}$/.test($('input[name=discount_percent]').val()) == false && /^1\.0{1,2}$/.test($('input[name=discount_percent]').val()) == false && $('input[name=discount_percent]').val() != 1){
			// 	validator.addErrorMsg('请填写会员折扣率');
			// }
			return validator.passed();
	}
	
	function substr_user(){
		var user_na = $('input[name=email]').val().split('@');
		$('input[name=user_name]').val(user_na[0]);
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
	
	function after_tr(id){
		if($(".address_check").length != 0)return false;
		//$.each($("body").children('.address_check'),function(n){alert(n)});
		$.ajax({
		   type: "POST",
		   url: "user/ajax_edit_address",
		   dataType: "JSON",
		   data: "address_id="+id,
		   success: function(i){
				$('tr#address_edit_'+id).after(i.after);
		   }
		});
	}
	
	function remove(id){
		$('tr#address_after_'+id).remove();
	}
	
	function ajax_edit_address(id){
		var consignee = $('input[name=consignee]').val();
		var province = $('select[name=province]').val();
		var city = $('select[name=city]').val();
		var district = $('select[name=district]').val();
		var address = $('input[name=address]').val();
		var zipcode = $('input[name=zipcode]').val();
		var tel = $('input[name=tel]').val();
		var mobile = $('input[name=mobile]').val();
		$.ajax({
		   type: "POST",
		   url: "user/edit_address",
		   dataType: "JSON",
		   data: "address_id="+id+"&consignee="+consignee+"&province="+province+"&city="+city+"&district="+district+"&address="+address+"&zipcode="+zipcode+"&tel="+tel+"&mobile="+mobile,
		   success: function(i){
			   	if(i.msg != ''){
					alert('无权限');return false;   
				}
				if(i.check == 1){
					alert('添加项不为空');
					return;
				}
				if(i.check == 2){
					alert('修改成功');
					$('tr#address_after_'+id).remove();
					$('td#consignee_'+id).html(consignee);
					
					$('td#province_'+id).html(i.province);
					$('td#city_'+id).html(i.city);
					$('td#district_'+id).html(i.district);
					
					$('td#address_'+id).html(address);
					$('td#zipcode_'+id).html(zipcode);
					$('td#tel_'+id).html(tel);
					$('td#mobile_'+id).html(mobile);
					return;
				}
				
		   }
		});	
	}
	
	function discount_value(val){
		if(val == 2){
			$('input[name=discount_percent]').val('1');
			$('input[name=discount_percent]').attr('readonly','readonly');
			return false;
		}
		$('input[name=discount_percent]').val('<?php echo $user_arr->discount_percent?>');
		$('input[name=discount_percent]').removeAttr('readonly','readonly');
	}
	
	//]]>
</script>
<div class="main">
  	<div class="main_title"><span class="l">会员管理 >> 编辑 </span><a href="user/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
	<?php print form_open_multipart('user/proc_edit/'.$user_arr->user_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
  <table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="topTd">
<!-- //用于阻止 chrome表单自动填充的占位符 -->
<input class='item-hide' type="text" />
<input class='item-hide' type="password"/>
<!-- //用于阻止 chrome表单自动填充的占位符 -->
</td>
			</tr>
			<tr>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input"><span class="item_title">*必填信息</span></td>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input"><span class="item_title">*选填信息</span></td>
		  </tr>
			<tr>
				<td class="item_title">EMAIL:</td>
				<td class="item_input">
                <input <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> name="email" <?php echo !empty($user_arr->email) ? 'readonly="readonly"' : '';?>  value="<?php echo $user_arr->email?>" class="textbox require" type="text" onblur="return substr_user();" /><input name="email_type" type="hidden" value="<?php echo !empty($user_arr->email) ? '1' : '0';?>" />
                </td>
				<td class="item_title">真实姓名:</td>
				<td class="item_input"><input type="text" name="real_name" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php echo $user_arr->real_name?>" /></td>
			</tr>
			<tr>
			  <td class="item_title">手机:</td>
			  <td class="item_input">
              	<input  <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> name="mobile" value="<?php echo $user_arr->mobile?>" class="textbox require" type="text" />
              	<input name="mobile_type"  type="hidden" value="<?php echo !empty($user_arr->mobile) ? '1' : '0';?>" />
              </td>
			  <td class="item_title">性别:</td>
			  <td class="item_input">
              <input <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> type="radio" name="sex" value="1" id="sex" <?php echo $user_arr->sex == 1 ? 'checked="checked"' : '';?> />
			    男
			    <input <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> type="radio" name="sex" value="2" id="sex" <?php echo $user_arr->sex == 2 ? 'checked="checked"' : '';?> />
			    女
                 </td>
		  </tr>
			<tr>
				<td class="item_title">用户名:</td>
				<td class="item_input">
				<input name="user_name" type="text" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox require" value="<?php echo $user_arr->user_name?>" />
				</td>
				<td class="item_title">生日:</td>
				<td class="item_input">
				<input name="birthday" type="text" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php if(!empty($user_arr->birthday)) {echo $user_arr->birthday;}?>" />
                </td>
			</tr>
			<tr>
			  <td class="item_title">修改密码:</td>
			  <td class="item_input">
			  <input name="password" type="password" class="textbox require" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> />
			  </td>
			  <td class="item_title">身份证号:</td>
			  <td class="item_input">
              <input type="text" name="identity_code" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php echo $user_arr->identity_code?>" />
				</td>
		  </tr>
          <tr>
			  <td class="item_title">确认密码:</td>
			  <td class="item_input">
			  <input name="password_check" type="password" class="textbox require" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> />
			  </td>
			  <td class="item_title">护照号:</td>
			  <td class="item_input">
			  <input type="text" name="passport_code" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php echo $user_arr->passport_code?>" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">会员类型:</td>
			  <td class="item_input">
              <select <?php echo $perms['user_type'] == 2 ? 'disabled="disabled"' : '';?> name="user_type" id="user_type" onchange="return discount_value(this.value);">
			    <option value="3" <?php echo $user_arr->user_type == 1 ? 'selected="selected"' : '';?>>代销商</option>
			    <option value="2" <?php echo $user_arr->user_type == 0 ? 'selected="selected"' : '';?>>普通会员</option>
			  </select>
              </td>
			   <td class="item_title">单位名称:</td>
			  <td class="item_input">
			  <input type="text" name="company_name" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php echo $user_arr->company_name?>" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">会员折扣率：</td>
			  <td class="item_input">
              <?php echo $user_arr->discount_percent?>             
			  </td>
			  <td class="item_title">单位职务:</td>
			  <td class="item_input">
			  <input type="text" name="company_position" <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> class="textbox" value="<?php echo $user_arr->company_position?>" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">启用：</td>
			  <td class="item_input">
              <?php if($user_arr->is_use == 0):?>
                    <input <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> name="is_use" type="radio" id="is_use" value="0" <?php echo $user_arr->is_use == 0 ? 'checked="checked"' : '';?>  />
			        启用
                    <?php else:?>
			        <input <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> type="radio" name="is_use" value="1" <?php echo $user_arr->is_use == 1 ? 'checked="checked"' : '';?> id="is_use" />
			        停用
                    <?php endif;?>
              </td>
			  <td class="item_title">单位类型:</td>
			  <td class="item_input">
	              <select <?php echo $perms['user_edit'] == 2 ? 'disabled="disabled"' : '';?> name="company_type">
						<?php foreach($company_type_list as $key => $type):?>
				    	<option value="<?php echo $key;?>" <?php echo $user_arr->company_type == $key ? 'selected="selected"' : '';?>><?php echo $type;?></option>
						<?php endforeach;?>
				  </select>
              </td>
    </tr>
			<tr>
				<td class="item_title">&nbsp;</td>
				<td class="item_input">
				<?php if($perms['user_edit'] == 1):?>
				<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                <?php endif;if($perms['useraddr_edit'] == 1):?>
                <input type="button"  value="新增地址" onclick="javascript:location.href='/user/add_address/<?php echo $user_arr->user_id?>'"  class="am-btn am-btn-secondary" />
                <?php endif;?>
                </td>
				<td class="item_title">&nbsp;</td>
				<td class="item_input">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
<table width="1170"  cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
			  <tr>
					<td colspan="13" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="46">ID</th>
			      <th width="60">联系人</th>
			      <th width="68">省</th>
				  <th width="56">市</th>
				  <th width="59">县/区</th>
				  <th width="368">详细地址</th>
				  <th width="48">邮编</th>
				  <th width="61">电话</th>
				  <th width="87">手机</th>
				  <th width="40">默认</th>
				  <th width="136">创建时间</th>
				  <th width="78">操作</th>
				</tr>
		<?php foreach($address as $item):?>	
	      <tr class="row" id="address_edit_<?php echo $item->address_id?>">
	        <td><?php echo $item->address_id?></td>
	        <td id="consignee_<?php echo $item->address_id?>"><?php echo $item->consignee?></td>
	        <td id="province_<?php echo $item->address_id?>"><?php echo $province[$item->province]->region_name?></td>
	        <td id="city_<?php echo $item->address_id?>"><?php echo $city[$item->city]->region_name?></td>
	        <td id="district_<?php echo $item->address_id?>"><?php echo $district[$item->district]->region_name?></td>
	        <td id="address_<?php echo $item->address_id?>"><?php echo $item->address?></td>
	        <td id="zipcode_<?php echo $item->address_id?>"><?php echo $item->zipcode?></td>
	        <td id="tel_<?php echo $item->address_id?>"><?php echo $item->tel;?></td>
	        <td id="mobile_<?php echo $item->address_id?>"><?php echo $item->mobile;?></td>
	        <td><?php echo $item->is_used == 1 ? /* '<img src="public/images/yes.gif" />' 改为图片样式byRock */ '<span class="yesForGif"></span>' : '<a style="cursor:pointer;" onclick="return confirm(\'确定更改为默认地址？\')" href="user/edit_default_address/'.$item->address_id.'/'.$user_arr->user_id.'"><span class="noForGif"></span></a>'?><!--<img src="public/images/no.gif" /> 改为图片样式byRock--></td>
	        <td><?php echo $item->create_date?></td>
	        <td>
            <?php if($perms['useraddr_view'] == 1 || $perms['useraddr_edit'] == 1):?>
            <a onclick="return after_tr(<?php echo $item->address_id?>);"  style="cursor:pointer;" class="edit" title="编辑"></a> 
            <?php endif;if($perms['useraddr_edit'] == 1):?>
            <a class="del" href="javascript:void(0);" rel="user/del_address/<?php echo $item->address_id?>/<?php echo $user_arr->user_id;?>" title="删除" onclick="do_delete(this)"></a>
            <?php endif;?>
            </td>
    </tr>
		<?php endforeach;?>
			    <tr>
					<td colspan="13" class="bottomTd"> </td>
				</tr>
			</table>	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
