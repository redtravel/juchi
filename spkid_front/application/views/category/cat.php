<!--分类查找-->
<div class="rush_search">
    <div class="search_items">
	<div class="search_type type_dl">
	    <div class="type_left">分类：</div>
	    <div class="type_right">
		<div class="all_types">
		    <ul class="ul_type first_ul">
			<?php
			$cat_id = isset($category->cat->id) && !empty($category->cat->id) ? $category->cat->id : 0;
			//$cat_name = isset($category->cat->name) && !empty($category->cat->name) ? $category->cat->name : '暂无';
                        if (empty($cat_id) && $args['nav_type'] == 1) {
                                $cat_id = $cat['type_id'];
                        }
			?>
			<span class="type_all">[ <a hidefocus="" href="/<?=$nav_type?>-<?=$cat_id?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-1.html">全部</a> ]</span>
			<?php
			$second_cat = 0; //$cat == $category->cat->id ? 0 : $category->cat->id
			$brand_arr = $args['type_id'] == $cat_id && isset($category->brand) ? $category->brand : 0;
			$size_arr = $args['type_id'] == $cat_id && isset($category->size) ? $category->size : 0;
			$checked_second = 0;
			if (isset($category->cat) && !empty($category->cat)) {
			    foreach ($category->cat as $key1 => $item1) {
				if ($key1 != "id" && $key1 != "name") {
				    if ($key1 == $args['type_id']) {
					$second_cat = $item1;
                                        $second_cat->id = $key1;
					if ($args['nav_type'] != 2) {
                                            $brand_arr = $item1->brand;
                                        }
				    }
				    //如果是三级，选出所属二级，以及二级下所有的三级
				    foreach ($item1 as $key2 => $item2) {
					if ($key2 != "brand" && $key2 != "size"&& $key2 != "name") {//third
                                            if (!empty($args['type_id']) && $key2 == intval($args['type_id'])) {
						$checked_second = intval($key1);
						$second_cat = $item1;
                                                $second_cat->id = $key1;
                                                if ($args['nav_type'] != 2) {
                                                    $brand_arr = $item1->brand;
                                                }
					    }
                                            if ($args['nav_type'] == 3) {
                                                $brand_arr = $item1->brand;
                                            }
					}
				    }
				    //
				    foreach ($item1 as $key2 => $item2) {
					if ($key2 == 'name') {//seconds
					    $second_checked = $args['type_id'] == intval($key1) || intval($key1) == $checked_second ? 'class="active_type"' : '';
					    ?>
		    			<li <?= $second_checked; ?> > <a hidefocus="" href="/<?=$nav_type?>-<?= $key1 ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-1.html"><?= $item2; ?></a><?php echo $args['nav_type'] != 1 && ($args['type_id'] == intval($key1) || intval($key1) == $checked_second) ? '<b></b>' : '' ;?></li>
					    <?php
					}
				    }
				}
			    }
			}
			?>
		    </ul>
		    <?php
		    if ($args['nav_type'] != 1 && !empty($second_cat)) {
			?>
    		    <div class="show_type">
                        <span class="type_all">[ <a hidefocus="" href="/<?=$nav_type?>-<?= $second_cat->id ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html">全部</a> ]</span>
    			<ul class="show_type_ul" style="border-bottom: medium none;float: none;">
				<?php
				foreach ($second_cat as $key3 => $item3) {
				    if ($key3 != "brand" && $key3 != "size"&& $key3 != "name" && $key3 != "famale" && $key3 != "id") {
					$third_checked = !empty($args['type_id']) && $args['type_id'] == $key3 ? 'class="active_type"' : '';
					?>
	    			    <li <?= $third_checked; ?>><a hidefocus="" href="/<?=$nav_type?>-<?= $key3 ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-1.html"><?= isset($item3->name)?$item3->name:""; ?></a></li>
					<?php
				    }
				}
				?>
    			</ul>
    		    </div>
			<?php
		    }
		    ?>
		</div>
	    </div>
	</div>
        
	<div class="search_brand">
	    <div class="type_left">品牌：</div>
	    <div class="type_right"> <a hidefocus="" id="more_brand" class="clicksuo" href="javascript:void(0)" style="display: none;"></a> 
                <span class="type_all"><?php if ($args['nav_type'] != 2) : ?>[ <a hidefocus="" href="/<?=$nav_type?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>--<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-2.html">全部</a> ]
                    <?php else: ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?>
                </span>
		<ul id="brand_ul" class="brand_ul" style="height: auto;">
		    <?php
		    $brand_all = array();
		    if (isset($brand_arr->male)) {
			foreach ($brand_arr->male as $key => $val) {
			    $brand_all[] = $key;
			    if (!empty($args['brand_id']) && $args['brand_id'] == $key) {
				?>
	    		    <li class="active_type" > <a hidefocus="" href="javascript:;"><?= $val ?></a> </li>
				<?php
			    } else {
				?>
	    		    <li> <a hidefocus="" href="/<?=$nav_type?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $key ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-2.html"><?= $val ?></a> </li>
				<?php
			    }
			}
		    }
		    if (isset($brand_arr->famale)) {
			foreach ($brand_arr->famale as $key => $val) {
			    if (!in_array($key, $brand_all)) {
				$brand_all[] = $key;
				if (!empty($args['brand_id']) && $args['brand_id'] == $key) {
				    ?>
				    <li class="active_type" > <a hidefocus="" href="javascript:;"><?= $val ?></a> </li>
				    <?php
				} else {
				    ?>
				    <li> <a hidefocus="" href="/<?=$nav_type?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $key ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>-2.html"><?= $val ?></a> </li>
				    <?php
				}
			    }
			}
		    }
                    if ($args['nav_type'] == 2) { ?>
                    <li class="active_type" > <a hidefocus="" href="javascript:;"><?= $brand['brand_name'] ?></a> </li>
                    <?php
                    }
		    ?>
		</ul>
		<span id="btnForMoreBrand" class="btnForMoreListNormal">更多</span>
	    </div>
	</div>
        
        <div class="search_sex">
    	    <div class="type_left">性别：</div>
    	    <div class="type_right type_right_size"> <a hidefocus="" id="more_size" class="clicksuo" href="javascript:void(0)" style="display: none;"></a> <span class="type_all">[ <a hidefocus="" href="/<?=$nav_type?>-<?= $args['type_id'] ?>--<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html">全部</a> ]</span>
    		<ul id="ul_sex" class="ul_sex" style="height: auto;">
    		    <li <?php if ($args['sex_id'] == MALE) { ?>class="active_size" <? }?> > <a hidefocus="" <?php if ($args['sex_id'] == MALE) { ?>href="javascript:;"<?php } else { ?> href="/<?=$nav_type?>-<?= $args['type_id'] ?>-<?= MALE ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html"<?php }; ?> >男</a> </li>
    		    <li <?php if ($args['sex_id'] == FAMALE) { ?>class="active_size" <? }?>> <a hidefocus="" <?php if ($args['sex_id'] == FAMALE) { ?>href="javascript:;"<?php } else { ?> href="/<?=$nav_type?>-<?= $args['type_id'] ?>-<?= FAMALE ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>--<?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html"<?php }; ?> >女</a> </li>
    		</ul>
    	    </div>
    	</div>
    </div>
</div>