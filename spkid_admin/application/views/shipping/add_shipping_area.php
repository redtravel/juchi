<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('shipping_area_name', '请填写区域名称');
			return validator.passed();
	}
	
	function change_region(type,value,are){
		if(type == 1){
			$('select[name=city]')[0].options.length = 1;
			$('select[name=district]')[0].options.length = 1;
		}
		if(type == 2){
			$('select[name=district]')[0].options.length = 1;
		}
		$.ajax({
		   type: "POST",
		   url: "shipping/ajax_region",
		   dataType: "JSON",
		   data: "type="+type+"&parent_id="+value,
		   success: function(msg){
			 for(i in msg.list){
				$('select[name='+are+']')[0].options.add(new Option(msg.list[i].region_name , msg.list[i].region_id+'|'+msg.list[i].region_name));
			 }
		   }
		});
	}
	
	function addRegion(){
		if($('select#district').val() != '' &&  $('select#district').val() != null){
			var dis = $('select#district').val().split('|');
			if($(":input#che_"+dis[0]).length > 0){alert('选定的地区已经存在。');return false;}
			var con = '<input name="area[]" checked="checked" type="checkbox" id="che_'+dis[0]+'" value="'+dis[0]+'" /> '+dis[1]+'&nbsp;';
			$('td#addregion').append(con);
			return false;
		}
		if($('select#city').val() != '' &&  $('select#city').val() != null){
			var dis = $('select#city').val().split('|');
			if($(":input#che_"+dis[0]).length > 0){alert('选定的地区已经存在。');return false;}
			var con = '<input name="area[]" checked="checked" type="checkbox" id="che_'+dis[0]+'" value="'+dis[0]+'" /> '+dis[1]+'&nbsp;';
			$('td#addregion').append(con);
			return false;
		}
		if($('select#province').val() != '' &&  $('select#province').val() != null){
			var dis = $('select#province').val().split('|');
			if($(":input#che_"+dis[0]).length > 0){alert('选定的地区已经存在。');return false;}
			var con = '<input name="area[]" checked="checked" type="checkbox" id="che_'+dis[0]+'" value="'+dis[0]+'" /> '+dis[1]+'&nbsp;';
			$('td#addregion').append(con);
			return false;
		}
		
		var dis = $('select#country').val().split('|');
		if($(":input#che_"+dis[0]).length > 0){alert('选定的地区已经存在。');return false;}
		var con = '<input name="area[]" checked="checked" type="checkbox" id="che_'+dis[0]+'" value="'+dis[0]+'" /> '+dis[1]+'&nbsp;';
		$('td#addregion').append(con);

	}
	//]]>
</script>
<div class="main">
<div class="main_title"><span class="l">配送方式区域管理 >> 新增 </span><a href="shipping/operate/<?php echo $shipping_id;?>" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('shipping/proc_add_shipping_area/'.$shipping_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="150">配送区域名称:</td>
				<td class="item_input"><input name="shipping_area_name" class="textbox require" id="shipping_area_name" /></td>
			</tr>            
			<tr>
			  <td class="item_title">货到付款:</td>
			  <td class="item_input">
			  		<label>
                    <input name="is_cod" type="radio" id="is_cod_n" value="0" checked="checked" />
                      否
                    </label>
                    <label>
                   	<input type="radio" name="is_cod" value="1" id="is_cod_y" />
			        	是
			        </label>
	          </td>
		  	</tr>
			<tr>
			  <td class="item_title">所辖地区:</td>
				<td height="35" class="item_input" id="addregion"></td>
			</tr>
			<tr>
			  <td class="item_title">可选任意维度添加。如只添加中国，表示整个中国。</td>
			  <td class="item_input">
              <span style="vertical-align: top">国家： </span>
		      <select name="country" id="country" style="width: 80px;" size="10">
              <option value="1|中国" selected="selected">中国</option>
		      </select>
              <span style="vertical-align: top">省份： </span>
		      <select name="province" id="province" onchange="return change_region(1,this.value,'city')" style="width: 80px;" size="10">
              <option value="" selected="selected">请选择...</option>
			  <?php foreach($province as $item):?>
              <option value="<?php echo $item->region_id?>|<?php echo $item->region_name?>"><?php echo $item->region_name?></option>
              <?php endforeach;?>
	          </select>
              <span style="vertical-align: top">城市： </span>
			  <select name="city" id="city" onchange="return change_region(2,this.value,'district')" style="width: 80px;" size="10">
              <option value="" selected="selected">请选择...</option>
	          </select>
              <span style="vertical-align: top">区/县： </span>
              <select name="district" id="district" style="width: 130px;" size="10">
              <option value="" selected="selected">请选择...</option>
	          </select>
              <span style="vertical-align: top">
              <input class="am-btn am-btn-primary" type="button" onclick="addRegion()" value="+">
              </span>
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