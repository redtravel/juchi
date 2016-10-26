<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/help.css'); ?>" type="text/css" />
<div id="content" style="padding-top:20px;">
	<div style="line-height:20px;">
	<h4 style="text-align:center;font-size:18px;font-weight:bold;padding-bottom:10px;"><?php print $article->title ?></h4>
	<p style="color:#666;text-align:center;padding-bottom:10px;border-bottom:1px dotted #ddd;margin-bottom:20px;"><?php print $article->create_date;?><?php if($article->author) print "&nbsp;&nbsp;&nbsp;&nbsp;作者：{$article->author}";?><?php if($article->source) print "&nbsp;&nbsp;&nbsp;&nbsp;来源：{$article->source}";?></p>
	<?php print $article->content; ?>   
	</div>
	<div class="cl"></div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>