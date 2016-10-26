<!--分页-->
<!--<? if ($pages > 1) { ?>
	<div class="pager">
		<div class="pagelist">
		<? if ($page <= 1) { ?>
		<span hidefocus="" class="prev_page">上一页</span>
		<? } else { ?>
		<a class="prev_page" href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page - 1 ?>.html" hidefocus>上一页</a>
		<? } ?>

		<? foreach ($arr_pagelist_num as $arr_list_num): ?>
		<? if ($arr_list_num == "...") { ?>
			<span hidefocus="" class="morepage">...</span>
		<? } ?>
		<? if ($arr_list_num == $page) { ?>
			<span hidefocus="" class="current"><?= $arr_list_num ?></span>
		<? } elseif ($arr_list_num != "...") { ?>
			<a href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $arr_list_num ?>.html" hidefocus><?= $arr_list_num ?></a>
		<? } ?>
		<? endforeach; ?>

		<? if ($page >= $pages) { ?>
		<span hidefocus="" class="next_page">下一页</span> 
		<? } else { ?>
		<a class="next_page" href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page + 1 ?>.html" hidefocus>下一页</a>
		<? } ?>
		</div>
	</div>
<? } ?>-->

<? if ($pages > 1) { ?>
	<div class="switch_block_page">
		<div>
		<? if ($page <= 1) { ?>
		    <a hidefocus="" class="preBtn" <?php if($page == 1 ){?>style="display:none"<? } ?>>上一页</a>
		<? } else { ?>
		<a class="preBtn" href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page - 1 ?>.html<?= isset($is_preview)&&$is_preview?"?is_preview=1":"";?>" hidefocus>上一页</a>
		<? } ?>

		<? foreach ($arr_pagelist_num as $arr_list_num): ?>
		<? if ($arr_list_num == "...") { ?>
			<a hidefocus="" class="nosel">...</a>
		<? } ?>
		<? if ($arr_list_num == $page) { ?>
			<a hidefocus="" class="sel"><?= $arr_list_num ?></a>
		<? } elseif ($arr_list_num != "...") { ?>
			<a href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $arr_list_num ?>.html<?= isset($is_preview)&&$is_preview?"?is_preview=1":"";?>" hidefocus><?= $arr_list_num ?></a>
		<? } ?>
		<? endforeach; ?>

		<? if ($page >= $pages) { ?>
		<a hidefocus="" class="nextBtn" <?php if($page == $pages ){?>style="display:none"<? } ?> >下一页</a>
		<? } else { ?>
		<a class="nextBtn" href="/rush-<?= $args['rush_id'] ?>-<?= $args['type_id'] ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>-<?= $args['sort'] ?>-<?= $page + 1 ?>.html<?= isset($is_preview)&&$is_preview?"?is_preview=1":"";?>" hidefocus>下一页</a>
		<? } ?>
		</div>
	</div>
<? } ?>