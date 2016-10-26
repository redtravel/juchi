<?php foreach($product_list as $product):?>
                         <li>
                         <a href="/pdetail-<?php echo $product->product_id?>.html">
                         <div class="all-goods-img"><img src="<?php echo img_url($product->img_url)?>"></div>
                         <p class="all-goods-mc"><?php echo $product->product_name?></p>
                         <div class="all-goods-js"><?php echo $product->size_name?></div>
<?php if($product->price_show):?>
<div class="all-goods-price"><span>&yen;<?php echo $product->shop_price?></span><em>&yen;<?php echo $product->market_price?></em></div> 
<?php else:?>
                         <div class="all-goods-price"><input name="" type="button" class="show-xunjia" value="询价"></div>
<?php endif;?> 
                         </a>
                         </li>
<?php endforeach?>                                                
