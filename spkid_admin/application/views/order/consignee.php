<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript" src="public/js/region.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('consignee', '请填写收货人');
		var tel = $.trim($(':input[name=tel]').val());
		var mobile = $.trim($(':input[name=mobile]').val());
		if(tel=='' && mobile=='') validator.addErrorMsg('电话与手机至少填写一项');
		if($(':checkbox:checked[name=cac]').length<1){
			validator.selected('country', '请选择国家');
			validator.selected('province', '请选择省');
			validator.selected('city', '请选择城市');
			if($(':input[name=district]')[0].options.length>1){
				validator.selected('district', '请选择地区');
			}
			validator.required('address', '请填写详细收货地址');
		}		
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> <?php print $act=='add'?'新增订单':'编辑订单' ?> >> 收货地址 </span><span class="r"><a href="order/index" class="return">返回列表</a></span></div>
	<div class="blank5"></div>
	<div id="product_list">
		<?php include 'order_product.php' ?>
	</div>
	<?php print form_open('order/proc_consignee',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('order_id'=>$order->order_id,'act'=>$act));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<?php if (isset($address_list)): ?>
				<tr>
					<td class="item_title">收货地址</td>
					<td class="item_input">
						<select name="address_id" onchange="load_address(this.value)">
							<option value="">选择已保存的地址</option>
							<?php foreach ($address_list as $key => $address): ?>
								<option value="<?php print $address->address_id ?>" <?php print $address->address_id = $address_id?'selected':'' ?>>
								<?php print $address->consignee.' '.$address->address.' '.$address->tel.' '.$address->mobile ?>
								</option>
							<?php endforeach ?>
						</select>
					</td>
				</tr>
			<?php endif ?>
			<tr>
				<td class="item_title">收件人</td>
				<td class="item_input">
				<?php print form_input('consignee',$order->consignee,'class="textbox require"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">地区</td>
				<td class="item_input">
					<?php print form_dropdown('country',array(''=>'国家')+get_pair($country_list,'region_id','region_name'),$order->country,'id="selCountries" onChange="region.changed(this, \'selProvinces\')"'); ?>

					<?php print form_dropdown('province',array(''=>'省')+get_pair($province_list,'region_id','region_name'),$order->province,'id="selProvinces" onChange="region.changed(this, \'selCities\')"'); ?>

					<?php print form_dropdown('city',array(''=>'市')+get_pair($city_list,'region_id','region_name'),$order->city,'id="selCities" onChange="region.changed(this, \'selDistricts\')"'); ?>

					<?php print form_dropdown('district',array(''=>'区')+get_pair($district_list,'region_id','region_name'),$order->district,'id="selDistricts"'); ?>

				</td>
			</tr>
			<tr>
				<td class="item_title">详细地址</td>
				<td class="item_input">
					<?php print form_input('address',$order->address,'class="textbox require" size="90"') ?>
					<label>
					<?php print form_checkbox('cac',1,$order->shipping_id==SHIPPING_ID_CAC); ?>
					自提
					</label>
				</td>
			</tr>
			<tr>
				<td class="item_title">邮编</td>
				<td class="item_input">
					<?php print form_input('zipcode',$order->zipcode,'class="textbox"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">电话</td>
				<td class="item_input">
					<?php print form_input('tel',$order->tel,'class="textbox require"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">手机</td>
				<td class="item_input">
					<?php print form_input('mobile',$order->mobile,'class="textbox require"') ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">最佳送货时间</td>
				<td class="item_input">
					<?php print form_input('best_time',$order->best_time,'class="textbox"') ?>
				</td>
			</tr>
			
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
				<?php if ($act=='add'): ?>
					<input type="button" name="myprev" value="上一步" class="am-btn am-btn-primary" onclick="location.href=base_url+'order/product/<?php print $order->order_id;?>?act=add'">
					
				<?php endif ?>
					<?php print form_submit('mysubmit',$act=='add'?'下一步':'确定','class="am-btn am-btn-primary"') ?>	
					<input type="button" name="mycancel" value="取消" class="am-btn am-btn-primary" onclick="location.href=base_url+'order/info/<?php print $order->order_id;?>'">
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>