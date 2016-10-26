<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="wb:webmaster" content="1b0aaa11bfcb13ec" />
<meta property="qc:admins" content="155113257762172517756375" />
<meta name="360-site-verification" content="e4559f696acf5850ebd9c08a5a1f7b27" />
<meta name="Keywords" content="<?=PAGE_KEYWORDS?>" />
<meta name="Description" content="<?=PAGE_DESCRIPTION?>" />
<title><?=@$page_title?><?=PAGE_TITLE_SITE_NAME?></title>
<link href="<?=static_style_url("css/tuan2.css")?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?=static_style_url("js/jquery.js")?>"></script>
<script type="text/javascript" src="<?=static_style_url("js/forBasic.js")?>?v=20130913"></script>
<script type="text/javascript" src="<?=static_style_url("js/jquery.lazyload.min.js")?>"></script>
<script type="text/javascript" src="<?=static_style_url("js/cart.js")?>?v=20130913"></script>
<script type="text/javascript" src="<?=static_style_url()?>/js/scrollBar.js"></script>
<script type="text/javascript" src="<?=static_style_url("js/ad.js")?>"></script>
<script type="text/javascript" src="<?=static_style_url("js/tuan.js")?>"></script>
<script type="text/javascript">
    var static_host = '<?=static_style_url("")?>';
    var img_host = '<?=get_img_host(); ?>';
    var base_url = '<?=BASE_URL?>';
</script>

</head>
<body>
<!--头部内容开始-->
<div class="tHeader">
<!--logo层-->
    <div id="tHeadLogo">
    	<div class="mainTopBoxInfoLeft">
			<ul>
				<li><a id="goBaby" href="http://www.baobeigou.com" title="返回<?php print SITE_NAME;?>"></a></li>
				<li><span id="forQuality">品质保障</span></li>
				<li><span id="per100Brand">100%品牌授权</span></li>
				<li><span id="qiDay">7天无理由退货</span></li>
				<li><a href="javascript:addFavorite()" id="addFavirate"  class="main_darkgray_black">点击收藏本站</a></li>
			</ul>   
		</div>
    	
    	<div class="mainTopBoxInfoRight">
    		<ul>
            <!--未登录-->
            <li class="noLeftLine" id='li_login'><a href="/user/login?back_url=%2Findex" class="main_darkgray_black">登录</a></li>
            <li id='li_register'><a href="/user/register" class="main_darkgray_black">快速注册</a></li>
            <!--已登录-->
            <li id="userInfobox" style="display:none">欢迎您， <a href="/user" id="userInfo" class="main_black mainImgUser"><b id="mainIconUser">zhangkaixin1983</b></a>
              <div class="infoPersonalBox" style="display:none;">
                <ul id="myinfo">
                  <li><a href="/user/order" id="myOrder">我的订单</a></li>
                  <li><a href="/user/collection" id="myFav">我的收藏</a></li>
                  <li><a href="/user/points" id="myCount">我的积分</a></li>
                  <li><a href="/user/token" id="myCash">我的现金券</a></li>
                  <li><a href="/user/profile" id="myInfo">我的资料</a></li>
                  <li><a href="/user/logout" id="myQuit">退出账号</a></li>
                </ul>
              </div>
            </li>
            <li>|&nbsp;<a href="/user" target="_blank" class="main_darkgray_black">会员中心</a></li>
            <li style="height:25px; _padding-top:10px; _margin-left:5px;">|&nbsp;<b id="sevOnline" onclick="NTKF.im_openInPageChat('','')">在线客服</b></li>
          </ul>
        </div>
    </div>
    <!--导向层-->
    <div id="tHeadNav">
    	<div class="floatL">
    		<a href="/tuan" title="妈咪团" id="logoMamituan"></a>
    		<ul id="tuanList">
    			<li class="tSpanBox"><span>今日团购</span><s><?=$tuan_today_goods_num?></s></li>
    			<li><div class="main_white" title="全部团购">全部团购</div><s><?=$tuan_all_goods_num?></s></li>
    		</ul>
    	</div>
    	<div class="floatR">
    		<a href="http://www.baobeigou.com" target="_blank" title="品牌特卖"><p id="brandNum" >(<?=$tuan_today_brands_num?>品牌)</p></a>
    		<p id="toyNum" onclick="window.open('http://www.baobeigou.com/zhuanti/shangcheng.html')"></p>
    		<div id="tCartsBox">
    			<span id="mainCartNumTuan" >(0件)</span>
    			<!--已有商品加入购物车-->
    	        <div style="display:none" id="mainCartBox"></div>
    	      
    		</div>
    			<!--没有商品加入购物车-->
    			<!--<div style="display:none;" id="mainCartBoxNull"> <span class="">购物车中还没有商品，赶紧选购吧！</span> </div>-->
    	</div>
   		<div class="icon_new"></div>
    </div>
</div>
<!--头部内容结束-->

<script type="text/javascript">
    update_cart_num ();
NTKF_PARAM = {
  siteid:"kf_9958",
  settingid: "kf_9958_1363574202058",
  itemid: "",
  uid:"",
  uname:"",
  orderid: "",
  orderprice: ""
} 
</script>
