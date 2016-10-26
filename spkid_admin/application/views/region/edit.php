<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	/*
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('region_name', '请填写地区名称');
		return validator.passed();
	}
	*/
	
	function check_form(type){
		var region_name = $('#region_name').val();
		if(!region_name){
			alert('请填写地区名称');
			return false;
		}
		if(type == 1){
			var online_shipping_fee = $('#online_shipping_fee').val();
			var cod_shipping_fee = $('#cod_shipping_fee').val();
			if(online_shipping_fee != 'undefined' && online_shipping_fee != ''){
				if(isNaN(online_shipping_fee) || online_shipping_fee < 1){
					alert('输入的“在线支付运费”必须大于0或为空');
					return false;
				}
			}
			if(cod_shipping_fee != 'undefined' && cod_shipping_fee != ''){
				if(isNaN(cod_shipping_fee) || cod_shipping_fee < 1){
					alert('输入的“货到付款运费”必须大于0或为空');
					return false;
				}
			}
		}
		return true;
	}
	//]]>
</script>
<div class="main">
  <div class="main_title"><span class="l">地区管理 >> 编辑</span> <a href="javascript:history.go(-1)" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('region/proc_edit/'.$region->region_id.'/'.$region_type.'/'.$parent_id,array('name'=>'mainForm','onsubmit'=>'return check_form('.$region_type.')'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">地区名称:</td>
				<td class="item_input">
                <input name="region_name" class="textbox require" id="region_name" value="<?php echo $region->region_name;?>" /></td>
			</tr>
			<?php if ($region_type == 1):?>
			<tr>
				<td class="item_title">在线支付运费:</td>
				<td class="item_input">
                <input name="online_shipping_fee" id="online_shipping_fee" value="<?php echo $online_shipping_fee==0.00?'':$online_shipping_fee;?>" /> 元&nbsp;&nbsp;<span style="color:red">(金额必须是大于0或者为空，精确到元)</span></td>
			</tr>
			<tr>
				<td class="item_title">货到付款运费:</td>
				<td class="item_input">
                <input name="cod_shipping_fee" id="cod_shipping_fee" value="<?php echo $cod_shipping_fee==0.00?'':$cod_shipping_fee;?>" /> 元&nbsp;&nbsp;<span style="color:red">(金额必须是大于0或者为空，精确到元)</span></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>