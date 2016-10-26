<?php include APPPATH."views/mobile/header.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.js?v=version')?>"></script>

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/common.js?v=version')?>"></script>

<div class="views">
<div class="view view-main" data-page="index">
     <div class="pages find-page">
          <a href="/" class="find-img external"><img src="<?php echo static_url('mobile/img/404-logo.jpg')?>" /></a>
          <div class="find-con">出错了！<br/>1.页面可能已被删除<br/>2.地址错误<a href="/" class="find-home external">返回首页</a></div>
          
     </div>
	</div>

</div>






<?php include APPPATH . "views/mobile/common/footer-js.php";?>

<?php include APPPATH."views/mobile/footer.php"; ?>
