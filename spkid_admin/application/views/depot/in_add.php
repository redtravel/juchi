<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=depot_in_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
	});
	var add_type_purchase = '<?php echo (!empty($spec_type['1']))?$spec_type['1']:0;?>';
	var add_type_depotout = '<?php echo (!empty($spec_type['2']))?$spec_type['2']:0;?>';
	var add_type_transfer = '<?php echo (!empty($spec_type['4']))?$spec_type['4']:0;?>';
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('depot_in_type', '请选择入库类型');
			validator.selected('depot_depot_id', '请选择入库仓库');
			validator.required('depot_in_date', '请填写实际入库时间');
			var depot_out_type = $.trim($('select[name=depot_in_type]').val());
			if(depot_out_type >0 && (depot_out_type == add_type_purchase)){
				validator.required('order_sn', '请选择来源单号');
			}
			if(validator.errMsg.length == 0){
				document.getElementById('order_sn').disabled = '';
			}
			return validator.passed();
	}
	function change_type(obj){
		if(obj.value >0 && (obj.value == add_type_purchase || obj.value == add_type_depotout || obj.value == add_type_transfer)){
			document.getElementById('sel_code_button').disabled = '';
		} else
		{
			document.getElementById('sel_code_button').disabled = 'disabled';
		}
		$('input[type=text][name=order_sn]').val('');
		document.getElementById('order_id').value = 0;
	}

	function showWin(str)
	{
		var depot_out_type = $.trim($('select[name=depot_in_type]').val());
		if(depot_out_type >0 && (depot_out_type == add_type_purchase || depot_out_type == add_type_depotout || depot_out_type == add_type_transfer)){
			//var op = 0;
			var type = 0;
			if(depot_out_type == add_type_purchase)
			{
				type = 1;
			}
			else if(depot_out_type == add_type_depotout)
			{
				type = 2;
			}
			else if(depot_out_type == add_type_transfer)
			{
				type = 4;
			}
			//var loOBJ = new Object();
			//var lonewWin = window.showModalDialog("/depotio/show_sel_win/type/"+type,loOBJ,"dialogHeight:600px;dialogWidth:800px;center:yes;help:no;status:no;resizable:no");
			window.open("/depotio/show_sel_win/type/"+type,'newwindow',"height=800,width=800,toolbar=no,titlebar=no,location=no,menubar=no,resizable=no,z-look=yes");
			/*if(loOBJ.pass){
				$('input[type=text][name=order_sn]').val(loOBJ.order_sn);
				document.getElementById('order_id').value = loOBJ.order_id;
              }*/
		}

	}

	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">入库管理 &gt;&gt; 新增入库单</span> <span class="r">[ <a href="/depotio/in">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/depotio/proc_add_in',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">入库类型:</td>
				<td class="item_input"><?php print form_dropdown('depot_in_type',$type_list,'0',' onchange="change_type(this);"');?></td>
			</tr>
			<tr>
				<td class="item_title">来源单号:</td>
				<td class="item_input">
				<?php print form_input(array('name'=>'order_sn','id'=>'order_sn', 'class'=>'textbox','value'=>'','disabled'=>'disabled'));?>
				<input type="hidden" name="order_id" id="order_id" value="0" />&nbsp;
				<input type="button" name="sel_code_button" id="sel_code_button" value="选择" onclick="showWin();" disabled="disabled"  />
  				</td>
			</tr>
			<tr>
				<td class="item_title">入库仓库:</td>
				<td class="item_input"><?php print form_dropdown('depot_depot_id',$depot_list);?></td>
			</tr>
			<tr>
				<td class="item_title">实际入库时间:</td>
				<td class="item_input"><?php print form_input(array('name'=>'depot_in_date', 'class'=>'textbox require','value'=>date('Y-m-d')));?></td>
			</tr>
			<tr>
				<td class="item_title">入库备注:</td>
				<td class="item_input"><?php print form_input(array('name'=>'depot_in_reason', 'class'=>'textbox'));?></td>
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
