<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
var address_id = 0;
$(function(){
	<?php if (isset($show_address_add) && $show_address_add == 0): ?>
	document.getElementById('add_edit_address_div').style.display = "none";
	document.getElementById('add_edit_address_title').style.display = "none";
	<?php endif; ?>

	//点击显示新增地址簿模块
    $('.addAddressBtn').live('click',function(){
		    $('#add_edit_address_div').show();
    });
});

function clear_address()
{
	$('input[type=text][name=consignee]').val('');
	$('input[type=text][name=address]').val('');
	$('input[type=text][name=zipcode]').val('');
	$('input[type=text][name=tel]').val('');
	$('input[type=text][name=mobile]').val('');
	$('select[name=province]').val(0);
	$('select[name=city]').val(0);
	$('select[name=district]').val(0);
	address_id = 0;
}

function reset_address()
{
	var edit_address_store_name = $('input[type=hidden][name=edit_address_store_name]').val();
	var edit_address_store_address = $('input[type=hidden][name=edit_address_store_address]').val();
	var edit_address_store_zipcod = $('input[type=hidden][name=edit_address_store_zipcod]').val();
	var edit_address_store_tel = $('input[type=hidden][name=edit_address_store_tel]').val();
	var edit_address_store_mobile = $('input[type=hidden][name=edit_address_store_mobile]').val();
	var edit_address_store_region = $('input[type=hidden][name=edit_address_store_region]').val();

	show_address_edit(address_id,edit_address_store_name,edit_address_store_address,edit_address_store_zipcod,edit_address_store_tel,edit_address_store_mobile,edit_address_store_region,0);
}

function check_del(address_id)
{
	if (address_id > 0)
	{
		if (confirm('确认删除该地址吗?'))
		{
			$.ajax({
					url:'/user/address_del',
					data:{is_ajax:true,address_id:address_id,rnd:new Date().getTime()},
					dataType:'json',
					type:'POST',
					success:function(result){
						if(result.error==0){
							alert(result.msg);
							$('#listdiv').html(result.content);

							if (result.show_address_add == 1)
							{
								document.getElementById('add_edit_address_div').style.display = "block";
								document.getElementById('add_edit_address_title').style.display = "block";
							}
						}else{
							alert(result.msg);
						}
					}
				});
		}
	}
	return false;
}

function set_default(address_id)
{
	if (address_id > 0)
	{
		$.ajax({
				url:'/user/address_edit',
				data:{is_ajax:true,address_id:address_id,is_used:1,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						//alert(result.msg);
						$('#listdiv').html(result.content);
						//clear_address();

						if (result.show_address_add == 0)
						{
							document.getElementById('add_edit_address_div').style.display = "none";
							document.getElementById('add_edit_address_title').style.display = "none";
						}
					}else{
						alert(result.msg);
					}
				}
			});

		return false;
	}
}

function check_add_form(input_id)
{
		var consignee = $('input[type=text][name=consignee]').val();
        var address = $('input[type=text][name=address]').val();
        var zipcode = $('input[type=text][name=zipcode]').val();
        var tel = $('input[type=text][name=tel]').val();
        var mobile = $('input[type=text][name=mobile]').val();
        var province = $('select[name=province]').val();
        var city = $('select[name=city]').val();
        var district = $('select[name=district]').val();

        if (input_id > 0)
        {
        	input_address_id = address_id;
        } else
        {
        	input_address_id = 0;
        }

        var consignee_len = consignee.replace(/[^\x00-\xff]/g, "**").length;
		var address_len = address.replace(/[^\x00-\xff]/g, "**").length;
		var reg = /^[0-9\-*_+=&%$#@!?\s\(\)\[\]\\\/]+$/;
		var regu = "^[0-9]+$";
                var regMobile = /^1[3|4|5|8][0-9]\d{4,8}$/;
		var re = new RegExp(regu);

		$('input[type=text][name=consignee]').css("border-color","");
        $('input[type=text][name=address]').css("border-color","");
        $('input[type=text][name=zipcode]').css("border-color","");
        $('input[type=text][name=tel]').css("border-color","");
		$('input[type=text][name=mobile]').css("border-color","");

		var obj = null;
		var err_msg = '';

		if (input_id > 0 && input_address_id == 0)
		{
			err_msg = "无效的地址参数";
		}

		if(err_msg == '' && $.trim(consignee) =='')
		{
            err_msg = "请输入收货人";
            obj = $('input[type=text][name=consignee]');
        }

        if(err_msg == '' && consignee_len > 20)
        {
           	err_msg = '收货人名称过长，不能超过20个字符';
        	obj = $('input[type=text][name=consignee]');
        }

        if(err_msg == '' &&  parseInt(province) <= 0)
        {
           	err_msg = '请选择省份';
           	obj = $('#province');
        }

        if(err_msg == '' &&  parseInt(city) <= 0)
        {
           	err_msg = '请选择城市';
        }

        if(err_msg == '' &&  parseInt(district) <= 0)
        {
           	err_msg = '请选择区县';
        }

		if(err_msg == '' && $.trim(address) =='')
		{
           	err_msg = "请输入详细地址";
           	obj = $('input[type=text][name=address]');
       	}

       	if(err_msg == '' && address_len > 100)
        {
           	err_msg = '详细地址过长，不能超过100个字符';
        	obj = $('input[type=text][name=address]');
        }

        if(err_msg == '' && $.trim(zipcode) =='')
		{
           	err_msg = "请输入邮政编码";
           	obj = $('input[type=text][name=zipcode]');
       	}

       	if(err_msg == '' && !re.test(zipcode))
       	{
       		err_msg = "无效的邮政编码";
       		obj = $('input[type=text][name=zipcode]');
	    }

	    if(err_msg == '' && $.trim(mobile) =='' && $.trim(tel) =='')
		{
           	err_msg = "请输入手机或者固定电话至少一项";
           	obj = $('input[type=text][name=mobile]');
       	}

       	if(err_msg == '' && mobile.length>30)
       	{
       		err_msg = "非法的手机号";
           	obj = $('input[type=text][name=mobile]');
       	}

       	if(err_msg == '' && $.trim(mobile) !='' && !regMobile.test(mobile))
        {
           	err_msg = "非法的手机号";
        	obj = $('input[type=text][name=mobile]');
        }

        if(err_msg == '' && tel.length>30)
       	{
       		err_msg = "非法的家庭电话号码";
           	obj = $('input[type=text][name=tel]');
       	}

       	if(err_msg == '' && $.trim(tel) !='' && !reg.test(tel))
        {
           	err_msg = "非法的家庭电话号码";
        	obj = $('input[type=text][name=tel]');
        }

		if(err_msg != '')
		{
			if(obj)
			{
				//obj.css("border-color","red");
				$('.errorInfo').hide();
				obj.parent().find('.errorInfo').css({'display':'inline-block'});
                                obj.parent().find('.errorInfo').html(err_msg);
			}
			//alert(err_msg);
			return false;
		}

		$('input[type=button][name=add_submit]').attr("disabled", "disabled");

			$.ajax({
				url:'/user/address_edit',
				data:{is_ajax:true,address_id:address_id,consignee:consignee,address:address,zipcode:zipcode,tel:tel,mobile:mobile,
					province:province,city:city,district:district,address_id:input_address_id,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						alert(result.msg);
						$('#listdiv').html(result.content);
						clear_address();

						if (result.show_address_add == 0)
						{
							document.getElementById('add_edit_address_div').style.display = "none";
							document.getElementById('add_edit_address_title').style.display = "none";
						}
						if (input_address_id > 0)
						{
							document.getElementById('add_edit_address_title').innerHTML = '新增地址';
							document.getElementById('add_address_butt').style.display = 'inline';
							document.getElementById('edit_address_butt').style.display = 'none';
						}
					}else{
						alert(result.msg);
					}
					$('input[type=button][name=add_submit]').attr("disabled", "");
				}
			});

		return false;
}

function show_address_edit (input_address_id, consignee, address, zipcode, tel, mobile, regionstr, num)
{
	var region_arr = regionstr.split('_');
	if(num == 0){
		document.getElementById('add_edit_address_div').style.display = "block";
		//document.getElementById('add_edit_address_title').style.display = "block";

		$('input[type=hidden][name=edit_address_store_name]').val(consignee);
		$('input[type=hidden][name=edit_address_store_address]').val(address);
		$('input[type=hidden][name=edit_address_store_zipcod]').val(zipcode);
		$('input[type=hidden][name=edit_address_store_tel]').val(tel);
		$('input[type=hidden][name=edit_address_store_mobile]').val(mobile);
		$('input[type=hidden][name=edit_address_store_region]').val(regionstr);

		address_id = input_address_id;

		$('input[type=text][name=consignee]').val(consignee);
		$('input[type=text][name=address]').val(address);
		$('input[type=text][name=zipcode]').val(zipcode);
		$('input[type=text][name=tel]').val(tel);
		$('input[type=text][name=mobile]').val(mobile);
		$('select[name=province]').val(region_arr[0]);

//		document.getElementById('add_edit_address_title').innerHTML = '修改配送地址';
		document.getElementById('add_address_butt').style.display = 'none';
		document.getElementById('edit_address_butt').style.display = 'inline';
		change_region($('select[name=province]').val(), 2, 'city');
		setTimeout("show_address_edit('', '', '', '', '', '', '"+regionstr+"', 1)",1000);
	}
	if(num == 1){
		$('select[name=city]').val(region_arr[1]);
		change_region($('select[name=city]').val(), 3, 'district')

		setTimeout("show_address_edit('', '', '', '', '', '', '"+regionstr+"', 2)",1000);
	}
	if(num == 2){
		$('select[name=district]').val(region_arr[2]);
	}
}

function change_region (region_id, num, target)
{
	if (region_id > 0)
	{
		$.ajax({
			url:'/user/region_change',
			data:{is_ajax:true,region_id:region_id,type:num,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if(result.error==0){
					var sel = document.getElementById(target);
					sel.length = 1;
					sel.selectedIndex = 0;
					if (result.regions)
					{
					    for (i = 0; i < result.regions.length; i ++ )
					    {
					      var opt = document.createElement("OPTION");
					      opt.value = result.regions[i].region_id;
					      opt.text  = result.regions[i].region_name;
					      sel.options.add(opt);
					    }
					}
				}else{
					alert(result.msg);
				}
			}
		});
	}
}
</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
		>
		<a href="/user">会员中心</a>
		>
		<a class="now">地址管理</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="list_block" id="listdiv">
			<?php endif; ?>
			<h2>地址管理</h2>
			<div class="list_block_content">
				<?php if (!empty($address_list)):
				    $_akey = 0;
				 foreach ($address_list as $akey => $address): ?>
				<div class="adress <?php if ($address->is_used == 1): ?>adress_def<?php endif?>">
					<div class="adress_c">
						<?php if ($address->address_id == $address_id): ?><div>默认地址</div><?php else : ?><div>地址<?= ++$_akey ?></div><?php  endif; ?>
						<?php echo $address->consignee ?>，中国，<?php echo $address->province_name ?>，<?php echo $address->city_name ?>，<?php echo $address->district_name ?>，<?php echo $address->address ?>，<?php echo $address->zipcode ?>，<?php echo !empty($address->mobile)?(empty($address->tel)?$address->mobile:$address->mobile."(".$address->tel.")"):$address->tel; ?>
					</div>
					<div class="adress_barea">
						<span class="btn_g_62" onclick="show_address_edit('<?php echo $address->address_id ?>','<?php echo $address->consignee ?>','<?php echo $address->address ?>','<?php echo $address->zipcode ?>','<?php echo $address->tel ?>','<?php echo $address->mobile ?>','<?php echo $address->province ?>_<?php echo $address->city ?>_<?php echo $address->district ?>',0);">修改</span>
						<?php if ($address->is_used == 0): ?>
						<span class="btn_g_62" onclick="check_del('<?php echo $address->address_id ?>');">删除</span>
						<span class="mr" onclick="set_default('<?php echo $address->address_id ?>');">设为默认</span>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; endif; ?>
				<a class="btn_gray_124 addAddressBtn">新增收货地址</a>
				<div class="list_block_content" id="add_edit_address_div" style="display:none;">
					<div class="bold" id="add_edit_address_title">新增收货地址</div>
					<table width="738" border="0" cellspacing="3" cellpadding="0">
						<tr>
							<td width="12%" align="right"><span class="cred l">*</span> 收货人姓名：</td>
							<td>
								<input type="text" name="consignee" id="consignee" class="t120" />
								<span class="cred errorInfo">收货人姓名不能为空!</span>
							</td>
						</tr>
						<tr>
							<td align="right"><span class="cred l">*</span> 所在区域：</td>
							<td>
								<select name="province" id="province" onchange="change_region(this.value,2,'city');">
									<option value="0">请选择省</option>
									<?php foreach ($province_list as $province): ?>
									<option value="<?php echo $province->region_id ?>"><?php echo $province->region_name ?></option>
									<?php endforeach; ?>
								</select>
								<select name="city" id="city" onchange="change_region(this.value,3,'district');">
									<option value="0">请选择市</option>
								</select>
								<select name="district" id="district">
									<option value="0">请选择区</option>
								</select>
								<span class="cred errorInfo">所在区域不能为空!</span>
							</td>
						</tr>
						<tr>
							<td align="right"><span class="cred l">*</span> 详细地址：</td>
							<td>
								<input type="text" name="address" id="address" class="t250" />
								<span class="cred errorInfo">收货地址不能为空!</span>
							</td>
						</tr>
						<tr>
							<td align="right"><span class="cred l">*</span> 手机：</td>
							<td>
								<input type="text" class="t120" name="mobile" id="mobile" />
								<span class="cred errorInfo">手机不能为空!</span>
							</td>
						</tr>
						<tr>
							<td align="right"> 电话：</td>
							<td>
								<input type="text" name="tel" id="tel" class="t120" />
							</td>
						</tr>
						<tr>
							<td align="right"><span class="cred l">*</span> 邮编：</td>
							<td><input type="text" name="zipcode" id="zipcode" class="t120"/> <span class="cred errorInfo">邮编不能为空!</span></td>
								
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div id="add_address_butt" >
									<!-- <input type="button" onclick="clear_address();" class="u_sbtn add_submit" value="清空" /> -->
									<a name="add_submit" onclick="check_add_form(0);" class="btn_g_75 add_submit">提交</a>
								</div>
								<div id="edit_address_butt" style="display:none;">
									<a onclick="reset_address();" class="btn_g_75 add_submit">恢复</a>
									<a name="add_submit" onclick="check_add_form(1);" class="mr">修改地址</a>
								</div>
							</td>
						</tr>
					</table>
					<input name="edit_address_store_name" type="hidden" value="" />
					<input name="edit_address_store_address" type="hidden" value="" />
					<input name="edit_address_store_tel" type="hidden" value="" />
					<input name="edit_address_store_mobile" type="hidden" value="" />
					<input name="edit_address_store_zipcod" type="hidden" value="" />
					<input name="edit_address_store_region" type="hidden" value="" />
				</div>
			</div>
			<?php if($full_page): ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>