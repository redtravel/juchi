<?php
include APPPATH . "views/mobile/header.php";
 ?>

<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/swiper.min.css?v=version')?>">
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('mobile/css/tabs.css?v=version'); ?>" media="all"  charset="utf-8" />
<style>
.navbar-inner {
-webkit-box-pack: start;
-ms-flex-pack: start;
-webkit-justify-content: flex-start;
justify-content: center;
}

.page {
	background-color: #0D7AA5;
	color: white;
}
.showClass {
	display: block;
}
.paddding-left-title {
	padding-left: 1.5em;
}
.paddding-left-sub-title {
	padding-left: 2.2em;
}
.paddding-top-detail {
	padding-top: 2px;
}
.title-font {
	font-size: 1.2em;
}
div > div.col-20 > div > div > a.active {
	background-color: rgba(49, 141, 178, 0.7);
}
.swiper_box1 {
	height: 40vh;
}
.vcenter {
	display: -webkit-box;
	-webkit-box-orient: horizontal;
	-webkit-box-pack: center;
	-webkit-box-align: center;
	display: -moz-box;
	-moz-box-orient: horizontal;
	-moz-box-pack: center;
	-moz-box-align: center;
	display: -o-box;
	-o-box-orient: horizontal;
	-o-box-pack: center;
	-o-box-align: center;
	display: -ms-box;
	-ms-box-orient: horizontal;
	-ms-box-pack: center;
	-ms-box-align: center;
	display: box;
	box-orient: horizontal;
	box-pack: center;
	box-align: center;
}
.justify {
	display: -webkit-box;
	display: -webkit-flex;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-pack: justify;
	-webkit-justify-content: space-between;
	-ms-flex-pack: justify;
	justify-content: space-between;
}
.swiper-slide ul {
	margin: 10px auto;
}
.swiper-item {
	width: 100%;
	
	position: relative; background-color:#fff;
}

.swiper-item-mc{  padding-left: 5px; color: #666;
    font-size: 1em;
    line-height: 18px;
    text-align: left; margin-top: 5px;
    padding: 0 4px;
    height: 38px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.swiper-slide {
	
	font-size: 0.9em;
	background: #fff;
	/* Center slide text vertically */
	
	display: -webkit-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	-webkit-box-pack: center;
	-ms-flex-pack: center;
	-webkit-justify-content: center;
	justify-content: center;
	-webkit-box-align: center;
	-ms-flex-align: center;
	-webkit-align-items: center;
	align-items: center;
	background-color: #0D7AA5;
}
.swiper-item {
	width: 100%;
	position: relative;
}
.swiper-item img {
	width: 100%;
}

.swiper-slide > a > img {
	width:100%;
}


.certification_box ul {
	display: flex;
	flex-flow: row;
}
.certification_box ul li {
	flex: 2;
	text-align: center;
}
.size {
	background: white;
	height: 3em;
	line-height: 3em;
	color: black;
}
.brand-name {
	font-size: 1.2em;
}
.register-code { color: #c1c4c6; font-size: 1.0em;

}
.yueya_shop_box { width:100%;}
.yueya_shop_box .yueya_shop_price { padding:0 10px; font-size: 1.2em; font-family: "Myriad Pro", Helvetica, Arial,"Microsoft YaHei";}
.shop_price,
.shop_price_youhui {
	font-size: 1.4em;
	color: orange; 
}
.market_price {
	padding-left: 10px;
	font-size: 0.8em;
}


.comment_item_r em{ font-style:normal; font-weight:normal; font-size:0.8em;}
.comment_item_r strong{ font-weight:normal; }
.comment_reply h2{font-size:1.2em; }

.chanpingshuoming dt{ margin-top:15px; font-weight:bold; font-size:1.2em;}
.chanpingshuoming dd{ margin-bottom:10px;}

</style>

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
			<div class="center">问题留言</div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	
		<h4 style="color:#fff; text-align:center; padding:20px 0 0 10px;">请确认以下联系方式，便于客服及时联系您！</h4>
		<div class="list-block liuyan-box-info" style="margin:10px 0;">
			<ul>
			  <li>
			    <div class="item-content">	
			      <div class="item-media"><i class="user-tracey"></i></div>		      
			      <div class="item-inner"> 			        
			        <div class="item-input">
			          <input type="text" placeholder="姓名：" name="name" class="" style="background:white; padding-left:5px;" value="<?php echo $user_name?>">
			        </div>
			      </div>
			    </div>
			  </li>
			  
			  <li>
			    <div class="item-content">	
			    <div class="item-media"><i class="phone-ico-hu"></i></div>		      
			      <div class="item-inner">			        
			        <div class="item-input">
			          <input type="tel" placeholder="联系方式：" name="mobile" style="background:white; padding-left:5px;" value="<?php echo $mobile?>">
			        </div>
			      </div>
			    </div>
			  </li>
			  
			</ul>
           </div> 
            <div class="popup-Iliuya-content">
	    <div class="order-details-rr">
		 <textarea name="popup-liuya-content" class="liuyan-hu" placeholder='请填写您关于产品的问题，提交后，我们尽快请专家整理答案回复于您，并有可能将此问题，录入产品的”说明“选项中' onfocus="this.style.color='#000'; this.value='';" style="color: #f00;">
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

<div class="popup popup-tabs-pannel tab_hu">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : ''?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content hu-tck"></div>
</div>

<div class="popup popup-detail1 tab_hu">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : ''?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content hu-tck">
		<?php echo $p->detail1;?>
	</div>
</div>
<div class="popup popup-detail2 tab_hu">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : ''?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content hu-tck">
		<?php echo $p->detail2;?>
	</div>
</div>
<div class="views">
	<!-- Your main view, should have "view-main" class-->
	<div class="view view-main">

		<!-- 询价的toolbar -->
		<div class="toolbar">
			<div class="toolbar-inner row no-gutter">

				<?php if( !empty($collect_data) && deep_in_array($p->product_id, $collect_data)) { ?>
				<div class="col-20 heart-hu-red">
					<a class="link" href="#"></a>
				</div>
				<?php }else{ ?>
				<div class="col-20 heart-hu-gray" onclick="add_to_collect (<?php echo $p ->product_id; ?>,0,this,'heart-hu');" >
					<a class="link" href="#"></a>
				</div>
				<?php } ?>

				<?php if ( $p->price_show ): ?>
				<div class="col-20 questions-hu tiwen Iliuya">
					<a class="link" href="#"></a>
				</div>
				<div class="col-60 registration-hu Ixunjia">
					<a class="link" href="#">我要询价</a>
				</div>
				<?php else: ?>
				<div class="col-20 questions-hu tiwen Iliuya">
					<a class="link" href="#"></a>
				</div>
				<div class="col-60 registration-hu xunjia">
				<?php if(isset($p->is_zhanpin) && $p->is_zhanpin): ?>
				<a class="link" href="#" disabled="disabled">加入购物车</a>
				<?php else: ?>
					<a class="link open-panel" href="#" data-panel="right">加入购物车</a>
				<?php endif; ?>
				</div>
				<?php endif; ?></div>
		</div>
		<!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
		<div class="pages">
			<!-- Index Page-->
			<div data-page="index" class="page">

				<!-- navbar -->
				<div class="navbar">
					<div class="navbar-inner">
						<div class="left">
							 <div id="nativeShare"></div>
							<a href="#" class="link back history-back"> <i class="icon icon-back"></i>
							</a>
						</div>
						<div class="center c_name">
							<?php echo $title; ?></div>
						<div class="right">
                                                    <a href="/cart/" class="link icon-only external"> <i class="icon cartico"></i>
                                                    <span class="number number2" id="cart_num">0</span>
                                                    </a>
                                                    
							<a href="javascript:void(0)" class="link icon-only short-func-btn"> <i class="icon icon-bars"></i>
							</a>
						</div>

					</div>
				</div>
				<!-- ends navbar -->
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
				<!-- Scrollable page content-->
				<div class="page-content edu-fot">

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
                                <!-- 产品售价信息-->
					<div class="educational-training">
						<div class="edc-dw">
						     <div class="pd-hu">
						          <h2><?php echo $p->brand_name . ' ' . $p->product_name?></h2>
							  <div class="register-code" style="height:1em"><?php echo isset($p->pack_method) && $p->pack_method ? '包装方式：' . $p->pack_method : ''; ?></div>
						     </div>
						    <div class="browse browse2"><span><?php echo get_page_view('product',$p->product_id)?></span></div>
				                </div>
				      </div>
			     <!-- 产品售价信息-->
			     <!-- 品牌厂商-->
			            <div class="hu-manufacturers open-popover" data-popover=".popover-brand">
				        <div class="order-details-rr">
				         <a href="#"><span>品牌厂商：<?php echo $p->brand_name ? $p->brand_name : ""; ?></span></a>
					</div>
				    </div>
			     <!-- 品牌厂商-->	

			     <!-- 产品售价信息-->
			     <?php if ( !$p->price_show ): ?>
                    <div class="yueya_shop_box">
					    <div class="yueya_shop_price">
					    	
					         演示站售价：
					          <span class="shop_price">&#65509;<?php echo $p ->product_price ? $p->product_price : ""; ?></span><s class="market_price">&#65509;<?php echo $p ->market_price ? $p -> market_price : ""; ?></s>
					      	
						  <div class="yueya_shop_price_add"><?php echo $p->subhead?></div>
					   </div>
				    </div>
                             <!--  产品售价信息 -->

				<!-- 产品小标 -->
			        <section>
				        <div class="certification"><img src="<?php echo static_style_url('mobile/img/pro-xq-pic.jpg');?>"></div>
				    </section>
		        <!-- ends 产品小标 -->
		       <?php endif;?>

			<!-- 产品简介 -->
			<section class="hu-detail-product">
				 <div class="juli-plick">
				 <span class="hu-xq-js">产品简介：<?php echo $p->product_desc ? $p->product_desc: ''?></span>
				      
				 </div>
			</section>
                       <!-- 产品简介 -->
		       
		       <!-- 规格和数量 -->
			<section class="hu-detail-product size <?php echo isset($p->is_zhanpin) && $p->is_zhanpin ? '' : 'open-panel'?>" data-panel="right" style="margin-bottom:12px;">
			       <div class="order-details-rr">
			            <div class="not-paid-wr clearfix">
				         已选择：
                                         <?php if($default_sub_id > 0): ?>
				          <span id="choosed" class="choosed"><?=$sub_list[$color_id]['sub_list'][$default_sub_id]->size_name?></span>
                                          <span id="choosed_num">x 1</span>
					 <?php endif; ?>
				    </div>
				    <div class="not-paid-jt2"></div>
				    
			       </div>
			</section>
			<!-- 规格和数量 -->

			<!-- tabbars 分类介绍：详情，说明 ， 测评，视频-->			
				<div class="tabbar buttons-row">
						<a class="tab-link button open-popup" data-fun="xiangqing" href="#tab1" data-popup=".popup-detail1"> 图文详情 </a> <a class="tab-link button" data-fun="shuoming" href="#tab2"> 注册证件 </a> <a class="tab-link button  open-popup" data-fun="ceping" href="#tab3"  data-popup=".popup-detail2"> 测评视频 </a> <a class="tab-link button" data-fun="pingjia" href="#tab4"> 留言评论 </a>
				</div>
			
			
			
			<!-- 关联产品 -->
			<div class="hu-detail-product hu-glcps">
			     <div class="order-details-rr ">关联产品</div>
			
			</div>

			<!-- Swiper -->
			<div class="swiper-container swiper-container-link-products" style="margin:10px 0;">
				<div class="swiper-wrapper">
					<?php foreach ($link_product_list as $k =>$v):?>
						<a class="swiper-slide external" product_id="<?php echo $v ->product_id; ?>" href="/pdetail-<?php echo $v ->product_id; ?>" style="display:block">
							<div class="swiper-item">
								<div><img src="<?php echo img_url($v ->img_url); ?>" alt=""></div>
								<div class="swiper-item-mc"><?php echo $v->brand_name  . ' ' . $v->product_name;?></div>
								<!-- <div><?php echo $v->product_name?></div> -->
							</div>
						</a>
						
					<?php endforeach; ?>
				</div>
				<!-- Add Pagination -->
				<!-- <div class="swiper-pagination swiper-pagination-link-products"></div> -->
			</div>
			<!-- ends 关联产品 -->
		<!-- 备用 购物须知和购物指南 -->
		<!-- <div style="padding:left;10px;padding-right:10px;margin-top:10px;margin-bottom:10vh;position:relative">
		<div>购物指南：</div>

		<div>
			<a href="#" class="button" style="color:black;margin:10px auto;width:40%;background:rgba(255,255,255,0.5);border-radius:5px">购物须知</a>
		</div>
	</div>
	-->
	<!-- ends 备用 购物须知和购物指南-->
</div>
</div>
</div>
</div>
</div>

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
<!-- Right Panel with Cover effect -->
<div class="panel panel-right panel-cover">
<div class="toolbar">
<div class="toolbar-inner row">
<?php if ( $p->price_show ): ?>
<div class="right col-100 registration-hu Ixunjia">
	<a class="link" href="#" style="color:#fff;">我要询价</a>
</div>
<?php else: ?>
<div class="right col-100">
<a href="#" class="link" onclick="add_to_cart();" style="color:#fff;">加入购物车</a>
</div>
<?php endif;?>
</div>
</div>
<div class="content-block">
<div class="row"  style="margin-top:-20px; padding-left:6px; border-bottom:1px solid silver;">
<div class="col-33">
<img src="<?php echo img_url($g_list[$color_id]['default']->img_url).'.85x85.jpg';?>" alt=""></div>
<div class="col-66">
商品名称：
<?php echo $title; ?>
<br/>
价格：
<?php 
	if ($p->price_show) {
		echo "请询价";
	} else {
		echo $p->product_price;		
	}
?>
</div>
</div>
<div class="row" style="padding:15px 8px;">
<div class="col-20">规格</div>
<div class="col-80">
    <input type="hidden" id="cur_sub_id" value="<?=($default_sub_id > 0) ? $sub_list[$color_id]['sub_list'][$default_sub_id]->sub_id : 0;?>">
    <input type="hidden" id="cur_sub_num" value="<?=($default_sub_id > 0) ? $sub_list[$color_id]['sub_list'][$default_sub_id]->sale_num : 0;?>">
<?php if(isset($sub_list) && !empty($sub_list)):?>
<?php foreach($sub_list[$color_id]['sub_list'] as $k => $v):?>
<a href="javascript:void(0)" class="link-check<?php if(!$v->sale_num): ?> sale-no<?php else: ?> buy_size <?php echo $default_sub_id == $k ? 'active' : ''?><?php endif; ?>
	" data-id="<?=$v->sub_id?>" data-val="<?=$v->sale_num?>">
	<?php echo $v->size_name?></a>
<?php endforeach; ?>
<?php endif; ?></div>
</div>
<div class="row" style="padding:15px 8px;">
<div class="col-20">数量</div>
<div class="col-80 row buy_num">
<div class="col-20">
	<a href="javascript:void(0)" class="button button-fill button-raised btn-minus">-</a>
</div>
<div class="col-20">
	<input type="text" name="number" value="1" style="width:100%;height:34px; text-align:center; border:none; border:solid 1px #ccc;"/>
</div>
<div class="col-20">
	<a href="javascript:void(0)" class="button button-fill button-raised btn-plus">+</a>
</div>
<div class="col-40"></div>
</div>
</div>
</div>
</div>

<script>
	var tag_id = product_id = '<?php echo $p ->product_id ? $p -> product_id : 0; ?>';
	var tagType = 1; //代表产品
	var comment_type = 1;//代表咨询
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

<?php
include APPPATH . "views/mobile/common/footer-js.php";
 ?>


<script>
	$(function (){
	    var list = $(".navbar .center");
	    list.each(function(index, element) {
	        var str = $(this).text();
	        $(this).text(cutStr(str, 22)+'...');
	    });
	    $('.toolbar.tabbar').remove(); 
	});
	
</script>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/swiper.min.js?v=version'); ?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/tabs.js?v=version'); ?>"></script>

<script type="text/javascript">var jiathis_config = {
	summary: "",
	pic: "http://e.hiphotos.baidu.com/image/w%3D400/sign=3599991bc45c1038247ecfc28210931c/8435e5dde71190efaa0f0bdecc1b9d16fdfa603f.jpg",
	shortUrl: false,
	hideMore: true
}
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
});</script>
<script>//		$$('.demo-picker-modal').on('click', function () {
//           myApp.pickerModal('.picker-modal-demo');
//      });
req = function(path, success, error) {
	return $$.ajax({
		url: path,
		dataType: 'json',
		success: success,
		error: error
	});
};
</script>

<script>//default value set to 1
var num_input = $$('.buy_num input[name="number"]');
var buy_size = $$('.buy_size.active').text();

$$(document).on('click', '.buy_size', function(e) {
	var _this = $$(this);
	$(this).parent().children('.buy_size').removeClass('active');
	_this.addClass('active');
        $$("#cur_sub_id").val(_this.attr("data-id"));
        $$("#cur_sub_num").val(_this.attr("data-val"));
        $$("#choosed").html(_this.html());
        $$("#choosed_num").html('x 1');
        num_input.val(1);
});

function check_buy_num(type) {
	var buy_num = parseInt(num_input.val());
        var v_max = $$("#cur_sub_num").val();
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

function add_to_cart () {
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
    if(!num)
    {
        myApp.alert('请输入购买数量');
        return false;
    }
    if(num < 1 || num > max_num) {
        myApp.alert('请输入购买数量');
        return false;
    }
        
    $.ajax({
            url:'cart/add_to_cart',
            data:{sub_id:sub_id,num:num,rnd:new Date().getTime()},
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
                myApp.alert('加入购物车成功');
                //location.href='/cart';
            }
    });
}
update_cart_num();


// 设置 表格  宽度 
	$$(".hu-xq-js table").css("width","100%");
	$$(".hu-xq-js table").attr("cellpadding",0);
	$$(".hu-xq-js table").attr("cellspacing",0);
	$$('#kefu').on('click', function(e){
	    myApp.confirm('服务时间 9:00-21:00', '现在拨打热线', function () {
            $$('<a href="tel://4009905920">拨打电话</a>').click();            
        });
	});
</script>

<?php
include APPPATH . "views/mobile/footer.php";
 ?>