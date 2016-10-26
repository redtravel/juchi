<?php include APPPATH . "views/common/header.php"; ?>
<!-- 品牌大全 -->
<div class="brandBox">
	<a name="热门品牌" id="热门品牌"></a>
	<div class="lineBox">
		<h3><span>热门品牌</span><!-- 外部调用锚链实例<a href="#食品·保健">女性·时尚</a> --></h3>
		<div class="pp_auto">
			<ul class="list_pic">
                <?php foreach($top_brand as $brand):?>
                <li><a href="<?php print $brand['url'];?>" title="<?php print $brand['name'];?>" target="_blank">
                        <img width="108px" height="54px" alt="<?php print $brand['name'];?>" src="<?php print img_url($brand['logo']);?>.108x54.jpg">
                    </a>
                        <a class="brandName" href="<?php print $brand['url'];?>" target="_blank"><?php print $brand['name'];?></a>
                </li>
                <?php endforeach;?>
			</ul>
		</div>
	</div>
</div>
<?php foreach($all_category as $category):?>
<div class="brandBox">
    <a name="<?php print $category['name'];?>" id="brand-<?php print $category['id'];?>"></a>
	<div class="lineBox">
		<h3><span><?php print $category['name'];?></span></h3>
		<div class="pp_auto">
			<ul class="list_pic">
                <?php foreach($category['brand'] as $brand):?>
                <li><a href="<?php print $brand['url'];?>" title="<?php print $brand['name']?>" target="_blank">
                        <img width="108px" height="54px" alt="<?php print $brand['name']?>" src="<?php print img_url($brand['logo']);?>.108x54.jpg">
                    </a>
                    <a class="brandName" href="<?php print $brand['url'];?>" target="_blank"><?php print $brand['name']?></a>
                </li>
				<?php endforeach;?>
            </ul>
		</div>
	</div>
</div>
<?php endforeach;?>

<?php include APPPATH . 'views/common/footer.php'; ?>