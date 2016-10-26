<?php print '<?xml version="1.0"?>'."\n";?>
<rss version="2.0" 
xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>爱童网Google Merchant Center Feed</title>
<link>http://52kid.cn</link>
<description>爱童网是专注于儿童品牌服饰的一流网络折扣购物平台，在爱童网(52kid.cn)购物平台上以比零售大幅优惠的折扣价，向中国消费者提供优质、受欢迎的品牌正品，商品囊括时装、护肤品、箱包、配饰等等，琳琅满目。</description>
<?php foreach($ps as $p): ?>
<item>
<g:id><?php print $p->product_sn.$p->color_sn; ?></g:id>
<title><?php print htmlspecialchars("{$p->brand_name} {$p->product_name} {$p->color_name}"); ?></title>
<description><?php print htmlspecialchars("{$p->brand_name} {$p->product_name} {$p->color_name}"); ?></description>
<g:google_product_category><?php print htmlspecialchars($p->gcat); ?></g:google_product_category>
<g:product_type><?php print htmlspecialchars("{$p->pcategory_name} > {$p->category_name}"); ?></g:product_type>
<link><?php print front_url("product-{$p->product_id}-{$p->color_id}.html"); ?></link>
<g:image_link><?php print $p->g['default']; ?></g:image_link>
<?php foreach(array_slice($p->g['part'],0,10) as $img): ?>
<g:additional_image_link><?php print $img; ?></g:additional_image_link>
<?php endforeach;?>
<g:condition>new</g:condition>
<g:availability>in stock</g:availability>
<g:price><?php print $p->shop_price; ?> CNY</g:price>
<g:brand><?php print htmlspecialchars($p->brand_name); ?></g:brand>
<g:mpn><?php print htmlspecialchars($p->provider_productcode); ?></g:mpn>
<g:gender><?php print $p->gender; ?></g:gender>
<g:age_group>kids</g:age_group>
<g:color><?php print $p->color_name; ?></g:color>
</item>
<?php endforeach;?>
</channel>
</rss>