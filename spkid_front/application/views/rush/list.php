<div id="wrap" class="m18">

<script type="text/javascript" src="<?=static_style_url("js/jquery.lazyload.min.js")?>"></script>
<div id="container">
	<div class="w_980" id="content">
 	<?php if ($ad && !empty($ad['pic_url'])) : ?>
            <div id="list_ad" class="sec_bnnr"><a href="<?php print $ad['ad_link']; ?>"><img src="<?php print img_url($ad['pic_url']); ?>" alt="" /></a></div>
        <?php endif; ?>
	<?php if ($args['nav_type'] == 3) : ?>	
                <div class="mshop_bar">
		<div class="info">
			<p id="provider_logo" class="thumb b_pink"><a href="<?php print '/provider-' .$provider['provider_id'] .'.html'; ?>"><img src="<?php print img_url($provider['logo']); ?>" alt="<?php print $provider['display_name']; ?>" width="108" height="86" /></a></p>
			<a href="<?php print '/provider-' .$provider['provider_id'] .'.html'; ?>"><h2 style="width:260px"><?php print $provider['display_name']; ?></h2></a>
			<p class="num"><em><?php print $provider['product_num']; ?></em> 个商品销售中</p>
		</div>
		
		<div class="menu" style="width: 100px;bottom: 40px;">
			<a href="<?php print '/provider-' .$provider['provider_id'] .'.html'; ?>" class="bt bt20_23 gray">
                            <span style="width: 80px;">所有商品</span>
                        </a>
		</div>
		
		<div class="shop_cp" id="sell_coupon_layer">
                    <span style="font-size:14px;">经销品牌：</span>
                    <?php foreach ($provider_brand as $brand) : ?>
                    <a href="<?php print '/brand-' .$brand['brand_id'] .'.html'; ?>" target="_blank" title="<?php print $brand['brand_name']; ?>">
                        <img src="<?php print img_url($brand['brand_logo'] .'.76x38.jpg'); ?>" alt="<?php print $brand['brand_name']; ?>" width="76px" height="38px" />
                    </a>
                    <?php endforeach; ?>
		</div>
		
	</div>
	<?php endif; ?>
<!--分类查找-->
<?php include APPPATH . 'views/category/cat.php'; ?>	
<!-- 导航开始 -->
<?php include APPPATH . 'views/category/nav.php'; ?>

<div id="detail">
	
	<div id="search_result_item_list" class="rushListNewBox">
		<?php $i = 0; foreach ($product_list as $product) : ?>
		<dl class="J_pro_items" id="J_pro_<?php print $product->product_id;?>" data-hover="dl_hover">
            <dt class="pro_list_pic">
			<a href="/product-<?php print $product->product_id;?>.html" mars_sead="te_list_list_img0" title="<?php print $product->product_name;?>" target="_blank">
				<img class="J_first_pic" width="220" height="220" fslazy="<?php print img_url($product->img_url.".220x220.jpg"); ?>" alt="<?php print $product->product_name;?>" style="display: inline;">
			</a>
			<span class="sold_tag soldout_tag" id="J_soldout_<?php print $product->product_id;?>" style="display: block;"></span>
			</dt>
			<dd class="gray"><?php print $product->brand_name;?> </dd>
            <dd class="pro_list_tit"><a href="/product-<?php print $product->product_id;?>.html" mars_sead="te_list_list_name0" target="_blank"><?php print $product->product_name;?></a></dd>
			<dd class="pro_list_data">
				<span class="deep_red"><em>¥<?php print $product->shop_price; ?></em>(<?php print round($product->shop_price/$product->market_price*10, 1)?>折)</span><del class="gray">¥<?php print $product->market_price; ?></del>
			</dd>
        </dl>
		<?php $i++; endforeach;?>
	</div>
	</div>
	</div>
	</div>	
</div>
<script type="text/javascript" src="<?php print static_style_url('js/list.js'); ?>"></script>
