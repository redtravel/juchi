<?php foreach ($product_list as $product) : ?>
<li>
<a href="/pdetail-<?php print $product->product_id;?>.html">
   <div class="all-goods-img"><img src="<?php print img_url($product->img_url.".220x220.jpg"); ?>"></div>
   <p class="all-goods-mc"><?php print $product->product_name;?></p>
   <div class="all-goods-js"><?=filter_html_des($product->product_desc)?></div>
   <div class="all-goods-price"><span>￥<?php print $product->shop_price; ?></span><em>￥<?php print $product->market_price; ?></em></div> 
</a>
</li>
<?php endforeach; ?>