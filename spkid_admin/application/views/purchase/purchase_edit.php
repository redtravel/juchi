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
	<div class="main_title"><span class="l">采购管理 &gt;&gt; 采购单基础信息</span> <span class="r">[ <a href="/purchase/index">返回列表 </a>]</span></div>
	<div class="produce">
		<ul>
	         <li class="p_sel conf_btn"><span>基础信息</span></li>
	         <li class="p_nosel conf_btn" onclick="location.href='/purchase/edit_product/<?php print $row->purchase_id; ?>'"><span>采购单商品</span></li>
	     </ul>

	<div class="pc base">
	<?php print form_open('purchase/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('purchase_id'=>$row->purchase_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">采购单编号:</td>
				<td class="item_input"><?php print $row->purchase_code;?></td>
			</tr>
			<tr>
				<td class="item_title">供应商:</td>
				<td class="item_input">
				<?php if ($is_edit && !$row->has_product): ?>
					<select id="purchase_provider" name="purchase_provider" onchange="get_provider_batch(this);" >
						<?php foreach ($provider_list as $key => $val): ?>
						<option value="<?php print $key; ?>" <?php if ($key==$row->purchase_provider): ?>selected="selected"<?php endif;?>><?php print $val; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<?php print $provider_list[$row->purchase_provider];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">批次号:</td>
				<td class="item_input">
				<?php if ($is_edit && !$row->has_product): ?>
					<select id="purchase_batch" name="purchase_batch" onchange="get_batch_brand(this);">
						<?php foreach ($batch_list as $key => $val): ?>
						<option value="<?php print $key; ?>" <?php if ($key==$row->batch_id): ?>selected="selected" <?php endif;?> ><?php print $val; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<?php print $batch_list[$row->batch_id];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">品牌:</td>
				<td class="item_input">
				<?php if ($is_edit && !$row->has_product): ?>
					<input type="hidden" id="brand_id" name="purchase_brand" value="<?php print $row->purchase_brand; ?>">
					<input type="text" id="brand_name" readonly="readonly" class="textbox require" style="width:180px;" value="<?php print $brand->brand_name; ?>">
				<?php else: ?>
					<?php print $brand->brand_name; ?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">采购发起时间:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'purchase_order_date', 'id'=>'purchase_order_date', 'class'=>'textbox','value'=>date('Y-m-d',strtotime($row->purchase_order_date))));?>
				<?php else: ?>
				<?php print date('Y-m-d',strtotime($row->purchase_order_date));?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">预期交货时间:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'purchase_delivery','id'=>'purchase_delivery','class'=>'textbox','value'=> ( $row->purchase_delivery == '0000-00-00 00:00:00' ? '': date('Y-m-d',strtotime($row->purchase_delivery)) ) ));?>
				<?php else: ?>
				<?php print date('Y-m-d',strtotime($row->purchase_delivery));?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">采购单总金额:</td>
				<td class="item_input"><?php print $row->purchase_amount;?></td>
			</tr>
			<tr>
				<td class="item_title">采购单总数量:</td>
				<td class="item_input"><?php print $row->purchase_number;?></td>
			</tr>
			<tr>
				<td class="item_title">实际采购完工数量:</td>
				<td class="item_input"><?php print $row->purchase_finished;?></td>
			</tr>
			<tr>
				<td class="item_title">锁定/锁定人:</td>
				<td class="item_input"><?php print $row->lock_status_name." / ".$row->lock_name;?></td>
			</tr>
			<tr>
				<td class="item_title">状态/操作人:</td>
				<td class="item_input">
				<?php print $row->purchase_status_name." / ".$row->oper_name;?>
				</td>
			</tr>
			<tr>
				<td class="item_title">备注:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'purchase_remark', 'class'=>'textbox','value'=>$row->purchase_remark));?>
				<?php else: ?>
				<?php print $row->purchase_remark;?>
				<?php endif; ?>
				</td>
			</tr>
			<?php if ($is_edit): ?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div></div></div>
<?php include(APPPATH.'views/common/footer.php');?>