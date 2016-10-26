<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/plist.css'); ?>" type="text/css" />

<div id="content">
<div class="bread_line ablack">您现在的位置： <a href="index.html">首页</a> &gt; <a href="brands.html">品牌故事</a> &gt; <?php print $brand->brand_name; ?></div>
  <div class="brand_story">
    <?php print adjust_path($brand->brand_story); ?>
  </div>
  <div class="cl"></div>  
</div>
<?php include APPPATH.'views/common/footer.php'; ?>