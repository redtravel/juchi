<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/help.css'); ?>" type="text/css" />
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
		&gt; 资讯中心 &gt;
		<a class="now"><?= $article->title ?></a>
	</div>
	<div class="left">
		<div class="helpCenterC">
		<?php foreach($all_cat as $cat): ?>
			<dl>
				<dt><?php print $cat->cat_name ?></dt>
						<?php
							if (empty($cat->article_list) || count($cat->article_list) > 1) {
								echo '<dd></dd>';
							}
						?>
				<?php foreach($cat->article_list as $a): ?>
								<?php if ($a->article_id == $article->article_id) { ?>
								<dd class="sel"><?php print mb_strlen($a->title) > 8 ? mb_substr($a->title, 0, 6) . "..." : $a->title ?></dd>
								<?php } else { ?>
								<dd><a href="/article-<?php print $a->article_id ?>.html" title="<?=$a->title?>"><?php print mb_strlen($a->title) > 8 ? mb_substr($a->title, 0, 6) . "..." : $a->title ?></a></dd>
								<?php } ?>
				<?php endforeach; ?>
			</dl>
		<?php endforeach; ?>
		</div>
	  <!-- <div class="left_bg_bottom"></div> -->
	  <div class="cl"></div>
	</div> 
  <div class="right">
	  <div class="right_top fc right_t"><?php print $article->title ?></div>
	  <div class="right_c">
		<?php print $article->content; ?>
	  </div>
	  <!-- <div class="right_bottom"></div> -->
	</div>
  <div class="cl"></div>
</div>
<script type="text/javascript">
     $(function(){
        if($('dd').hasClass('sel')){
            $('dd[class=sel]')[0].parentNode.children[0].style.background='#faa';
        }
    });  
 </script>
<?php include APPPATH.'views/common/footer.php'; ?>