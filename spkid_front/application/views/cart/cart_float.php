<?php foreach ($cart_list as $cart): ?>
<div class="car_p_block">
	<span class="cpb_img"><img src="<?php print static_style_url('data/gallery/'.$cart->img_30_40);?>" width="30" height="40" /></span>
	<span class="cpb_txt"><?php print $cart->product_name ?><br />[<?php print $cart->color_name ?>] [<?php print $cart->size_name ?>]</span>
	<span class="cpb_r cf60"><?php print $cart->product_price; ?> x <?php print $cart->product_num; ?></span>
</div>
<?php endforeach ?>
<?php if ($cart_list): ?>  
<div class="car_pro_bpri">共&nbsp;<span class="fred"><?php print $cart_summary['product_num']; ?></span>&nbsp;件商品&nbsp;&nbsp;总计：<span class="fred">￥<?php print number_format($cart_summary['product_price'],2,'.','')?>元</span></div>
<div class="car_js">
	<a href="cart"><img src="<?php print static_style_url('img/common/car_jz.png'); ?>" width="60" height="25" /></a>
</div>
<?php else: ?>
<div class="car_pro_none">您的购物车没有商品，请选购！</div>
<?php endif ?>