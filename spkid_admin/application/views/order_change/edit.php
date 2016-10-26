
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>

	<style type="text/css">
        .dis{display:block;}
		.inline{display:inline;}
		.none{display:none;}
    </style>

	<script type="text/javascript">

		$(function(){

	    });
	    var order_id = "<?php print $change['order_id']; ?>";
		var alertflag = 0;
		var lastobj = null;

		  function check_suggestion_form(){
		  	var eles=document.forms['suggestion_form'].elements;
		  	if(eles['suggestion_content'].value==""){
		  		alert(need_suggestion_content);
		  		return false;
		  	}
		  	if(eles['suggestiontype_id'].value=='0'){
		  		alert(need_suggestiontype_id);
		  		return false;
		  	}

		  }

		  function check_save(){
		   	var eles=document.forms['theForm'].elements;
		   	var valid = false;
		   	for(i=0; i< eles.length; i++){

		   		if(eles[i].type=="text" && eles[i].name.substr(0,4)=="num_" && eles[i].value >0 ){
		   			valid = true;
		   			break;
		   		}
		   	}

		   	if(!valid){
		   		alert("没有选择要换货的商品!");
		   		return false;
		   	}
			document.forms['theForm'].action="/order_change/post_save";
		  	document.forms['theForm'].submit();

		  }

		  function check_shipping()
		  {
				alert("请先填写快递单号!");
		   		return false;
		  }

		  function check_depotshipsave()
		  {
		  	var eles=document.forms['theForm'].elements;
		  	var valid = false;
		  	if(eles['shipping_id'].value == 0)
		  	{
		  		alert("没有选择配送方式!");
		   		return false;
		  	}
			document.forms['theForm'].action="/order_change/depotshipsave";
		  	document.forms['theForm'].submit();
		  	return false;
		  }

		  function check_invoice()
		  {
		  	var eles=document.forms['theForm'].elements;
		  	var valid = false;
		  	if(eles['invoice_no'].value == '')
		  	{
		  		alert("没有填写快递单号!");
		   		return false;
		  	}
			document.forms['theForm'].action="/order_change/invoice_save";
		  	document.forms['theForm'].submit();
		  	return false;
		  }

		  function handleOnFucus(obj)
		  {
			if(lastobj == obj.name)
			{
				alertflag = 0;
			}
			lastobj = null;
		  }

		    function checkmax(obj, aid_str, maxvalue, goods_str, maxgoods)
		    {
				if(alertflag == 1)
				{
			            alertflag = 0;
			            return false;
				}

				lastobj = obj.name;
				if(Utils.isInt(obj.value) == false || parseInt(obj.value) < 0)
				{
			            alertflag = 1;
			            alert("不是有效的换货数量!");
			            obj.focus();
			            return false;
				}
				lastobj = null;
		    }

			function addchangeitem(obj)
			{
				var objvalue = $('#sel_'+obj).val();
		        var numvalue = $('#sum_'+obj).val();
				var depotvalue = $('#depot_'+obj).val();
				var depotarr = depotvalue.split('___');
				var depot_id = depotarr[0];
				var depot_name = depotarr[1];

				if(objvalue == 0)
				{
					alert('请选择要更换的商品');
					return false;
				}
				if($('#tr_'+objvalue).attr('class')!= 'none')
				{
					alert('要更换的商品已经在编辑区，不能重复添加');
					return false;
				}
				$('#tr_'+objvalue).removeClass();
		        $('#num_'+objvalue).val(numvalue);
			}

			function delchangeitem(obj)
			{
				$('#num_'+obj).val(0);
		        $('#tr_'+obj).attr('class','none');
			}

	</script>
	<div class="main">
		<div class="main_title"><span class="l">换货单管理 &gt;&gt; 编辑换货单</span> &nbsp;单号：<?php print $change['change_sn']; ?><span class="r">[ <a href="/order_change">返回列表 </a>]</span></div>
		<div class="produce" id="order_return" style="background-color:#fff;">
			<form action="/order_change/operate" method="post" name="theForm">
			<div class="list-div" style="margin-bottom: 5px">
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0" style="margin-top:0;">
			  <tr>
			    <td height="40" colspan="4">
			      <div align="center">
			      	<input type="hidden" name="order_id" value="<?php print $change['order_id']; ?>" />
					<input type="hidden" name="change_id" value="<?php print $change['change_id']; ?>" />
				      	<input name="save" type="submit" class="am-btn am-btn-secondary" value="保存"  <?php print $operable_list['save']?'':'style="color:grey;" disabled'; ?> onclick="return check_save();" />
					    <input name="lock" type="submit" class="am-btn am-btn-secondary" value="锁定"  <?php print $operable_list['lock']?'':'style="color:grey;" disabled'; ?> />
					    <input name="unlock" type="submit" class="am-btn am-btn-secondary" value="解锁"  <?php print $operable_list['unlock']?'':'style="color:grey;" disabled'; ?> />
					    <input name="service_confirm" type="submit" class="am-btn am-btn-secondary" value="客审"  <?php print $operable_list['service_confirm']?'':'style="color:grey;" disabled'; ?> />
				        <input name="unservice_confirm" type="submit" class="am-btn am-btn-secondary" value="反客审"  <?php print $operable_list['unservice_confirm']?'':'style="color:grey;" disabled'; ?> />
				        <input name="shipped" type="submit" class="am-btn am-btn-secondary" value="入库"   <?php print $operable_list['shipped']?'':'style="color:grey;" disabled'; ?>/>
				        <input name="shipping" type="submit" class="am-btn am-btn-secondary" value="发货"  <?php print $operable_list['shipping']?'':'style="color:grey;" disabled'; ?> <?php if(empty($change['invoice_no'])): ?>onclick="return check_shipping();"<?php endif;?> />
				        <input name="invalid" type="submit" class="am-btn am-btn-secondary" value="作废"  <?php print $operable_list['invalid']?'':'style="color:grey;" disabled'; ?> />
				        <input name="is_ok" type="submit" class="am-btn am-btn-secondary" value="完结"  <?php print $operable_list['is_ok']?'':'style="color:grey;" disabled'; ?> />
				        <?php if($change['odd']):?>
                                        <input name="odd_cancel" type="submit" class="am-btn am-btn-secondary" value="取消问题单标记"  <?php print $operable_list['odd_cancel']?'':'style="color:grey;" disabled'; ?> />
                                        <?php else:?>
                                        <input name="odd" type="submit" class="am-btn am-btn-secondary" value="标记为问题单"  <?php print $operable_list['odd']?'':'style="color:grey;" disabled'; ?> />
                                        <?php endif;?>
				    <!--|<input type="button" onclick="location.href='/order_change/shipping/<?php print $change['change_id']; ?>'" class="am-btn am-btn-primary" value="发货记录/手工发货" />
				    <input type="button" onclick="window.open('/order_change/edit/print/1/<?php print $change['change_id']; ?>')" class="am-btn am-btn-primary" value="打印退货单"  />-->
			    </div>
			    </td>
			  </tr>

			  <tr>
			    <th colspan="4">基本信息</th>
			  </tr>
			  <tr>
			    <td width="18%"><div align="right"><strong>换货单号：</strong></div></td>
			    <td width="34%"><?php print $change['change_sn']; ?></td>
			    <td width="15%"><div align="right"><strong>关联订单号：</strong></div></td>
			    <td><?php print $order['order_sn']; ?> <a href="/order/info/<?php print $order['order_id']; ?>" target="_blank">查看关联订单</a></td>
			  </tr>
			  <tr>
			    <td width="18%"><div align="right"><strong>换货单状态：</strong></div></td>
			    <td  colspan="3"><?php print implode('&nbsp;',  format_change_status($change,TRUE)); ?></td>
			  </tr>

			  <tr>
			    <td><div align="right"><strong>配送方式：</strong></div></td>
			    <td>
			     <select name="shipping_id" <?php print ($operable_list['point_shipping_type']||$operable_list['edit_shipping_type']||$operable_list['depotshipsave'])?'':' disabled'; ?> >
			     <option value="0">请选择</option>
			     <?php if (!empty($shipping_list_arr)): ?>
			     <?php foreach ($shipping_list_arr as $key=>$value): ?>
			     <option value="<?php print $key; ?>" <?php print $change['shipping_id'] == $key?'selected':''; ?> ><?php print $value; ?></option>
			     <?php endforeach; ?>
			     <?php endif; ?>
			     </select>&nbsp;
			     <?php if($operable_list['depotshipsave']): ?>
			     <input name="depotshipsave" type="submit" class="am-btn am-btn-secondary" value="保存配送方式" <?php print $operable_list['depotshipsave']?'':'style="color:grey;" disabled'; ?> onclick="return check_depotshipsave();" />
			     <?php endif; ?>
			    </td>
			    <td><div align="right"><strong>发货单号：</strong></div></td>
			    <td><?php if ($operable_list['shipping']): ?><input type="text" id="invoice_no" name="invoice_no" value="<?php print $change['invoice_no']; ?>" /><input type="submit" class="am-btn am-btn-secondary" value="保存" onclick="return check_invoice();"><?php else: ?><?php print $change['invoice_no']; ?><?php endif; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>购货人：</strong></div></td>
			    <td><?php print $change['user_name']; ?>
			    </td>
			    <td><div align="right"><strong>换货申请时间：</strong></div></td>
			    <td><?php print $change['formated_create_date']; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>审核时间：</strong></div></td>
			    <td><?php print $change['formated_confirm_date']; ?>

			    </td>
			    <td><div align="right"><strong>入库时间：</strong></div></td>
			    <td><?php print $change['formated_shipped_date']; ?></td>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>发货时间：</strong></div></td>
			    <td><?php print $change['formated_shipping_date']; ?>
			    </td>
			    <td><div align="right"></div></td>
			    <td></td>
			  </tr>
			 </table>
			</div>
			<?php if($operable_list['edit_product']): ?><input type="hidden" value="1" name="priv_edit_product"><?php endif; ?>
			<div style="margin-bottom: 5px">
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="7" scope="col">换货记录信息</th>
			    </tr>
			  <tr>
			  	<td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
			    <td scope="col"><div align="center"><strong>商品款号</strong></div></td>
			    <td scope="col"><div align="center"><strong>供应商货号</strong></div></td>
			    <td scope="col"><div align="center"><strong>原始颜色尺码</strong></div></td>
			    <td scope="col"><div align="center"><strong>换货数量</strong></div></td>
			    <td scope="col"><div align="center"><strong>换货颜色尺码</strong></div></td>
			    <td scope="col"><div align="center"><strong>出库储位</strong></div></td>
			  </tr>
			  <?php if (!empty($change_product)): ?>
			  <?php foreach ($change_product as $product): ?>
			  <tr>
			    <td>
			    <?php print $product['product_name']; ?> [ <?php print $product['brand_name']; ?> ]
			    </td>
			    <td align="center"><?php print $product['product_sn']; ?></td>
			    <td align="center"><?php print $product['provider_productcode']; ?></td>
			    <td align="center"><?php print $product['src_color_name']; ?>--<?php print $product['src_size_name']; ?></td>
			    <td align="center"><?php print $change['change_status'] <= 1 && $change['shipping_status'] == 0 && $product['gl_consign_num'] == -1 && $product['gl_num'] < $product['consign_num']?'永久缺货':''; ?> <?php print isset($product['change_num'])?$product['change_num']:0; ?><?php print $product['unit_name']; ?><?php print $product['consign_num'] > 0?' / 虚'.$product['consign_num'].$product['unit_name']:''; ?></td>
			    <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?></td>
			    <td align="center"><?php print isset($product['depot_out'])?$product['depot_out']:''; ?>
			    </td>
			  </tr>
			  <?php endforeach; ?>
			  <?php endif; ?>
			</table>
		</div>
		<div class="list-div" style="margin-bottom:5px;">
		<?php if($operable_list['edit_shipping_type']): ?>
		  <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
		   <tr>
		    <th colspan="8" scope="col">订单商品编辑区 <?php if($operable_list['edit_consignee']): ?><input type="hidden" value="1" name="priv_edit_consignee"><?php endif; ?>
		    </th>
		    </tr>
		  <tr>
		  	<td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
		    <td scope="col"><div align="center"><strong>商品款号</strong></div></td>
		    <td scope="col"><div align="center"><strong>供应商货号</strong></div></td>
		    <td scope="col"><div align="center"><strong>可换数量</strong></div></td>
		    <td scope="col"><div align="center"><strong>颜色尺码</strong></div></td>
		    <td scope="col"><div align="center"><strong>换货数量</strong></div></td>
		    <td scope="col"><div align="center"><strong>换货颜色尺码</strong></div></td>
		    <td scope="col"><div align="center"><strong>操作</strong></div></td>
		  </tr>
			<?php if (!empty($change_product_detail)): ?>
			<?php foreach ($change_product_detail as $product): ?>
			<?php foreach ($product['product_list'] as $item): ?>
			<?php if (isset($product['product_num']) && $product['product_num'] > 0): ?>
		  <tr id="tr_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>" <?php print (isset($item['show_num']) && $item['show_num'] > 0)?'':'class="none"'; ?>>
		    <td>
		    <?php print $product['product_name']; ?></a> [ <?php print $product['brand_name']; ?> ]
		    </td>
		    <td align="center"><?php print $product['product_sn']; ?></td>
		    <td align="center"><?php print $product['provider_productcode']; ?></td>
		    <td><div align="center"><?php print $product['product_num']; ?><?php print $product['unit_name']; ?></div></td>
		    <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?></td>
		    <td align="center">
			<input type="text" name="num_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>" id="num_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>" value="<?php print isset($item['show_num']) && $item['show_num']>0?$item['show_num']:'0'; ?>" size="3" <?php if ($product['product_num'] <= 0): ?>onfocus="this.blur();return false" onblur="this.value=0"<?php else: ?>onfocus="handleOnFucus(this);" onblur="checkmax(this, '<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>', '<?php print $item['gl_num']; ?>', '<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>', '<?php print $product['product_num']; ?>')"<?php endif; ?> />
		    </td>
		    <td align="center">
		    <?php print $item['color_name']; ?>--<?php print $item['size_name']; ?>
			</td>
		    <td align="center"><input type="button"  value="删除"  class="am-btn am-btn-secondary" onclick="delchangeitem('<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>');" /></td>
		  </tr>
		  	<?php endif; ?>
		  	<?php endforeach; ?>
			<?php endforeach; ?>
			<?php endif; ?>
	    </table>
		    <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			   <tr>
			    <th colspan="9" scope="col">商品信息</th>
			   </tr>
			  <tr>
			  	<td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
			    <td scope="col"><div align="center"><strong>款号|供应商货号</strong></div></td>
			    <td scope="col"><div align="center"><strong>可换数量</strong></div></td>
			    <td scope="col"><div align="center"><strong>颜色尺码 [库存]</strong></div></td>
			    <td scope="col"><div align="center"><strong>换货颜色尺码 [库存]</strong></div></td>
			    <td scope="col"><div align="center"><strong>换货数量</strong></div></td>
			    <td scope="col"><div align="center"><strong>操作</strong></div></td>
			 </tr>
			 	<?php if (!empty($change_product_detail)): ?>
				<?php foreach ($change_product_detail as $product): ?>
				<?php if (isset($product['product_num']) && $product['product_num'] > 0): ?>
				  <tr>
				    <td>
				    <?php print $product['product_name']; ?><br /> [ <?php print $product['brand_name']; ?> ]
				    </td>
				    <td align="center"><?php print $product['product_sn']; ?><br /><?php print $product['provider_productcode']; ?></td>
				    <td align="center"><?php print $product['product_num']; ?><?php print $product['unit_name']; ?><br/><?php print $product['consign_num'] > 0?' / 虚'.$product['consign_num'].$product['unit_name']:''; ?></td>
				    <td align="center"><?php print $product['color_size_name']; ?></td>
				    <td align="center">
				    <select name="sel_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" id="sel_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>">
				     <option value="0">请选择</option>
				     <?php foreach ($product['selarr'] as $key=>$value): ?>
				     <option value="<?php print $key; ?>"><?php print $value; ?></option>
				     <?php endforeach; ?>
				     </select>
				     <select name="depot_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" id="depot_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>">
				     <?php foreach ($depot_arr as $key=>$value): ?>
				     <option value="<?php print $key; ?>"><?php print $value; ?></option>
				     <?php endforeach; ?>
				     </select>
					</td>
					<td align="center">
					<input type="text" name="sum_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" id="sum_<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" value="0" size="3" />
					</td>
				    <td align="center"><input type="button"  value="添加"  class="am-btn am-btn-secondary" onclick="addchangeitem('<?php print $product['op_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>');" /></td>
				  </tr>
		  		<?php endif; ?>
				<?php endforeach; ?>
				<?php endif; ?>
		    </table>
		  <?php endif; ?>
		</div>

		<div class="list-div" style="margin-bottom:5px;">
		<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
		  <tr>
		    <th colspan="4">换货人信息  <?php if($operable_list['edit_consignee']): ?><input type="hidden" value="1" name="priv_edit_consignee"><?php endif; ?>
		    </th>
		    </tr>
		 <tr>
		    <td><div align="right"><strong>换货原因：</strong></div></td>
		    <td colspan="3">
		    <input type="text" name="change_reason" value="<?php print $change['change_reason']; ?>" style="width:500px;" <?php print $operable_list['edit_consignee']?'':' disabled'; ?>  />
		    </td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong>换货人：</strong></div></td>
		    <td>

		    <input type="text" name="consignee" value="<?php print $change['consignee']; ?>"<?php print $operable_list['edit_consignee']?'':' disabled'; ?> />
		    </td>
		    <td><div align="right"><strong>电子邮件：</strong></div></td>
		    <td>
		    <input type="text" name="email" value="<?php print $change['email']; ?>" <?php print $operable_list['edit_consignee']?'':' disabled'; ?> />
		   </td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong>地址：</strong></div></td>
		    <td>
		        <select name="province" id="selProvinces" onChange="region.changed(this, 2, 'selCities');" <?php print $operable_list['edit_consignee']?'':' disabled'; ?>>
		            <option value="0">请选择</option>
		             <?php if (!empty($province_list)):?>
				     <?php foreach ($province_list as $province): ?>
				     <option value="<?php print $province['region_id']; ?>" <?php print ($change['province'] == $province['region_id'])?'selected':''; ?>><?php print $province['region_name']; ?></option>
				     <?php endforeach;?>
		  			 <?php endif;?>
		        </select> <select name="city" id="selCities" onchange="region.changed(this, 3, 'selDistricts')" <?php print $operable_list['edit_consignee']?'':' disabled'; ?>>
		            <option value="0">请选择</option>
		             <?php if (!empty($city_list)):?>
				     <?php foreach ($city_list as $city): ?>
				     <option value="<?php print $city['region_id']; ?>" <?php print ($change['city'] == $city['region_id'])?'selected':''; ?>><?php print $city['region_name']; ?></option>
				     <?php endforeach;?>
		  			 <?php endif;?>
		        </select>
		        <select name="district" id="selDistricts" <?php print $operable_list['edit_consignee']?'':' disabled'; ?>>
		            <option value="0">请选择</option>
		             <?php if (!empty($district_list)):?>
				     <?php foreach ($district_list as $district): ?>
				     <option value="<?php print $district['region_id']; ?>" <?php print ($change['district'] == $district['region_id'])?'selected':''; ?>><?php print $district['region_name']; ?></option>
				     <?php endforeach;?>
		  			 <?php endif;?>
		        </select>
		        <input type="text" name="address" value="<?php print $change['address']; ?>" size="50" <?php print $operable_list['edit_consignee']?'':' disabled'; ?> />
		    </td>
		    <td><div align="right"><strong>邮编：</strong></div></td>
		    <td>
		    <input type="text" name="zipcode" value="<?php print $change['zipcode']; ?>" <?php print $operable_list['edit_consignee']?'':' disabled'; ?> />
		   </td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong>电话：</strong></div></td>
		    <td>
		    <input type="text" name="tel" value="<?php print $change['tel']; ?>" <?php print $operable_list['edit_consignee']?'':' disabled'; ?> /></td>
		    <td><div align="right"><strong>手机：</strong></div></td>
		    <td>
		    <input type="text" name="mobile" value="<?php print $change['mobile']; ?>" <?php print $operable_list['edit_consignee']?'':' disabled'; ?> />
		    </td>
		  </tr>
		</table>
		</div>
		</form>

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

			<form name="suggestion_form" action="/order_change/post_suggest/<?php print $change['change_id']; ?>" method="POST" onsubmit="return check_suggestion_form()">
			<div class="list-div" style="margin-bottom: 5px;" >
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="5">意见信息</th>
			  </tr>
			  <tr>
			    <td valign="top" style="text-align:right;"><strong>签写意见</strong></td>
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
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
