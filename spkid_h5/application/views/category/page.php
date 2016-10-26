<!-- fclub 分页 
<? if ($pages > 1) { ?>
    <div class="pager">
        <div class="pagelist">
	    <? if ($page <= 1) { ?>
		<span hidefocus="" class="prev_page">上一页</span>
	    <? } else { ?>
		<a class="prev_page" href="/category-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page - 1 ?>.html" hidefocus>上一页</a>
	    <? } ?>

	    <? foreach ($arr_pagelist_num as $arr_list_num): ?>
		<? if ($arr_list_num == "...") { ?>
	    	<span hidefocus="" class="morepage">...</span>
		<? } ?>
		<? if ($arr_list_num == $page) { ?>
	    	<span hidefocus="" class="current"><?= $arr_list_num ?></span>
		<? } elseif ($arr_list_num != "...") { ?>
	    	<a href="/category-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $arr_list_num ?>.html" hidefocus><?= $arr_list_num ?></a>
		<? } ?>
	    <? endforeach; ?>

	    <? if ($page >= $pages) { ?>
		<span hidefocus="" class="next_page">下一页</span> 
	    <? } else { ?>
		<a class="next_page" href="/category-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page + 1 ?>.html" hidefocus>下一页</a>
	    <? } ?>
        </div>
    </div>
<? } ?>
-->

<!--baobeigou 分页-->
<? if ($pages > 1) { ?>
	<div class="switch_block_page">
		<div>
		<? if ($page <= 1) { ?>
		    <a hidefocus="" class="preBtn" <?php if($page == 1 ){?>style="display:none"<? } ?>>上一页</a>
		<? } else { ?>
		<a class="prev_page" href="/<?php print $nav_type; ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page - 1 ?><?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html" hidefocus>上一页</a>
		<? } ?>

		<? foreach ($arr_pagelist_num as $arr_list_num): ?>
		<? if ($arr_list_num == "...") { ?>
			<a hidefocus="" class="nosel">...</a>
		<? } ?>
		<? if ($arr_list_num == $page) { ?>
			<a hidefocus="" class="sel"><?= $arr_list_num ?></a>
		<? } elseif ($arr_list_num != "...") { ?>
			<a href="/<?php print $nav_type; ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $arr_list_num ?><?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html" hidefocus><?= $arr_list_num ?></a>
		<? } ?>
		<? endforeach; ?>

		<? if ($page >= $pages) { ?>
		<a hidefocus="" class="nextBtn" <?php if($page == $pages ){?>style="display:none"<? } ?> >下一页</a>
		<? } else { ?>
		<a class="next_page" href="/<?php print $nav_type; ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['brand_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page + 1 ?><?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html" hidefocus>下一页</a>
		<? } ?>
		</div>
	</div>
<? } ?>


