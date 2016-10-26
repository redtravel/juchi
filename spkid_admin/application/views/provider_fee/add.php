<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/depot.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.selected('provider_id', '请选择供应商');
		validator.selected('brand_id', '请选择品牌');
		validator.selected('batch_id', '请选择批次');
		validator.selected('category_id', '请选择费用名目');
		validator.isNonNegative('detail_price', '请输入金额,且只能是数字',true);
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">供应商费用明细 >> 新增 </span><a href="provider_fee" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('provider_fee/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
                <td class="item_title">供应商:</td>
                <td class="item_input">
                    <select name="provider_id" onchange="get_purchase_batch( 'provider_id' , 'batch_id' ,'该供应商无可用批次，请先创建！',1);">
                        <option value="">供应商</option>
                        <?php foreach($provider_list as $provider) print "<option value='{$provider->provider_id}'>{$provider->provider_code}</option>"?>
                    </select>
                </td>
			</tr>
			<tr>
                <td class="item_title">批次:</td>
                <td class="item_input">
                    <select name="batch_id"><option value="">请选择</option></select>
                </td>
			</tr>
			<tr>
                <td class="item_title">品牌:</td>
                <td class="item_input">
                    <select name="brand_id">
                        <option value="">品牌</option>
                        <?php foreach($brand_list as $brand) print "<option value='{$brand->brand_id}'>{$brand->brand_name}</option>"?>
                    </select>
                </td>
			</tr>
			<tr>
                <td class="item_title">费用名目:</td>
                <td class="item_input">
                    <select name="category_id">
                        <option value="">费用名目</option>
                        <?php foreach($fee_category_list as $fee_category) print "<option value='{$fee_category->category_id}'>{$fee_category->category_name}</option>"?>
                    </select>
                </td>
			</tr>
			<tr>
				<td class="item_title">金额:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'detail_price','class' => 'textbox require')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">备注:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'remark','class' => 'textbox')); ?>
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