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
<div class="view view-main" data-page="<?=$active_tab?>">
    <div class="toolbar tabbar-labels tabbar">
        <div class="toolbar-inner">
            <a href="#index"   class="link ripple"><i class="footer tabbar-demo-icon-1 tabbar-selecte"></i></a>
            <a href="#course"  class="link ripple"><i class="footer tabbar-demo-icon-2"></i></a>
            <a href="#article" class="link ripple"><i class="footer tabbar-demo-icon-3"></i></a>
            <a href="#user"    class="link ripple"><i class="footer tabbar-demo-icon-4"></i></a>        
        </div>
    </div>
    
    <div class="pages">

<!-- start index -->
		<div data-page="index" class="page<?php if ($active_tab!='index') echo ' cached';?>">
             <div class="navtion">
                  <div class="menu clearfix">
                       <div class="logo"><a href="/auth" class="external"><span class="yy-icon-xx yy-sprite-icon"></span></a></div>
                       <form  id="index_searchForm" class="yy-search">
                            <div class="sub-ss">
                               <div class="yy-search-form-box">
                                     <span class="yy-icon-xx yy-search-icon"></span>
                                     <div class="yy-search-input "><input type="text" name="keyword" value="" placeholder="输入搜索内容" ></div>
                                </div>
                            </div>
                       </form>
                       <div class="menu-login"><a href="#" class="login">登录</a></div>
                  </div>
            </div>      
                   
             
       <div class="page-content infinite-scroll public-bg" style=" padding-bottom:80px;" data-template="infiniteProductTemplate" data-source="/index/ajax_goods_list/index" data-parent=".listb ul">
               

          <div class="swiper-box">
               <div class="swiper-container">        
                    <div class="swiper-wrapper">
                         <div class="item-hide"><H2>近期牙科材料，齿科材料，口腔材料优惠展销区</H2></div>
                <?php foreach($index_focus_image as $item):?>
    <div class="swiper-slide"><a class="external" href="<?php echo $item['href']?>" title="<?php echo $item['title']?>"><img src="<?php echo img_url($item['img_src'])?>"></a></div>
          <?php endforeach;?>
                  </div>
                 <div class="swiper-pagination"></div>
            </div>
        </div>
                        
    <div class="feature-wrap"><div class="feature-wrap-pic"><img src="<?php echo static_url('mobile/img/index-ico-v.png?v=version');?>" /></div></div>
                        
    <div class="item-hide"><H2>优秀牙科材料，齿科材料，口腔材料品牌展示区</H2></div>


<div class="classification">
        <ul class="clearfix">
             <li>
             <a href="/product/searchResultByType.html?kw=手套&type_id=18" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="list-ico list-one"></span></div></div>
                     <div class="index-bt">手套</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=手机&type_id=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="list-ico list-two"></span></div></div>
                     <div class="index-bt">手机</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=根管&type_id=4" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-green"><span class="list-ico list-three"></span></div></div>
                     <div class="index-bt">根管</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=印模&type_id=613" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="list-ico list-four"></span></div></div>
                     <div class="index-bt">印模</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=树脂&type_id=186" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="list-ico list-five"></span></div></div>
                     <div class="index-bt">树脂</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=正畸&type_id=7" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-green"><span class="list-ico list-six"></span></div></div>
                     <div class="index-bt">正畸</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?kw=清仓&type_id=622" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="list-ico list-seven"></span></div></div>
                     <div class="index-bt">清仓</div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/ptype_list.html" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="list-ico list-eight"></span></div></div>
                     <div class="index-bt">分类</div>
                </div>
             </a>
             </li>
             
        </ul>
</div>

<div class="item-hide"><H2>优质牙科材料，齿科材料，口腔材料特卖区</H2></div> 

<div class="index-av">
     <div class="index-av-lb clearfix">
          <div class="index-shop bdr-r "><a href="/tuan/index/product" class="external"><img src="<?php echo static_url('mobile/img/shangpin.png?v=version');?>"/></a></div>
          <div class="index-temai bdr-r "><a href="/temaiqu" class="external"><img src="<?php echo static_url('mobile/img/temai.png?v=version');?>"/></a></div>
          <div class="index-edu"><a href="/tuan/index/course" class="external"><img src="<?php echo static_url('mobile/img/kecheng.png?v=version');?>"/></a></div>
     </div>
</div>





<div class="listb">
     
         <ul class="sbox clearfix">
        <?php 
        if ($index_good):
        foreach($index_good as $good):?>
        
            <li class="bdr-r">
            <div class="products-list clearfix">
            <a href="/pdetail-<?php echo $good['product_id']?>.html" class="external">
                <div class="img_sbox"><img class="lazy " data-src="<?php echo img_url($good['img_url']);?>">
                    <?php if($good['is_promote']):?>
                         <div class="daojishi"  data="<?php echo $good['is_promote'] ? $good['promote_end_date'] : 0?>">             
                         </div>
                    <?php endif;?> 
        
                </div>
                <div class="prod_name"><span><?php echo $good['brand_name'] ;?></span><?php echo $good['product_name'];?></div>
                <div class="bline clearfix">
                    <div class="favoheart"><?php echo get_page_view('product',$good['product_id'],false);?>关注</div>
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
     
            </div>
            </li>
        <?php endforeach;endif;?>
         </ul>

</div>
                
</div>

</div> 
<!-- end index -->

<!-- 课程培训-->
<div data-page="course" class="page<?php if ($active_tab!='course') echo ' cached';?>">
    
<!--navtion start--> 
     <div class="navtion">
          <div class="menu clearfix">
               <h1 class="nav-bt">演示站课堂</h1>
               <div class="menu-login menu-search"><span class="nav-search"></span></div>
          </div>
     </div>  
<!--navtion end--> 

<div class="page-content infinite-scroll article-bg" data-distance="100"  data-template="html" data-source="/index/ajax_goods_list/course" data-parent=".forum-list">

<!--swiper-box start-->  
           <div class="swiper-box">
                <div class="swiper-container">        
                     <div class="swiper-wrapper">
                         <div class="item-hide"><H2>近期牙科材料，齿科材料，口腔材料优惠展销区</H2></div>
							<?php //foreach($course_focus_image as $item):?>
                                      <div class="swiper-slide">
                                     <a class="external" href="<?php //echo $item['href']?>" title="<?php //echo $item['title']?>"><img src="http://s.test.com/mobile/img/banner.jpg<?php //echo img_url($item['img_src'])?>"></a>
                            
                            </div>
                            <?php //endforeach;?>
                    </div> 
                   <div class="swiper-pagination"></div>
             </div>
        </div>
<!--swiper-box end--> 
    
    <div class="feature-wrap"><div class="feature-wrap-pic"><img src="<?php echo static_url('mobile/img/edu-pic.png?v=version');?>" /></div></div>
       
 
<!-- 
      <div class="dental-class">
           <form class="dental-search-form">
                 <div class="search-courses clearfix">
                      <div class="dentist-search"><i class="dentist-public class-ss"></i></div>
                      <div class="dentist-search-input"><input type="text" value="" class="dentist-ss" placeholder="搜索课程"></div>
                      <div class="dentist-detel"><i class="dentist-public class-detel"></i></div>
                </div>
           </form>
           <ul class="dental-ification clearfix">
           <li><a href="/product/searchResultByType.html?stype=1&kw=种植&type_id=602&sort=10" class="external"><div class="dental-shape dental-orange">
           <span class="dental-ico zhongzhi"></span></div>种植</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=修复&type_id=603&sort=10" class="external"><div class="dental-shape dental-blue">
           <span class="dental-ico xiufu"></span></div>修复</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=口内牙周&type_id=606&sort=10" class="external"><div class="dental-shape dental-green">
           <span class="dental-ico zhengji"></span></div>口内牙周</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=口外&type_id=605&sort=10" class="external"><div class="dental-shape dental-orange">
           <span class="dental-ico kouwai"></span></div>口外</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=正畸&type_id=604&sort=10" class="external"><div class="dental-shape dental-blue">
           <span class="dental-ico kounei"></span></div>正畸</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=管理&type_id=619&sort=10" class="external"><div class="dental-shape dental-green">
           <span class="dental-ico guanli"></span></div>管理</a></li>
           <li><a href="/product/searchResultByType.html?stype=1&kw=儿童牙科&type_id=620&sort=10" class="external"><div class="dental-shape dental-orange">
           <span class="dental-ico ertong"></span></div>儿童牙科</a></li>                     
           <li><a href="/product/searchResultByType.html?stype=1&kw=其它&type_id=607&sort=10" class="external"><div class="dental-shape dental-white">
           <span class="dental-ico qita"></span></div>其它</a></li>
           <li id="xixi" onclick="javascript:showModel()"><a href="#"><div class="dental-shape dental-white"><span class="dental-ico qita"></span></div>其它</a></li>
           </ul>
    </div>
   
   <div class="qita-list" id="qita-list" style="display:none;" onclick="hideModel()"> 
         <div class="qita-jt"></div>
         <ul class="qita-nr clearfix">
         <li><a href="/product/searchResultByType.html?stype=1&kw=会议通知&type_id=4" class="external">会议通知</a></li>
         <li><a href="/product/searchResultByType.html?stype=1&kw=院感&type_id=4" class="external">院感（感染控制）</a></li>
         <li><a href="/product/searchResultByType.html?stype=1&kw=营销&type_id=4" class="qita-none external">营销（洽谈）</a></li>
         <li><a href="/product/searchResultByType.html?stype=1&kw=口腔摄影&type_id=4" class="external">口腔摄影</a></li>
         <li><a href="/product/searchResultByType.html?stype=1&kw=DSD笑线设计&type_id=4" class="external">DSD笑线设计</a></li>
         <li><a href="/product/searchResultByType.html?stype=1&kw=美白&type_id=4" class="qita-none external">美白</a></li>
         </ul>
   </div>
<script>
function showModel() {	    
    	var oBtn = document.getElementById("qita-list");	 
		oBtn.style.display = "block";
    }
	
	function hideModel(){
		var oBtn = document.getElementById("qita-list");	         
        oBtn.style.display = "none";        
	}

</script>       
-->

<div class="classification">
     <ul class="clearfix">
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=种植&type_id=602&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="edu-ico edu-one"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=种植&type_id=602&sort=10">种植</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=修复&type_id=603&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="edu-ico edu-two"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=修复&type_id=603&sort=10">修复</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=口内牙周&type_id=606&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-green"><span class="edu-ico edu-three"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=口内牙周&type_id=606&sort=10">口内</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=口外&type_id=605&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="edu-ico edu-four"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=口外&type_id=605&sort=10">口外</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=正畸&type_id=604&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="edu-ico edu-five"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=正畸&type_id=604&sort=10">正畸</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=管理&type_id=619&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-green"><span class="edu-ico edu-six"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=管理&type_id=619&sort=10">管理</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=儿童牙科&type_id=620&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-blue"><span class="edu-ico edu-seven"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=儿童牙科&type_id=620&sort=10">儿科</a></div>
                </div>
             </a>
             </li>
             <li>
             <a href="/product/searchResultByType.html?stype=1&kw=其它&type_id=607&sort=10" class="external">
                <div class="index-lb clearfix">
                     <div class="index-ico"><div class="index-list list-orange"><span class="edu-ico edu-eight"></span></div></div>
                     <div class="index-bt"><a href="/product/searchResultByType.html?stype=1&kw=其它&type_id=607&sort=10">更多</a></div>
                </div>
             </a>
             </li>
             
        </ul>
</div>


<div class="forum-list">
      <?php foreach($courses as $course):
          if (is_object($course)) $course = (array)$course;
            $product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : array();
      ?>
        <dl class="edu-list clearfix">

            <a href="/product-<?php print $course['product_id']; ?>.html" class="external">
              <dt><img src="<?php print img_url($course['img_url']); ?>" alt="<?php print $course['product_name']; ?>"></dt>
              <dd>
              <div class="edu-yb-jl">
                  
                  <h2 class="edu-biaoti"><?php echo $course['product_name'];?></h2>
                  <div class="edu-time">时间：<span><?php echo date("m月d日", strtotime($course['package_name']));?>-<?=date("m月d日", strtotime($product_desc_additional['desc_waterproof']))?></span></div>
                  <div class="edu-address">地点：<span><?php echo $product_desc_additional['desc_material'];?></span></div>
                  <div class="edu-tec clearfix">
                       <div class="edu-mc">讲师：<span><?php echo $course['subhead'];?></span></div>
                       <div class="edu-public edu-jt"></div>
                  </div>
                  
              </div>
              <div class="edu-ifo clearfix">

                   <div class="edu-ll bdr-r"><span class="edu-public edu-eye"><? echo get_page_view('product',$course['product_id'],false);?></span></div>
                   <div class="edu-baoming">已报名：<?=$course['ps_num']?></div>
              </div>
              </dd>
              <div class="edu-public <?php if(date('Y-m-d') <= $product_desc_additional['desc_waterproof']): ?>''<?php else: ?>edu-guoqi<?php endif; ?>"></div>
            </a>
        </dl>

    <?php endforeach;?>

</div>
                
                
                
                
                
            </div>
        </div>
 <!-- end 课程培训-->
<!-- start article -->
        <div data-name="article" data-page="article" class="page<?php if ($active_tab!='article') echo ' cached';?>">
            <div class="navbar">
                <div class="navbar-inner">
                              <div class="left"><a href="/article/search" class="link icon-only open-panel external"> <i class="icon searchico"></i></a></div>
                    <div class="center">文章视频</div>
<div class="item-hide"><H2>牙科材料，齿科材料专题视频及行业文章</H2></div>
                </div>
            </div>
            <div class="page-content infinite-scroll  article-bg" data-distance="100" data-template="html" data-source="/index/get_article" data-parent=".listb02 ul">
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
    		    <div class="listb02 listb-article">
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
                <div class="page-content article-bg2">
	                <div class="content-block article-video">
			      <div class="user-profile"></div>
			      <div class="list-block center-list" style="margin-top:10px;">
                              <ul>
	                        <li><a href="/collect/index" class="item-link item-content"><div class="item-inner">我的关注</div></a></li>
                            <li><a href="/user/order" class="item-link item-content"><div class="item-inner">我的订单</div></a></li>
<li><a href="/user/comment" class="item-link item-content external"><div class="item-inner">我的评价</div></a></li>
	                        <li><a href="/address/index" class="item-link item-content external"><div class="item-inner">收货地址</div></a></li>
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

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/common.js?v=version');?>"></script>

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

    $('.yy-search-form-box').click(function(){
      location.href = '/search';
    });
    
    $(document).on('click', '.nav-search', function (e) {
      location.href = '/search?stype=1';
    });
    
    $('.login').click(function(){
        // if(isWeiXin()){
        //     myApp.alert('谢谢在微信中打开');
        // }else{
            login();
        // }
    });

    myApp.upscroller('回到顶部');
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
