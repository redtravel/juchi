<!--商品信息开始-->
<form name="productForm" action="/order_change/post_add" method="post" onsubmit="return check_add()">
<input type="hidden" name="order_id" value="<?php print $change['order_id']; ?>" />
<table class="order_table" width="100%" cellpadding="0" cellspacing="0">
   <tr class="row">
   <th colspan="8">订单商品编辑区 </th>
   </tr>
  <tr class="row">
    <td align="center">商品名称 [ 品牌 ]</td>
    <td align="center">商品款号</td>
    <td align="center">供应商货号</td>
    <td align="center">可换数量</td>
    <td align="center">颜色尺码</td>
    <td align="center">换货数量</td>
    <td>换货颜色尺码</td>
    <td>操作</th>
  </tr>
  <?php if (!empty($change_product)):?>
  <?php foreach ($change_product as $product): ?>
  <?php foreach ($product['product_list'] as $item): ?>
  <tr id="tr_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>" class="none">
    <td align="center">
    <?php print $product['product_name']; ?> <?php print isset($product['brand_name'])?'[ '.$product['brand_name'].' ]':''; ?>
    </td>
    <td align="center"><?php print $product['product_sn']; ?></td>
    <td align="center"><?php print $product['provider_productcode']; ?></td>
    <td><div align="center"><?php print $product['max_num']; ?><?php print $product['unit_name']; ?></div></td>
    <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?></td>
    <td align="center">
	<input type="text" name="num_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>" id="num_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>"  value="<?php print isset($item['show_number']) && $item['show_number']>0?$item['show_number']:0; ?>" size="3" <?php if ($product['max_num'] <= 0 || ($item['gl_num'] <= 0 && $item['gl_consign_num'] <= 0 && $item['gl_consign_num'] != -2) ): ?>onfocus="this.blur();return false" onblur="this.value=0"<?php else: ?>onfocus="handleOnFucus(this);" onblur="checkmax(this, '<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>', '<?php print $item['gl_num']; ?>', '<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>', '<?php print $product['max_num']; ?>')"<?php endif; ?> />
    </td>
    <td align="center">
    <?php print $item['color_name']; ?>--<?php print $item['size_name']; ?>
	</td>
    <td align="center"><input type="button"  value="删除"  class="am-btn am-btn-secondary" onclick="delchangeitem('<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>_<?php print $item['color_id']; ?>_<?php print $item['size_id']; ?>');" /></td>
  </tr>
  <?php endforeach;?>
  <?php endforeach;?>
  <?php endif;?>
  </table>
    <table class="order_table" width="100%" cellpadding="0" cellspacing="0">
	   <tr>
	   <th colspan="8">订单商品信息</th>
       </tr>
	  <tr>
	  	<td align="center">商品名称 [ 品牌 ]</td>
	    <td align="center">商品款号|货号</td>
	    <td align="center">价格</th>
	    <td align="center">可换数量</th>
	    <td align="center">颜色尺码</td>
	    <td align="center">换货颜色尺码</td>
	    <td align="center">换货数量</td>
	    <td align="center">操作</td>
	 </tr>
	 	<?php if (!empty($change_product)):?>
  		<?php foreach ($change_product as $product): ?>
		  <tr>
		    <td align="center">
		    <?php print $product['product_name']; ?> <?php print isset($product['brand_name'])?'[ '.$product['brand_name'].' ]':''; ?>
		    </td>
		    <td align="center"><?php print $product['product_sn']; ?><br /><?php print $product['provider_productcode']; ?></td>
		    <td align="center"><?php print $product['formated_product_price']; ?> <br />F：<?php print $product['shop_price']; ?></td>
		    <td align="center"><?php print $product['max_num']; ?><?php print $product['unit_name']; ?><br/><?php if ($product['max_consign_num'] > 0): ?>虚<?php print $product['max_consign_num']; ?><?php print $product['unit_name']; ?><?php endif; ?></td>
		    <td align="center"><?php print $product['color_size_name']; ?></td>
		    <td align="center">
		    <select name="sel_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" id="sel_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>">
		     <option value="0">请选择</option>
		     <?php if (!empty($product['selarr'])):?>
		     <?php foreach ($product['selarr'] as $key=>$value): ?>
		     <option value="<?php print $key; ?>"><?php print $value; ?></option>
		     <?php endforeach;?>
  			 <?php endif;?>
		     </select>
			</td>
			<td align="center">
			<input type="text" name="sum_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" id="sum_<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>" value="0" size="3" />
			</td>
		    <td align="center"><input type="button"  value="添加"  class="am-btn am-btn-secondary" onclick="addchangeitem('<?php print $product['rec_id']; ?>_<?php print $product['product_id']; ?>_<?php print $product['color_id']; ?>_<?php print $product['size_id']; ?>_<?php print $product['track_id']; ?>');" /></td>
		  </tr>
  		<?php endforeach;?>
  		<?php endif;?>
    </table>

<table class="order_table" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th colspan="4">换货人信息
    </th>
    </tr>
  <tr>
    <td><div align="right"><strong>换货人：</strong></div></td>
    <td>

    <input type="text" name="consignee" value="<?php print $change['consignee']; ?>"  />
    </td>
    <td><div align="right"><strong>电子邮件：</strong></div></td>
    <td>
    <input type="text" name="email" value="<?php print $change['email']; ?>"  />
   </td>
  </tr>
  <tr>
    <td><div align="right"><strong>地址：</strong></div></td>
    <td>
        <select name="province" id="selProvinces" onChange="region.changed(this, 'selCities');">
            <option value="0">请选择</option>
             <?php if (!empty($province_list)):?>
		     <?php foreach ($province_list as $province): ?>
		     <option value="<?php print $province['region_id']; ?>" <?php print ($change['province'] == $province['region_id'])?'selected':''; ?>><?php print $province['region_name']; ?></option>
		     <?php endforeach;?>
  			 <?php endif;?>
        </select> <select name="city" id="selCities" onchange="region.changed(this, 3, 'selDistricts')">
            <option value="0">请选择</option>
             <?php if (!empty($city_list)):?>
		     <?php foreach ($city_list as $city): ?>
		     <option value="<?php print $city['region_id']; ?>" <?php print ($change['city'] == $city['region_id'])?'selected':''; ?>><?php print $city['region_name']; ?></option>
		     <?php endforeach;?>
  			 <?php endif;?>
        </select>
        <select name="district" id="selDistricts">
        	<option value="0">请选择</option>
             <?php if (!empty($district_list)):?>
		     <?php foreach ($district_list as $district): ?>
		     <option value="<?php print $district['region_id']; ?>" <?php print ($change['district'] == $district['region_id'])?'selected':''; ?>><?php print $district['region_name']; ?></option>
		     <?php endforeach;?>
  			 <?php endif;?>

        </select>
        <input type="text" name="address" value="<?php print $change['address']; ?>" size="40"  />
    </td>
    <td><div align="right"><strong>邮编：</strong></div></td>
    <td>
    <input type="text" name="zipcode" value="<?php print $change['zipcode']; ?>"  />
   </td>
  </tr>
  <tr>
    <td><div align="right"><strong>电话：</strong></div></td>
    <td>
    <input type="text" name="tel" value="<?php print $change['tel']; ?>" /></td>
    <td><div align="right"><strong>手机：</strong></div></td>
    <td>
    <input type="text" name="mobile" value="<?php print $change['mobile']; ?>" />
    </td>
  </tr>
 <tr>
		<td align="right"><strong>换货原因</strong></td>
		<td colspan="3">
		<select name="change_reason" id="change_reason">
                <option value="次品-具体问题">次品-具体问题</option>
                <option value="尺码不合">尺码不合</option>
                <option value="发错货">发错货</option>
                <option value="发错尺码">发错尺码</option>
                <option value="顾客原因">顾客原因</option>
                </select>
		</td>
	</tr>
	<tr>
	<td colspan="4" align="center"><input type="submit" name="post_add" value="提交申请" class="am-btn am-btn-primary"  /></td>
	</tr>
</table>

</div>
</form>
