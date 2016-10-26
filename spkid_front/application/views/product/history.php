<div class="w200_block ablack" id="link_product" style="display:none;"></div>
<div class="side_block">
    <h3 class="f13b">您浏览过以下商品</h3>
    <ul>
	<?php
	if (!empty($history)) {
	    foreach ($history as $p) {
		?>
		<li>
		    <a href="product-<?php print $p->product_id ?>.html" target="_blank">
			<img src="<?php print img_url( $p->img_170_170 ) ?>" width="158" height="172" />
		    </a>
		    <a href="product-<?php print $p->product_id?>.html" target="_blank">
			<?php print $p->brand_name ?>
		    </a>
		    <a href="product-<?php print $p->product_id ?>.html" target="_blank">
			<?php print $p->product_name ?>
		    </a>
		    <a class="shichangjia">市场价：<font class="y_p">￥<?php print number_format($p->market_price, 0, '.', '') ?></font></a>
		    <a><font class="cred">￥<?php print number_format($p->product_price, 0, '.', '') ?></font><font class="font12">(<?php print $p->discount_percent; ?>折)</font></a>
		</li> 
		<?php
	    }
	}
	?>
    </ul>
</div>