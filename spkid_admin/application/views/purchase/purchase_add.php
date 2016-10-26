<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/depot.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=purchase_order_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
		$('input[type=text][name=purchase_delivery]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('purchase_provider', '请选择供应商');
			validator.selected('purchase_batch', '请选择批次号');
			validator.required('purchase_brand', '品牌不能为空');
			validator.required('purchase_order_date', '请填写采购发起时间');
			//validator.required('purchase_delivery', '请填写预期交货时间');
			if( validator.isInput('purchase_delivery') ){
				validator.islt('purchase_order_date', 'purchase_delivery', '采购发起时间不能早于预期交货时间');
			}
			
			return validator.passed();
	}
	function get_provider_batch(dom) {
		$("#purchase_batch option").remove();
		var emptyStr = '<option value="">请选择</option>';
		$("#purchase_batch").append(emptyStr);
		$("#brand_id").val('');
		$("#brand_name").val('');
		var url = '/purchase_batch/get_provider_batch/'+dom.value;
		$.get(url,function(result){
			$.each($.parseJSON(result), function() {
		        var htmlStr = '<option value="'+this.batch_id+'">'+this.batch_code+'</option>';
		        $("#purchase_batch").append(htmlStr);
		    });
		});
	}
	function get_batch_brand(dom) {
		var url = '/purchase_batch/get_batch_brand/'+dom.value;
		$("#brand_id").val('');
		$("#brand_name").val('');
		$.get(url,function(result){
			var brand = $.parseJSON(result);
			if(brand != null) {
				$("#brand_id").val(brand.brand_id);
				$("#brand_name").val(brand.brand_name);
			}
		});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">采购管理 &gt;&gt; 新增采购单</span> <span class="r">[ <a href="/purchase/index">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/purchase/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">供应商:</td>
				<td class="item_input">
					<select id="purchase_provider" name="purchase_provider" onchange="get_provider_batch(this);" data-am-selected="{searchBox: 1,maxHeight: 300}">
						<?php foreach ($provider_list as $key => $val): ?>
						<option value="<?php print $key; ?>"><?php print $val; ?></option>
						<?php endforeach; ?>
					</select>
					<?php //print form_dropdown('purchase_provider',$provider_list,empty($provider_id)?"":$provider_id,"onchange=get_purchase_batch_use('该供应商无可用批次，请先创建！')");?>
				</td>
			</tr>
			<tr>
				<td class="item_title">批次号:</td>
				<td class="item_input">
					<select id="purchase_batch" name="purchase_batch" onchange="get_batch_brand(this);">
						<option value="">请选择</option>
					</select>
					<?php //print form_dropdown('purchase_batch',(empty($batch_list)?array("请选择"):$batch_list),empty($batch_id)?'':$batch_id,"onchange=get_batch_brand(this);" );?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌:</td>
				<td class="item_input">
				<?php //print form_dropdown('purchase_brand',$brand_list);?>
				<input type="hidden" id="brand_id" name="purchase_brand">
				<input type="text" id="brand_name" readonly="readonly" class="textbox require" style="width:180px;">
				</td>
			</tr>
			<tr>
				<td class="item_title">采购发起时间:</td>
				<td class="item_input"><?php print form_input(array('name'=>'purchase_order_date','id'=>'purchase_order_date', 'class'=>'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">预期交货时间:</td>
				<td class="item_input"><?php print form_input(array('name'=>'purchase_delivery','id'=>'purchase_delivery','class'=>'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title">备注:</td>
				<td class="item_input"><?php print form_input(array('name'=>'purchase_remark', 'class'=>'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'添加'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>