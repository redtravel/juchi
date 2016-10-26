<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/plist.css'); ?>" type="text/css" />

<div id="content">
	<div class="bread_line ablack">您现在的位置： <a href="index.html">首页</a> &gt; <?php print $article->title; ?></div>
	<div class="brand_story">
    	<?php print $article->content; ?>
	</div>
	<div class="cl"></div>  
</div>
<?php include APPPATH.'views/common/footer.php'; ?>