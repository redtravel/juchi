<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	<?php if ($is_edit): ?>
		$('input[type=text][name=depot_in_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+0'});
	<?php endif; ?>
	});

	var add_type_purchase = '<?php echo (!empty($spec_type['1']))?$spec_type['1']:0;?>';
	var add_type_depotout = '<?php echo (!empty($spec_type['2']))?$spec_type['2']:0;?>';

	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('depot_in_type', '请选择入库类型');
			validator.selected('depot_depot_id', '请选择入库仓库');
			validator.required('depot_in_date', '请填写实际入库时间');
			var depot_out_type = $.trim($('select[name=depot_in_type]').val());
			if(depot_out_type >0 && (depot_out_type == add_type_purchase || depot_out_type == add_type_depotout)){
				validator.required('order_sn', '请选择来源单号');
			}
			if(validator.errMsg.length == 0){
				document.getElementById('order_sn').disabled = '';
			}
			return validator.passed();
	}

	function change_type(obj){
		if(obj.value >0 && (obj.value == add_type_purchase || obj.value == add_type_depotout)){
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
		if(depot_out_type >0 && (depot_out_type == add_type_purchase || depot_out_type == add_type_depotout)){
			//var op = 0;
			var type = 0;
			if(depot_out_type == add_type_purchase)
			{
				type = 1;
			}
			else
			{
				type = 2;
			}
			//var loOBJ = new Object();
			//var lonewWin = window.showModalDialog("/depotio/show_sel_win/type/"+type,loOBJ,"dialogHeight:600px;dialogWidth:800px;center:yes;help:no;status:no;resizable:no");
			/*if(loOBJ.pass){
				$('input[type=text][name=order_sn]').val(loOBJ.order_sn);
				document.getElementById('order_id').value = loOBJ.order_id;
        }*/
            window.open("/depotio/show_sel_win/type/"+type,'newwindow',"height=800,width=800,toolbar=no,titlebar=no,location=no,menubar=no,resizable=no,z-look=yes");
		}



	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">入库管理 &gt;&gt; 入库单基础信息</span> <span class="r">[ <a href="/depotio/in">返回列表 </a>]</span></div>
	<div class="produce">
		<ul>
	         <li class="p_sel conf_btn"><span>基础信息</span></li>
	         <li class="p_nosel conf_btn" onclick="location.href='/depotio/edit_in_product/<?php print $row->depot_in_id; ?>'"><span>入库商品</span></li>
	     </ul>

	<div class="pc base">
	<?php print form_open('/depotio/proc_edit_in',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('depot_in_id'=>$row->depot_in_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="12%">入库单编号:</td>
				<td class="item_input"><?php print $row->depot_in_code;?></td>
			</tr>
			<tr>
				<td class="item_title">入库类型:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
					<?php if ($row->has_product): ?>
					<?php print form_dropdown('depot_in_type',$type_list,$row->depot_in_type,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('depot_in_type',$type_list,$row->depot_in_type,' onchange="change_type(this);"');?>
					<?php endif; ?>
				<?php else: ?>
					<?php print $type_list[$row->depot_in_type];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">来源单号:</td>
				<td class="item_input">
				<?php if ($is_edit && !$row->has_product): ?>
				<?php print form_input(array('name'=>'order_sn', 'id'=>'order_sn', 'class'=>'textbox','value'=>$row->order_sn,'disabled'=>'disabled'));?>
				<input type="hidden" name="order_id" id="order_id" value="0" />&nbsp;
				<input type="button" name="sel_code_button" id="sel_code_button" value="选择" onclick="showWin();" <?php echo ($row->depot_in_type == $spec_type['1'] || $row->depot_in_type == $spec_type['2'])?'':'disabled="disabled"' ?>  />
  				<?php else: ?>
					<?php print $row->order_sn;?>
					<input type="hidden" name="order_id" id="order_id" value="<?php print $row->order_id;?>" />&nbsp;
					<input type="hidden" name="order_sn" id="order_sn" value="<?php print $row->order_sn;?>" />&nbsp;
				<?php endif; ?>
  				</td>
			</tr>
			<tr>
				<td class="item_title">入库仓库:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
					<?php if ($row->has_product): ?>
					<?php print form_dropdown('depot_depot_id',$depot_list,$row->depot_depot_id,' disabled="disabled"');?>
					<?php else: ?>
					<?php print form_dropdown('depot_depot_id',$depot_list,$row->depot_depot_id);?>
					<?php endif; ?>
				<?php else: ?>
					<?php print $depot_list[$row->depot_depot_id];?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">实际入库时间:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'depot_in_date', 'class'=>'textbox require','value'=>date('Y-m-d',strtotime($row->depot_in_date))));?>
				<?php else: ?>
				<?php print date('Y-m-d',strtotime($row->depot_in_date));?>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">入库单总数量:</td>
				<td class="item_input"><?php print $row->depot_in_number;?></td>
			</tr>
			<tr>
				<td class="item_title">入库单总金额:</td>
				<td class="item_input"><?php print $row->depot_in_amount;?></td>
			</tr>
			<tr>
				<td class="item_title">锁定/锁定人:</td>
				<td class="item_input"><?php print $row->lock_status_name." / ".$row->lock_name;?></td>
			</tr>
			<tr>
				<td class="item_title">状态/操作人:</td>
				<td class="item_input">
				<?php print $row->depot_in_status_name." / ".$row->oper_name;?>
				</td>
			</tr>
			<tr>
				<td class="item_title">入库备注:</td>
				<td class="item_input">
				<?php if ($is_edit): ?>
				<?php print form_input(array('name'=>'depot_in_reason', 'class'=>'textbox','value'=>$row->depot_in_reason));?>
				<?php else: ?>
				<?php print $row->depot_in_reason;?>
				<?php endif; ?>
				</td>
			</tr>
			<?php if ($is_edit): ?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
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
