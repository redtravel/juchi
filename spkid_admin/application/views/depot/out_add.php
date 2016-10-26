<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=depot_out_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});

	});
	function check_form(){
		var validator = new Validator('mainForm');
			//validator.selected('provider_id', '请选择供应商');
			validator.selected('depot_out_type', '请选择出库类型');
			validator.selected('depot_depot_id', '请选择出库仓库');
			validator.required('depot_out_date', '请填写实际出库时间');
			return validator.passed();
	}
	function change_provider(dom) {
		var url = '/purchase_batch/get_provider_batch/'+dom.value;
		$("#batch_id option").remove();
		$("#batch_depot_tip").hide();
		$("#batch_trans_out_tip").hide();
		var emptyStr = '<option value="">请选择</option>';
		$("#batch_id").append(emptyStr);
		$. get(url, function(result) {
			$.each($.parseJSON(result), function() {
		        var htmlStr = '<option value="'+this.batch_id+'">'+this.batch_code+'</option>';
		        $("#batch_id").append(htmlStr);
		    });
        });
	}
	function change_batch(dom) {
		if($("select[name='depot_out_type']").val() != 7) {
			$("#batch_depot_tip").hide();
			$("#batch_trans_out_tip").hide();
			return;
		}
		var url = '/depotio/get_batch_depot/'+dom.value;
		$("#batch_depot_tip").hide();
		$. get(url, function(result) {
			result = $.parseJSON(result);
	        $("#batch_depot_tip").html(result.depot);
	        $("#batch_depot_tip").show();
	        if(result.trans_out) {
		        $("#batch_trans_out_tip").html('<a href="depotio/transaction_log/'+dom.value+'" target="_blank" style="color:red;">此批次含有待入待出商品</a>');
		        $("#batch_trans_out_tip").show();
	        } else {
	        	$("#batch_trans_out_tip").html('');
	        	$("#batch_trans_out_tip").show();
	        }
        });
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">出库管理 &gt;&gt; 新增出库单</span> <span class="r">[ <a href="/depotio/out">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/depotio/proc_add_out',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">出库类型:</td>
				<td class="item_input"><?php print form_dropdown('depot_out_type',$type_list);?></td>
			</tr>
			<tr>
				<td class="item_title">出库仓库:</td>
				<td class="item_input"><?php print form_dropdown('depot_depot_id',$depot_list);?></td>
			</tr>
			<tr>
				<td class="item_title">实际出库时间:</td>
				<td class="item_input"><?php print form_input(array('name'=>'depot_out_date', 'class'=>'textbox require','value'=>date('Y-m-d')));?></td>
			</tr>
			<tr>
				<td class="item_title">供应商:</td>
				<td class="item_input"><?php print form_dropdown('provider_id',$provider_list,'','onchange="change_provider(this)"');?></td>
			</tr>
			<tr>
                <td class="item_title">批次:</td>
                <td class="item_input">
                    <select id="batch_id" name="batch_id" onchange="change_batch(this);">
                        <option value="">请选择</option>
                    </select>
                    <span id="batch_depot_tip" style="display: none; padding-left: 10px; color: blue;"></span>
                    <span id="batch_trans_out_tip" style="display: none; padding-left: 10px; color: red;"></span>
                </td>
			</tr>
			<tr>
				<td class="item_title">出库备注:</td>
				<td class="item_input"><?php print form_input(array('name'=>'depot_out_reason', 'class'=>'textbox'));?></td>
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