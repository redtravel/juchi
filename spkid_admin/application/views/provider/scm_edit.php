<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
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
	<div class="main_title"><span class="l">
		<?php if(!empty($parent)){ ?>
			发货商管理 >> 直发设置 </span><a href="provider/scm_index/<?php print $parent->provider_id;?>" class="return r">返回列表</a>
		<?php }else{ ?>
			供应商管理 >> 直发设置 </span><a href="provider/index" class="return r">返回列表</a>
		<?php } ?>	
	</div>
	<div class="blank5"></div>
	<?php 
		if(empty($parent)){ print form_open_multipart('provider/proc_scm_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('provider_id'=>$row->provider_id));}
		else{print form_open_multipart('provider/proc_scm_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('provider_id'=>$row->provider_id,'parent_id'=>$parent->provider_id));}
	?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<?php if(!empty($parent)){ ?> 
			<tr>
				<td class="item_title">供应商:</td>
                <td class="item_input"><?php echo $parent->provider_name;?></td>
			</tr>
			<?php } ?>
                        <tr>
				<td class="item_title">登陆状态:</td>
                                <td class="item_input">
                                    <label><?php print form_radio('provider_status',0,!$row->provider_status,$perm_edit?'':'disabled'); ?>正常</label>
                                    <label><?php print form_radio('provider_status',1,$row->provider_status,$perm_edit?'':'disabled'); ?>锁定</label>					
				</td>
			</tr>
                        <tr>
				<td class="item_title">登陆用户名:</td>
				<td class="item_input"><?php print form_input('user_name',$row->user_name,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>	
                        <tr>
				<td class="item_title">登陆密码:</td>
				<td class="item_input"><?php print form_password('password','','class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>	
			<tr>
				<td class="item_title">公司名称:</td>
				<td class="item_input"><?php print form_input('official_name',$row->official_name,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">公司地址:</td>
				<td class="item_input"><?php print form_input('official_address',$row->official_address,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">开户银行:</td>
				<td class="item_input"><?php print form_input('provider_bank',$row->provider_bank,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">银行帐号:</td>
				<td class="item_input"><?php print form_input('provider_account',$row->provider_account,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">税率:</td>
				<td class="item_input"><?php print form_input('provider_cess',$row->provider_cess,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">负责人:</td>
				<td class="item_input"><?php print form_input('scm_responsible_user',$row->scm_responsible_user,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">负责人手机:</td>
				<td class="item_input"><?php print form_input('scm_responsible_phone',$row->scm_responsible_phone,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">负责人QQ:</td>
				<td class="item_input"><?php print form_input('scm_responsible_qq',$row->scm_responsible_qq,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">负责人EMAIL:</td>
				<td class="item_input"><?php print form_input('scm_responsible_mail',$row->scm_responsible_mail,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">订单处理联系人:</td>
				<td class="item_input"><?php print form_input('scm_order_process_user',$row->scm_order_process_user,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">订单处理人手机:</td>
				<td class="item_input"><?php print form_input('scm_order_process_phone',$row->scm_order_process_phone,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">订单处理人QQ:</td>
				<td class="item_input"><?php print form_input('scm_order_process_qq',$row->scm_order_process_qq,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">订单处理人EMAIL:</td>
				<td class="item_input"><?php print form_input('scm_order_process_mail',$row->scm_order_process_mail,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货地址:</td>
                                <td class="item_input"><?php print form_input('return_address',$row->return_address,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货邮编:</td>
                                <td class="item_input"><?php print form_input('return_postcode',$row->return_postcode,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货收货人:</td>
                                <td class="item_input"><?php print form_input('return_consignee',$row->return_consignee,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">退货收货人手机:</td>
                                <td class="item_input"><?php print form_input('return_mobile',$row->return_mobile,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>
                        <tr>
				<td class="item_title">短信价格:</td>
                                <td class="item_input"><?php print form_input('sms_price',$row->sms_price,'class="textbox wd280" '.($perm_edit?'':'disabled'));?></td>
			</tr>

			<tr>
			  <td class="item_title">所辖地区:</td>
				<td height="35" class="item_input" id="addregion">
                <?php 
				foreach($all_region as $item){
					echo '<input name="area[]" checked="checked" type="checkbox" id="che_'.$item->region_id.'" value="'.$item->region_id.'" /> '.$item->region_name.'&nbsp;';
				}
				?>
                </td>
			</tr>

			<tr>
			  <td class="item_title">&nbsp;</td>
			  <td class="item_input am-cf" style="height:230px;">
			  <div class='am-fl'>
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
              </div>
				<div calss="am-fr am-vertical-align">
				<section class="am-panel am-panel-default am-vertical-align-middle">
				  <header class="am-panel-hd">
				    <h3 class="am-panel-title">选择所辖地区举例：</h3>
				  </header>
				  <div class="am-panel-bd">
				    1.若配送整个<mark>江苏省</mark>，则只需添加<mark>江苏省</mark><br/>
				    2.若配送江苏省下面的<mark>南京市</mark>，则只需添加<mark>南京市</mark>；<mark>不需</mark>添加江苏省。
				  </div>
				</section>
				</div>
              </td>
		  </tr>
			<?php if ($perm_edit): ?>
				<tr>
					<td class="item_title"></td>
					<td class="item_input">
						<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
					</td>
				</tr>
			<?php endif ?>
			
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
