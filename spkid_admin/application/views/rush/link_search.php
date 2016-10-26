<table width="816" cellpadding=0 cellspacing=0 class="dataTable" rel="3">
	<tr>
		<td colspan=13 class="topTd"></td>
	</tr>
	<tr class="row">
		<th width="70">
		  <label><input type="checkbox"  name="ck_check_all" onclick="check_all();" />商品编号</label>
		</th>
		<th width="61">款号</th>
		<th width="78">名称</th>
		<th width="75">分类名称</th>
		<th width="44">图片</th>
		<th width="70">货号</th>
		<th width="34">库存</th>
		<th width="62">市场价格</th>
		<th width="76">本网站价格</th>
		<!--<th width="50">成本价</th>-->
		<th width="194">促销价格</th>
	</tr>
	
	<?php foreach($list as $r): ?>
	<tr class="row">
	  <td>
	    <input type="checkbox" flg="sel_product" name="sel_product_checkbox[]" value="<?php print $r->product_id; ?>" />
        <input type="hidden" name="category_id_<?php print $r->product_id; ?>" value="<?php print $r->category_id; ?>" />
      <?php print $r->product_id; ?></td>
	  <td><?php print $r->product_sn; ?></td>
	  <td><?php print $r->product_name; ?></td>
	  <td><?php print $r->category_name; ?></td>
	  <td>
	  <?php if(isset($r->gallery->img_40_40)):?>
      <img src="<?php echo base_url();?>public/data/images/<?php echo $r->gallery->img_40_40?>" />
	  <?php endif;?>
      </td>
	  <td><?php print $r->provider_productcode; ?></td>
	  <td><?php print $r->sub_total; ?></td>
	  <td><?php print $r->market_price;?></td>
	  <td><?php print $r->shop_price;?></td>
	  <!--<td></td>-->
	  <td>
	      <input type="text" value="<?php echo empty($r->percent_price) ? '' : $r->percent_price;?>" name="promote_price_<?php print $r->product_id; ?>" id="promote_price_<?php print $r->product_id; ?>" />
	  </td>
  </tr>	
  <?php endforeach; ?>

	<tr class="row">
		<td colspan="11">
         <div class="blank5">
       </div>
       <input type="button"  onclick="return add_rush_product();"  name="am-btn am-btn-secondary" id="am-btn am-btn-secondary" value="添加所选商品为限时抢购" />
       
       
       <div class="blank5">
       </div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
        <div class="blank5"></div>
        </td>
	</tr>
	<tr>
		<td colspan=13 class="bottomTd"></td>
	</tr>
</table>
