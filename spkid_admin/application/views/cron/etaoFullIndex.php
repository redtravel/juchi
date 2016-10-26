<?php print '<?xml version="1.0" encoding="utf-8"?>'."\n";?>
<root>
	<version>1.0</version>
	<modified><?php print $this->time; ?></modified>
	<seller_id>爱童网官网</seller_id>
	<cat_url><?php print img_url('data/etao/SellerCats.xml'); ?></cat_url>
	<dir><?php print img_url('data/etao/item/'); ?></dir>	
	<item_ids>
		<?php foreach($ps as $p): ?>
		<outer_id action="upload"><?php print ($p->product_id+100000000000).($p->color_id+100000000000); ?></outer_id>
		<?php endforeach; ?>
	</item_ids>
</root>