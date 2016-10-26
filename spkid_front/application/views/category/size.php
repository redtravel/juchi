<!--分类查找-->
<div class="rush_search">
    <div class="search_items">
	<div class="search_type type_dl">
	    <div class="type_left">分类：</div>
	    <div class="type_right">
		<div class="all_types">
		    <ul class="ul_type first_ul">
			<?php
				    foreach ($category["cat"] as $key2 => $item2) {
					    $second_checked = $args['type_id'] == intval($item2["type_id"]) ? 'class="active_type"' : '';
					    ?>
		    			<li <?= $second_checked; ?> > <a hidefocus="" href="/category-<?= $item2["type_id"] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--.html"><?= $item2["type_name"]; ?></a></li>
					    <?php
				    }
			    
			
			?>
		    </ul>
		    
		</div>
	    </div>
	</div>
    	<div class="search_sex">
    	    <div class="type_left">性别：</div>
    	    <div class="type_right type_right_size"> 
                
    		<ul id="ul_sex" class="ul_sex" style="height: auto;">
                    <?php if ($args['sex_id'] == MALE) { 
                        ?>
                    <li <?php if ($args['sex_id'] == MALE) { ?>class="active_size" <? }?> > <a hidefocus="" <?php if ($args['sex_id'] == MALE) { ?>href="javascript:;"<?php } else { ?> href="/category-<?= $args['type_id'] ?>-<?= MALE ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--.html"<?php }; ?> >男</a> </li>
                            <?php
                    } else {
                         ?>
                    <li <?php if ($args['sex_id'] == FAMALE) { ?>class="active_size" <? }?>> <a hidefocus="" <?php if ($args['sex_id'] == FAMALE) { ?>href="javascript:;"<?php } else { ?> href="/category-<?= $args['type_id'] ?>-<?= FAMALE ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--.html"<?php }; ?> >女</a> </li>
                            <?php
                        
                        }?>
    		    
    		    
    		</ul>
    	    </div>
    	</div>

	<div class="search_brand">
	    <div class="type_left">品牌：</div>
	    <div class="type_right"> <a hidefocus="" id="more_brand" class="clicksuo" href="javascript:void(0)" style="display: none;"></a> <span class="type_all">[ <a hidefocus="" href="/category-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>--<?= $args['size_id'] ?>--.html">全部</a> ]</span>
		<ul id="brand_ul" class="brand_ul" style="height: auto;">
		    <?php
			foreach ($category["brand"] as $key => $val) {
			    $brand_all[] = $key;
			    if (!empty($args['brand_id']) && $args['brand_id'] == $val["brand_id"]) {
				?>
	    		    <li class="active_type" > <a hidefocus="" href="javascript:;"><?= $val["brand_name"] ?></a> </li>
				<?php
			    } else {
				?>
	    		    <li> <a hidefocus="" href="/category-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $val["brand_id"] ?>-<?= $args['size_id'] ?>--.html"><?= $val["brand_name"] ?></a> </li>
				<?php
			    }
			}
		    
		    ?>
		</ul>
		<span id="btnForMoreBrand" class="btnForMoreListNormal">更多</span>
	    </div>
	</div>

	<div class="search_size">
	    <div class="type_left">尺码：</div>
	    <div class="type_right type_right_size"> <a hidefocus="" id="more_size" class="clicksuo" href="javascript:void(0)" style="display: none;"></a> 
		<ul id="ul_size" class="ul_sex" style="height: auto;">
                    <li class="active_size" > <a hidefocus="" href="javascript:;"><?= $category["size"]["size_name"] ?></a> </li>
		    
		</ul>
	    </div>
	</div>
    </div>
</div>