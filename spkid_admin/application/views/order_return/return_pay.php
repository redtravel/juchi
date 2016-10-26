<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
		var js_voucher_no='<?php print isset($voucher_no)?$voucher_no:''; ?>';
		var js_voucher_value='<?php print isset($voucher_value)?$voucher_value:''; ?>';

		$(function(){
            pay_type_change();
	    });

		function checkPaymentAmount(){

			var eles = document.forms['theForm'].elements;
			var pay_name = eles['pay_id'].options[eles['pay_id'].selectedIndex].text;

			if(pay_name=='扣款'){
	            var deduct_type='';
	            for(i=0; i<eles.length;i++){
	                if(eles[i].name=='deduct_type' && eles[i].checked){
	                    deduct_type = eles[i].value;
	                    break;
	                }
	            }
	            if(deduct_type==''){
	                alert('请选择扣款类型');
	                return false;
	            }
	            if(deduct_type=='其它'){
	                var deduct_type_express = eles['deduct_type_express'].value;
	                if(deduct_type_express==''){
	                    alert('请填写扣款类型')
	                    return false;
	                }
	            }
			}

			var payment_amount = parseFloat(eles['payment_amount'].value);
			if (payment_amount != NaN&&payment_amount >0 ){
	            return true;
			}else{
	            alert('返款金额必须为正值');
	            return false;
			}
	    }

	    function pay_type_change(){
			var eles = document.forms['theForm'].elements;
			var pay_name = eles['pay_id'].options[eles['pay_id'].selectedIndex].text;
			if(pay_name=='扣款'){
		            document.getElementById('deduct_type').style.display='';
			}else{
		            document.getElementById('deduct_type').style.display='none';
		            eles['payment_amount'].disabled=false;
			}
	    }

	    function auto_deduct(return_id){
	    	$.ajax({
	            url: '/order_return/auto_deduct',
	            data: {is_ajax:1,return_id:return_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error!=0){
		                return false;
		            }
		            location.reload();
	            	return true;
	            }
	        });
	    }
	</script>
	<div class="main">
		<div class="main_title"><span class="l">退货单管理 &gt;&gt; 返款明细</span> &nbsp;单号：<?php print $return['return_sn']; ?><span class="r">[ <a href="/order_return">返回列表 </a>]</span></div>
		<div class="produce">
			<div class="list-div" style="margin-bottom:5px;">
				<table class="dataTable" width="100%" cellpadding="3" cellspacing="1">
					<tr>
						<th colspan="8">返扣款明细</th>
					</tr>
					<tr>
						<td scope="col"><div align="center"><strong>支付日期</strong></div></td>
						<td scope="col"><div align="center"><strong>返款方式</strong></div></td>
						<td scope="col"><div align="center"><strong>交易帐号</strong></div></td>
						<td scope="col"><div align="center"><strong>返款金额</strong></div></td>
						<td scope="col"><div align="center"><strong>交易号</strong></div></td>
						<td scope="col"><div align="center"><strong>支付说明</strong></div></td>
						<td scope="col"><div align="center"><strong>操作员</strong></div></td>
						<td scope="col"><div align="center"><strong>操作</strong></div></td>
					</tr>
					<?php if (!empty($pay_arr)): ?>
					<?php foreach ($pay_arr as $pay_item): ?>
					<tr>
						<td scope="col"><div align="center"><?php print $pay_item['payment_date']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['pay_name']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['payment_account']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['formated_payment_money']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['trade_no']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['payment_desc']; ?></div></td>
						<td scope="col"><div align="center"><?php print $pay_item['admin_name']; ?></div></td>
						<td scope="col"><div align="center">
						<?php if ($pay_item['payment_admin'] != -1): ?>
							<a href="/order_return/pay_remove/return_id/<?php print $return_id; ?>/payment_id/<?php print isset($pay_item['payment_id'])?$pay_item['payment_id']:'0'; ?>" onclick="return confirm('确认删除吗')">删除</a>
				        <?php endif; ?>
						</div></td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
					<tr>
						<td colspan="7" align="right">
						退货商品总金额：<?php print $return['formated_return_price']; ?> - 已返扣款金额：<?php print $return['formated_paid_price']; ?> =
						</td>
						<td><?php print $return['formated_returned_amount']; ?></td>
					</tr>
				</table>
				</div>
				<form name="theForm" action="/order_return/pay_post/return_id/<?php print $return_id; ?>/payment_id/<?php print isset($pay_item['payment_id'])?$pay_item['payment_id']:'0'; ?>" method="post" onsubmit="return checkPaymentAmount()">
				<div class="list-div">
				<table class="dataTable" cellpadding="3" cellspacing="1">
					<tr>
						<th colspan="2">添加支付明细</th>
					</tr>
					<tr>
						<td align="center" width="200"><strong>返款方式</strong></td>
						<td align="left">
						<select name="pay_id" id="pay_id" style="width:150px;" onchange="pay_type_change()" style="vertical-align:middle">
						<?php foreach ($pay_type_arr as $key=>$value): ?>
						<option value="<?php print $key; ?>" ><?php print $value; ?></option>
						<?php endforeach; ?>
						</select>
				                    <input type="button"  value="自动扣除折扣"  class="am-btn am-btn-secondary" style="vertical-align:middle" onclick="auto_deduct('<?php print $return_id; ?>');" />
				                </td>
					</tr>
					<tr style="display:block;" id="deduct_type">
						<td align="center" width="200"><strong>扣款类型</strong></td>
				                <td align="left">
				                    <input type="radio" name="deduct_type" value="运费" onclick="this.form.payment_amount.value='';this.form.payment_amount.disabled=false;" />运费
				                    <input type="radio" name="deduct_type" value="其它" onclick="this.form.payment_amount.value='';this.form.payment_amount.disabled=false;" />其它 <input type="text" name="deduct_type_express" size="10" />
				                </td>
					</tr>

					<tr style="display:none;">
						<td align="center" width="200"><strong>返款帐号</strong></td>
						<td align="left"><input type="text" name="payment_account" value="" size="30" /></td>
					</tr>

					<tr>
						<td align="center" width="200"><strong>返款金额</strong></td>
						<td align="left"><input type="text" name="payment_amount" value="" size="30" /><input type="hidden" name="voucher_value" value="<?php print isset($voucher_value)?$voucher_value:''; ?>" /></td>
					</tr>
					<tr style="display:none;">
						<td align="center" width="200"><strong>交易号</strong></td>
						<td align="left"><input type="text" name="trade_no" value="" size="30" /></td>
					</tr>
					<tr>
						<td align="center" width="200"><strong>支付说明</strong></td>
						<td align="left"><textarea name="payment_desc" cols="60" rows="3" ></textarea></td>
					</tr>
					<tr>
						<td colspan="2">
						<input type="submit" name="submit" value="<?php print isset($payment_id) && !empty($payment_id)?'修改支付明细':'添加支付明细'; ?>" class="am-btn am-btn-primary" />
				        <input type="button" name="back" value="返回" class="am-btn am-btn-primary" onclick="javascript:location.href='/order_return/edit/<?php print $return_id; ?>'" />
						</td>
					</tr>

				</table>
				</div>
			</form>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
