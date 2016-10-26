<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn" xmls:og="http://opengraphprotocol.org/schema">
<head id="ctl00_ctl00_Head2">
    <title><?=@$page_title?><?=PAGE_TITLE_SITE_NAME?></title>
    <meta property="wb:webmaster" content="0ee011016dbd95d9" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge" />	
    <meta name="application-name" content="<?php print PAGE_TITLE_SITE_NAME;?>" />
    <link rel="shortcut icon" href="<?php print base_url()?>favicon.ico" />
	<meta name="author" content="mammytree" />
	<meta name="keywords" content="<?=PAGE_KEYWORDS?>" />
	<meta name="reply-to" content="services@mammytree.com" />
	<meta name="ROBOTS" content="所有" />
	<meta name="description" content="<?=PAGE_DESCRIPTION?>" />
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/main.css")?>" media="all" charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/basic.css")?>" media="all" charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/common_new.css")?>" media="all" charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/layoutFlow.css")?>" media="all" charset="utf-8" />
	 <script type="text/javascript" src="<?=static_style_url("js/jquery.js")?>"></script>
	<script type="text/javascript" src="<?=static_style_url("js/util.js")?>" ></script>
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/order.css")?>" media="all" charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="<?=static_style_url("css/orderCN.css")?>" media="all" charset="utf-8" />
    <script type="text/javascript">
    __PAGE_VALUE=<?=get_page_value()?>;
    var GMKT = GMKT || {};
    GMKT.ServiceInfo = {"ClientLang":"zh-cn", "DateFormat":"yyyy/MM/dd HH:mm:ss", "money_format":"0,000.00", "currency":"\u5143", "culture":"zh-cn", "viewCurrencyCode":"CNY", "LoginCookieName":"GMKT.FRONT", "LangCookieName":"gmktLang", "nation":"CN", "ServerTime":"2013/12/03 10:38:15", "currencyCode":"CNY", "region":"CN"};
    GMKT.DeviceInfo = {"Code":"Windows_NT_5.1::Firerfox::Desktop", "BrowserVersion":"Gecko/20100101 Firefox/24.0", "Kind":"Desktop", "BrowserName":"Firerfox", "DeviceName":"Windows NT 5.1", "OS":""};

    var static_host = '<?=static_style_url("")?>';
    var img_host = '<?=get_img_host(); ?>';
    var base_url = '<?=BASE_URL?>';
</script>
</head>
<body>
	<!--头部开始 -->
	<div id="header">
		<div class="gnb_bar">
            <ul class="lst_lft" id="menu_user" style="display: none;">
				<li class="first" id="logout_view"><a autonewwindow="off" href="/user/logout">退出</a></li>
				<li id="logout_view2"><a autonewwindow="off" href="/user/profile">我的信息</a></li>
				<li id="welcome_msg">[您好 lolipops]</li>
			</ul>
            <ul class="lst_lft" id="menu_visitor">
                <li class="first" id="login_view"><a autonewwindow="off" href="/user/login">登录</a></li>
                <li id="login_view2"><a autonewwindow="off" href="/user/register">免费注册</a></li>
			</ul>
			<div class="util">
				<ul class="lst">
					<li><a autonewwindow="off" href="/user/order">我的订单</a></li>
					<li class="my"><a>我的购物信息</a></li>
					<li class="cart"><a href="/cart" id="head_cart_num">购物袋 0 件</a></li>
					<li><a class="btn_chkout" autonewwindow="off"  href="/cart">结 账</a></li>
				</ul>
			</div>

			<div class="ly_my" style="display:none;">
				<ul class="lst"> 
					<li><a autonewwindow="off" href="/user/order">我的订单</a></li>
					<li><a autonewwindow="off" href="/user/collection">我的收藏</a></li>
					<li><a autonewwindow="off" href="/user/points">我的积分</a></li>
				</ul>
			</div>
		</div>
		
		<div class="gnb">
            <h1 id="ctl00_ctl00_MainContentHolder_left_logo_banner" class="h_logo"><img src="<?php print static_style_url('image/logo.jpg');?>" name="BI_1104" border="0" usemap="#Logo_1104" id="BI_1104"/>
			<map name="Logo_1104">
                <area shape="rect" coords="3,10,134,56" href="<?php print base_url();?>">
			</map></h1>
<? $nav_top_ad = nav_top_ad();
if(!empty($nav_top_ad)):?>
			<div id="ctl00_ctl00_MainContentHolder_right_logo_banner" class="bnnr_area"><a href="<?=$nav_top_ad->ad_link?>" target="_blank"><img src="<?=img_url($nav_top_ad->pic_url)?>" alt="" width="200" height="60" /></a></div>
<?endif?>		
                </div>

		<!-- 菜单内容 -->
		<div class="gnb_menu">
			<div class="fix_w">
	            <ul  class="major">
	                <li class="firstchild cate1"><a autonewwindow="off" href="/">首页</a></li>

			         <? $nav_list = get_nav();
                        $nav_goods_type = get_nav_subtype_brand();
                        if (!empty($nav_goods_type)) {
                            $nav_subtype = $nav_goods_type['nav_subtype'];
                            $nav_brand = $nav_goods_type['nav_brand'];                        
                            $goods_type = $nav_goods_type['goods_type'];
                        }
                        foreach ($nav_list as $k => $nav): ?>
			<li class="cate<?=$k+2?>" index="<?=$k+1?>" hover="on"><a href="/category-<?=$nav['category_id']?>.html" autonewwindow="off"  title="<?=$nav['category_name']?>"><?=$nav['category_name']?></a></li>
                         <? endforeach ?>
                        <li class="cate6" index="6" hover="off"> <a href="/brands.html" autonewwindow="off" title="品牌大全">品牌大全</a></li>
				</ul>
				<div class="ly_sub" id="group_layer" style="display:none;"></div>
			</div>
		</div>
	</div><!-- 头部结束 -->

<script type="text/javascript">
// 菜单数据
//<![CDATA[
var __gnbGroupHtmlList = new Array();
<? if (isset($nav_subtype)) : ?>
var __gnbGroupHtmlList = <?=$nav_subtype?>;
<? endif ?>
// 菜单热卖品牌LOGO数据
var __gnbBrandGroupHtmlList = new Array();
<? if (isset($nav_brand)) : ?>
__gnbBrandGroupHtmlList = <?=$nav_brand?>;
<? endif ?>	
//]]>
</script>
  
<!-- 菜单交互JS -->
<script type="text/javascript">

	$(document).ready(function (e) {

		//$('#ac_layer').css({ "top": ($('#search____keyword').offset().top - 3), "left": (324) });
		if (window.gnbTop && gnbTop.EventBind) gnbTop.EventBind();
		if (window.gnbTop && gnbTop.GnbMenu) gnbTop.GnbMenu();
	});
var gnbTop = {
	EventBind: function () {
		$(".util .my").click(function () {
			$(".ly_my").css({ "left": $(".util .my").position().left + $(".util").position().left });
			$(".ly_my").toggle();
		});
	},
	GnbMenu: function () {
		var ly_sub_delay;

		$(".gnb_menu .major>li").each(function (index) { //프론트에서 index 값으로 제어해서 index 값을 스크립트로 부여
			var idx_major = $(".gnb_menu .major>li").index(this) + 1;
			$(this).attr("index", idx_major++);
		});

		$(".gnb_menu .major li[hover='on']").hover(function () {

			clearTimeout(ly_sub_delay);
			$(".gnb_menu .major li").removeClass("on");
			$(this).addClass("on");

			var idx_major = $(".gnb_menu .major li").index(this);

			if (idx_major > 0) $(".ly_sub").show();
			if ($(".gnb_menu .major").width() == 980) { //main
				if (idx_major + 1 < 5) {
					$(".ly_sub").css({ "left": $(this).position().left });
				} else {
					$(".ly_sub").css({ "left": $(".gnb_menu .major li").eq(4).position().left - 119 + "px" });
				}
			} else {
				if (idx_major + 1 < 4) {
					$(".ly_sub").css({ "left": $(this).position().left });
				} else {
					$(".ly_sub").css({ "left": $(".gnb_menu .major li").eq(3).position().left - 49 + "px" });
				}
			}

			if (idx_major > 0 && idx_major - 1 < __gnbGroupHtmlList.length)
				$("#group_layer").html(__gnbGroupHtmlList[idx_major - 1].html + __gnbBrandGroupHtmlList[idx_major - 1].html);
			else
				$("#group_layer").hide();


		}, function () {
			ly_sub_delay = setTimeout(function () { $(".ly_sub").hide(); $(".gnb_menu .major li").removeClass("on"); }, 100);
		});;

		$(".ly_sub").hover(function () {
			clearTimeout(ly_sub_delay);
			$(".ly_sub").show();
		}, function () {
			$(".gnb_menu .major li").removeClass("on");
			$(".ly_sub").hide();
		});

	}
} 
</script>
