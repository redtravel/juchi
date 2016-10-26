<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>



<script type="text/javascript">
	function check_form(){
			var validator = new Validator('mainForm');
			validator.required('consignee', '请填写联系人');
			validator.selected('province', '请选择省' , true);
			validator.selected('city', '请选择市' , true);
			validator.selected('district', '请选择县/区' , true);
			validator.required('address', '请填写详细地址');
			validator.required('zipcode', '请填写邮编');
			if($('input[name=tel]').val() == '' && $('input[name=mobile]').val() == ''){
				validator.addErrorMsg('手机或者电话必填写一个');
			}
			return validator.passed();
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
	//]]>
</script>
<div class="main">
    	<div class="main_title"><span class="l">联系地址 >> 新增 </span><a href="user/edit/<?php echo $user_id?>" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('user/proc_add_address/'.$user_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
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
			  <td class="item_input"><?php print form_input(array('name'=> 'mobile','class'=> 'textbox require'));?></td>
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