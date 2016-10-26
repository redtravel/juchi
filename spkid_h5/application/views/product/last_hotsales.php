<div class="side_block">
    <h3 class="h3TitleHot">最近热卖</h3>
    <ul>
	<?php
	if (!empty($list)) {
	    foreach ($list as $p) {
            $p = (object)$p;
		?>
		<li>
		    <a href="product-<?php print $p->product_id ?>.html" target="_blank">
			<img src="<?php print isset($p->img_url) ? img_url( $p->img_url.".175x175.jpg") : ''; ?>" width="175" height="175" />
		    </a><br/>
		    <a href="product-<?php print $p->product_id ?>.html" target="_blank">
			<?php print @$p->brand_name; ?>
		    </a>
		    <a href="product-<?php print $p->product_id ?>.html" target="_blank">
			<?php print $p->product_name ?>
		    </a>
		    <a class="shichangjia">市场价：<font class="y_p">￥<?php print @number_format($p->market_price, 0, '.', '') ?></font></a>
		    <a><font class="cred">￥<?php print number_format($p->shop_price, 0, '.', '') ?></font><font class="font12">(<?php print @$p->discount_percent; ?>折)</font></a>
		</li> 
		<?php
	    }
	}
	?>
    </ul>
</div>
