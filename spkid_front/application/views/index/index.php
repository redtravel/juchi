<?php function mark_product($p = NULL) {
  if (!$p) {
    echo '';    
  }
  $star = '<div class="recommend">';
  $end = '</div>';
  $mid = '';
  if ($p->is_hot ){
    $mid = '热品';
    echo $star . $mid . $end;
  } elseif($p->is_new) {
    $mid = '热品';
    echo $star . $mid . $end;
  } elseif($p->is_zhanpin) {
    $mid = '展品';
    echo $star . $mid . $end;
  } elseif($p->is_offcode) {
    $mid = '促销';
    echo $star . $mid . $end;
  }

  echo '';
}
?>
<?php include APPPATH . 'views/common/header.php'?>
<script src="<?php echo static_style_url('pc/js/jquery-latest.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/slides.min.jquery.js?v=version')?>"></script>
<script type="text/javascript">
		$(function(){
			$('#slides').slides({
				effect: 'slide',
				play:3000
			});
			
			$('#slides2').slides({
				effect: 'slide2',
				play:3000
			});
		})
</script>

<script src="<?php echo static_style_url('pc/js/oka_slider_model.js?v=version')?>"></script>
<script type="text/javascript">
			$(function(i){
				$('.banner').oka_slider_model({
					'type': 0
				});
				$('.demo-2').oka_slider_model({ 
					'type': 1
				});
				$('.demo-3').oka_slider_model({ 
					'type': 2
				});
				$('.demo-4').oka_slider_model({ 
					'type': 3
				});
				$('.demo-5').oka_slider_model({ 
					'type': 4
				});
				$('.demo-6').oka_slider_model({ 
					'type': 5
				});
				$('.demo-7').oka_slider_model({ 
					'type': 6
				});
				$('.demo-8').oka_slider_model({ 
					'type': 7
				});
				$('.demo-9').oka_slider_model({ 
					'type': 8
				});
				$('.demo-10').oka_slider_model({ 
					'type': 9
				});
			});
</script>


<div class="wrap-mian">
    <div class="home-container">
        <div class="container">
            <div class="slider_model banner">
                <div class="slider_model_box">
                           <?php foreach($pc_top_carousel as $v):?>
                           <a href="<?php echo $v['href']?>" title="<?php echo $v['title']?>" tppabs="#" target="_blank"><img src="<?php echo img_url($v['img_src']);?>"></a>
                           <?php endforeach;?>
                     </div>
            </div>
        </div>
          
 <!--yy-lb start-->
    <div class="yy-lb">
        <ul class="f-regular-list clearfix">
        <li class="copyright-bz"><p>严格准入标准</p></li>
        <li class="copyright-tuihuo"><p>7天无理由退货</p></li>
        <li class="copyright-mianfei"><p>15天免费换货</p></li>
        <li class="copyright-pinpai"><p>品牌授权</p></li>
        <li class="copyright-rongyu"><p>权威荣誉</p></li>
        </ul>
    </div>
<!--yy-lb end-->
       
<!--slides start-->
<div id="rexiaodanpin">
	<div id="slides" class="home-rank-outer">
        <div class="home-rank-title"><span>热销单品</span></div>
	    <ul class="slides_container">
		    <li>
		    	<?php foreach($pc_hot_sale_product['first']['items'] as $k => $v):?>   
                <a href="/pdetail-<?php echo $v[0]->product_id;?>" target="_blank">
	                <div class="home-rank-img-wp"><img src="<?php echo img_url($v[0]->img_url);?>"></div>
	                <div class="home-rank-pro-title"><?php echo $v[0]->product_name;?></div>
	                <div class="home-rank-price">演示站优享：<span>￥<?php echo $v[0]->shop_price;?></span><em><?php echo $v[0]->market_price;?></em></div>
	           	</a>
                <?php endforeach;?>
          	</li>
          
          	<li>
	            <?php foreach($pc_hot_sale_product['second']['items'] as $k => $v):?>   
                <a href="/pdetail-<?php echo $v[0]->product_id;?>" target="_blank">
	                <div class="home-rank-img-wp"><img src="<?php echo img_url($v[0]->img_url);?>"></div>
	                <div class="home-rank-pro-title"><?php echo $v[0]->product_name;?></div>
	                <div class="home-rank-price">演示站优享：<span>￥<?php echo $v[0]->shop_price;?></span><em><?php echo $v[0]->market_price;?></em></div>
	           	</a>
                <?php endforeach;?>
          	</li>
      	</ul>
	</div>
</div>
<!--slides end-->
     
<!--oral-care-start-->
<div class="oral-care clearfix" id="kouqiangxiufu">
    <div class="oral-care-tit"><i class="color-mark blue"></i><div class="floor-name">口腔修复</div></div>
    <div class="yueya-pro-lb">               
        <div class="oral-care-left">
            <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][0][0]->product_id?>" class="oral-care-team">
                <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][0][0]->product_name?></div>
                <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][0][0]->ad_code?></div>
                <div class="oral-price">
                       <?php if (1 == $pc_remcommand_pro['first']['items'][0][0]->price_show):?>
                       <span>待定</span>
					   <?php else: ?>
                       <span>￥<?php echo $pc_remcommand_pro['first']['items'][0][0]->shop_price?></span>
                       <em><?php echo $pc_remcommand_pro['first']['items'][0][0]->market_price?></em>
                       <?php endif;?>
                </div>
                <?php mark_product($pc_remcommand_pro['first']['items'][0][0]);?>
                <img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][0][0]->img_url);?>">
            </a>
            <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][1][0]->product_id?>" class="oral-care-team">
                <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][1][0]->product_name?></div>
                <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][1][0]->ad_code?></div>
                <div class="oral-price">
					<?php if (1 == $pc_remcommand_pro['first']['items'][1][0]->price_show):?>
                    <span>待定</span>
                    <?php else: ?>
                    <span>￥<?php echo $pc_remcommand_pro['first']['items'][1][0]->shop_price?></span>
                    <em><?php echo $pc_remcommand_pro['first']['items'][1][0]->market_price?></em>
                    <?php endif;?>
      	        </div>
               	<?php mark_product($pc_remcommand_pro['first']['items'][1][0]);?>
               	<img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][1][0]->img_url);?>">
            </a>
        </div>
        
        <div class="oral-care-cen">
            <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][2][0]->product_id?>" class="oral-care-team oral-care-main">
               	<div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][2][0]->product_name?></div>
               	<div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][2][0]->ad_code?></div>
               	<div class="oral-price">
				    <?php if (1 == $pc_remcommand_pro['first']['items'][2][0]->price_show):?>
                    <span>待定</span>
                   	<?php else: ?>
                    <span>￥<?php echo $pc_remcommand_pro['first']['items'][2][0]->shop_price?></span>
                    <em><?php echo $pc_remcommand_pro['first']['items'][2][0]->market_price?></em>
                  	<?php endif;?>
              	</div>
              	<?php mark_product($pc_remcommand_pro['first']['items'][2][0]);?>
             	<img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][1][0]->img_url);?>">           
          	</a>
        </div>
      
        <div class="oral-care-right">
            <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][3][0]->product_id?>" class="oral-care-team  oral-care-main2">
               	<div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][3][0]->product_name?></div>
               	<div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][3][0]->ad_code?></div>
               	<div class="oral-price">
    	            <?php if (1 == $pc_remcommand_pro['first']['items'][3][0]->price_show):?>
			            <span>待定</span>
		            <?php else: ?>
        	            <span>￥<?php echo $pc_remcommand_pro['first']['items'][3][0]->shop_price?></span>
        	            <em><?php echo $pc_remcommand_pro['first']['items'][3][0]->market_price?></em>
                   	<?php endif;?>
              	</div>
              	<?php mark_product($pc_remcommand_pro['first']['items'][3][0]);?>
              	<img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][3][0]->img_url);?>">
           	</a>              
          	<a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][4][0]->product_id?>" class="oral-care-team oral-care-main2">
              	<div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][4][0]->product_name?></div>
              	<div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][4][0]->ad_code?></div>
              	<div class="oral-price">            
    	            <?php if (1 == $pc_remcommand_pro['first']['items'][4][0]->price_show):?>
			        <span>待定</span>
		            <?php else: ?>
        	        <span>￥<?php echo $pc_remcommand_pro['first']['items'][4][0]->shop_price?></span>
        	        <em><?php echo $pc_remcommand_pro['first']['items'][4][0]->market_price?></em>
                    <?php endif;?>
             	</div>
             	<?php mark_product($pc_remcommand_pro['first']['items'][4][0]);?>
             	<img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][4][0]->img_url);?>">
        	</a>
  		</div>   
    </div>
    <div class="see-more"><a href="/category-0-0-0-0-11.html" target="_blank">查看更多</a></div>
</div>
<!--oral-care-end-->

<!--oral-care start-->
     <div class="oral-care2 clearfix" id="kouqianghuli">
          <div class="oral-care-tit"><i class="color-mark green"></i><div class="floor-name">口腔护理</div></div>
          <div class="yueya-pro-lb">
               <div class="oral-care-left">
                       <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][0][0]->product_id?>" class="oral-care-team">
                          <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][0][0]->product_name?></div>
                          <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][0][0]->ad_code?></div>
                          <div class="oral-price">
                               <?php if (1 == $pc_remcommand_pro['first']['items'][0][0]->price_show):?>
                               <span>待定</span>
                               <?php else: ?>
                               <span>￥<?php echo $pc_remcommand_pro['first']['items'][0][0]->shop_price?></span>
                               <em><?php echo $pc_remcommand_pro['first']['items'][0][0]->market_price?></em>
                               <?php endif;?>
                         </div>
                         <?php mark_product($pc_remcommand_pro['first']['items'][0][0]);?>
                         <img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][0][0]->img_url);?>">
                      </a>
                      <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][1][0]->product_id?>" class="oral-care-team">
                         <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][1][0]->product_name?></div>
                         <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][1][0]->ad_code?></div>
                         <div class="oral-price">
                             <?php if (1 == $pc_remcommand_pro['first']['items'][1][0]->price_show):?>
                             <span>待定</span>
                             <?php else: ?>
                             <span>￥<?php echo $pc_remcommand_pro['first']['items'][1][0]->shop_price?></span>
                             <em><?php echo $pc_remcommand_pro['first']['items'][1][0]->market_price?></em>
                             <?php endif;?>
                        </div>
                       <?php mark_product($pc_remcommand_pro['first']['items'][1][0]);?>
                       <img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][1][0]->img_url);?>">
                    </a>
                </div>
            
               <div class="oral-care-cen">
                    <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][2][0]->product_id?>" class="oral-care-team oral-care-main">
                       <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][2][0]->product_name?></div>
                       <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][2][0]->ad_code?></div>
                       <div class="oral-price">
                            <?php if (1 == $pc_remcommand_pro['first']['items'][2][0]->price_show):?>
                            <span>待定</span>
                           <?php else: ?>
                            <span>￥<?php echo $pc_remcommand_pro['first']['items'][2][0]->shop_price?></span>
                            <em><?php echo $pc_remcommand_pro['first']['items'][2][0]->market_price?></em>
                           <?php endif;?>
                      </div>
                      <?php mark_product($pc_remcommand_pro['first']['items'][2][0]);?>
                       
                      <img src="<?php echo static_style_url('pc/images/41a2bf9fa26e7de15fd1109ed810c23e.jpg')?>">        
                  </a>
               </div>
          
               <div class="oral-care-right">
                <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][3][0]->product_id?>" class="oral-care-team  oral-care-main2">
                   <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][3][0]->product_name?></div>
                   <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][3][0]->ad_code?></div>
                   <div class="oral-price">
        	            <?php if (1 == $pc_remcommand_pro['first']['items'][3][0]->price_show):?>
				            <span>待定</span>
			            <?php else: ?>
            	            <span>￥<?php echo $pc_remcommand_pro['first']['items'][3][0]->shop_price?></span>
            	            <em><?php echo $pc_remcommand_pro['first']['items'][3][0]->market_price?></em>
                       <?php endif;?>
                  </div>
                  <?php mark_product($pc_remcommand_pro['first']['items'][3][0]);?>
                  <img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][3][0]->img_url);?>">
               </a>
              
              <a href="/pdetail-<?php echo $pc_remcommand_pro['first']['items'][4][0]->product_id?>" class="oral-care-team oral-care-main2">
                  <div class="oral-name"><?php echo $pc_remcommand_pro['first']['items'][4][0]->product_name?></div>
                  <div class="oral-advantage"><?php echo $pc_remcommand_pro['first']['items'][4][0]->ad_code?></div>
                  <div class="oral-price">            
        	            <?php if (1 == $pc_remcommand_pro['first']['items'][4][0]->price_show):?>
				        <span>待定</span>
			            <?php else: ?>
            	        <span>￥<?php echo $pc_remcommand_pro['first']['items'][4][0]->shop_price?></span>
            	        <em><?php echo $pc_remcommand_pro['first']['items'][4][0]->market_price?></em>
                        <?php endif;?>
                 </div>
                 <?php mark_product($pc_remcommand_pro['first']['items'][4][0]);?>
                 <img class="lazy" src="<?php echo img_url($pc_remcommand_pro['first']['items'][4][0]->img_url);?>">
            </a>
      </div>
   
        </div>
        <div class="see-more"><a href="/category-0-0-0-0-11.html" target="_blank">查看更多</a></div>

    </div>
<!--oral-care end--> 


<!--home-rank-outer start-->
<div id="rexiaokecheng">
	<div id="slides2" class="home-rank-outer2">
        <div class="home-rank-title"><span>热销课程</span></div>
	    <ul class="slides_container">          
		    <li>
		    <?php foreach($pc_hot_sale_course['first']['items'] as $k => $v):?>   
                <a href="/product-<?php echo $v[0]->product_id;?>" target="_blank">
	                <div class="home-rank-img-wp"><img src="<?php echo img_url($v[0]->img_url);?>"></div>
	                <div class="home-rank-pro-title"><?php echo $v[0]->product_name;?></div>
	               	<div class="home-rank-price">演示站优享：<span>￥<?php echo $v[0]->shop_price;?></span><em><?php echo $v[0]->market_price?></em></div>
	           	</a> 
            <?php endforeach;?>                     
          	</li>
          	<li>
   			<?php foreach($pc_hot_sale_course['second']['items'] as $k => $v):?>   
                <a href="/product-<?php echo $v[0]->product_id;?>" target="_blank">
	                <div class="home-rank-img-wp"><img src="<?php echo img_url($v[0]->img_url);?>"></div>
	                <div class="home-rank-pro-title"><?php echo $v[0]->product_name;?></div>
	               	<div class="home-rank-price">演示站优享：<span>￥<?php echo $v[0]->shop_price;?></span><em><?php echo $v[0]->market_price?></em></div>
	           	</a> 
            <?php endforeach;?>        
          	</li>
      	</ul>
	</div>
</div>
<!--home-rank-outer end-->        
 

<!--dentist start-->
    <div class="dentist clearfix" id="yayijiangtang">
         <div class="oral-care-tit"><i class="color-mark orange"></i><div class="floor-name">牙医讲堂</div></div>
         <ul class="dentist-list">
            <div class="dentist-chaochu">
                <?php foreach($pc_remcommand_course['first']['items'] as $k => $v):?>
                  <li data-href="/product-<?php echo $v[0]->product_id?>">
                    <a href="javascript:void(0)"><img src="<?php echo img_url($v[0]->img_url);?>"></a>
                    <div class="dentist-hover">
                    <a href="javascript:void(0)">
                    <img src="<?php echo img_url($v[0]->img_url);?>">
                    <h2><?php echo $v[0]->product_name;?></h2>
                    <?php $product_desc_additional = (!empty($v[0]->product_desc_additional)) ? json_decode($v[0]->product_desc_additional, true) : array(); ?>
                    <span class="dentist-kaike dentist-public">开课：<?php echo date("Y.m.d", strtotime($v[0]->package_name));?>
                                    <?php if (isset($product_desc_additional['desc_waterproof'])) echo '-' . date("Y.m.d", strtotime($product_desc_additional['desc_waterproof']))?></span>
                    <span class="dentist-shoucang dentist-public">收藏：<?php echo $v[0]->collect_num;?>人</span>
                    <span class="dentist-jiangshi dentist-public">讲师：<?php echo $v[0]->subhead;?></span>
                    <div class="dentist-xinx dentist-public clearfix">
                       <span class="dentist-bm">报名：<?php echo $v[0]->ps_num;?>人</span>
                       <span class="dentist-sprice">                   		
                            <?php if(1 == $v[0]->price_show):?>
                                <?php echo '待定';?>
                            <?else:?>	
                                ￥<?php echo $v[0]->product_price;?>
                            <?php endif;?>
                       </span>
                    </div>
                    </a>
                    </div>
                  </li>
                <?php endforeach;?>
                 
            </div>
         </ul>
        <div class="see-more"><a href="/index/course" target="_blank">查看更多</a></div>
    </div>
<!--dentist end-->        
 
<!--dental-video-new start-->
<?php if(isset($index_video_list) && !empty($index_video_list)) :?>            
<div class="dental-video-new" id="yayishipin">
    <div class="oral-care-tit"><i class="color-mark violet"></i><div class="floor-name">牙医视频</div></div> 
    <ul class="dental-video-new-lb">
        <div class="dental-chaochu">
        <?php foreach ($index_video_list as $val_new) : ?>
            <li class="play-video">
              	<a class="play-video-pic" href="/video/detail/<?php echo $val_new->ID;?>">
	                <img src="<?php echo $val_new->cover;?>">
	                <!-- <div class="dental-video-time">01:38</div> -->
	                <div class="play-btn"></div>
             	</a>
             	<div class="dental-video-title"><?php echo $val_new->post_title;?></div>
	            <div class="dental-video-wt">
	                <span class="mingchen"><?php echo $val_new->display_name;?></span>
	                <span class="dental-time"><?php echo current(explode(' ', $val_new->post_date));?></span>
	            </div>
	            <div class="dental-video-info">
	                <span class="dental-volume"><i></i><?php echo $val_new->views;?></span>
	                <span class="dental-num"><i></i><?php echo $val_new->comment_count;?></span>
	            </div>
          	</li>
        <?php endforeach; ?>
        </div>
    </ul>
    <div class="see-more"><a href="/video.html" target="_blank">查看更多</a></div>    
</div> 
<?php endif;?> 
<!--dentist-end-->          
 
 
<!--exhibition-goods-start-->
<div class="exhibition-goods" id="zhanlanshangpin">
     <div class="oral-care-tit"><i class="color-mark green2"></i><div class="floor-name">展示商品</div></div>
     <ul class="exhibition-goods-show">
        <?php foreach ($pc_show_pro['first']['items'] as $k => $v):?>
          <li><a href="pdetail-<?php echo $v[0]->product_id?>"><img src="<?php echo img_url($v[0]->img_url)?>"><p><?php echo $v[0]->product_name?></p></a></li>
        <?php endforeach;?>         
        
     </ul>
    
</div>
<!--exhibition-goods-end-->     


<!--cooperation-start-->
<ul class="cooperation-list" id="hezuohuoban">
    <div class="oral-care-tit"><i class="color-mark red"></i><div class="floor-name">合作伙伴</div></div>
    <div class="cooperation">
        <ul>
        	<?php echo $brand_list[0]->ad_code?>
        	<li class="cooperation-text"><a href="/about_us/team_work"><div>+</div>申请合作</a></li>
        </ul>
    </div>
</ul>
<!--cooperation-end-->
  	</div>
</div>


<div class="fp-lift">
     <div class="mui-lift" id="J_FpLift" style="display:none;">
          <div class="nav-header">导航</div>
          
          <a data-anchor="rexiaodanpin"   href="#rexiaodanpin"    class="mui-lift-nav color-orange"><div class="mui-lift-nav-name">热销单品</div></a>
          <a data-anchor="kouqiangxiufu"  href="#kouqiangxiufu"   class="mui-lift-nav color-blue"><div class="mui-lift-nav-name">口腔修复</div></a>
          <a data-anchor="kouqianghuli"   href="#kouqianghuli"    class="mui-lift-nav color-green"><div class="mui-lift-nav-name">口腔护理</div></a>
          <a data-anchor="rexiaokecheng"  href="#rexiaokecheng"   class="mui-lift-nav color-orange"><div class="mui-lift-nav-name">热销课程</div></a>
          <a data-anchor="yayijiangtang"  href="#yayijiangtang"   class="mui-lift-nav color-pink"><div class="mui-lift-nav-name">牙医讲堂</div></a>
          <a data-anchor="yayishipin"     href="#yayishipin"      class="mui-lift-nav color-violet"><div class="mui-lift-nav-name">牙医视频</div></a>
          <a data-anchor="zhanlanshangpin" href="#zhanlanshangpin" class="mui-lift-nav color-green2"><div class="mui-lift-nav-name">展览商品</div></a>
          <a data-anchor="hezuohuoban" href="#hezuohuoban"     class="mui-lift-nav color-red"><div class="mui-lift-nav-name">合作伙伴</div></a>
          <div id="back-to-top"><a href="#top" class="nav-back"><span></span></a></div>
          
    </div>
</div>


<!-- <div class="contain-right" id="contain-right">
     <div   class="mobile-browser">
          <a href="javascript:void(0);"><span>手机端浏览</span></a>
     </div>
     <div class="online-customers"><a href="javascript:void(0);"><span>24小时在线客服</span></a></div>
     
     <div class="mobile-hover" style="display:none;">
          <img src="<?php echo static_style_url('pc/images/code.png')?>">
          <span></span>
     </div>

</div> -->

<script>
	$("#J_FpLift a").click(function(){
		$("#J_FpLift a").removeClass("mui-lift-cur-nav");
		$(this).addClass("mui-lift-cur-nav");	
	});	
	$(function () {
        //离页面顶部
        var J_FpLift = $('#J_FpLift');
        floatTool(J_FpLift, 400,
            function () {
                J_FpLift.show();
                var isactive = false;
                J_FpLift.find('a[data-anchor]').each(function () {
                    var anchorId = $(this).data('anchor');
                    if (anchorId.length > 0) {
                        var anchorElement = $('#' + anchorId);
                        if (anchorElement.length > 0) {
                            var anchorOffsetTop = anchorElement.offset().top;
                            if (anchorOffsetTop != 0 && !isactive) {
                                var val = $(document).scrollTop() - anchorOffsetTop;
                                if (val < 30 && Math.abs(val)<500) {
                                    $(this).addClass('active');
                                    isactive = true;
                                    return;
                                }
                            }
                            $(this).removeClass('active');
                        }
                    }
                });
            }
            ,
            function () {
                J_FpLift.hide();
            }
        );
    });
	function floatTool(obj, documentScrollTop, showMethod, hideMethod) {
        $(window).on('scroll', function () {
            if ($(document).scrollTop() > documentScrollTop) {
                showMethod();
            }
            else {
                hideMethod();
            }
        });
    }

	$(".mobile-browser").hover(function () {
        $(".mobile-hover").show();
    }, function () {
        $(".mobile-hover").hide();
    });

    $(".mobile-hover").hover(function () {
        $(".mobile-hover").show();
    }, function () {
        $(".mobile-hover").hide();
    });

	$(function(){
	 	$('.dentist-chaochu li').click(function(e){
	    	var host = '<?php echo FRONT_HOST;?>';    
	    	window.open(host + $(this).attr('data-href'));    
	  	});
	});	
</script>

<?php include APPPATH . "views/common/meiqia.php"; ?>  
<?php include APPPATH . 'views/common/footer.php'?>