<!-- 导航开始 -->
<div class="rush_nav">
    <div class="rush_page">
    <a style="text-decoration: none;cursor:default;"><font color="red"><b><?=$page?></b></font> / <?=$pages?>
   	  <? if($page <= 1){ ?>
      <a href="#" class="page_left_bg" hidefocus>上一页</a>
      <? }else{ ?>
      <a href="/<?=$nav_type?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$args['brand_id']?>-<?=$args['size_id']?>-<?=$args['sort']?>-<?=$page-1?><?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html" class="page_left_bg" hidefocus>上一页</a>
      <? } ?>
      <em>|</em>
      <? if($page>=$pages){ ?>
      <a href="#" class="page_right_bg" hidefocus>下一页</a>
	  <? }else{ ?>
	  <a href="/<?=$nav_type?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$args['brand_id']?>-<?=$args['size_id']?>-<?=$args['sort']?>-<?=$page+1?><?php if($args['nav_type']==3) print '-'.$args['provider_id'] ?>.html" class="page_right_bg" hidefocus>下一页</a>
      <? } ?>
    </div>
	<div class="shping">
		<p style="float:left;">排序：</p>
        <div class="topdiv_on down">
			<a title="默认排序" href="/<?php print "{$nav_type}-{$args['type_id']}-{$args['sex_id']}-{$args['brand_id']}-{$args['size_id']}-0-{$page}" . ($args['nav_type'] == 3 ? "-{$args['provider_id']}" : ''); ?>.html" rel="nofollow" <?php print $args['sort'] == 0 ? 'class="on"' : ''; ?>>
                <span>默认</span>
                <img alt="" src="<?php print $args['sort'] == 0 ? static_style_url('image/c_down_h.png') : static_style_url('image/c_down.png'); ?>">
            </a>
        </div>
        <div class="topdiv_on down">
            <a title="最新发布" href="/<?php print "{$nav_type}-{$args['type_id']}-{$args['sex_id']}-{$args['brand_id']}-{$args['size_id']}-3-{$page}" . ($args['nav_type'] == 3 ? "-{$args['provider_id']}" : ''); ?>.html" rel="nofollow" <?php print $args['sort'] == 3 ? 'class="on"' : ''; ?>>
                <span>最新</span>
                <img alt="" src="<?php print $args['sort'] == 3 ? static_style_url('image/c_down_h.png') : static_style_url('image/c_down.png'); ?>">
            </a>
        </div>
		<?php if($args['sort']==1):?>
        <div class="topdiv_on up">
            <a title="价格从高到低" href="/<?php print "{$nav_type}-{$args['type_id']}-{$args['sex_id']}-{$args['brand_id']}-{$args['size_id']}-2-{$page}" . ($args['nav_type'] == 3 ? "-{$args['provider_id']}" : ''); ?>.html" rel="nofollow" <?php print $args['sort'] == 1 ? 'class="on"' : ''; ?>>
                <span>价格</span>
                <img alt="" src="<?php print $args['sort'] == 1 ? static_style_url('image/c_down_h.png') : static_style_url('image/c_down.png'); ?>">
            </a>
        </div>   
		<?php else:?>
		<div class="topdiv_on up">
            <a title="价格从低到高" href="/<?php print "{$nav_type}-{$args['type_id']}-{$args['sex_id']}-{$args['brand_id']}-{$args['size_id']}-".  ($args['sort'] == 2 ? '1' : '2') ."-{$page}" . ($args['nav_type'] == 3 ? "-{$args['provider_id']}" : ''); ?>.html" rel="nofollow" <?php print $args['sort'] == 2 ? 'class="on"' : ''; ?>>
                <span>价格</span>
                <img alt="" src="<?php print $args['sort'] == 2 ? static_style_url('image/c_up_h.png') : static_style_url('image/c_up.png'); ?>">
            </a>
        </div>  
		<?php endif;?>

    </div>
  </div>
<!-- 导航结束 -->
