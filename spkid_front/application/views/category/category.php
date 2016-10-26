<?php include APPPATH."views/common/header.php"; ?>
<title><?php echo $page_title ?></title>
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/common_new.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/basic.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/board.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript">
$(function(){
		$("img[fslazy]").lazyload({
			threshold: 200,
			effect: "fadeIn"
		})
	});
</script>
<div id="content">
    <div class="plistMain">
	<!--产品列表-->
	<?php include APPPATH . 'views/rush/list.php'; ?>
    </div>
    <!--分页-->
    <?php include APPPATH . 'views/category/page.php'; ?>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
