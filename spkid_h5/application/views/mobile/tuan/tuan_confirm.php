<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<title>演示站付款</title>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta name="format-detection" content="telephone=no" />
<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
<link rel="stylesheet" href="<?php echo static_style_url('mobile/tuan/tuan.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/tuan/mui.css?v=version')?>">
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/jquery-1.4.4.min.js?v=version')?>"></script>
<style type="text/css">
			.mui-preview-image.mui-fullscreen {
				position: fixed;
				z-index: 20;
				background-color: #000;
			}
			.mui-preview-header,
			.mui-preview-footer {
				position: absolute;
				width: 100%;
				left: 0;
				z-index: 10;
			}
			.mui-preview-header {
				height: 44px;
				top: 0;
			}
			.mui-preview-footer {
				height: 50px;
				bottom: 0px;
			}
			.mui-preview-header .mui-preview-indicator {
				display: block;
				line-height: 25px;
				color: #fff;
				text-align: center;
				margin: 15px auto 4;
				width: 70px;
				background-color: rgba(0, 0, 0, 0.4);
				border-radius: 12px;
				font-size: 16px;
			}
			.mui-preview-image {
				display: none;
				-webkit-animation-duration: 0.5s;
				animation-duration: 0.5s;
				-webkit-animation-fill-mode: both;
				animation-fill-mode: both;
			}
			.mui-preview-image.mui-preview-in {
				-webkit-animation-name: fadeIn;
				animation-name: fadeIn;
			}
			.mui-preview-image.mui-preview-out {
				background: none;
				-webkit-animation-name: fadeOut;
				animation-name: fadeOut;
			}
			.mui-preview-image.mui-preview-out .mui-preview-header,
			.mui-preview-image.mui-preview-out .mui-preview-footer {
				display: none;
			}
			.mui-zoom-scroller {
				position: absolute;
				display: -webkit-box;
				display: -webkit-flex;
				display: flex;
				-webkit-box-align: center;
				-webkit-align-items: center;
				align-items: center;
				-webkit-box-pack: center;
				-webkit-justify-content: center;
				justify-content: center;
				left: 0;
				right: 0;
				bottom: 0;
				top: 0;
				width: 100%;
				height: 100%;
				margin: 0;
				-webkit-backface-visibility: hidden;
			}
			.mui-zoom {
				-webkit-transform-style: preserve-3d;
				transform-style: preserve-3d;
			}
			.mui-slider .mui-slider-group .mui-slider-item img {
				width: auto;
				height: auto;
				max-width: 100%;
				max-height: 100%;
				object-fit: cover;
			}
			.mui-android-4-1 .mui-slider .mui-slider-group .mui-slider-item img {
				width: 100%;
			}
			.mui-android-4-1 .mui-slider.mui-preview-image .mui-slider-group .mui-slider-item {
				display: inline-table;
			}
			.mui-android-4-1 .mui-slider.mui-preview-image .mui-zoom-scroller img {
				display: table-cell;
				vertical-align: middle;
			}
			.mui-preview-loading {
				position: absolute;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				display: none;
			}
			.mui-preview-loading.mui-active {
				display: block;
			}
			.mui-preview-loading .mui-spinner-white {
				position: absolute;
				top: 50%;
				left: 50%;
				margin-left: -25px;
				margin-top: -25px;
				height: 50px;
				width: 50px;
			}
			.mui-preview-image img.mui-transitioning {
				-webkit-transition: -webkit-transform 0.5s ease, opacity 0.5s ease;
				transition: transform 0.5s ease, opacity 0.5s ease;
			}
			@-webkit-keyframes fadeIn {
				0% {
					opacity: 0;
				}
				100% {
					opacity: 1;
				}
			}
			@keyframes fadeIn {
				0% {
					opacity: 0;
				}
				100% {
					opacity: 1;
				}
			}
			@-webkit-keyframes fadeOut {
				0% {
					opacity: 1;
				}
				100% {
					opacity: 0;
				}
			}
			@keyframes fadeOut {
				0% {
					opacity: 1;
				}
				100% {
					opacity: 0;
				}
			}
			p img {
				max-width: 100%;
				height: auto;
			}
			.mui-slider-img-content {
				position: absolute;
				bottom: 10px;
				left: 10px;
				right: 10px;
				color: white;
				text-align: center;
				line-height: 21px
			}
		</style>
</head>
<body>
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/mui.js?v=version')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/mui.zoom.js?v=version')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/mui.previewimage.js?v=version')?>"></script>
<div class="confirm-bg">
     <div class="wrap-space2">
          <div class="confirm-explain">
               <div class="explain-jl">
                    <h3>尊敬的<?php print $wechat_nickname;?>医生,你好!</h3>
                    <p class="yhgz">按照活动优惠规则，报名的最后一步是付款。目前该优惠商品，还剩余<?php print $product_num;?>件，以付款时间为报名先后的依据。</p>
                    <p class="thank">谢谢你参加演示站发起的本次优惠活动。</p>
                    <p class="ceo">演示站CEO 尹红平</p>
                    <p class="ceo">上海欧思蔚奥医疗器械有限公司</p>
               </div>
          </div>
          <div class="cartoon"></div>
          <div class="security">
               <div class="security-tit"><span class="public-tit security-dw"></span></div>
                  <div class="security-nr clearfix">
                       <div class="safe"><span>兔子布克：</span>兔子布克可以保证你的安全</div>
                      <div class="security-tooth"><img src="<?php echo static_style_url('mobile/img/tooth2.png')?>"></div>
                  </div>
          </div>
          
          <div class="security cation">
               <div class="cation-tit"><span class="public-tit cation-dw"></span></div>
               <div class="yueya-js">
                   <p><span>演示站：</span>演示站起源于欧思蔚奥(OSWELL DENTAL) 的“阳澄湖计划”。该计划的核心理念是：打造中国优质牙科正畸产品的一站式出口销售平台，并注册成立演示站，通过电子商务的形式，同步开展国内业务。
                   目前，演示站经过批准，获得了齿科医疗器械材料的B类电商牌照。上海欧思蔚奥医疗器材有限公司是演示站注册成立的载体。</p>
                   <div class="yueya-img">
                        <div class="mui-content-padded"><p><img src="<?php echo static_style_url('mobile/img/zhgengshu.jpg')?>" data-preview-src="" data-preview-group="1" /></p></div>
                   </div>
               </div>
 
      <script>
	
		
      </script>
               
     </div>
          
          
          
          
          
   </div>
</div>

<div class="confirm-white">
    <div class="wrap-space4">
        <a href="javascript:void(0)" class="activity-anniu look-at">看看再说</a><a href="javascript:void(0)" class="activity-anniu buy">付款购买</a>
    </div> 
</div>
<div class="loading">
    <div class="loading-pic"></div>
</div>

<?php include(APPPATH.'views/common/tongji.php'); ?>
</body>

<script>
mui.previewImage();
var product_type = '<?php echo $product_type;?>';
var product_detail_url = '/' + (product_type == 2 ? 'product' : 'pdetail') + '-<?php print $product_id;?>.html';


$(function(){
      $('.look-at').click(function(){
      $(".loading").show();
      location.href = '/';
    });

    $('.activity-anniu.buy').click(function(){
        $(".loading").show();
        location.href = product_detail_url;
    });
})
    


</script>
</html>
