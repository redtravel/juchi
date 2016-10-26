<?php include APPPATH."views/mobile/header.php"; ?>
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css?v=version')?>">
<div class="popover popover-advar">
	<div class="popover-inner">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="center">推荐头像</div>
			</div>
		</div>
        <div class="content-block">
        <ul class="shangchuan-tx clearfix">
           
               <li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img1.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img2.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img3.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img4.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img5.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img6.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img7.jpg')?>" /></div></li><li><div class="shangchuang-hu"><img src="<?php echo static_url('mobile/touxiang/img8.jpg')?>" /></div></li>
        
           
        </ul>
        </div>

        <div class="yywtoolbar">
        <div class="yywtoolbar-inner buttons-row">
            <a href="#" class="button color-oranges button-fill save-advar">保存</a>
        </div>
        </div>
	</div>
</div>
<div class="views">
<!-- 演示站商城-->
<div class="view view-main" data-page="index">

    <div class="toolbar tabbar-labels tabbar">
      <div class="toolbar-inner">
      <a href="#index" class="link<?php if ($active_tab=='index') echo ' active';?> ripple"> <i class="icon tabbar-demo-icon-1"></i><span class="tabbar-label">商品展销</span></a>
        <a href="#course" class="link<?php if ($active_tab=='course') echo ' active';?> ripple"><i class="icon tabbar-demo-icon-2"></i><span class="tabbar-label">教育培训</span></a>
        <a href="#article" class="link<?php if ($active_tab=='article') echo ' active';?> ripple"><i class="icon tabbar-demo-icon-3"></i><span class="tabbar-label">文章视频</span></a>
        <a href="#user" class="link<?php if ($active_tab=='user') echo ' active';?> ripple"><i class="icon tabbar-demo-icon-4"></i><span class="tabbar-label">个人中心</span></a>        
      </div>
</div>
    <div class="pages">

<!-- start index -->
		<div data-page="index" class="page<?php if ($active_tab!='index') echo ' cached';?>">
                   <div class="navbar">
                         <div class="navbar-inner">
                              <div class="left"><a href="/product/search" class="link icon-only open-panel external"> <i class="icon searchico"></i></a></div>
                              <div class="center c_name">演示站商城</div>
<div class="item-hide"><H1>国内专业牙科材料，齿科材料，口腔材料一战式展销平台</H1></div>
                              <div class="right">
		                   <a href="/cart/" class="link icon-only external"> <i class="icon cartico"></i><span class="number number2" id="cart_num">0</span></a>
		                   <a href="/product/ptype_list" data-popover=".popover-links" class="link icon-only open-popover external"><i class="icon csearchico"></i></a>
                              </div>
                        </div>
                   </div>
            <div class="page-content infinite-scroll public-bg" data-template="infiniteProductTemplate" data-source="/index/ajax_goods_list/index" data-parent=".listb ul">
                <div class="content-block">

                        <div class="swiper-box">
                            <div class="swiper-container">        
                                <div class="swiper-wrapper">
<div class="item-hide"><H2>近期牙科材料，齿科材料，口腔材料优惠展销区</H2></div>
<?php foreach($index_focus_image as $item):?>
          <div class="swiper-slide">

            <a class="external" href="<?php echo $item['href']?>" title="<?php echo $item['title']?>"><img src="<?php echo img_url($item['img_src'])?>"></a>

</div>
<?php endforeach;?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>

<div class="item-hide"><H2>优秀牙科材料，齿科材料，口腔材料品牌展示区</H2></div>
<?php
if (!empty($ad)){
foreach($ad as $a){
    echo adjust_path($a->ad_code);
}
}
if (!empty($ad1)){
foreach($ad1 as $a){
    echo '<div style="margin-top:10px">'.adjust_path($a->ad_code).'</div>';
}
}
?>
<div class="item-hide"><H2>优质牙科材料，齿科材料，口腔材料特卖区</H2></div> 

<div class="listb clearfix">
 <ul class="sbox ">
<?php 
if ($index_good):
foreach($index_good as $good):?>

     <li>
	<div class="products-list clearfix">
	<a href="/pdetail-<?php echo $good['product_id']?>.html" class="external">
        <div class="img_sbox"><img class="lazy " data-src="<?php echo img_url($good['img_url']);?>">
            <?php if($good['is_promote']):?>
                 <div class="daojishi"  data="<?php echo $good['is_promote'] ? $good['promote_end_date'] : 0?>">             
                 </div>
            <?php endif;?> 

        </div>
        <div class="prod_name"><?php echo $good['brand_name'] . ' ' . $good['product_name']?></div>
        <div class="bline clearfix">
        <div class="favoheart"><?php echo get_page_view('product',$good['product_id'],false);?></div>
            <?php if(isset($good['price_show']) && $good['price_show']):?>
                <div class="price_bar xunjia_product" ><span class="prod_pprice">询价</span></div>
            <?php else:?>
                <div class="price_bar" style=""><span class="prod_pprice"><?php 
                $now = time();
                $good['is_promote'] = $good['is_promote'] && strtotime($good['promote_start_date'])<=$now && strtotime($good['promote_end_date'])>=$now ;
                $good['product_price'] = $good['is_promote'] ? $good['promote_price'] : $good['shop_price'];
                echo $good['product_price']?></span></div>
            <?endif;?>
        </div>
	</a>
<?php if ( $good['is_hot'] ){ ?> 
	<div class="mark mark_sale">热品</div>
<div class="item-hide">齿科材料</div>
<?php }elseif ( $good['is_new'] ){ ?> 
	<div class="mark mark_new">新品</div>
<?php }elseif ( $good['is_zhanpin'] ){ ?> 
	<div class="mark mark_show">展品</div>
<?php }elseif ( $good['is_offcode'] ){ ?> 
	<div class="mark mark_offcode">促销</div>
<div class="item-hide">牙科材料</div>
<?php } ?>
	</div>
    </li>
	
   
    



<?php endforeach;endif;?>
 </ul>
</div>

                </div>
            </div>

        </div> <!-- end index -->
        <!-- 课程培训-->
        <div data-page="course" class="page<?php if ($active_tab!='course') echo ' cached';?>">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="center">课程培训</div>
<div class="item-hide"><H2>牙科材料，齿科材料产品使用、牙科技术培训班</H2></div>
                </div>
            </div>
            <div class="page-content infinite-scroll public-bg" data-distance="100"  data-template="html" data-source="/index/ajax_goods_list/course" data-parent=".listb02 ul">
                <div class="content-block">

                    <div class="edupage">

                        <div class="edu_focuspic">
                            <?php foreach($course_ad as $cad): ?>
                            <img class="lazy " data-src="<?php print img_url($cad->pic_url); ?>"/>
                            <?php endforeach; ?>
                        </div>

                        <div class="listb02">
                            <ul>
			<?php foreach($courses as $course):
			    $product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : array();
			?>
                                <li>
                                    <a href="/product-<?php print $course['product_id']; ?>.html" class="external">
                                    <div class="<?php if($product_desc_additional['desc_waterproof'] >= date("Y-m-d")): ?>edu-box1<?php else: ?>edu-box2<?php endif; ?>">
				        <div class="juli-plick">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img class="lazy " data-src="<?php print img_url($course['img_url']); ?>.85x85.jpg" alt="<?php print $course['product_name']; ?>" /></div>
                                            <div class="l-infobox">
                                            <p class="l-item1"><?php echo $course['product_name'];?></p>
                                            <p class="t-name1">讲师：<?php echo $course['subhead'];?></p>
                                                <p class="l-time1"><?php echo date("m月d日", strtotime($course['package_name']));?>-<?=date("m月d日", strtotime($product_desc_additional['desc_waterproof']))?> <span>地点：<?php echo $product_desc_additional['desc_material'];?></span></p>
<div class="item-hide">课程类别：牙科材料、技术线下培训班</div>
                                                
                                                <?php 
$now = date('Y-m-d H:i:s');
    if($course['is_promote'] && $now >= $course['promote_start_date'] && $now <= $course['promote_end_date']):?>
                                                <p class="l-sale1">通过演示站报名 立减&nbsp;&yen;<?=$course['shop_price'] - $course['promote_price']?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="status-line2 clearfix">
                                            <div class="attention"><? echo get_page_view('course',$course['product_id'],false);?></div>
                                            <div class="signin_num">已报名:<?=$course['ps_num']?></div>
                                            <div class="signin_bar"><span class="<?php if($product_desc_additional['desc_waterproof'] >= date("Y-m-d")): ?>signin<?php else: ?>yibaoming<?php endif; ?>"></span></div>
                                        </div>
					</div>

                                    </div>

                                </a>
                                </li>
<?php endforeach;?>
                            </ul>

                        </div>

                    </div>

                </div>
            </div>
        </div>
 <!-- end 课程培训-->
<!-- start article -->
        <div data-name="article" data-page="article" class="page<?php if ($active_tab!='article') echo ' cached';?>">
            <div class="navbar menu">
                <div class="navbar-inner">
                              <div class="left"><a href="/article/search" class="link icon-only open-panel external"> <i class="icon searchico"></i></a></div>
                    <div class="center">文章视频</div>
<div class="item-hide"><H2>牙科材料，齿科材料专题视频及行业文章</H2></div>
                </div>
            </div>
            <div class="page-content article-bg infinite-scroll" data-template="html" data-source="/index/get_article">
            <div class="content-block article-video">
                    <div class="buttons-row">
                        <a href="#cat_0" class="tab-link active button button-secondary">人气</a>
                        <a href="#cat_3" class="tab-link button button-secondary">技术</a>
                        <a href="#cat_1" class="tab-link button button-secondary">行业</a>
                    </div>
            <div class="tabs tabs-lb">
<?php foreach($articles as $cat => $articleCat): ?>
        <div id="<?php echo $cat?>" class="tab<?php if('cat_0' == $cat) echo ' active'?>">
		   <div class="article-lb"> 
    		    <div class="listb02">
    		        <ul>
                        <?php foreach($articleCat as $article): ?>
                            <li>                               
                                <div class="edu-box1">
				  <div class="juli-plick">
                                    <a class="external" href="/article/detail/<?php echo $article['id']?>">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img class="lazy " data-src="<?php echo $article['cover']?>" alt="<?php echo $article['title']?>"/></div>
                                            <div class="l-infobox2">
                                            <p class="l-item1 article-tit"><?php echo $article['title'];if(isset($article['video'])):?><span class="shipin-hu"></span><?php endif;?></p>
                                            	<p class="t-name1 article-write">作者:<?php echo $article['author']?><span><?php echo current(explode(' ', $article['date']))?></span></p>
                                                <p class="l-sale1 article-new"><?php echo $article['intro']?></p>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="status-line2 clearfix">
                                        <div class="article-ico">
            					            <div class="attention"><?php echo get_page_view('article',$article['id'],false);?></div>
                                            <div class="information"><?php echo $article['total']?></div>
<div class="item-hide">文章类别：牙科材料</div>
            					        </div>
                                        <?php if( !empty($collect_data) && deep_in_array($article['id'], $collect_data)) { ?>
                                            <div class="article-heart article-heart-red"></div>
                                        <?php }else{ ?>
                                            <div class="article-heart article-heart-gray" onclick="add_to_collect(<?php echo $article['id'];?>,2,this,'article-heart');"></div>
                                        <?php } ?>                                          
                                    </div>
				    </div>
                                 </div>                               
                            </li>
                        <?php endforeach;?>
                    </ul>
    		    </div>
		    </div><!--article-->
        </div>
<?php endforeach;?>
            </div>
            </div>
            </div>
        </div> <!-- end article -->

        <div data-page="user" class="page<?php if ($active_tab!='user') echo ' cached';?>">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="center">个人中心</div>
                </div>
            </div>
                <div class="page-content public-bg">
	                <div class="content-block article-video">
			      <div class="user-profile"></div>
			      <div class="list-block center-list" style="margin-top:10px;">
                              <ul>
	                        <li><a href="/collect/index" class="item-link item-content"><div class="item-inner">我的关注</div></a></li>
                            <li><a href="/user/order" class="item-link item-content"><div class="item-inner">我的订单</div></a></li>
<li><a href="/user/comment" class="item-link item-content external"><div class="item-inner">我的评价</div></a></li>
	                        <li><a href="/address/index" class="item-link item-content"><div class="item-inner">收货地址</div></a></li>
	                        <li><a href="/user/course" class="item-link item-content"><div class="item-inner">我的课程</div></a></li>
	                        <li class="center-lb-hu"><a href="/account/index" class="item-link item-content"><div class="item-inner">账户管理</div></a></li>
                            <li class="center-lb-hu"><a href="#" class="item-link item-content"><div class="item-inner">礼品中心</div></a></li>
                              </ul>
	                        <li><a href="/user/setup" class="item-link item-content external"><div class="item-inner">设置</div></a></li>
	                        
                       </div>
			      
			      
		        </div>
                    
                </div>
        </div>
    </div>
</script>
</div>
<!--<div class="view product-view">
<div class="pages">
</div>
</div>-->
<!-- view -->
</div>
<?php include APPPATH."views/mobile/common/template7.php"; ?>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/index.js?v=version');?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/jquery.yomi.js?v=version');?>"></script>
<script type="text/javascript">
if( /index-user/.test(location.href) ) myApp.mainView.loadPage('#user');
update_cart_num();
$('.popover-advar img').each(function(){
    $(this).click(function(){
        $('.popover-advar img').removeClass('selected');
        $(this).addClass('selected');
    })
})
    $('.save-advar').click(function(){
        //保存头像
        var advar = $('.popover-advar img.selected').attr('src');
            advar_ = advar.split('/').pop();
        $.post('/user/save_advar', {advar:advar_}, function(data){
            $('.personal-center-xx>img').attr('src', advar);
        });
        myApp.closeModal('.popover-advar');
        
    })

    myApp.upscroller('回到顶部');
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
