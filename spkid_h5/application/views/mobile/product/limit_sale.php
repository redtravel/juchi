<?php include APPPATH . "views/mobile/header.php";?>	

<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.colors.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/main.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/swiper.min.css?v=version')?>">
<style>
	
	.swiper-item-mc{ font-size:0.875em; color:#333;}
	.tab-cotent > ul > li {
		-text-align:center;
		margin-bottom:10px;
	}

</style>
<script>
var static_host='';
</script>

<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>
<div class="popover popover-brand">
<div class="popover-angle"></div>
<div class="popover-inner">
	<div class="navbar">
	<div class="navbar-inner">
		<div class="center"><?php echo $p ->brand_name ? $p -> brand_name : ""; ?></div>
	</div>
	</div>
<div style="padding:10px;font-size:0.8em">
<?php echo $p->brand_info ? $p->brand_info : ''?></div>

<?php if($p->brand_story):?>
<div class="buttons-row">
<a href="/brand/brand_product/<?php echo $p ->brand_id; ?>" class="button external">查看更多</a>
</div>
<?php endif; ?></div>
</div>

<div class="popover popover-menu">
	<div class="popover-angle"></div>
	<div class="popover-inner">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="center">演示站商城</div>
			</div>
		</div>
		<div class="content-block share_modal hcenter">
			<div class="jiathis_style_32x32">
				<a class="jiathis_button_weixin"></a>
				<a class="jiathis_button_qzone"></a>
				<a class="jiathis_button_cqq"></a>
				<a class="jiathis_button_tsina"></a>
				<a class="jiathis_counter_style"></a>
			</div>
		</div>
	</div>
</div>

<!-- 留言对话框 -->
<div class="popup popup-Iliuya public-bg">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">产品评价</div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
		
    <div class="popup-Iliuya-content">
	    <div class="order-details-rr">
	    	<input type="hidden" placeholder="姓名：" name="name" class="" style="background:white; padding-left:5px;" value="<?php echo $user_name?>">
	    	<input type="hidden" placeholder="联系方式：" name="mobile" style="background:white; padding-left:5px;" value="<?php echo $mobile?>">
		 <textarea name="popup-liuya-content" class="liuyan-hu" placeholder='商品使用感受怎么样，快来说说吧~对其他用户很有帮助哟' onfocus="this.style.color='#000'; this.value='';" style="color: #f00;">
		  </textarea> 
        </div>
		
	</div>
	
	<div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100  payment-hu"><a class="link button-tijiao" href="javascript:void(0)" >提交</a></div>
        </div>
    </div>
</div>
<!-- ends 留言对话框 -->
    <div class="views" >
    <!-- 演示站商城-->
        <div class="view view-main" data-page="index">
            <div class="pages">
    		    <div data-page="index" class="page no-toolbar edu-fot">
                     <div class="toolbar">
                           <div class="toolbar-inner2 row no-gutter">
                               
   			                 
   			                  <div class="col-15"></div>

							 <div class="col-25 new-cart" style=""><a class="link external" href="/cart/"><span class="number pd-number" id="cart_num" ></span></a></div>

							 <?php if ( $p->price_show ): ?>
							 <div class="col-60 registration-hu Ixunjia">
							 	<a class="link" href="javascript:void(0)">我要询价</a>
							 </div>
							 <?php else: ?>
							 
							 
							 <?php if(isset($p->is_zhanpin) && $p->is_zhanpin): ?>
							 
							 <div class="col-60 registration-hu new-xunjia"><a class="link" href="javascript:void(0)" disabled="disabled">加入购物车</a></div>
							 <?php else: ?>

                                <?php if($num_sale < 100 && $down_time ==1  && $is_limit == 1): ?>
							 	   <div class="col-60 registration-hu new-xunjia"><a class="link addcart" href="javascript:void(0)" >加入购物车</a></div>
							 	<?php else: ?>
                                    <div class="col-60 registration-hu new-xunjia"><a class="link" href="javascript:void(0)" disabled="disabled">加入购物车</a></div>
							 <?php endif; ?>
							 <?php endif; ?>
							 <?php endif; ?>
   			                   
			               </div>
	                 </div>
                
                
                
                    <div class="navbar">
                        <div class="navbar-inner">
                            <div class="left"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                            <div class="center c_name"><?php echo $title; ?></div>
                            <div class="right"><a href="javascript:void(0)" class="link icon-only short-func-btn"> <i class="icon icon-bars"></i></a></div>
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
     							<!-- <div class="swiper-pagination "></div> -->
     							
     						</div>
     					</div>

                        <?php if(!empty($limit_sale)):?>
                            <div class="v-limit clearfix" >
                                <div class="xinpinhuodong">新品活动</div>
                                <div id="timeCounter" class="timeCounter">
                                    <div class="timeCounter-date">今天</div>
                                    <div class="timeCounter-juli">
                                    <?php if($down_time == 0){ ?>
                                        <a style="color:#fff;">准时开抢</a>
                                        <span id="t_h"></span>
                                    <?php }elseif($down_time == 1){ ?>
                                        距结束
                                        <span id="t_h"></span>
                                    <? }elseif($down_time == 2){ ?>
                                        <a style="color:#fff;">已结束</a>
                                        <span id="t_h"></span>
                                    <?php } ?>
                                    </div>
                                    <div class="yiqiang"  id="yiqiang"><?php if($down_time == 2){ echo "明日19点至20点准时开抢！";}else{ ?>已抢<?php echo $num_sale;?>% <span class="timeCounter-scroll"><i style="width:<?php echo $num_sale;?>%;"></i></span><?php } ?></div>
                                </div>
                            </div>
                        <?php endif;?>

						<!--轮播图结束-->
                        <div class="new-details-xx clearfix">
                            <div class="new-details-jl clearfix">
                                <div class="clearfix">
                                    <p class="new-details-moeny">
                                        <span>
                                            <?php if ( $p->price_show ): ?>
                                                <em style="font-size:14px">点击左下角咨询客服</em>
                                            <?php else:?>
                                            <em>&#65509;</em>
                                            <?php echo $p ->product_price ? $p->product_price : ""; ?>
                                            <i><s>&#65509;<?php echo $p ->market_price ? $p -> market_price : ""; ?></s></i></span>
                                        <?php endif;?>
                                    </p>
                                    <p class="new-details-ico clearxif"><span class="new-browse"><?php echo get_page_view('product',$p->product_id)?></span><span class="new-heart" onclick="add_to_collect2(<?= $p->product_id;?>,0,change_collect_status);"></span></p>
                                </div>
                                <div class="new-details-bt"><span class="open-popover" data-popover=".popover-brand"><?php echo $p->brand_name;?></span><?php echo $p->product_name;?></div>
                                <p class="new-packing">包装方式：<span><?php echo isset($p->pack_method) && $p->pack_method ? $p->pack_method : ''; ?></span></p>                              		
                            </div>
                        </div>
                        <p class="new-bcoro"></p>
                        <!--促销活动-->
                        <div class="sales-promotion page-content">
                            <div class="sales-title">促销</div>
                            <div class="new-details-jl sales-promotion-xx clearfix content-block">
                                <p><a href="#" class="sales-promotion-but">限购</a> 3月21日-25日 19点-20点 0元抢购</p>
                                <p class="sales-fanxian"><a href="#" class="sales-promotion-but">返现</a> 抢购成功再返现100元代金券（代金券仅用于成箱包装手套购买抵用套）<a href="javascript:void(0);" class="sales-use-rules alert-text-title">使用规则</a></p>
                            </div>
                        </div>
                        <p class="new-bcoro"></p>
                        <div class="new-details-xx new-no-bottom clearfix">
                             <div class="new-details-jl clearfix">
                              <div class="new-details-color clearfix">
                                 <span>规格：</span>
                                 <div class="new-color-fl">
                                     <input type="hidden" id="cur_sub_id" value="<?=($default_sub_id > 0) ? $sub_list[$color_id]['sub_list'][$default_sub_id]->sub_id : 0;?>">
                                     <input type="hidden" id="cur_sub_num" value="<?=($default_sub_id > 0) ? $sub_list[$color_id]['sub_list'][$default_sub_id]->sale_num : 0;?>">
                                     <?php if(isset($sub_list) && !empty($sub_list)):?>
                                     <?php foreach($sub_list[$color_id]['sub_list'] as $k => $v):?>
                                     
                                     	<a href="javascript:void(0)" data-val="<?=$v->sale_num?>" data-id="<?=$v->sub_id?>" class="<?php if(!$v->sale_num): ?> no-point<?php else: ?> buy_size <?php echo $default_sub_id == $k ? 'new-selet' : ''?><?php endif; ?>"><?php echo $v->size_name?></a>
                                     <?php endforeach; ?>
                                     <?php endif; ?>
                                     </div>
                                 </div>
                                 <div class="new-details-color2 clearfix">
                                      <span>数量：</span>
                                      <div class="new-number">
                                          <a href="javascript:void(0)" class="new-number-reduction">-</a><a href="javascript:void(0)"><input name="number" type="text" class="new-number-input buy_num" value="1" id="choosed_num" readOnly="true" ></a><a href="javascript:void(0)" class="new-number-reduction new-number-reduction2">+</a>
                                      </div>
                                 </div>

                              </div>
                                 
                         
                            
                    </div>
                        
                   <p class="new-bcoro"></p>
                   
                  
                   
                   <ul class="tab-tab new-details-tab clearfix">
                        <li><a href="javascript:void(0)" class="new-selet">产品介绍</a></li>
                        <li><a href="javascript:void(0)">用户评价<?php echo '(' . count($liuyan_list) . ')';?></a></li>
                        <li><a href="javascript:void(0)">学习视频</a></li>
                        <li><a href="javascript:void(0)">产品资质</a></li>
                   </ul>
                   
                   
                   <div class="tab-cotent new-details-lb clearfix">
                        <ul>
                        	<li style="display:block">
                        		<div class="new-details-nr"><?php echo $p->detail1;?></div>
			                   <section>
			   				        <div class="certification">
			   				        	<img src="<?php echo static_style_url('mobile/img/pro-xq-pic.jpg?v=version')?>" class="">
			   				        </div>
			   				    </section>
								<?if (count($liuyan_list)): ?>	
								<p class="new-bcoro"></p>
								<p style="border-bottom:1px solid #dbdbdb; padding:10px;">最新用户评价</p>
    		   				    <ul class="new-comment-lb">
    		   				    	<?php if(count($liuyan_list) >= 5):?>
    		   				    	<?php for($i = 0; $i < 5; $i++):?>
    								<li>
    									<div class="new-detail-pl clearfix">
    								     <div class="new-pl-tx"></div>
    								     <div class="new-pl-nr">
    								          <div class="new-pl-zz clearfix">
    								              <div class="pl-mc"><?php echo $liuyan_list[i]->user_name? $liuyan_list[i]->user_name: $liuyan_list[i]->admin_user_name;?></div>
    								              <div class="pl-time"><?php print  $liuyan_list[i]->comment_date; ?></div>
    								          </div>
    								          <div class="pl-xinxi clearfix"><?php print  $liuyan_list[i]->comment_content ?></div>
    								     </div>
    								     
    								 	</div>
    								 </li>                        
    		                     	<?php endfor;?>
    		                     <?php else:?>
    								<?php foreach($liuyan_list as $l):?>
    									<li>
    										<div class="new-detail-pl clearfix">
    									     <div class="new-pl-tx"></div>
    									     <div class="new-pl-nr">
    									          <div class="new-pl-zz clearfix">
    									              <div class="pl-mc"><?php echo $l->user_name?$l->user_name:$l->admin_user_name;?></div>
    									              <div class="pl-time"><?php print $l->comment_date; ?></div>
    									          </div>
    									          <div class="pl-xinxi clearfix"><?php print $l->comment_content ?></div>
    									     </div>
    									     
    									 	</div>
    									 </li>
    								<?php endforeach;?>
    		                     <?php endif;?>
    		                         
    		                        
    		                         
    		                         
    		                    </ul>
    		                <?php endif;?>

                        	</li>
							<li style="display:none">
								
								<div class="new-comment"><a href="javascript:void(0)" class="Iliuya">发表评价</a><span>评价已购买的商品</span></div>
								<?php if(count($liuyan_list)):?>
								<ul class="new-comment-lb">
									<?php foreach($liuyan_list as $l):?>
										<li>
											<div class="new-detail-pl clearfix">
										     <div class="new-pl-tx"></div>
										     <div class="new-pl-nr">
										          <div class="new-pl-zz clearfix">
										              <div class="pl-mc"><?php echo $l->user_name?$l->user_name:$l->admin_user_name;?></div>
										              <div class="pl-time"><?php print $l->comment_date; ?></div>
										          </div>
										          <div class="pl-xinxi clearfix"><?php print $l->comment_content ?></div>
										     </div>
										     
										 	</div>
										 </li>
									<?php endforeach;?>
								</ul>
								<?php else:?>
									<div style="text-align:center">
									暂无内容</div>
								<?php endif;?>
							</li>
							<li style="display:none;">
								<?php if(!empty($p->detail2)):?>
									<?php echo $p->detail2;?>
								<?php else:?>
									<div style="text-align:center">
									暂无内容</div>
								<?php endif;?>
							</li>
							<li style="display:none" class="new-details-nr"></li>
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
                    	<!-- <div class="swiper-pagination swiper-pagination-link-products"></div> -->
                    </div>
                    <!-- ends 关联产品 -->
                    
            </div>
            
            


        </div>

    </div>
</div>
</div>
<?php include APPPATH . "views/mobile/common/footer-js.php";?>
<script type="text/javascript">
	// 限时抢购活动

    //倒计时
    var start_time = 0;
    var end_time = 0;
    $$.ajax({
        url:'/special/date_time',
        data:{rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        async: false,
        success:function(result){
            start_time = new Date(result.start_time);
            end_time = new Date(result.end_time);
        }
    });
    function GetRTime(){
        var NowTime = new Date();
        if(NowTime.getTime() >= start_time.getTime() && NowTime.getTime() <= end_time.getTime()){           
            var t = end_time.getTime() - NowTime.getTime();
            var h = 0;
            var m = 0;
            var s = 0;
            if(t >= 0){
                h=Math.floor(t/1000/60/60%24);
                m=Math.floor(t/1000/60%60);
                s=Math.floor(t/1000%60);
                if(m < 10) m = "0" + m; 
                if(s < 10) s = "0" + s; 
            }else{
                clearInterval(GetRTime);
            }        
            document.getElementById("t_h").innerHTML = "0"+ h + ":" + m + ":" + s;
        }
    }
    setInterval(GetRTime,0);
    //进度条
    function v_scroll(){
        var NowTime = new Date();
        var v_product_id = '<?php echo $p ->product_id ? $p -> product_id : 0; ?>';
        $$.ajax({
            url:'/special/v_scroll',
            data:{product_id:v_product_id,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            async: false,
            success:function(result){
                if(result.num_sale < 100 && NowTime.getTime() <= end_time.getTime()){
                    document.getElementById("yiqiang").innerHTML = '已抢'+ result.num_sale +'%<span class="timeCounter-scroll"><i style="width:'+result.num_sale+'%;"></i></span>';
                }else{
                    document.getElementById("yiqiang").innerHTML = '明日19点至20点准时开抢！';
                    clearInterval(v_scroll);
                }
            }
        });
    }
    setInterval(v_scroll,10000);

</script>
<!-- 页面逻辑 start-->
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/swiper.min.js?v=version'); ?>"></script>
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
	var product_additional = {
		'package_name' : '<?php echo isset($p->package_name) ? $p->package_name : ''?>',
		'product_name' : '<?php echo isset($product_additional[0]['product_name']) ? $product_additional[0]['product_name'] : ''?>',
		'brand_name' : '<?php echo isset($p->brand_name) ? $p->brand_name : ''?>',
		'product_weight' : '<?php echo isset($p->product_weight) ? $p->product_weight : ''?>',
		'property' : '<?php echo isset($product_additional[0]['property']) ? $product_additional[0]['property'] : ''?>',
		'register_no' : '<?php echo isset($product_additional[0]['register_no']) ? $product_additional[0]['register_no'] : ''?>',
		'standard' : '<?php echo isset($product_additional[0]['standard']) ? $product_additional[0]['standard'] : ''?>',
		'medical1' : '<?php echo isset($product_additional[0]['medical1']) ? $product_additional[0]['medical1'] : ''?>',
		'medical2' : '<?php $medical = array('1' => 'Ⅰ级', '2' => 'Ⅱ级', '3' => 'Ⅲ级'); echo isset($product_additional[0]['medical2'])&&array_key_exists($product_additional[0]['medical2'], $medical) ? $medical[$product_additional[0]['medical2']] : ''?>',
		'scope' : '<?php echo isset($product_additional[0]['scope']) ? $product_additional[0]['scope'] : ''?>',
	};
</script>

<script>
	var jiathis_config = {
		summary: "",
		pic: "http://e.hiphotos.baidu.com/image/w%3D400/sign=3599991bc45c1038247ecfc28210931c/8435e5dde71190efaa0f0bdecc1b9d16fdfa603f.jpg",
		shortUrl: false,
		hideMore: true
	}

	$$('#cart_num').hide();
	$$('.addcart').on('click', add_to_cart);
	function add_to_cart () {
		var v_max = $$("#cur_sub_num").val();
	    if (v_max < 1) {
			myApp.addNotification({
					hold: 1500,
					additionalClass: 'middle',
	                custom: '<p style="padding:30px;text-align:center">该商品已卖光</p>',
	                onClose: function(){},
	                closeOnClick: true

	            });
			return;

	    };
		var is_for_buy = '<?php echo $p->price_show?>';
		if (is_for_buy == '1') {
			myApp.alert('加入购物车失败');
			return;
		};

	    var sub_id = $$("#cur_sub_id").val();
	    var max_num = $$("#cur_sub_num").val();
	    var num = parseInt(num_input.val());
	    if (!sub_id) 
	    {
	        myApp.alert('请选择规格');
	        return false;
	    }
	    if(!num || num < 1)
	    {
	        myApp.alert('请输入购买数量');
	        return false;
	    }
	    if(num > max_num) {
	        myApp.alert('库存不足');
	        return false;
	    }
	    //$$('.addcart').off('click');
	        
	    $.ajax({
	            url:'/cart/add_to_cart',
	            data:{sub_id:sub_id,num:num,limit_sale:1,rnd:new Date().getTime()},
	            dataType:'json',
	            type:'POST',
	            success:function(result){
	                if(result.err == 2){
	                    checkLogin(false);
	                    return;
	                }
	                if(result.msg) myApp.alert(result.msg);
	                if(result.err) return;
	                update_cart_num();
	                $(".notifications>ul").empty();
					myApp.addNotification({
							hold: 1500,
							additionalClass: 'middle',
	                        custom: '<p style="padding:30px;text-align:center">加入购物车成功</p>',
	                        onClose: function(){},
	                        closeOnClick: true

	                    });
	                //location.href='/cart';
	                
	                myApp.closePanel();
	            }
	    });
	}
	update_cart_num();
	//default value set to 1
	var num_input = $$('.buy_num');
	var buy_size = $$('.buy_size.new-selet').text();

	$$(document).on('click', '.buy_size', function(e) {
		var _this = $$(this);
		$(this).parent().children('.buy_size').removeClass('new-selet');
		_this.addClass('new-selet');
	    $$("#cur_sub_id").val(_this.attr("data-id"));
	    $$("#cur_sub_num").val(_this.attr("data-val"));
	    $$("#choosed").html(_this.html());
	    $$("#choosed_num").html('x 1');
	    num_input.val(1);
	});

	function check_buy_num(type) {
		var buy_num = parseInt(num_input.val());
	    var v_max = $$("#cur_sub_num").val();
	    if (v_max < 1) {
			myApp.addNotification({
					hold: 1500,
					additionalClass: 'middle',
                    custom: '<p style="padding:30px;text-align:center">该商品已卖光</p>',
                    onClose: function(){},
                    closeOnClick: true

                });
			return;

	    };
		switch(type) {
			case '+':
				++buy_num;
			break;
			case '-':
				--buy_num;
			break;
			default:
			break;
		}

		if (isNaN(buy_num)) {
			buy_num = 1;
		};
		if (buy_num > v_max) {
			buy_num = v_max		
		};

		if (buy_num <= 0) {
			buy_num = 1;		
		};	
		num_input.val(buy_num);
	        $$("#choosed_num").html('x '+buy_num);
	}

	$$(document).on('click', '.btn-plus', function(e) {
		check_buy_num('+');
	});

	$$('.buy_num input[name="number"]').on('blur', function(e) {
		check_buy_num();
	});


	$$(document).on('click', '.btn-minus', function(e) {
		check_buy_num('-');
	});

	function change_collect_status() {
	     $$('.new-heart').addClass('new-heart-active');
	}

	//Iliuya 表示评价	
	$$(document).on('click', '.Iliuya, .Ixunjia', function(e){
	    if(!checkLogin(false)){
	        return false;
	    }
	    if($$(this).hasClass('Ixunjia')) {
	        $$('.popup-Iliuya .navbar .center').text('我要询价');
	        comment_type = 4;
	    }

	    if($$(this).hasClass('Iliuya')) {
	        $$('.popup-Iliuya .navbar .center').text('我要评价');
	        comment_type = 2;
	    }

	    $$('.popup-Iliuya-content textarea').trigger('focus');
	    myApp.popup('.popup-Iliuya');
	})

	function setProParams(){
		var html = "<dl class='chanpingshuoming'>";
		if (product_additional.package_name) {
			html += "<dt>" + '包装显示名称：' + "</dt>"
			+ "<dd>" + product_additional.package_name + '</dd>'
		};	    	

		if (product_additional.product_weight && parseFloat(product_additional.product_weight)) {
			html += "<dt>" + '商品毛重：' + "</dt>"
			+ "<dd>" + product_additional.product_weight + 'g' + '</dd>'
		};
		
		if (product_additional.brand_name) {
			html += "<dt>" + '品牌：' + "</dt>"
			+ "<dd>" + product_additional.brand_name + '</dd>';
		};

		if (product_additional.medical1 && product_additional.medical2) {
			html += "<dt>" + '医械类别' + "</dt>"
			+ "<dd>" + product_additional.medical2 + ' ' + product_additional.medical1 + '</dd>'
		} else if(product_additional.medical1 == 0) {
			+ "<dd>" + '非医疗机械' + '</dd>'
		}
		
		if (product_additional.register_no) {
			html += "<dt>" + '注册证号：' + "</dt>"
			+ "<dd>" + product_additional.register_no + '</dd>'
		};

		if (product_additional.product_name) {
			html += "<dt>" + '产品名称：' + "</dt>"
			+ "<dd>" + product_additional.product_name + '</dd>';
		}
		
		if (product_additional.standard) {
			html += "<dt>" + '产品标准' + "</dt>"
			+ "<dd>" + product_additional.standard + '</dd>'
		};
		
		if (product_additional.property) {
			html += "<dt>" + '产品性能结构及组成' + "</dt>"
			+ "<dd>" + product_additional.property + '</dd>'
		};
		
		if (product_additional.scope) {
			html += "<dt>" + '适用范围' + "</dt>"
			+ "<dd>" + product_additional.scope + '</dd>'	
		};	    	
		
		html += "</dl>";
		var tips = '<div style="text-align:center">暂无内容</div>';
		if (!$$(html).text()) {
			$$('.tab-cotent > ul > li').eq(3).html(tips);	
		} else {
			$$('.tab-cotent > ul > li').eq(3).html(html);	
		}
		
	}

	
	
	//页面初始化
	setProParams();

	$$('.tab-tab li').click(function(){

		var new_index = $$(this).index();
		$$('.tab-tab li a').removeClass('new-selet');
		$$('.tab-tab li').eq(new_index).find('a').addClass('new-selet');
		$$('.tab-cotent > ul > li').hide();	
		$$('.tab-cotent > ul > li').eq(new_index).show();	
		$('html, body').animate({scrollTop:0}, 'slow');
	});

    $$('.alert-text-title').on('click', function () {
        if ($$('.picker-modal.modal-in').length > 0) {
            myApp.closeModal('.picker-modal.modal-in');
        }
        myApp.pickerModal(
            '<div class="picker-modal" style="height: 200px;">' +
                '<div class="toolbar">' +
                    '<div class="toolbar-inner">' +
                        '<div class="left"></div>' +
                        '<div class="right"><a href="#" class="close-picker" style="color:#ffffff;right:20px;">我知道了</a></div>' +
                    '</div>' +
                '</div>' +
                '<div class="picker-modal-inner">' +
                    '<div class="content-block">' +
                        '<p style="color:#17A1E5;text-align:center;">使用规则</p>' +
                        '<p style="color:#666666;margin-left:50px;">1.每单限用一张，一次性使用不找零</p>' +
                        '<p style="color:#666666;margin-left:50px;">2.购买时未使用则视为自动放弃</p>' +
                        '<p style="color:#666666;margin-left:50px;">3.本券不可退换，过期作废</p>' +
                        '<p style="color:#666666;margin-left:50px;">4.本代金券与账户绑定，不可转让</p>' +
                    '</div>' +
                '</div>' +
            '</div>'
        )
    });                  

	myApp.upscroller('回到顶部');
</script>

<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>

<!-- 页面逻辑 end-->

<?php
include APPPATH . "views/mobile/common/meiqia.php";
include APPPATH . "views/mobile/footer.php";
 ?>