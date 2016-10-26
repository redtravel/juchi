<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('provider_code', '请填写供应商代码');
                        validator.required('provider_name', '请填写供应商名称');
                        validator.required('provider_cooperation', '请选择供应商合作方式');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">供应商管理 >> 新增 </span><a href="provider/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('provider/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">上级供应商(厂家):</td>
				<td class="item_input"><?php print form_dropdown('parent_id',get_pair($top_providers,'provider_id','provider_code,provider_name'),array($provider_id),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?></td>
			</tr>
			<tr>
				<td class="item_title">供应商代码:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_code','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">供应商名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_name','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">供应商合作方式:</td>
				<td class="item_input"><?php print form_dropdown('provider_cooperation', get_pair($all_cooperation,'cooperation_id','cooperation_name'),2);?></td>
			</tr>
			<tr>
				<td class="item_title">公司名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'official_name','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">开户银行:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_bank','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">银行帐号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_account','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">纳税号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'tax_no','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?>禁用</label>
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?>启用</label>
				</td>
			</tr>
                        <tr>
				<td class="item_title">LOGO图:</td>
				<td class="item_input"><?php print form_upload(array('name'=> 'logo','class'=> 'wd280'));?></td>
			</tr>
                        <tr>
				<td class="item_title">前台显示名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'display_name','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货地址:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_address','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货邮编:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_postcode','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货收货人:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_consignee','class'=> 'textbox wd280'));?></td>
			</tr>
                        <tr>
				<td class="item_title">退货收货人手机:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_mobile','class'=> 'textbox wd280'));?></td>
			</tr>
                        <tr>
				<td class="item_title">短信价格:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sms_price','class'=> 'textbox wd280'));?></td>
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
