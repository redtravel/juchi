<?php include APPPATH . "views/common/header.php"; ?>
<!-- 店铺大全 -->
<div class="brandBox">
	<a name="热门店铺" id="shop-hot"></a>
	<div class="lineBox">
		<h3><span>热门店铺</span><!-- 外部调用锚链实例<a href="#食品·保健">女性·时尚</a> --></h3>
		<div class="pp_auto">
			<ul class="list_pic">
                <?php foreach($top_provider as $provider):?>
                <li><a href="/provider-<?php print $provider['id'];?>.html" title="<?php print $provider['name'];?>" target="_blank">
                        <img width="120px" height="96px" alt="<?php print $provider['name'];?>" src="<?php print img_url($provider['logo']);?>">
                    </a>
                        <a class="brandName" href="/provider-<?php print $provider['id'];?>.html" target="_blank"><?php print $provider['name'];?></a>
                </li>
                <?php endforeach;?>
			</ul>
		</div>
	</div>
</div>
<?php foreach($all_category as $category):?>
<div class="brandBox">
    <a name="<?php print $category['name'];?>" id="provider-<?php print $category['id'];?>"></a>
	<div class="lineBox">
		<h3><span><?php print $category['name'];?></span></h3>
		<div class="pp_auto">
			<ul class="list_pic">
                <?php foreach($category['provider'] as $provider):?>
                <li><a href="/provider-<?php print $provider['id'];?>.html" title="<?php print $provider['name']?>" target="_blank">
                        <img width="120px" height="96px" alt="<?php print $provider['name']?>" src="<?php print img_url($provider['logo']);?>">
                    </a>
                    <a class="brandName" href="/provider-<?php print $provider['id'];?>.html" target="_blank"><?php print $provider['name']?></a>
                </li>
				<?php endforeach;?>
            </ul>
		</div>
	</div>
</div>
<?php endforeach;?>

<?php include APPPATH . 'views/common/footer.php'; ?>