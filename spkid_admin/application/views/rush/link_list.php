<table width="816" cellpadding=0 cellspacing=0 class="dataTable" rel="3">
	<tr>
		<td colspan=13 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="70">商品编号</th>
		<th>款号</th>
		<th>名称</th>
		<th>分类名称</th>
		<th>供应商货号</th>
		<th>库存</th>
		<th>市场价格</th>
		<th>本网站价格</th>
		<!--<th>成本价</th>-->
		<th>促销价格</th>
		<!--<th>描述</th>-->
		<th>排序</th>
		<th>操作</th>
	</tr>
	
	<?php foreach($link_product as $r): ?>
	<tr class="row" id="remove_tr_<?php print $r->rec_id; ?>">
	  <td><?php print $r->product_id; ?></td>
	  <td><?php print $r->product_sn; ?></td>
	  <td><?php print $r->product_name; ?></td>
	  <td><?php print $r->category_name; ?></td>
	  <td><?php print $r->provider_productcode; ?></td>
	  <td><?php print $r->sub_total; ?></td>
	  <td><?php print $r->market_price;?></td>
	  <td><?php print $r->shop_price;?></td>
	  <!--<td><?php //if($r->cooperation_id == 1){echo $r->cost_price;}elseif($r->cooperation_id == 2){echo $r->consign_price;}?></td>-->
	  <td><?php print $r->promote_price;?></td>
<!--	  <td><?php print edit_link('rush/edit_product_field', 'desc', $r->rec_id, $r->desc?$r->desc:'点击填写');?></td>-->
	  <td>
	  <?php print edit_link('rush/edit_product_field', 'sort_order', $r->rec_id, $r->sort_order);?>
	  </td>
	  <td>
      <?php if($perms['rush_product_edit'] == 1 && $check->status != 2 && $check->status != 3):?>
      <span style="cursor:pointer; color:#00F;" onclick="return remove_link(<?php print $r->rec_id;?>)">移除</span>
      <!--<span style="cursor:pointer; color:#00F;" onclick="show_upload_dialog(<?php print $r->rec_id;?>)">上传banner</span>-->
      <?php endif;?>
      <!--<?php print img_tip(PUBLIC_DATA_IMAGES, $r->image_before_url,200);?>
      <?php print img_tip(PUBLIC_DATA_IMAGES, $r->image_ing_url,200);?>-->
      </td>
  </tr>	
  <?php endforeach; ?>
    <tr>
        <td colspan=11 class="bottomTd"></td>
    </tr>
</table>