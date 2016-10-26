<?php include APPPATH . "views/mobile/header.php";?>	

<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.colors.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/main.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('css/tuan.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/swiper.min.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/lightGallery.css?v=version')?>">
<style>
	
	.swiper-item-mc{ font-size:0.875em; color:#333;}
	.tab-cotent > ul > li {
		-text-align:center;
		margin-bottom:10px;
	}

    .loading {
        position: fixed;
        left: 50%;
        top: 50%;
        padding: 8px;
        margin-left: -25%;
        margin-top: -25%;
        -background: rgba(0, 0, 0, 1);
        z-index: 11000;
        border-radius: 4px;
        display:none;
    }
    

</style>
<script>
var static_host='';
</script>

<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>
<div class="loading">
    <img src="<?php echo static_style_url('mobile/img/toothbook.gif?v=version')?>" class="">
</div>

<!-- 留言对话框 -->
<div class="popup popup-Iliuya public-bg">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">团购报名</div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
		
    <div class="popup-Iliuya-content" style="padding-top:30px;">
	    <div class="order-details-rr">
	    	<input placeholder="姓名：" name="name" class="" style="background:white; padding-left:5px;" value="">
	    	<input placeholder="手机号" name="mobile" style="background:white; padding-left:5px;" value="">
		 
        </div>
		
	</div>
	
	<div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100  payment-hu"><a class="link button-tijiao" href="javascript:void(0)" >提交</a></div>
        </div>
    </div>
</div>
<!-- ends 留言对话框 -->
    <div class="views">
    <!-- 演示站商城-->
        <div class="view view-main" data-page="index">
            <div class="pages ">
    		    <div data-page="index" class="page no-toolbar edu-fot">
                     <div class="toolbar">
                           <div class="toolbar-inner2 row no-gutter">
                               
   			                 
   			                 <div class="col-15"></div>
							 
							 
							<div class="col-80 "><a class="link woyaobaoming">我要报名</a></div>
							 
                             
   			                 
			               </div>
	                 </div>
                
                
                
                    <div class="navbar">
                        <div class="navbar-inner">
                            
                            <div class="center c_name"><?php echo $title; ?></div>
                            
                        </div>
                    </div>
                    
                    
                    
                    <!-- 顶部快捷键 -->
                    <div class="short-func-box animated">
                    	<div class="short-func-row">
                    		<section id="kefu">
                    			<div class="kefu-erji hu-pro-gg"><p>客服</p></div>
                    		</section>
                    		<section id="fenleijiansuo">
                    			<div class="classification-search hu-pro-gg"><p>分类检索</p></div>
                    		</section>
                    		<section id="fenxiang" class="open-popover"  data-popover=".popover-menu">
                    			<div class="hu-share hu-pro-gg"><p>分享</p></div>
                    		</section>
                    		<section id="shouye">
                    			<div class="hu-home hu-pro-gg"><p>首页</p></div>
                    		</section>
                    	</div>
                    </div>
                    <!-- ends 顶部快捷键 -->
                    
                    
                    <div class="page-content " style=" padding-top:20px;">

                        <!-- 优惠信息 starts -->
                        <div class="new-details-xx clearfix">
                            <div id="tuanBroadLeft" class="animated swing wobble">
                            <h3>30.00</h3>
                            <a href="javascript: void(0)" target="_self" title="立即购买" id="btnBuyNow0"></a>
                            <ul id="priceOff">
                            <li class="moveLiNormal"><p>市场价</p><s>50元</s></li>
                            <li class="moveLiNormal"><p>折扣</p><s>6.0折</s></li>
                            <li class="moveLiNormal"><p>节省</p><s>20元</s></li>
                            </ul>
                            <p id="tuanComeDown">
                            <b id="timeDay">0</b>天<b id="timeHour">11</b>时<b id="timeMinu">54</b>分<b id="timeSecond">20</b>秒
                            <script type="text/javascript" src="http://static.yueyawang.com/js/countDown.js"></script>
                            <script type="text/javascript">countDown({startTime:'2016-05-02 14:00:00',endTime:'2016-05-20 23:59:59',nowTime:'2016-05-20 11:33:10',dayElement:'timeDay',hourElement:'timeHour',minuElement:'timeMinu',secElement:'timeSecond',callback:function(){}});</script>
                            </p>
                            <div class="tuanCountNum">
                            <span>0人</span><h4>已购买</h4>
                            <p>数量有限，下手要快哦！</p>
                            </div>
                            </div>  
                        </div>
                        <!-- 优惠信息 ends -->
                         <!--轮播图开始-->
     					<div class="swiper-box">
     						<div class="swiper-container swiper1_lunbo">
     							<div class="swiper-wrapper">
     								<?php foreach ($g_list as $colorId => $v): ?>
     								<?php foreach ($v as $k => $item): ?>
                                     <? if ($k =='default') { ?>
     								    <div class="swiper-slide hu-xqi-pic"><a href="#" title="<?php echo $title?>"><img src="<?php echo img_url($item->img_url)?>"></a></div>    
     								<?php }elseif($k =='part'){ foreach ($item as $val) { ?>
     									<div class="swiper-slide hu-xqi-pic"><a href="#" title="<?php echo $title?>"><img src="<?php echo img_url($val->img_url)?>"></a></div> 			
     								<?php } } ?>
     								
     								<?php endforeach; ?>
     								<?php endforeach; ?>
     							</div>
     							<div class="swiper-pagination "></div>
     						</div>
     					</div>
							<!--轮播图结束-->
                        
                        
                             <!-- 该产品优惠活动 end-->
                       
                        <div class="list-block">
                           <ul>
                             <li><span class="col-50">135*****23423</span><span class="col-50" style="text-aligh:right">张三</span></li>
                             <li><span class="col-50">135*****23423</span><span class="col-50">张三</span></li>
                             <li><span class="col-50">135*****23423</span><span class="col-50">张三</span></li>

                             
                           </ul>
                         </div>
                        
                   <p class="new-bcoro"></p>
                   
                   <div class="tab-cotent new-details-lb clearfix">
                        <ul>
                        	<li style="display:block">
                                <ul id="auto-loop" class="gallery" style="display:none"></ul>
                        		<div class="new-details-nr"><?php echo $p->detail1;?></div>

			                    <section>
			   				        <div class="certification">
			   				        	<img src="<?php echo static_style_url('mobile/img/pro-xq-pic.jpg?v=version')?>" class="">
			   				        </div>
			   				    </section>
								

                        	</li>
							
							
                        </ul>
                   </div> 
                   
   				    
                   
                   
                  
                    <p class="new-bcoro"></p>
                   
                    
                    <!-- 关联产品 -->
                    <div class="hu-detail-product hu-glcps">
                         <div class="order-details-rr ">关联产品</div>
                    
                    </div>

                    <!-- Swiper -->
                    <div class="swiper-container swiper-container-link-products" style="margin:10px;">
                    	<div class="swiper-wrapper">
                    		<?php foreach ($link_product_list as $k =>$v):?>
                    			<a class="swiper-slide external" product_id="<?php echo $v ->product_id; ?>" href="/pdetail-<?php echo $v ->product_id; ?>" style="display:block">
                    				<div class="swiper-item">
                    					<div><img src="<?php echo img_url($v ->img_url); ?>" alt=""></div>
                    					<div class="swiper-item-mc"><?php echo $v->brand_name  . ' ' . $v->product_name;?></div>
                    					
                    				</div>
                    			</a>
                    			
                    		<?php endforeach; ?>
                    	</div>
                    	<!-- Add Pagination -->
                    	
                    </div>
                    <!-- ends 关联产品 -->
                    
            </div>
            
            


        </div>

    </div>
</div>
</div>
<?php include APPPATH . "views/mobile/common/footer-js.php";?>

<!-- 页面逻辑 start-->
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/swiper.min.js?v=version'); ?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/lightGallery.min.js?v=version'); ?>"></script>
<script>
    //详情内容画廊
    $(document).ready(function(){
        var v_img_loop = '';
        $(".new-details-nr img").each(function(){
            v_img_loop += '<li data-src="'+ $(this)[0].src +'"><a href="#"><img src="'+ $(this)[0].src +'" /></a></li>';
        });
        $("#auto-loop").append(v_img_loop);

        $('.tab-cotent img').on('click', function(){
            $('#auto-loop').show();
            $('#auto-loop li').eq(0).click();
        });
        $("#auto-loop").lightGallery({
            loop:true,
            auto:true,
            pause:4000,
            counter:true,
            caption:'yeuyawang',
            onCloseAfter:function(){$('#auto-loop').hide()}
        });

    });
</script>
<script>
	//创建轮播图和关联产品
	var swiper_link_products = new Swiper('.swiper-container-link-products', {
		pagination: '.swiper-pagination-link-products',
		slidesPerView: 3,
		paginationClickable: true,
		spaceBetween: 5,
		nextButton: '.swiper-button-next',
		prevButton: '.swiper-button-prev',
	});

	var swiper = new Swiper('.swiper1_lunbo', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		spaceBetween: 40,
		centeredSlides: true,
		autoplay: 4500,
		autoplayDisableOnInteraction: false,
		slidesPerView: 1,
		loop: true
	});
	var tag_id = product_id = '<?php echo $p ->product_id ? $p -> product_id : 0; ?>';
	var tagType = 1; //代表产品
	var comment_type = 2;//代表评价

    $$(document).on('ajaxStart', function (e) {
        $$('.loading').show();
    });
    $$(document).on('ajaxComplete', function () {
        $$('.loading').hide();
    });
	
</script>

<script>

    $$(".woyaobaoming").on('click', function(){
        
        myApp.modalname_mobile(false,'我要团购',function(username, password){

            }, 
            function(){
                $$('.loading').show();
            }
        )
        
    });

	

	myApp.upscroller('回到顶部');
</script>


<!-- 页面逻辑 end-->

<?php
include APPPATH . "views/mobile/common/meiqia.php";
include APPPATH . "views/mobile/footer.php";
 ?>