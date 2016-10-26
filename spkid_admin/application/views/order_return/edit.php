
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">

		$(function(){
            $('input[type=text][name=hope_time]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
	    });

	    var oldAgencyId = <?php print isset($order['agency_id'])?$order['agency_id']:0; ?>;
  		var order_id = <?php print isset($order['order_id'])?$order['order_id']:0; ?>;

		function check_suggestion_form(){
		  	var eles=document.forms['suggestion_form'].elements;
		  	if(eles['suggestion_content'].value==""){
		  		alert('请提点建议');
		  		return false;
		  	}
		  	if(eles['suggestiontype_id'].value=='0'){
		  		alert('请选择建议');
		  		return false;
		  	}

		  }

		function check_save(){
		   	if(!check_form_input())
		   	{
		        return false;
		    }
		   	document.forms['theForm'].action="/order_return/post_save";
		    return true;
		}

		function check_form_input(){
			var order_id = parseInt($('input[name=order_id][type=hidden]').val());
			if(isNaN(order_id)||order_id<1){
	            alert('订单ID错误!');
	            return false;
	        }
			var return_id = parseInt($('input[name=return_id][type=hidden]').val());
			if(isNaN(return_id)||return_id<1)
			{
				alert('退货单ID错误!');
				return false;
			}
			var return_product = new Array();
			var max_error = false;
			var param_error = false;

			$('tr.tr_product').each(function(i){
				var tr = $(this);
				var op_id = parseInt($('input[type=hidden][name^="op_id"]',tr).val());
				var product_number = parseInt($('input[type=text][name^="product_number"]',tr).val());

				var max_return_number = parseInt($('input[type=hidden][name^="max_return_number"]',tr).val());
				if(isNaN(op_id)||isNaN(product_number)||isNaN(max_return_number)||product_number<0){
					param_error = true;
				}
				if(product_number > max_return_number){
					max_error = true;
				}
				if(product_number>0){
					return_product.push(op_id+'|'+product_number);
				}
			});
			if(max_error){
				alert('退货数量超过商品可退数量！');
				return false;
			}
			if(param_error){
				alert('输入数据错误：退货数量请输入非负整数！');
				return false;
			}
			/*if(return_product.length<1){
				alert('请选择退货商品！');
				return false;
        }*/
			return true;

		}

		    function pre_calc_voucher(){
		        if(!check_form_input()){
		            return false;
		        }
		        var order_id = parseInt($('input[name=order_id][type=hidden]').val());
		        var return_id = parseInt($('input[name=return_id][type=hidden]').val());
		        var return_product = new Array();
		       $('tr.tr_product').each(function(i){
		           var tr = $(this);
		           var op_id = parseInt($('input[type=hidden][name^="op_id"]',tr).val());
	            	var product_num = parseInt($('input[type=text][name^="product_num"]',tr).val());
	            	var max_return_number = parseInt($('input[type=hidden][name^="max_return_number"]',tr).val());
		           return_product.push(op_id+'|'+product_num);
		       });

		       $.ajax({
		            url: '/order_return/pre_calc_voucher',
		            data: {is_ajax:1,order_id:order_id,return_product:return_product.join('$'),return_id:return_id, rnd : new Date().getTime()},
		            dataType: 'json',
		            type: 'POST',
		            success: function(result){
		                if(result.msg) {alert(result.msg)};
		            }
		        });
		    }
            
        //选择返回用户运费
        function changeCheckbox(obj,ename){
            if($(obj).prop('checked')){
                $('input[type=text][name='+ename+']').removeAttr('readonly');
            }else{
                $('input[type=text][name='+ename+']').attr('readonly','readonly');
                $('input[type=text][name='+ename+']').val('');
            }
        }
        function inputNumber(obj){
            var val = $(obj).val();
            if(isNaN(val)){
                alert(val+"不是合法数字!");
                $(obj).val('');
            }
        }
        //add by shangguannan 2013-04-18
	</script>
	<div class="main">
		<div class="main_title"><span class="l">退货单管理 &gt;&gt; 编辑退货单</span> &nbsp;单号：<?php print $return['return_sn']; ?><span class="r">[ <a href="/order_return">返回列表 </a>]</span></div>
		<div class="produce" id="order_return" style="background-color:#fff;">
			<form action="/order_return/operate" method="post" name="theForm">
			<div>
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0" style=" margin-top:0;">
			  <tr>
			    <td height="40" colspan="4">
			      <div align="center">
					<input type="hidden" name="order_id" value="<?php print $return['order_id']; ?>" />
					<input type="hidden" name="return_id" value="<?php print $return['return_id']; ?>" />
				      	<input name="save" type="submit" class="am-btn am-btn-secondary" value="保存" <?php print $operable_list['save']?'':'style="color:grey;" disabled'; ?> onclick="return check_save();" />
					    <input name="lock" type="submit" class="am-btn am-btn-secondary" value="锁定" <?php print $operable_list['lock']?'':'style="color:grey;" disabled'; ?> />
					    <input name="unlock" type="submit" class="am-btn am-btn-secondary" value="解锁" <?php print $operable_list['unlock']?'':'style="color:grey;" disabled'; ?> />
					    <input name="service_confirm" type="submit" class="am-btn am-btn-secondary" value="客审" <?php print $operable_list['service_confirm']?'':'style="color:grey;" disabled'; ?> />
				        <input name="unservice_confirm" type="submit" class="am-btn am-btn-secondary" value="反客审" <?php print $operable_list['unservice_confirm']?'':'style="color:grey;" disabled'; ?> />
				        <input name="pay" type="submit" class="am-btn am-btn-secondary" value="财审" <?php print $operable_list['pay']?'':'style="color:grey;" disabled'; ?> />
				        <input name="ship" type="submit" class="am-btn am-btn-secondary" value="入库"  <?php print $operable_list['ship']?'':'style="color:grey;" disabled'; ?>/>
				        <input name="invalid" type="submit" class="am-btn am-btn-secondary" value="作废" <?php print $operable_list['invalid']?'':'style="color:grey;" disabled'; ?> />
				        <input name="is_ok" type="submit" class="am-btn am-btn-secondary" value="完结" <?php print $operable_list['is_ok']?'':'style="color:grey;" disabled'; ?> />
				    <!--<input type="button" onclick="window.open('/order_return/edit/print/1/<?php print $return['return_id']; ?>')" class="am-btn am-btn-primary" value="打印退货单"  />-->
			    </div>
			    </td>
			  </tr>

			  <tr>
			    <th colspan="4">基本信息</th>
			  </tr>
			  <tr>
			    <td width="18%"><div align="right"><strong>退货单号：</strong></div></td>
			    <td width="34%"><?php print $return['return_sn']; ?></td>
			    <td width="15%"><div align="right"><strong>关联订单号：</strong></div></td>
			    <td><?php print $order['order_sn']; ?> <a href="/order/info/<?php print $order['order_id']; ?>" target="_blank">查看关联订单</a></td>
			  </tr>
			  <tr>
			    <td width="18%"><div align="right"><strong>退货单状态：</strong></div></td>
			    <td  colspan="3"><?php print implode(' ',$return['status']); ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>购货人：</strong></div></td>
			    <td><?php print $return['user_name']; ?>
			    </td>
			    <td><div align="right"><strong>退货申请时间：</strong></div></td>
			    <td><?php print $return['formated_create_date']; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>审核时间：</strong></div></td>
			    <td><?php print $return['formated_confirm_date']; ?>

			    </td>
			    <td><div align="right"><strong>返款时间：</strong></div></td>
			    <td><?php print $return['formated_finance_date']; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>入库时间：</strong></div></td>
			    <td><?php print $return['formated_shipping_date']; ?>
			    </td>
			    <td><div align="right"><strong>发货单号：</strong></div></td>
			    <td><?php print $return['invoice_no']; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>涉及抵用券的处理</strong></div></td>
			    <td colspan="3">
			       <input type="radio" name="voucher_back" value="1" <?php print $voucher_back != 'payback'?'checked':''; ?> <?php print $operable_list['edit_product']?'':'disabled'; ?>/> 优先拆分现金券
			       <input type="radio" name="voucher_back" value="2" <?php print $voucher_back == 'payback'?'checked':''; ?> <?php print $operable_list['edit_product']?'':'disabled'; ?> /> 优先返还现金券
			       <input type="button" name="bt_pre_calc_voucher" value="预览现金券返还详情" onclick="pre_calc_voucher();" <?php print $operable_list['edit_product']?'':'style="color:grey;" disabled'; ?> />
			    </td>
			  </tr>
			  <?php if ($order['shipping_fee'] > 0): ?>
			  <tr>
			    <td><div align="right"><strong>是否返还原订单运费</strong></div></td>
			    <td colspan="3">
			       <input type="radio" name="return_shipping_fee" value="0" <?php print $return['return_shipping_fee'] <= 0?'checked':''; ?> <?php print $operable_list['edit_product']?'':'disabled'; ?>/> 不返还
			       <input type="radio" name="return_shipping_fee" value="1" <?php print $return['return_shipping_fee'] > 0?'checked':''; ?> <?php print $operable_list['edit_product']?'':'disabled'; ?> /> 返还
			       &nbsp;&nbsp;<font color="#666">[ 注：只有原订单的最后一笔退单才可以返还运费 ]</font> <font color="red">原订单运费：<?php print $order['formated_shipping_fee']; ?>
			       <?php if ($return['return_shipping_fee'] > 0): ?>
			       <?php if ($return['pay_status'] == 0): ?>
			       , 会于财审时自动返还给用户
			       <?php elseif ($return['pay_status'] == 1): ?>
			       , 已于财审时自动返还给用户
			       <?php endif; ?>
			       <?php else: ?>
			       ，该笔退单不返还运费
			       <?php endif; ?>
			       </font>
			    </td>
			  </tr>
			  <?php endif; ?>
              <!-- 添加退货时选择是否退还用户运费 -->
                <tr>
                    <td><div align="right"><strong>是否返还用户退货运费：</strong></div></td>
                    <td colspan="3">
                        <input type="checkbox" onclick="changeCheckbox(this,'return_user_shipping_fee');" <?php print $user_shipping_fee_info['has_fee']?'checked':''; ?> <?php print $operable_list['edit_product']?'':'disabled'; ?>/>返还
                        <input type="text" name="return_user_shipping_fee" value="<?php print $user_shipping_fee_info['has_fee']?$user_shipping_fee_info['fee']:''; ?>" size="3" readonly/>
                    </td>
                </tr>
              <!-- 添加退货时选择是否退还用户运费 -->
			 </table>
			</div>

			<div class="list-div">
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
                              <th colspan="13" scope="col">退货商品信息 <?php if ($is_consign_num>0): ?><span style="color: red">[ 虚退订单 ]</span><?php endif;?><?php if ($operable_list['edit_product']): ?><input type="hidden" value="1" name="priv_edit_product"><?php endif; ?><?php print isset($product_alert)?$product_alert:''; ?>
			    </th>
			    </tr>
			  <tr>
			    <td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
			    <td scope="col"><div align="center"><strong>商品款号</strong></div></td>
			    <td scope="col"><div align="center"><strong>商品条码</strong></div></td>
			    <td scope="col"><div align="center"><strong>入库仓</strong></div></td>
			    <td scope="col"><div align="center"><strong>入库储位</strong></div></td>
			    <td scope="col"><div align="center"><strong>供应商货号</strong></div></td>
			    <td scope="col"><div align="center"><strong>价格</strong></div></td>
			    <td scope="col"><div align="center"><strong>可退数量</strong></div></td>
			    <td scope="col"><div align="center"><strong>实退数量</strong></div></td>
			    <td scope="col"><div align="center"><strong>颜色尺码</strong></div></td>
			    <td scope="col"><div align="center"><strong>生产批号</strong></div></td>
			    <td scope="col"><div align="center"><strong>有效期</strong></div></td>
			    <td scope="col"><div align="center"><strong>小计</strong></div></td>
			  </tr>
			  <?php foreach ($return_product as $product): ?>
			  <tr class="tr_product">
			    <td>
			    <?php print $product['product_name']; ?> [ <?php print $product['brand_name']; ?> ]
			    <br/><?php print isset($product['track_sn'])?$product['track_sn']:''; ?>
			    <?php print isset($product['virtual_shipping']) && !empty($product['virtual_shipping'])?'虚':''; ?>
			   <input type="hidden" name="rec_id[]" value="<?php print $product['return_rec_id']; ?>" />
			   <input type="hidden" name="track_id[]" value="<?php print $product['track_id']; ?>" />
			   <input type="hidden" name="product_id[]" value="<?php print $product['product_id']; ?>" />
			   <input type="hidden" name="color_id[]" value="<?php print $product['color_id']; ?>" />
			   <input type="hidden" name="size_id[]" value="<?php print $product['size_id']; ?>" />
			   <input type="hidden" name="op_id[]" value="<?php print $product['rec_id']; ?>" />
			   <input type="hidden" name="max_return_number[]" value="<?php print $product['product_num']; ?>" />
			    </td>
			    <td align="center"><?php print $product['product_sn']; ?></td>
			    <td align="center"><?php print $product['provider_barcode']; ?></td>
                            <td align="center"><?php print $product['depot_name']; ?></td>
			    <td align="center"><?php print $product['location_name']; ?></td>
			    <td align="center"><?php print $product['provider_productcode']; ?></td>
			    <td align="center"><?php print $product['product_price']; ?> /F：<?php print $product['shop_price']; ?></td>
			    <td align="center"><?php print $product['product_num'].$product['unit_name']; ?></td>
			    <td><div align="center">
			    <input type="text" <?php if ($is_consign_num>0): ?>readonly="readonly"<?php endif; ?> name="product_number[]" value="<?php print $product['return_product_num']; ?>" size="3" disabled/>
			    </div></td>
			    <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?> </td>
			    <td align="center"><?php print $product['production_batch']; ?></td>
			    <td align="center"><?php print ($product['expire_date'] == '0000-00-00' || $product['expire_date'] == '0000-00-00 00:00:00' || $product['expire_date'] == '')?'无':$product['expire_date']; ?></td>
			    <td><div align="center"><?php print isset($product['formated_subtotal'])?$product['formated_subtotal']:'0.00'; ?></div></td>
			  </tr>
			  <?php endforeach; ?>
			  <tr>
			      <td colspan="13" style="text-align:right;"><strong>合计：</strong><?php print $return['formated_return_price']; ?></td>
		      </tr>
			</table>
			</div>

			<div class="list-div">
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<th colspan="7">返扣款明细
					<?php if ($operable_list['pay_list']): ?>
					<a href="/order_return/pay/<?php print $return['return_id']; ?>" >编辑</a>
					<?php endif; ?>
					</th>
				</tr>
				<tr>
			            <td scope="col"><div align="center"><strong>支付日期</strong></div></td>
			            <td scope="col"><div align="center"><strong>返扣款方式</strong></div></td>
			            <td scope="col"><div align="center"><strong>交易帐号</strong></div></td>
			            <td scope="col"><div align="center"><strong>交易金额</strong></div></td>
			            <td scope="col"><div align="center"><strong>交易号</strong></div></td>
			            <td scope="col"><div align="center"><strong>备注</strong></div></td>
			            <td scope="col"><div align="center"><strong>操作员</strong></div></td>

				</tr>
				<?php foreach ($pay_arr as $pay_item): ?>
				<tr>
					<td scope="col"><div align="center"><?php print $pay_item['payment_date']; ?></div></td>
					<td scope="col"><div align="center"><?php print $pay_item['pay_name']; ?></div></td>
					<td scope="col"><div align="center"><?php print $pay_item['payment_account']; ?></div></td>
					<td scope="col"><div align="center"><?php print $pay_item['formated_payment_money']; ?></div></td>
					<td scope="col"><div align="center"><?php print $pay_item['trade_no']; ?></div></td>
					<td scope="col"><div align="center"><?php print $pay_item['payment_desc']; ?></div></td>
			                <td scope="col"><div align="center"><?php print $pay_item['admin_name']; ?></div></td>
				</tr>
				<?php endforeach; ?>
				<tr>
<td colspan="7" style="text-align:right;">
			                <strong>合计：</strong>
			                退货商品总金额：<strong><?php print $return['formated_return_price']; ?></strong>
					 - 已返扣款金额：<strong><?php print $return['formated_paid_price']; ?></strong> =
			                <?php if ($return['returned_amount'] < 0): ?> 多返扣款 <strong><font color="red"><?php print $return['formated_returned_amount']; ?></font></strong>
			                <?php else: ?>
			                尚需返扣款 <strong><?php print $return['formated_returned_amount']; ?></strong>
			                <?php endif; ?>
			            </td>
				</tr>
			</table>
			</div>

			<div class="list-div">
			 <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="4">退货人信息  <?php if($operable_list['edit_consignee']): ?><input type="hidden" value="1" name="priv_edit_consignee"><?php endif; ?>
			    </th>
		       </tr>
			 <tr>
			    <td><div align="right"><strong>退货原因</strong></div></td>
			    <td colspan="3" align="left">
					<select name="return_reason" id="return_reason" <?php print $operable_list['edit_consignee']?'':'disabled'; ?>>
	                <option value="顾客原因" <?php print ($return['return_reason'] == '顾客原因')?"selected":"" ?> >顾客原因</option>
	                <option value="运营原因" <?php print ($return['return_reason'] == '运营原因')?"selected":"" ?> >运营原因</option>
	                <option value="仓库原因" <?php print ($return['return_reason'] == '仓库原因')?"selected":"" ?> >仓库原因</option>
	                <option value="快递原因" <?php print ($return['return_reason'] == '快递原因')?"selected":"" ?> >快递原因</option>
	                <option value="其他原因" <?php print ($return['return_reason'] == '其他原因')?"selected":"" ?> >其他原因</option>
	                </select>
			    </td>
			  </tr>
			<tr>
			    <td><div align="right"><strong>预期到货时间</strong></div></td>
			    <td colspan="3" align="left">
			    <input name="hope_time" value="<?php print $return['hope_time']; ?>" style="width:80px;" <?php print $operable_list['edit_consignee']?'':'disabled'; ?>/>
			    </td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>退货人：</strong></div></td>
			    <td align="left">

			    <input type="text" name="consignee" value="<?php print $return['consignee']; ?>" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> />
			    </td>
			    <td><div align="right"><strong>电子邮件：</strong></div></td>
			    <td align="left">
			    <input type="text" name="email" value="<?php print $return['email']; ?>" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> />
			   </td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>地址：</strong></div></td>
			    <td align="left"><input type="text" name="address" value="<?php print $return['address']; ?>" size="50" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> />
			    </td>
			    <td><div align="right"><strong>邮编：</strong></div></td>
			    <td>
			    <input type="text" name="zipcode" value="<?php print $return['zipcode']; ?>" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> />
			   </td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>电话：</strong></div></td>
			    <td>
			    <input type="text" name="tel" value="<?php print $return['tel']; ?>" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> /></td>
			    <td><div align="right"><strong>手机：</strong></div></td>
			    <td>
			    <input type="text" name="mobile" value="<?php print $return['mobile']; ?>" <?php print $operable_list['edit_consignee']?'':'disabled'; ?> />
			    </td>
			  </tr>
			</table>
			</form>
      </div>
			<div class="list-div" style="margin-bottom: 5px;height:120px;overflow-y:scroll; ">
			    <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			        <tr>
			            <th>操作者：</th>
			            <th>操作时间</th>
			            <th>订单状态</th>
			            <th>付款状态</th>
			            <th>发货状态</th>
			            <th>备注</th>
			        </tr>
			        <?php foreach ($action_list as $action): ?>
			        <tr>
			            <td><div align="center"><?php print $action['admin_name']; ?></div></td>
			            <td><div align="center"><?php print $action['create_date']; ?></div></td>
			            <td><div align="center"><?php print $action['status'][0]; ?></div></td>
			            <td><div align="center"><?php print $action['status'][1]; ?></div></td>
			            <td><div align="center"><?php print $action['status'][2]; ?></div></td>
			            <td><?php print $action['action_note']; ?></td>
			        </tr>
			        <?php endforeach; ?>
			    </table>
	  </div>

			<form name="suggestion_form" action="/order_return/post_suggest/<?php print $return['return_id']; ?>" method="POST" onsubmit="return check_suggestion_form()">
			<div class="list-div" style="margin-bottom: 5px;" >
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="5">意见信息</th>
			  </tr>
			  <tr>
			    <td style="text-align:right;" valign="top"><strong>签写意见</strong></td>
			  <td colspan="4" style="padding:10px 0;">
			  <select name="suggestiontype_id">
			  <option value="0">请选择意见类型</option>
			  <?php foreach ($suggestiontype_arr as $key=>$value): ?>
			  <option value="<?php print $key; ?>"><?php print $value; ?></option>
			  <?php endforeach; ?>
			  </select><br/><br/>
			  <textarea name="suggestion_content" cols="80" rows="3"></textarea>
			  <br/><br/>
			  <input type="submit" name="suggestion" value="发布意见" onclick="return check_suggestion_form()"/>
			  </td>
			  </tr>

			  <tr>
			    <th>色标</th>
			    <th>意见类型</th>
			    <th>创建人</th>
			    <th>意见内容</th>
			    <th>创建时间</th>
			  </tr>
			  <?php foreach ($suggestion_list as $suggestion): ?>
			  <tr>
			    <td><div align="center"><div style="width:15px; height:15px; background-color:<?php print $suggestion['type_color']; ?>;"></div></div></td>
			    <td><div align="center"><?php print $suggestion['type_name']; ?></div></td>
			    <td><div align="center"><?php print $suggestion['admin_name']; ?></div></td>
			    <td><div align="center"><?php print $suggestion['advice_content']; ?></div></td>
			    <td><div align="center"><?php print $suggestion['advice_date']; ?></div></td>
			  </tr>
			  <?php endforeach; ?>
			</table>
			</div>
			</form>
	</div>

<?php include_once(APPPATH.'views/common/footer.php'); ?>
