<?php print '<?xml version="1.0" encoding="utf-8"?>'."\n";?>
<root>
	<version>1.0</version>
	<modified><?php print $this->time; ?></modified>
	<seller_id>爱童网官网</seller_id>
	<seller_cats>
		<?php foreach($category_tree as $pcat): ?>
		<cat>
			<scid><?php print $pcat->category_id; ?></scid>
			<name><?php print $pcat->category_name; ?></name>
			<cats>
				<?php foreach($pcat->sub_items as $cat): ?>
				<cat>
					<scid><?php print $cat->category_id; ?></scid>
					<name><?php print $cat->category_name; ?></name>
				</cat>
				<?php endforeach; ?>
			</cats>	
		</cat>
		<?php endforeach; ?>
	</seller_cats>
</root>