	<div class="car_b_t f16b"><s class="cb_tsl"></s>购买过以上商品的用户还购买过<s class="cb_tsr"></s></div>
	<div class="car_b_c reco_area">
		<ul>
			<?php foreach ($list as $p): ?>        
			<li>
				<dl class="pro_block">
					<dt><a href="<?php print "product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank"><img src="<?php print img_url( $p->img_170_227) ?>" width="170" height="227" /></a></dt>
					<dd><span class="l"><?php print $p->brand_name ?></span></dd>
					<dd><a href="<?php print "product-{$p->product_id}-{$p->color_id}.html" ?>" class="c33" target="_blank"><?php print $p->product_name ?></a><!--<span class="r c99"><?php print $p->product_sn ?></span>--></dd>
					<dd><font class="pri">￥<?php print number_format($p->product_price,0,'.','') ?></font> (<?php print $p->discount_percent ?>折) <font class="y_p">￥<?php print number_format($p->market_price,0,'.','') ?></font></dd>
				</dl>
			</li>
			<?php endforeach ?>        
		</ul>      
	</div>
	<div class="car_b_b"><s class="cb_tsl"></s>&nbsp;<s class="cb_tsr"></s></div>
</div>