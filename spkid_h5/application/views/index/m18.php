<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript">
//<![CDATA[
// 跑马灯效果数据　畅销精品
var BestSellGoodsList = null;
<? if(isset($hot_goods)):?>
BestSellGoodsList = <?=$hot_goods?>;
<? endif ?>

// 轮播图数据
var bannerItem = null;
bannerItem = <?=json_encode($index_focus_image)?>;
// 今日促销数据
var dailyDealItem = null;
<? if(isset($today_promotions)):?>
dailyDealItem = <?=$today_promotions?>;
<? endif ?>
//麦麦团数据
var groupbuyItem = null;
<? if(isset($hot_sales)):?>
groupbuyItem = <?=$hot_sales?>;
<? endif ?>
// 热卖推荐代表数据
var serverSpecialList = null;
<? if(isset($nav_alias)):?>
serverSpecialList = <?=$nav_alias?>;
<? endif ?>
// 热卖推荐产品详情数据
var goodsList = null;
<? if(isset($recommend_goods)):?>
goodsList = <?=$recommend_goods?>;
<? endif ?>

var pro_no = 101;
var pre_sid = 0;
// 一级分类店铺商品信息
<? foreach ($provider_goods as $k => $goods): ?>
var ShopEventGoodsList<?=$k?>;
ShopEventGoodsList<?=$k?> = <?=json_encode($goods)?>;
<? endforeach ?>
</script>

<script src="<?=static_style_url('js/index.js?20130122104316')?>" type="text/javascript"></script>
<script language="javascript" type="text/javascript" >
	var loadImg = "http://static.image-qoo10.cn/qoo10/front/cm/common/image/ph.png";
</script>
<div id="container">
	<div id="content">
		<!-- SLIDE : PROMOTION -->
        <?if(isset($nav_footer_ad)):?>
        <div id="ctl00_ctl00_MainContentHolder_MainContentHolder_topBnnr" class="bnnr_prmt">
            <a href="<?=$nav_footer_ad->ad_link?>" target="_blank" onfocus="this.blur();">
            <img src="<?=img_url($nav_footer_ad->pic_url)?>" width="980" height="80" border="0">
            </a>
        </div>
        <?endif?>
        <!-- Top Banner, TimeSale, GroupBuy-->
        <div class="sld_prmt">
            <!-- PROMOTION : main banner navi -->
            <div class="visual">
                <div class="photo">
                    <ul id="ulMainbanner" style="position:relative; top:0px;"></ul>
                </div>
                <div class="bg"></div>
                    <ul class="info_area col5" id="mainBannerarea">
                        <?
                        $i = 0; 
                        foreach ($index_focus_image as $k => $row) : 
                        ?>
                        <li <?=(!$i) ? "class=\"on\"" : ""; ?>><a class="ellipsis" href="<?=$row['href']?>" target="_blank" title="<?=$row['title']?>"><?=$row['title']?></a></li>
                        <? $i++; 
                           endforeach; ?>
                    </ul>
                </div>

                <div class="aside">
                    <div class="tab_prmt" id="divTodaysale">
                        <h4>最新上线</h4>
                        <div class="panel">
                        </div>
                    </div>

                    <div class="tab_prmt" id="divTodayGroupBuy">
                        <h4>最近热卖</h4>
                        <div class="panel">
                        </div>
                    </div>
                </div>
            </div><!-- //SLIDE : PROMOTION -->

<script type="text/javascript">
	$(document).ready(function () {
		MainBanner.Init();
		SaleItemEvent.Init();
	});

	//Main banner control
	var ezSetTimeoutVariable = 0;
	var newSetTimeoutVarible = 0;
	var image_display_duration = 200;
	var idex = 0;
	var MainBanner = {
		from: 0,
		to: 1,
		ItemList: '',

		Init: function () {

			if (bannerItem != null) {
				MainBanner.ItemList = bannerItem;
				//MainBanner.to = Math.floor(Math.random() * (MainBanner.ItemList.length - 2)) + 1; //?? random index?

				MainBanner.to = Math.floor(Math.random() * (MainBanner.ItemList.length - 0)) + 1; //?? random index?

				MainBanner.RenderSlide(true); //from?? to?? ????
				$('#mainBannerarea li').mouseover(MainBanner.slideFun); //mouseover binding??
				ControlClassHandle($('#mainBannerarea li').eq(MainBanner.to - 1), 'li', 'on'); //navi?????

			} else {
				MainBanner.ItemList = new Array();
			}


		},
		slideFun: function () {
			if ($(this).hasClass('on')) return;
			MainBanner.to = MainBanner.ItemList.length;
			MainBanner.RenderSlide(false);
			ControlClassHandle(this, 'li', 'on');

			idex = $('#mainBannerarea li').index(this);
			if (ezSetTimeoutVariable > 0) clearTimeout(ezSetTimeoutVariable);
			ezSetTimeoutVariable = setTimeout("$('#ulMainbanner').animate({top: (idex * 338 * -1) + 'px'}, 500);", image_display_duration);

		},
		RenderSlide: function (bisFirst) {
			var htmlBuffer = "";

			if (MainBanner.ItemList.length < 1) {
				return;
			}

			while (MainBanner.from < MainBanner.to) {
				if (MainBanner.ItemList[MainBanner.from] != null) {
					htmlBuffer += '	<li style="display: block;">'
						+ '		<a href="' + MainBanner.ItemList[MainBanner.from].href + '" title="' + MainBanner.ItemList[MainBanner.from].title + '" target=\"_blank\"> '
						+ '			<img resizing="false" width="728" height="338" src="' + MainBanner.ItemList[MainBanner.from].img_src + '" alt="' + MainBanner.ItemList[MainBanner.from].title + '">'
						+ '		</a>'
						+ ' </li>';
				}

				MainBanner.from = MainBanner.from + 1;
			}

			if (bisFirst) {
				$("#ulMainbanner").html(htmlBuffer);
				$('#ulMainbanner').css("top", ((MainBanner.to - 1) * 338 * -1)); //random??? ?? ??.
			} else {
				$("#ulMainbanner").append(htmlBuffer);
			}
		}
	}

	var SaleItemEvent = {
		groupBuyList: '',
		dailyDealList: '',
		from: 0,
		to: 1,
		from1: 0,
		to1: 1,
		firstIdx: -1,
		tabIdx: 0,
		pagingIdx: 0,

		Init: function () {
			//$("#ulTodaySaleTab li").mouseover(SaleItemEvent.TabEvent);

			//daily deal
			if (dailyDealItem != null) {
				SaleItemEvent.dailyDealList = dailyDealItem;
				SaleItemEvent.PagingTab(0);

			}
			else {
				SaleItemEvent.dailyDealList = new Array();
				//$("#ulTodaySaleTab li:eq(1)").css("display", "none");
			}

			//group buy
			if (groupbuyItem != null) {
				SaleItemEvent.groupBuyList = groupbuyItem;
				SaleItemEvent.from1 = 0;
				SaleItemEvent.PagingTab(1);

			}
			else {
				SaleItemEvent.groupBuyList = new Array();
				//$("#ulTodaySaleTab li:eq(2)").css("display", "none");
			}

			/*if (timeSaleItem != null) {
			SaleItemEvent.firstIdx = 0;
			} else */
			//SaleItemEvent.tabIdx = SaleItemEvent.firstIdx;
			SaleItemEvent.RenderSlide(0);
			SaleItemEvent.RenderSlide1(0);
		},
		PagingTab: function (idx) {

			var icount = 0;

			var div_sale_type = "";
			var div_type = "";
			if (idx == 0) {
				div_sale_type = "#divTodaysale";
				div_type = "dd";
			} else {
				div_sale_type = "#divTodayGroupBuy";
				div_type = "gb";
			}

			$(div_sale_type + " div.panel").html("<div class='paging'></div><div class='item'><ul id='ulitemlist" + "_" + div_type + "' style='width:1000%;'></ul></div>");

			/*if (idx == 0) {
			icount = SaleItemEvent.timeSaleList.length;
			} else */
			if (idx == 0) {
				icount = SaleItemEvent.dailyDealList.length;
			}
			else if (idx == 1) {
				icount = SaleItemEvent.groupBuyList.length;
			}
			//alert(icount);
			for (var i = 0; i < icount; i++) {
				if (i == 0) {
					$(div_sale_type + " div.panel div.paging").append("<a class='on'><span>" + (i + 1) + "</span></a>");
				} else {
					$(div_sale_type + " div.panel div.paging").append("<a><span>" + (i + 1) + "</span></a>");
				}
			}
			if (idx == 0) {
				$(div_sale_type + " div.panel div.paging a").mouseover(SaleItemEvent.PagingTabSlide);
			} else {
				$(div_sale_type + " div.panel div.paging a").mouseover(SaleItemEvent.PagingTabSlide1);
			}
		},
		PagingTabSlide: function () {
			if ($(this).hasClass('on')) return;


			ControlClassHandle(this, 'a', 'on');

			SaleItemEvent.from = $("#divTodaysale div.panel div.item ul").children("li").length;

			SaleItemEvent.to = $("#divTodaysale div.panel div.paging a").index(this) + 1;



			SaleItemEvent.RenderSlide(1);
			SaleItemEvent.pagingIdx = $('#divTodaysale div.panel div.paging a').index(this);
			SaleItemEvent.slideFun("dd");

		},
		PagingTabSlide1: function () {
			if ($(this).hasClass('on')) return;


			ControlClassHandle(this, 'a', 'on');

			SaleItemEvent.from1 = $("#divTodayGroupBuy div.panel div.item ul").children("li").length;

			SaleItemEvent.to1 = $("#divTodayGroupBuy div.panel div.paging a").index(this) + 1;

			SaleItemEvent.RenderSlide1(1);
			SaleItemEvent.pagingIdx = $('#divTodayGroupBuy div.panel div.paging a').index(this);
			SaleItemEvent.slideFun("gb");
		},
		slideFun: function (sale_type) {
			if ($(this).hasClass('on')) return;

			/*if (newSetTimeoutVarible > 0) clearTimeout(newSetTimeoutVarible);
			$('#ulitemlist').animate({ left: (234 * SaleItemEvent.pagingIdx * -1) + 'px' }, 100, function () { SaleItemEvent.animate = false; });
			*/

			$("#ulitemlist" + "_" + sale_type + " li").each(function (index) {

				$(this).css("display", "none");
			});
			$("#ulitemlist" + "_" + sale_type + " li:eq(" + SaleItemEvent.pagingIdx + ")").fadeIn(100);
			//$(this).find('img').fadeIn(100);
			//SaleItemEvent.animate = false;
			//newSetTimeoutVarible = setTimeout("$('#ulitemlist').animate({ left: (238 * SaleItemEvent.pagingIdx * -1) + 'px' }, 200);", image_display_duration);

		},
		RenderSlide: function (idx, type) {
			var htmlBuffer = "";
			var item;
			if (SaleItemEvent.length < 1) return;
			item = SaleItemEvent.dailyDealList;
			while (SaleItemEvent.from < SaleItemEvent.to) {
				htmlBuffer = '<li>'
								+ '<a class="thumb" href=' + item[SaleItemEvent.from].href + ' target="_blank"><img resizing="false" width="140" height="140" title="' + item[SaleItemEvent.from].gd_nm + '" alt="' + item[SaleItemEvent.from].gd_nm + '" src="' + item[SaleItemEvent.from].img_src + '"></a>'
				//+ '<div class="ico_type ora">' + item[SaleItemEvent.from].SaleType + '</div>'
								+ '<a class="subject" href=' + item[SaleItemEvent.from].href + ' title="' + item[SaleItemEvent.from].gd_nm + '" target="_blank">' + item[SaleItemEvent.from].gd_nm + '</a>'
								+ '<div class="info">'
				//+ '<div class="left">' + item[SaleItemEvent.from].div_time + '</div>'
									+ '<div class="price ff_thm"><strong>' + item[SaleItemEvent.from].div_price_strong + '</strong></div>'
								+ '</div>'
							+ '</li>';
				if (type == 1) {
					$("#divTodaysale div.panel div.item ul").html(htmlBuffer);
				} else {
					$("#divTodaysale div.panel div.item ul").append(htmlBuffer);
				}

				SaleItemEvent.from = (SaleItemEvent.from + 1);

			}
		},
		RenderSlide1: function (idx, type) {
			var htmlBuffer = "";
			var item;

			item = SaleItemEvent.groupBuyList;
			if (item.length < 1) return;
			while (SaleItemEvent.from1 < SaleItemEvent.to1) {
				htmlBuffer = '<li>'
								+ '<a class="thumb" href=' + item[SaleItemEvent.from1].href + ' target="_blank"><img resizing="false" width="140" height="140" title="' + item[SaleItemEvent.from1].gd_nm + '" alt="' + item[SaleItemEvent.from1].gd_nm + '" src="' + item[SaleItemEvent.from1].img_src + '"></a>'
				//+ '<div class="ico_type ora">' + item[SaleItemEvent.from].SaleType + '</div>'
								+ '<a class="subject" href=' + item[SaleItemEvent.from1].href + ' target="_blank" title="' + item[SaleItemEvent.from1].gd_nm + '">' + item[SaleItemEvent.from1].gd_nm + '</a>'
								+ '<div class="info">'
				//+ '<div class="left">' + item[SaleItemEvent.from].div_time + '</div>'
									+ '<div class="price ff_thm"><strong>' + item[SaleItemEvent.from1].div_price_strong + '</strong></div>'
								+ '</div>'
							+ '</li>';
				if (type == 1) {
					$("#divTodayGroupBuy div.panel div.item ul").html(htmlBuffer);
				} else {
					$("#divTodayGroupBuy div.panel div.item ul").append(htmlBuffer);
				}

				SaleItemEvent.from1 = (SaleItemEvent.from1 + 1);

			}

		}
	}

	function ControlClassHandle(ParentElement, targetElement, className) {
		if ($(ParentElement).hasClass(className)) return;
		$(ParentElement).siblings(targetElement).removeClass(className);
		$(ParentElement).addClass(className);
	}

</script>
    <!-- SECTION : CATEGORY MAP -->
    <div id="ctl00_ctl00_MainContentHolder_MainContentHolder_category_map" class="section_prdsort"><h2>商品分类</h2>
        <? 
        $i = 1;
        $total = count($goods_type);
        foreach ($goods_type as $catid => $row) :
            if ($i > 6) continue; 
        ?>    
        <div class="lst_prdsort<?=($i > $total-2) ? " col4" : "";?>">
            <div class="lst">
                <h3 style="height: 59px;" ><a href="/category-<?=$catid?>.html" target="_blank"><?=$row['cat_name']?></a></h3><!-- 大类 -->
                <ul>
                    <? foreach ($row['goods_type'] as $type) : ?>
                    <li><a href="/category-<?=$type['category_id']?>.html" title="<?=$type['category_name']?>" target="_blank"><?=$type['category_name']?></a></li>
                    <? endforeach; ?>
                </ul>    
            </div>
            <? if ($i == $total-1) : ?>
            <!-- 商品分类右下角广告位 -->
            <div class="lst rgt html_area">
                <? if(isset($category_top_ad)):?>
                <?=$category_top_ad->ad_code?>
                <?endif;?>
            </div>
            <? endif ?>
            <? if ($i == $total) : ?>
            <!-- 商品分类右下角广告位 -->
            <div class="lst rgt html_area">
                <?if(isset($category_footer_ad)):?>
                <?=$category_footer_ad->ad_code?>
                <?endif;?>
            </div>        
            <? endif; ?>
        </div>
        <? 
        $i++;
        endforeach; 
        ?>
    </div>
	<!-- //SECTION : CATEGORY MAP -->

    <!-- SECTION : BRAND LIST & NOTICE-->
	<div id="ctl00_ctl00_MainContentHolder_MainContentHolder_group_aside" class="group_aside" style="height:272px;">
        <div id="ctl00_ctl00_MainContentHolder_MainContentHolder_ul_brand_list" class="section_hotbrand">
            <h2>热卖品牌</h2>
			<a target="_blank" class="lnk_all" href="/brands.html">[ 全部 ]</a>
            <ul>
                <?php foreach ($hot_brand_arr as $brand) : ?>  
                <li><a target="_qlink" href='/brand-<?=$brand['brand_id']?>.html' title='<?=$brand['brand_name']?>'><img src="<?=img_url($brand['brand_logo'])?>" alt='<?=$brand['brand_name']?>' width="110" height="52" /></a></li>
                <?php endforeach ?>
            </ul>
        </div>
		<div class="notice_area">
            <h2><a href="/article-63.html" target="_blank">通知</a></h2>
            <div class="lst">
                <a class="more fs_11" href="/article-63.html" target="_blank">更多</a>
                <ul>
                    <?php foreach ($notice as $a): ?>
                    <li class="firstchild" title="<?php print $a->title; ?>">
                        <a href="<?php print $a->url?$a->url:"article-{$a->article_id}.html" ?>" target="_blank" class="ellipsis"><?php print $a->title; ?></a>
                    </li>
                    <?php endforeach?>
                </ul>
            </div>
		</div>
	</div> 

	<!-- SECTION : BEST -->
	<div class="section_best" onselectstart="return false;">
		<h2 class="h_main">畅销精品 <em class="en ff_thm">BEST SELLER</em></h2>
		<ul class="nav_cate" id="bestseller_tab">
            <? foreach($nav_list as $nav): ?>        
            <li><a href="/category-<?=$nav['category_id']?>.html"><?=$nav['category_name']?></a></li>
            <? endforeach ?>       		        
		</ul>

		<div class="group_lst">
			<div class="lst" id="bestseller_slide">
				<ul style="left:-895px; width:1000%;"></ul>
			</div>

			<div class="paging_area" id="BestSellPaging">
				<a class="on"><span>1</span></a>
				<a><span>2</span></a>
				<a><span>3</span></a>
				<a><span>4</span></a>
			</div>

			<a class="btn_prv on" id="bestseller_prev">Prev</a>
			<a class="btn_nxt on" id="bestseller_next">Next</a>
		</div>
	</div><!-- //SECTION : BEST -->

	<div class="section_rcmd">
		<h2 class="h_main"><a style="cursor:default">热卖推荐</a></h2>
		<ul class="nav_cate">
            <li>
			<!--<a href="http://list.m18.com/gmkt.inc/Special/Special.aspx?sid=3401&banner_no=167647" title="双十一意犹未尽？不用等明年！">双十一意犹未尽？不用等明年！</a>-->
			</li>
		</ul>
    <div class="spot_tplt7" >
        <ul class="lst col7" id="ul_featuredEvent"></ul>
    </div>


	<div class="ly_tplt7" style="display: block;"  id="div_featured_goods">
		<!-- isM18 1??? qoo10, 2?? m18 type -->
		<!--
		<div class="glr">
		<div class="glr glr_v1">
		 -->
		 <div class="glr" onselectstart="return false;">
			<div class="scroll">
				<ul class="col5" style="width:1000%;overflow:visible;left:-182px;" id="ul_goods"></ul>
			</div>

			<a class="btn_prv" id="featrued_prv">Prev</a>
			<a class="btn_nxt" id="featrued_nxt">Next</a>
		</div>
	</div>
<script type="text/javascript">
	$(document).ready(function () {
		//Featured event
		SIDGroupBar.Init();
	});

	var SIDGroupBar = {
		SpecialList: "",
		GoodList: "",
		animate: false,
		pageItemCount: 0,
		ItemNextIndex: 0,
		ItemPretIndex: 0,
		widthPix: 182,
		sidTabInx: 0,
		ItemCount: 0,
		ItemIndex1: 0,
		ItemIndex2: 0,

		Init: function () {
			if (typeof (serverSpecialList) != 'undefined' && serverSpecialList != null) {
				SIDGroupBar.SpecialList = serverSpecialList;
			}
			else {
				SIDGroupBar.SpecialList = new Array();
				return false;
			}
			if (typeof (goodsList) != 'undefined') {
				SIDGroupBar.GoodList = goodsList;
			} else {
				SIDGroupBar.GoodList = new Array();
			}

			SIDGroupBar.pageItemCount = 5;

			SIDGroupBar.RenderHtml();


			$("#ul_featuredEvent li").hover(function () {
				$(this).siblings().removeClass("on");
				$(this).addClass("on");

				SIDGroupBar.sidTabInx = $('#ul_featuredEvent li').index(this);
				//var idx = $('#ul_SIDGroupBar li').index(this);

				SIDGroupBar.ItemCount = SIDGroupBar.GoodList[SIDGroupBar.sidTabInx].length;

				//SIDGroupBar.ItemCount = item.length;

				if (SIDGroupBar.ItemCount > 0) {
					SIDGroupBar.ItemIndex = 0;
					$('#ul_goods').css({ "left": "-182px" });
					SIDGroupBar.InitGoodsImgTag(SIDGroupBar.sidTabInx);

					if ("M" != "M") {
						$(".ly_tplt7").show();

						$(".ly_tplt7").css({ "top": $(this).position().top + 65 });
						$(".ly_tplt7").hover(function () {
							$(this).show();
						}, function () {
							$(this).hide();
						});
					}
				} else {
					$(this).addClass("none");
					$('#featrued_prv').unbind('click');
					$('#featrued_nxt').unbind('click');
				}
			}, function () {
				if ("M" != "M") {
					$(this).removeClass("on");
					$(".ly_tplt7").hide();
				}
			});

			if ("M" == "M") {
				SIDGroupBar.sidTabInx = 0;
				//var idx = $('#ul_SIDGroupBar li').index(this);
				SIDGroupBar.ItemCount = SIDGroupBar.GoodList[SIDGroupBar.sidTabInx].length;
				//SIDGroupBar.ItemCount = item.length;

				if (SIDGroupBar.ItemCount > 0) {
					SIDGroupBar.ItemIndex = 0;
					$('#ul_goods').css({ "left": "-182px" });
					SIDGroupBar.InitGoodsImgTag(SIDGroupBar.sidTabInx);
				} else {
					$(this).addClass("none");
					$('#featrued_prv').unbind('click');
					$('#featrued_nxt').unbind('click');
				}
			}

			$("#div_featured_goods").mouseleave(function () {
				if ("M" != "M") {
					$("#ul_featuredEvent li:eq(" + SIDGroupBar.sidTabInx + ")").removeClass("on");
				}
			});
			$("#div_featured_goods").mouseover(function () {
				$("#ul_featuredEvent li:eq(" + SIDGroupBar.sidTabInx + ")").addClass("on");
			});

		},
		RenderHtml: function () {
			var htmlBuffer = "";
			var gidParam = "";


			if (SIDGroupBar.SpecialList == null || SIDGroupBar.SpecialList.length < 1) return;

			var paramsid = "";
			var titleimg = 0;
			var paramlist = "";
			var paramSellerQid = "0";
			var control_type = "M";

			if (control_type != "M") {
				paramlist = window.location.href.split('?')[1];
				if (("&" + paramlist).split('&sid=').length > 1) {
					paramsid = "0";
				} else {
					paramlist = window.location.href.split('?')[0];
					paramsid = "0";
				}
			}

			var bool_selected = false;
			for (var i = 0; i < SIDGroupBar.SpecialList.length; i++) {
				
				var contents_type = "";

				if (typeof (SIDGroupBar.SpecialList[i].contents_type) != 'undefined' && SIDGroupBar.SpecialList[i].contents_type != null)
					contents_type = SIDGroupBar.SpecialList[i].contents_type;
					

				//??, qid ? sid?? ??? ??? ??
				if (contents_type == "minishop")
					bool_selected = SIDGroupBar.SpecialList[i].minishop_seller_qid == paramSellerQid ? true : false;
				else
					bool_selected = SIDGroupBar.SpecialList[i].sid == paramsid ? true : false;

				if (i == 0) {

					if (bool_selected) {
						htmlBuffer += '<li	class="firstchild selected" >';
					} else {
						if (control_type != "M") {
							htmlBuffer += '<li	class="firstchild" >';
						} else {
							htmlBuffer += '<li	class="on" >';
						}
					}
				} else {
					var strClass = "";
					if (i == (SIDGroupBar.SpecialList.length - 1)) {
						if (control_type != "M" && i == 4) {
							strClass = " lastchild ";
						} else {
							strClass = " last ";
						}
					}

					if (bool_selected) {
						htmlBuffer += '<li	class="selected' + strClass + '" >';
					} else {
						htmlBuffer += '<li class="' + strClass + '">';
					}
				}

				if (SIDGroupBar.SpecialList[i].gid == "0") {
					gidParam = "";
				} else {
					gidParam = "&gid=" + SIDGroupBar.SpecialList[i].gid;
				}

				//$get("www_url").value + "/gmkt.inc/Special/Special.aspx?sid=" + result.Rows[0].sid
				if (control_type != "M") {
					titleimg = 60;
				}
				else {
					titleimg = 50;
				}

				if (control_type == "M") {
					htmlBuffer += '<div>';
				}

				if (contents_type == "minishop") {
					var shopImg_src = SIDGroupBar.SpecialList[i].minishop_thumbnail == "" ? SIDGroupBar.SpecialList[i].minishop_shop_img : SIDGroupBar.SpecialList[i].minishop_thumbnail;
					htmlBuffer += '	<a target="_qlink" onclick="return Util.newTab(this, event);" class="thumb" href="' + Public.getCategoryServerUrl("/Minishop/Default.aspx?sell_cust_no=" + encodeURIComponent(SIDGroupBar.SpecialList[i].minishop_sell_cust_no) + "&pro_no=" + pro_no + "&qid=" + SIDGroupBar.SpecialList[i].minishop_seller_qid) + '"><img src="' + shopImg_src + '" width="' + titleimg + '" height="50" alt="' + SIDGroupBar.SpecialList[i].minishop_title + '" title="' + SIDGroupBar.SpecialList[i].minishop_title + '" onerror=\"$(this).hide();\" /></a>'
						+ '	<a target="_qlink" onclick="return Util.newTab(this, event);" class="subject" href="' + Public.getCategoryServerUrl("/Minishop/Default.aspx?sell_cust_no=" + encodeURIComponent(SIDGroupBar.SpecialList[i].minishop_sell_cust_no) + "&pro_no=" + pro_no + "&qid=" + SIDGroupBar.SpecialList[i].minishop_seller_qid) + '" title="' + SIDGroupBar.SpecialList[i].minishop_title + '">' + SIDGroupBar.SpecialList[i].minishop_title + '</a> ';
				}
				else {
					htmlBuffer += '	<a target="_qlink" onclick="return Util.newTab(this, event);" class="thumb" href="' + Public.getCategoryServerUrl("/category-" + SIDGroupBar.SpecialList[i].sid +'.html') + '"><img src="' + SIDGroupBar.SpecialList[i].featured_event_img + '" width="' + titleimg + '" height="50" alt="' + SIDGroupBar.SpecialList[i].title + '" title="' + SIDGroupBar.SpecialList[i].title + '" onerror=\"$(this).hide();\"  /></a>'
						+ '	<a target="_qlink" onclick="return Util.newTab(this, event);" class="subject" href="' + Public.getCategoryServerUrl("/category-" + SIDGroupBar.SpecialList[i].sid + '.html') + '" title="' + SIDGroupBar.SpecialList[i].title + '">' + SIDGroupBar.SpecialList[i].title + '</a> ';
				}

				if (control_type == "M") {
					htmlBuffer += '</div>';
				}
				htmlBuffer += '</li>';
			}
			$("#ul_featuredEvent").html(htmlBuffer);
		},
		InitGoodsImgTag: function (idx) {

			if (SIDGroupBar.GoodList.length < 1) {
				return;
			}
			var gidParam = "";
			var htmlBuffer = "";

			var item = SIDGroupBar.GoodList[idx];

			var grid = new Array();
			var isFirst = true;

			if (SIDGroupBar.ItemCount > SIDGroupBar.pageItemCount) {
				$('#featrued_prv').click(SIDGroupBar.FeaturedPre);
				$('#featrued_nxt').click(SIDGroupBar.FeaturedNext);

				$('#featrued_prv').dblclick(function () {
					return false;
				});
				$('#featrued_nxt').dblclick(function () {
					return false;
				});
			} else {
				$('#featrued_prv').unbind('click');
				$('#featrued_nxt').unbind('click');
			}

			SIDGroupBar.ItemIndex1 = SIDGroupBar.pageItemCount;
			SIDGroupBar.ItemIndex2 = SIDGroupBar.ItemCount - 1;


			for (var row = 0; row < item.length && row < SIDGroupBar.pageItemCount; row++) {
				var htmlBuffer = "";

				var img_id = "img_" + row;

				if (row == 0 || row == SIDGroupBar.pageItemCount) {
					//first li tag
					htmlBuffer += '	<li class="firstchild"></li>'
					htmlBuffer += '	<li>'
				} else {
					htmlBuffer += '	<li gd_no="' + item[row].gd_no + '">'
				}

				if (item[row].gid == "0") {
					gidParam = "";
				} else {
					gidParam = "&gid=" + item[row].gid;
				}

				var sell_prise = item[row].sell_price;
				var discount_prise = item[row].discount_price;
				var gd_nm = item[row].gd_nm;

				if (item[row].image == '' || item[row].image == null || item[row].image == undefined) {
				//	htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a target="_qlink" onclick="return Util.newTab(this, event);"  class="thumb" href="' + Public.getCategoryServerUrl("/Special/Special.aspx?sid=" + item[row].goodsSid + gidParam + "&pro_no=" + pro_no + "&pre_sid=" + pre_sid + '&goodscode=' + item[row].gd_no + '#ci') + '"><img load="N" resizing="false" gd_src="' + Public.getGoodsImagePath(item[row].img_contents_no, "M4_S", "N") + '" id="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>'
					htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a target="_qlink" onclick="return Util.newTab(this, event);"  class="thumb" href="' + Public.getCategoryServerUrl("/category-" + item[row].gd_no + '.html') + '"><img load="N" resizing="false" gd_src="' + Public.getGoodsImagePath(item[row].img_contents_no, "M4_S", "N") + '" id="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>'
				} else {
					//htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a  target="_qlink" onclick="return Util.newTab(this, event);"  class="thumb" href="' + Public.getCategoryServerUrl("/Special/Special.aspx?sid=" + item[row].goodsSid + gidParam + "&pro_no=" + pro_no + "&pre_sid=" + pre_sid + '&goodscode=' + item[row].gd_no + '#ci') + '"><img load="N" resizing="false" gd_src="' + item[row].image + '" id="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>'
					htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a  target="_qlink" onclick="return Util.newTab(this, event);"  class="thumb" href="' + Public.getCategoryServerUrl("/product-" + item[row].gd_no + '.html') + '"><img load="N" resizing="false" gd_src="' + item[row].image + '" id="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>'


				}


				htmlBuffer += '<a target="_qlink" onclick="return Util.newTab(this, event);" class="detail" href="' + Public.getCategoryServerUrl("/Special/Special.aspx?sid=" + item[row].goodsSid + gidParam + "&pro_no=" + pro_no + "&pre_sid=" + pre_sid + '&goodscode=' + item[row].gd_no + '#ci') + '">'
						+ '		<span class="bg"></span>'
						+ '		<span class="subject" title="' + gd_nm + '">' + gd_nm + '</span>'
						+ '		<span class="price"><strong>' + item[row].currency_prise + '</strong></span>'
						+ '		<span class="dc"><strong>' + item[row].percent + '</strong></span>'
						+ '	</a>'
						+ '</li>';
				if (row == SIDGroupBar.pageItemCount - 1) {
					htmlBuffer += '<li></li>';
				}
				if (isFirst) {
					$("#ul_goods").html(htmlBuffer);
					isFirst = false;
				} else {
					$("#ul_goods").append(htmlBuffer);
				}

				grid[row] = new Image();

				grid[row].onload = (function (img_id) {
					return function () {
						$("#" + img_id).attr("src", this.src);
					}
				})(img_id);

				if (item[row].image == '' || item[row].image == null || item[row].image == undefined) {
					grid[row].src = Public.getGoodsImagePath(item[row].img_contents_no, "M4_S", "N");
				} else {
					grid[row].src = item[row].image;
				}
				SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex + 1;

			}

			SIDGroupBar.SetGoodsEvent();
		},
		slideFive: function (opt) {


			for (var i = 0; i < SIDGroupBar.pageItemCount && i < SIDGroupBar.ItemCount; i++) {

				if (opt == 'Next') {
					SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex1;
				} else {
					SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex2;
				};
				var gidParam = "";
				var htmlBuffer = "";

				var item = SIDGroupBar.GoodList[SIDGroupBar.sidTabInx][SIDGroupBar.ItemIndex];


				var grid = new Array();

				var img_id = "img_" + SIDGroupBar.ItemIndex;

				if (item.gid == "0") {
					gidParam = "";
				} else {
					gidParam = "&gid=" + item.gid;
				}

				var sell_prise = item.sell_price;
				var discount_prise = item.discount_price;
				var gd_nm = item.gd_nm;
				if (item.sell_price == undefined || item.sell_price == null) {
					sell_prise = item.SELL_PRICE;
					discount_prise = item.DISCOUNT_PRICE;
					gd_nm = item.gd_nm;
				}

				if (item.image == '' || item.image == null || item.image == undefined) {
				//	htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a class="thumb" href="' + Public.getCategoryServerUrl("/Special/Special.aspx?sid=" + item.goodsSid + gidParam + "&pro_no=" + pro_no + "&pre_sid=" + pre_sid + '&goodscode=' + item.gd_no + '#ci') + '"><img gd_src="' + Public.getGoodsImagePath(item.img_contents_no, "M4_S", "N") + '" name="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>';
					htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a class="thumb" href="' + Public.getCategoryServerUrl("/product-" + item.gd_no + '.html') + '"><img gd_src="' + Public.getGoodsImagePath(item.img_contents_no, "M4_S", "N") + '" name="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>';
				} else {
					//htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a class="thumb" href="' + Public.getCategoryServerUrl("/Special/Special.aspx?sid=" + item.goodsSid + gidParam + "&pro_no=" + pro_no + "&pre_sid=" + pre_sid + '&goodscode=' + item.gd_no + '#ci') + '"><img gd_src="' + item.image + '" name="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>';
					htmlBuffer += '<div class="thumb_area" style="overflow:hidden"><a class="thumb" href="' + Public.getCategoryServerUrl("/product-" + item.gd_no + '.html') + '"><img gd_src="' + item.image + '" name="' + img_id + '" src="http://static.image-qoo10.cn/qoo10/front/cm/common/image/loading_180x180.gif" width="150" alt="' + gd_nm + '" title="' + gd_nm + '" /></a></div>';
				}



			        htmlBuffer += '<a class="detail" href="' + Public.getCategoryServerUrl("/product-" + item.gd_no + '.html') + '">'
					+ '		<span class="bg"></span>'
					+ '		<span class="subject" title="' + gd_nm + '">' + gd_nm + '</span>'
					+ '		<span class="price"><strong>' + item.currency_prise + '</strong></span>'
					+ '		<span class="dc"><strong>' + item.percent + '</strong></span>'
					+ '	</a>'

				if (opt == 'Next') {
					$("#ul_goods li").eq(i + 6).html(htmlBuffer);
					$("#ul_goods").append('<li gd_no="' + item.gd_no + '"></li>');



					SIDGroupBar.ItemIndex1 = SIDGroupBar.ItemIndex1 + 1 == SIDGroupBar.ItemCount ? 0 : SIDGroupBar.ItemIndex1 + 1;
					SIDGroupBar.ItemIndex2 = SIDGroupBar.ItemIndex2 + 1 == SIDGroupBar.ItemCount ? 0 : SIDGroupBar.ItemIndex2 + 1;
				} else {
					$("#ul_goods li").eq(0).html(htmlBuffer);
					$('#ul_goods').css({ "left": (SIDGroupBar.widthPix * (i + 2) * -1) + "px" });
					$("#ul_goods li").eq(0).before("<li></li>");
					SIDGroupBar.ItemIndex1 = SIDGroupBar.ItemIndex1 - 1 < 0 ? SIDGroupBar.ItemCount - 1 : SIDGroupBar.ItemIndex1 - 1;
					SIDGroupBar.ItemIndex2 = SIDGroupBar.ItemIndex2 - 1 < 0 ? SIDGroupBar.ItemCount - 1 : SIDGroupBar.ItemIndex2 - 1;

				}

				var imageTag = new Image();

				imageTag.onload = (function (img_id) {
					return function () {
						//$("#" + img_id).attr("src", this.src);
						var img_src = this.src;
						$("img[name=" + img_id + "]").each(function () {
							$(this).attr("src", img_src);
						});
					}
				})(img_id);

				if (item.image == '' || item.image == null || item.image == undefined) {
					imageTag.src = Public.getGoodsImagePath(item.img_contents_no, "M4_S", "N");
				} else {
					imageTag.src = item.image;
				}



			}




			SIDGroupBar.SetGoodsEvent();
		},
		SetGoodsEvent: function () {
			$(".ly_tplt7 li").hover(function () {
				$(this).siblings().removeClass("on");
				$(this).addClass("on");
				$("#ul_featuredEvent li:eq(" + SIDGroupBar.sidTabInx + ")").addClass("on");

				if ($(this).parents(".glr").attr("class") == "glr glr_v1") {
					$(this).children().children().children("img").attr("width", "138");
					//$(this).children().children().children("img").attr("height", "180");
				} else {
					$(this).children().children().children("img").attr("width", "180");
					//$(this).children().children().children("img").attr("height", "164");
				}
			}, function () {
				$(this).removeClass("on");
				if ("M" != "M") {
					$("#ul_featuredEvent li:eq(" + SIDGroupBar.sidTabInx + ")").removeClass("on");
				}
				if ($(this).parents(".glr").attr("class") == "glr glr_v1") {
					$(this).children().children().children("img").attr("width", "115");
					//$(this).children().children().children("img").attr("height", "150");
				} else {
					$(this).children().children().children("img").attr("width", "150");
					//$(this).children().children().children("img").attr("height", "150");
				}
			});
		},
		FeaturedNext: function () {

			if (SIDGroupBar.animate) {
				return;
			}

			SIDGroupBar.SlideFun('Next');
		},
		FeaturedPre: function () {
			if (SIDGroupBar.animate) return;

			SIDGroupBar.SlideFun('pre');
		},
		SlideFun: function (opt) {

			if (SIDGroupBar.animate) return;

			SIDGroupBar.animate = true;

			SIDGroupBar.slideFive(opt);

			if (opt == 'Next') {

				//				SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex + 1 == SIDGroupBar.ItemCount ? 0 : SIDGroupBar.ItemIndex + 1;

				//				$('#ul_goods').animate({ "left": (SIDGroupBar.widthPix * 2 * -1) + "px" }, 100, function () {
				//					SIDGroupBar.animate = false;
				//					$('#ul_goods').css({ "left": "-182px" });
				//					$("#ul_goods li").eq(1).remove();

				//				});
				//alert(SIDGroupBar.GoodList[SIDGroupBar.sidTabInx].length);


				$('#ul_goods').animate({ "left": (SIDGroupBar.widthPix * 6 * -1) + "px" }, 500, function () {
					SIDGroupBar.animate = false;
					$('#ul_goods').css({ "left": (SIDGroupBar.widthPix * -1) + "px" });
					$("#ul_goods li").eq(1).remove();
					$("#ul_goods li").eq(1).remove();
					$("#ul_goods li").eq(1).remove();
					$("#ul_goods li").eq(1).remove();
					$("#ul_goods li").eq(1).remove();

				});
			}

			else {//pre
				//				SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex - 1 < 0 ? SIDGroupBar.ItemCount - 1 : SIDGroupBar.ItemIndex - 1;

				//				$('#ul_goods').animate({ "left": "0px" }, 100, function () {
				//					SIDGroupBar.animate = false;
				//					$("#ul_goods li").eq(0).before("<li></li>");

				//					$('#ul_goods').css({ "left": "-182px" });
				//					$("#ul_goods li").eq(6).remove();
				//				});

				//SIDGroupBar.ItemIndex = SIDGroupBar.ItemIndex - 1 < 0 ? SIDGroupBar.ItemCount - 1 : SIDGroupBar.ItemIndex - 1;


				$('#ul_goods').animate({ "left": (SIDGroupBar.widthPix * -1) + "px" }, 500, function () {

					SIDGroupBar.animate = false;
					$('#ul_goods').css({ "left": (SIDGroupBar.widthPix * -1) + "px" });
					$("#ul_goods li").eq(6).remove();
					$("#ul_goods li").eq(6).remove();
					$("#ul_goods li").eq(6).remove();
					$("#ul_goods li").eq(6).remove();
					$("#ul_goods li").eq(6).remove();

				});
			}
		}
	}



</script>
	</div>
	
<? 
$i = 1;
foreach ($cate_topseven_provider_arr as $cat_id => $provider): 
?>
<div class="section_cate">
	<h2 class="h_main"><a href="/category-<?=$cat_id?>.html"><?=$provider['cat_name']?></a></h2>
	<ul class="nav_cate">
        <?
		foreach ($provider['provider_list'] as $key => $row): 
		if ($key >= 5) continue;
		?>
        <li><a href="/provider-<?=$row['provider_id']?>.html" title="<?=$row['display_name']?>" target="_blank"><?=$row['display_name']?></a>
		</li>
        <? endforeach ?><a href="/shops#<?=$provider['cat_name']?>" target="_blank" title="更多店铺"><img src="<?=static_style_url("image/more.gif")?>" alt="更多店铺" width="50px" height="17px"></a>
			
	</ul>
	
    <div class="spot_temp">      
            <?=isset($provider['provider_html']) ? $provider['provider_html']->ad_code : '';?>
        <div class="temp_ty" LIST="0">
            <div class="group_mnsh">
                <ul class="col7" id="ul_shop_event_group<?=$i?>">
                    <?php foreach ($provider['provider_list'] as $row): ?>
                    <li>
                        <a target="_blank" href="/provider-<?=$row['provider_id']?>.html" title="<?=$row['display_name']?>">
                            
                            <em class="tt"><?=$row['display_name']?></em>
                        </a>
                        <a target="_blank"  href="/provider-<?=$row['provider_id']?>.html">
                            <span class="thumb ">
                                <img resizing="false" load="N" src="<?=img_url($row['logo'])?>" alt="<?=$row['display_name']?>" width="100%" />
                            </span>
                            <span class="subject ellipsis"></span>
                        </a>
                    </li>
                    <?php endforeach; ?>	
                </ul>
            </div>
            <ul class="glr_area col5" id="ul_shop_event_items<?=$i?>"></ul>
        </div>

<script type="text/javascript">
$(document).ready(function () {
	ShopEventGroupBar<?=$i?>.Init(0, "http://static.image-qoo10.cn/qoo10/front/cm/common/image/ph.png");
});

var ShopEventGroupBar<?=$i?> = {
	list: '',
	idx: 0,
	isfirst: 0,
	count: 0,
	loadImg: '',
	Init: function (firstIdx,loadImg) {
		ShopEventGroupBar<?=$i?>.loadImg = loadImg;
		if (typeof ShopEventGoodsList<?=$i?> != undefined) ShopEventGroupBar<?=$i?>.list = ShopEventGoodsList<?=$i?>; else ShopEventGroupBar<?=$i?>.list = new Array(); //from .cs
		$("#ul_shop_event_group<?=$i?> li:eq(" + firstIdx + ")").addClass("on");
		$("#ul_shop_event_group<?=$i?> li").mouseover(ShopEventGroupBar<?=$i?>.Tab);
		ShopEventGroupBar<?=$i?>.idx = firstIdx;
		ShopEventGroupBar<?=$i?>.drawItem();
	},
	Tab: function () {
		if ($("this li").hasClass('on')) {
			return;
		}
		ShopEventGroupBar<?=$i?>.ControlClassHandle(this, 'li', 'on');
		ShopEventGroupBar<?=$i?>.idx = $("#ul_shop_event_group<?=$i?> li").index(this);
		ShopEventGroupBar<?=$i?>.drawItem();
	},
	ControlClassHandle: function (ParentElement, targetElement, className) {
		if ($(ParentElement).hasClass(className)) return;
		$(ParentElement).siblings(targetElement).removeClass(className);
		$(ParentElement).addClass(className);
	},
	drawItem: function () {
		var items = ShopEventGroupBar<?=$i?>.list[ShopEventGroupBar<?=$i?>.idx];
		var html = "";
		for (var i = 0; i < items.length; i++) {
			if (i == 0) {
				html += "<li class='firstchild' >";
			} else {
				html += "<li>";
			}
			/*if (ShopEventGroupBar<?=$i?>.isfirst == 0) {
				html += "<a title='" + items[i].gd_nm + "' class='thumb' target='_blank' href='" + items[i].href + "' ><img alt='" + items[i].gd_nm + "' onerror=\"this.src=img_host+'/image/no_image.gif';\" load='N' resizing='false' src='" + ShopEventGroupBar<?=$i?>.loadImg + "' gd_src='" + items[i].img_src + "' width='175' style='opacity: 1;' /></a>";
				ShopEventGroupBar<?=$i?>.isfirst = 1;
			}
			else {*/
				html += "<a class='thumb' target='_blank' href='" + items[i].href + "' title='" + items[i].gd_nm + "' ><img alt='" + items[i].gd_nm + "' onerror=\"this.src=img_host+'/image/no_image.gif';\" src='" + items[i].img_src + "' width='175' style='opacity: 1;' /></a>";
			//}
			html += "<a title='" + items[i].gd_nm + "' target='_blank' class='subject ellipsis' href='" + items[i].href + "'>" + items[i].gd_nm + "</a>";
			html += "<div class='price'><strong>" + items[i].price + "</strong></div>";
			html += "</li>";
		}
		$("#ul_shop_event_items<?=$i?>").html(html);
	}
};

</script>
    </div>
</div>
<? 
$i++;
endforeach; 
?>
	
<!-- 超值促销广告位 -->		
<?if(isset($promotions_ad)):?>
<?=$promotions_ad->ad_code?>
<?endif?>
	
	</div>
</div>
<script type="text/javascript">

	$(document).ready(function (e) {
		//缓加载及onerror事件
		//openLinkToNewWindow();
		if(window.DelayImageLoading && DelayImageLoading.Init) DelayImageLoading.Init();
	});
	function shopGroupImgOnError(obj, minishoptitle) {

		var li_class_name = ["bg_o b_black", "bg_o b_pink", "bg_o b_orange", "bg_o b_green", "bg_o b_blue"];
		var nickname = $(obj).parent().attr("title");
		$(obj).parent("span").addClass(li_class_name[Math.floor(Math.random() * li_class_name.length)]);
		$(obj).parent("span").append("<span class='bg'></span><span class='name'>" + minishoptitle + "</span>");
		$(obj).remove();
	}
</script>
        <!-- END BLOCK: QUICK INFORMATION -->

		<!-- SEM flaoating banner -->
		 
		<!-- END SEM flaoating banner -->
	</div>
<?php include APPPATH.'views/common/footer.php'; ?>
