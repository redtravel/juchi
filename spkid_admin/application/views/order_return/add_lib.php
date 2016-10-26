<!--商品信息开始-->
<form name="goodsForm" action="/order_return/post_add" method="post" onsubmit="return check_add()">
<input type="hidden" name="order_id" value="<?php print $return['order_id']; ?>" />
<input type="hidden" name="apply_id" value="<?php print $apply_id; ?>"/>
<table width="100%" cellpadding=0 cellspacing=0 class="order_table">
  <tr class="row"><th colspan="8" >退货商品信息 <?php print isset($product_alert)?$product_alert:''; ?> </th></tr>
  <tr class="row">
    <td width="180px">商品名称 [ 品牌 ]</th>
    <td>商品款号</td>
    <td>供应商货号</td>
    <td>价格</td>
    <td>可退数量</td>
    <td>退货数量</td>
    <td>颜色尺码[库存]</td>
	<td><strong>操作</strong></td>
  </tr>
  <?php foreach($return_product as $product): ?>
  <tr class="tr_goods">
    <td>
    <a id="recid_<?php print $product['op_id']; ?>" href="#" target="_blank" onmouseout="hide_goods_img(<?php print $product['op_id']; ?>);" onmouseover="show_goods_img(<?php print $product['op_id']; ?>);"><?php print $product['product_name']; ?></a> [ <?php print $product['brand_name']; ?> ]
    <div id="img_rec_<?php print $product['op_id']; ?>" style="display: none; background-color: #f0f0f0; border:1px #cccccc solid;width:222px;">
        <span style="float: right; padding:3px; color:#000000; font-weight: bold;font-size:14px; cursor: pointer;" onclick="hide_goods_img(<?php print $product['op_id']; ?>);">关闭</span><br>
    <img src="<?php print "public/data/images/".$product['img_url'].".220x220.jpg"; ?>"/>
    </div>
   <input type="hidden" name="track_id[]" value="<?php print $product['track_id']; ?>" />
    </td>
    <td><?php print $product['product_sn']; ?></td>
    <td><?php print $product['provider_productcode']; ?></td>
    <td><div align="center"><?php print $product['product_price']; ?> &nbsp;/<span style="color:#666; "> 本店价：<?php print $product['shop_price']; ?></span></div></td>
    <td><?php print $product['product_num']; ?><?php print $product['unit_name']; ?><br/><?php print ($product['consign_num'] > 0 )?'虚'.$product['consign_num']:''; ?></td>
    <td>
        <input type="hidden" name="op_id[]" value="<?php print $product['op_id']; ?>" />
        <input type="hidden" name="product_id[]" value="<?php print $product['product_id']; ?>" />
        <input type="hidden" name="color_id[]" value="<?php print $product['color_id']; ?>" />
        <input type="hidden" name="size_id[]" value="<?php print $product['size_id']; ?>" />
		<input type="hidden" name="sku[]" value="<?php print $product['product_sn'].' '.$product['color_sn'].' '.$product['size_sn']; ?>" />
		<input type="hidden" name="goods_barcode[]" value="<?php print $product['provider_barcode']; ?>" />
        <input type="hidden" name="max_return_number[]" value="<?php print $product['product_num']; ?>" />
        <input type="text" name="product_num[]" value="0" size="3"/>
    </td>
    <td><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?> [ <?php print $product['real_num']; ?>+<?php if ($product['consign_num'] == -1): ?>0<?php elseif ($product['consign_num'] == -2): ?>+<?php else: print $product['consign_num'];endif; ?> ]</td>:
    <td>
   <a href="order_return/print_barcode/<?php print urlencode($product['provider_barcode']); ?>/<?php print urlencode($product['product_name']); ?>/<?php print urlencode($product['color_name']); ?>/<?php print urlencode($product['size_name']); ?>/<?php print urlencode($product['provider_productcode']); ?>" target="_blank">打印条码</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<!--商品信息结束-->
<!-- 显示订单意见 -->
<?php if ($order_advice): ?>
    <table width="100%" cellpadding=0 cellspacing=0 class="order_table">
        <tr class="row"><th colspan="8" >原订单用户意见</th></tr>
        <tr class="row">
            <td >时间</td>
            <td >类型</td>
            <td >内容</td>
            <td >操作人</td>
        </tr>
        <?php foreach($order_advice as $advice): ?>
        <tr class="row">
            <td><?php print $advice->advice_date; ?></td>
            <td><span style="display:inline-block;width:15px;height:15px;background-color:<?php print $advice->type_color ?>;">&nbsp;</span>
            <?php print $advice->type_name; ?></td>
            <td><?php print $advice->advice_content; ?></td>
            <td><?php print $advice->admin_name; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<!--退货人信息开始-->
<table width="100%" cellpadding="0" cellspacing="0" class="order_table">
  <tr>
    <th colspan="4">退货人信息
    </th>
    </tr>
  <tr>
    <td><div align="right"><strong>退货人：</strong></div></td>
    <td>

    <input type="text" name="consignee" value="<?php print $return['consignee']; ?>"  />
    </td>
    <td><div align="right"><strong>电子邮件：</strong></div></td>
    <td>
    <input type="text" name="email" value="<?php print $return['email']; ?>"  />
   </td>
  </tr>
  <tr>
    <td><div align="right"><strong>地址：</strong></div></td>
    <td><input type="text" name="address" value="<?php print $return['country_name']; ?> <?php print $return['province_name']; ?> <?php print $return['city_name']; ?> <?php print $return['district_name']; ?> <?php print $return['address']; ?>" size="50"  />
    </td>
    <td><div align="right"><strong>邮编：</strong></div></td>
    <td>
    <input type="text" name="zipcode" value="<?php print $return['zipcode']; ?>"  />
   </td>
  </tr>
  <tr>
    <td><div align="right"><strong>电话：</strong></div></td>
    <td>
    <input type="text" name="tel" value="<?php print $return['tel']; ?>" /></td>
    <td><div align="right"><strong>手机：</strong></div></td>
    <td>
    <input type="text" name="mobile" value="<?php print $return['mobile']; ?>" />
    </td>
  </tr>
 <tr>
		<td align="right"><strong>退货原因</strong></td>
                <td colspan="3">
                <select name="return_reason" id="return_reason">
                <option value="顾客原因">顾客原因</option>
                <option value="运营原因">运营原因</option>
                <option value="仓库原因">仓库原因</option>
                <option value="快递原因">快递原因</option>
                <option value="其他原因">其他原因</option>
                </select>
                </td>
	</tr>
	<tr>
		<td align="right"><strong>预期到货时间</strong></td>
		<td colspan="3"><input name="hope_time" value="<?php print $return['hope_time']; ?>" style="width:80px;" /></td>
	</tr>
	<tr>
		<td align="right"><strong>对于涉及现金券的订单：</strong></td>
		<td colspan="3">
                <input type="radio" name="voucher_back" value="1" checked />拆分现金券
                <input type="radio" name="voucher_back" value="2"> 返还现金券
                <input type="button" name="bt_pre_calc_voucher" value="预览现金券返还详情" onclick="return pre_calc_voucher();" />
                </td>
	</tr>
		<?php if ($return['shipping_fee'] > 0): ?>
	<tr>
		<td align="right"><strong>是否返还原订单运费：</strong></td>
		<td colspan="3">
                <input type="radio" name="return_shipping_fee" value="0" checked > 不返还
                <input type="radio" name="return_shipping_fee" value="1"  />返还
                  &nbsp;&nbsp;<font color="#666">[ 注：只有原订单的最后一笔退单才可以返还运费 ] 原订单运费：<?php print $return['formated_shipping_fee']; ?></font>
                </td>
	</tr>
        <?php endif; ?>
        <!-- 添加退货时选择是否退还用户运费 -->
            <tr>
                    <td align="right"><strong>是否返还用户退货运费：</strong></td>
                    <td colspan="3">
                <input type="checkbox" onclick="changeCheckbox(this,'return_user_shipping_fee');"/>返还
                <input type="text" name="return_user_shipping_fee" onBlur="inputNumber(this);" size="3" readonly/>
                <select name ="shipping_name" onchange="select_shipping(this);">
                    <option>顺丰速递</option>
                    <option>申通速递</option>
                    <option>圆通速递</option>
                    <option>中通快递</option>
                    <option>EMS</option>
                    <?php foreach($shipping_name_list as $shipping_name): ?>
                        <option value="<?php echo $shipping_name->shipping_name?>" ><?php echo $shipping_name->shipping_name?></option>
                    <?php endforeach; ?>
                    <option>其他</option>
                </select>
            </td>
            </tr>
        <!-- 添加退货时选择是否退还用户运费 -->
	<tr>
	<td height="40" align="center">&nbsp;</td>
	<td colspan="3" align="left"><input type="submit" name="post_add" value="提交申请" class="am-btn am-btn-primary"  /></td>
	</tr>

</table>
</form>

<!-- 申请单商品信息 -->
<?php if ($return['apply_id'] && $apply_product): ?>
<table width="100%" cellpadding="3" cellspacing="1" class="order_table">
  <tr>
	<th colspan="9" scope="col">申请单商品信息</th>
  </tr>
  <tr>
	<td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
        <td scope="col"><div align="center"><strong>商品款号</strong></div></td>
        <td scope="col"><div align="center"><strong>供应商货号</strong></div></td>
        <td scope="col"><div align="center"><strong>价格</strong></div></td>
        <td scope="col"><div align="center"><strong>可退数量</strong></div></td>
        <td scope="col"><div align="center"><strong>申请退货数量</strong></div></td>
        <td scope="col"><div align="center"><strong>颜色尺码</strong></div></td>
	<td scope="col"><div align="center"><strong>退货原因</strong></div></td>
	<td scope="col"><div align="center"><strong>退货描述</strong></div></td>
  </tr>
  <?php foreach($apply_product as $product): ?>
  <tr>
	<td><?php print $product['product_name']; ?> [ <?php print $product['brand_name']; ?> ]</td>
        <td align="center"><?php print $product['product_sn']; ?></td>
        <td align="center"><?php print $product['provider_productcode']; ?></td>
        <td align="center"><?php print $product['product_price']; ?> /F：<?php print $product['shop_price']; ?></td>
        <td align="center"><?php print $product['n_product_num']; ?><?php print $product['unit_name']; ?></td>
        <td align="center"><?php print $product['product_number']; ?><?php print $product['unit_name']; ?></td>
        <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?></td>
        <td align="center"><?php print $product['reason']; ?></td>
        <td align="center"><?php print $product['description']; ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<!-- 申请单意见 -->
<?php if ($return['apply_id'] && $apply_suggest): ?>
<table width="100%" cellpadding="3" cellspacing="1" class="order_table">
  <tr>
	<th colspan="4" scope="col">申请单意见内容</th>
  </tr>
  <tr>
    <td scope="col"><div align="center"><strong>意见类型</strong></div></td>
    <td scope="col"><div align="center"><strong>创建人</strong></div></td>
    <td scope="col"><div align="center"><strong>意见内容</strong></div></td>
    <td scope="col"><div align="center"><strong>创建时间</strong></div></td>
  </tr>
  <?php foreach($apply_suggest as $suggest): ?>
  <tr>
    <td><div align="center"><?php print $suggest['suggest_type_name']; ?></div></td>
    <td><div align="center"><?php print $suggest['user_name']; ?></div></td>
    <td><div align="center"><?php print $suggest['suggest_content']; ?></div></td>
    <td><div align="center"><?php print $suggest['create_date']; ?></div></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
