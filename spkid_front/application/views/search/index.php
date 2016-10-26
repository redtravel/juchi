<?php include APPPATH . 'views/common/header.php'?>

<div class="wrap-mian wrap-min">
          
    <!--search-keywords start-->
    <div class="search-keywords">
         <p>搜索关键字：“<span><?php echo $kw;?></span>”得到 <span><?php echo count($product);?></span> 个产品，<span><?php echo count($exhibit);?></span> 个展品，<span><?php echo count($course);?></span> 个课程，<span><?php echo count($video);?></span> 个视频</p>
         <p>
          <?php if($search_hot){ foreach ($search_hot['list'] as $value) { ?>
             <a href="javascript:void(0);" class="hot-keyword"><?php echo $value['hotword_name'];?></a>
          <?php } } ?>
        </p>
    </div> 
    <!--search-keywords end-->


<!--related-products-start-->
<?php if($product){ ?>
<div class="related-products">
    <div class="related-products-title">相关产品</div>
    <div class="related-products-list">
          <div class="pro-lb">
               <ul class="clearfix">
               <?php foreach ($product as $pro_val) { ?>
               <li>
               <a href="/pdetail-<?php echo $pro_val->product_id;?>">
               <img src="<?php echo img_url($pro_val->img_url);?>">
               <h2><?php echo $pro_val->product_name;?></h2>
               <div class="related-products-js"><?php echo $pro_val->size_name;?></div>
               <div class="related-products-price"><span>￥<?php echo $pro_val->shop_price;?></span><em>￥<?php echo $pro_val->market_price;?></em></div>
               </a>
               </li>
              <?php } ?>
              </ul>
          </div>
   </div>
</div>
<?php } ?>
<!--related-products-end-->


<!--related-products-show-start-->
<?php if($exhibit){ ?>
<div class="related-products related-products-show">
    <div class="related-products-title">相关展品</div>
    <div class="related-products-list">
          <div class="pro-lb">
               <ul class="clearfix">
               <?php foreach ($exhibit as $ext_val) { ?>
               <li>
               <a href="/pdetail-<?php echo $ext_val->product_id;?>">
               <img src="<?php echo img_url($ext_val->img_url);?>">
               <h2><?php echo $ext_val->product_name;?></h2>
               <div class="related-products-js"><?php echo $ext_val->size_name;?></div>
               </a>
               </li>
               <?php } ?>
              </ul>
          </div>
    </div>
</div>
<?php } ?>
<!--related-products-start-->

<!--related-products-show-start-->
<?php if($course){ ?>
<div class="related-products correlated-curriculum">
    <div class="related-products-title">相关课程</div>
    <div class="related-products-list">
          <div class="pro-lb pro-kecheng">
               <ul class="clearfix">
               <?php foreach ($course as $cou_val) { ?>
               <li>
               <a href="/product-<?php echo $cou_val->product_id;?>">
               <img src="<?php echo img_url($cou_val->img_url);?>">
               <h2><?php echo $cou_val->product_name;?></h2>
               <div class="correlated-kc">开课: <?php echo date("Y/m/d", strtotime($cou_val->package_name));?></div>
               <div class="correlated-techer">讲师: <?php echo $cou_val->subhead;?></div>
               </a>
               </li>
               <?php } ?>
              </ul>
          </div>
    </div>
</div>
<?php } ?>
<!--related-products-start-->


<!--related-products-show-start-->
<?php if($video){ ?>
<div class="related-products related-video">
    <div class="related-video-title">相关视频</div>
    <div class="related-video">
         <div class="related-video-lb">
              <ul class=" clearfix">
              <?php foreach ($video as $vio_val) { ?>
              <li>
              <a href="/video/detail/<?php echo $vio_val->ID;?>">
              <div class="video-img"><img src="<?php echo $vio_val->img;?>"><span class="video-ico"></span></div>
              <span><?php echo $vio_val->post_title;?></span>
              </a>
              </li>
              <?php } ?>
              </ul>
         </div>
    </div>
</div>
<?php } ?>
<!--related-products-start-->

<script>
  $(function(){
    $('.hot-keyword').click(function(){
      location.href = '/search/index?kw=' + $(this).text();
    });
  });
</script>

<?php include APPPATH . 'views/common/footer.php'?>
