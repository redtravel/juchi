<?php print '<?xml version="1.0" encoding="utf-8"?>'."\n";?>
<item>
	<seller_id>爱童网官网</seller_id>
	<outer_id><?php print ($p->product_id+100000000000).($p->color_id+100000000000); ?></outer_id>
	<title><?php print htmlspecialchars($p->brand_name.' '.$p->product_name.' '.$p->color_name); ?></title>
	<type>fixed</type>
	<available>1</available>
	<price><?php print $p->shop_price; ?></price>
	<desc><?php print htmlspecialchars("{$p->brand_name} {$p->product_name}，市场价{$p->market_price}元, 爱童网仅售{$p->shop_price}元"); ?></desc>
	<brand><?php print htmlspecialchars($p->brand_name); ?></brand>
	<tags><?php print htmlspecialchars("{$p->category_name}\\{$p->pcategory_name}\\{$p->brand_name}"); ?></tags>
	<image><?php print $p->g['default']; ?></image>
	<more_images>
		<?php foreach($p->g['part'] as $img): ?>
		<img><?php print $img; ?></img>
		<?php endforeach; ?>
	</more_images>
	<scids><?php print $p->category_id; ?></scids>
	<post_fee>10.00</post_fee>
	<props>尺码:<?php print implode(',',$p->ss); ?>;</props>
	<showcase><?php print $p->is_promote?'true':'false'; ?></showcase>
	<href><?php print front_url("product-{$p->product_id}-{$p->color_id}.html"); ?></href>
</item>
