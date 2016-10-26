
$(document).ready(function () {
	
	Bestseller.Init(); //waiting for bestseller_item_list (from .cs)
	Bestseller.flag = true;

	//QStyleEvent.Init();
	InitCategoryGroup();
	/* SECTION : PRODUCT SORT (index.js, 121018) */
	$(".section_prdsort .lst").hover(function () {
		$(this).addClass("on");
		var h = $(this).height();
		$(this).children("h3").css("height", h);
		$(this).parent(".lst_prdsort").css("z-index", 1);
	}, function () {
		$(this).removeClass("on");
		$(this).children("h3").css("height", 59);
		$(this).parent(".lst_prdsort").css("z-index", 0);
	});

	Util.setOpenNewLinkEvent(); // 새창 열기 적용 2012-11-06 현덕해
});


//Best Sell goods list
var Bestseller = {
	flag: false,
	animate: false,
	list: '',
	tab_index: 0,
	page_index: 0,
	isNext: true,
	pageing_index: 0,
	itemCount: 0,
	pageCount: 0,
	lastCount: 0,
	isInit: false,
	Init: function () {
		if (typeof BestSellGoodsList != undefined) Bestseller.list = BestSellGoodsList; else Bestseller.list = new Array(); //from .cs
		$('#bestseller_prev').click(Bestseller.Prev);
		$('#bestseller_next').click(Bestseller.Next);
		$('#bestseller_tab li').mouseover(Bestseller.Tab);
		$('#BestSellPaging a').mouseover(Bestseller.PagingHandle);

		Bestseller.itemCount = Bestseller.list[0].length;
		Bestseller.lastCount = Bestseller.itemCount % 5;

		Bestseller.page_index = Math.floor(Math.random() * (4 - 0)); //초기 random index값
		
		if (Bestseller.lastCount > 0)
			Bestseller.pageCount = parseInt(Bestseller.itemCount / 5) + 1;
		else
			Bestseller.pageCount = Bestseller.itemCount / 5;

		$("#BestSellPaging a").siblings("a").removeClass("on");
		$("#BestSellPaging a:eq(" + ((Bestseller.page_index) % Bestseller.pageCount) + ")").addClass("on");

		//drawing 3 "li" group
		Bestseller.RenderSlide();
	},
	//right button
	Next: function () {
		if (!Bestseller.flag) return;
		if (Bestseller.animate) return;

		Bestseller.animate = true;
		Bestseller.page_index = (Bestseller.page_index + 1) % Bestseller.pageCount;
		$('#bestseller_slide ul').animate({ left: (895 * 2 * -1) + "px" }, 500, Bestseller.NextAfter);

	},
	NextAfter: function () {
		$('#bestseller_slide ul').css('left', "-895px");
		Bestseller.DeletePreElement(1);
		Bestseller.RenderOneSlide(2, (Bestseller.page_index + 1) % Bestseller.pageCount);
		Bestseller.animate = false;
		$("#BestSellPaging a").siblings("a").removeClass("on");
		$("#BestSellPaging a:eq(" + Bestseller.page_index + ")").addClass("on");
	},
	//left button
	Prev: function () {
		if (!Bestseller.flag) return;
		if (Bestseller.animate) return;

		Bestseller.animate = true;
		Bestseller.page_index = (Bestseller.page_index + Bestseller.pageCount - 1) % Bestseller.pageCount;
		$('#bestseller_slide ul').animate({ left: 0 }, 500, Bestseller.PrevAfter);

	},
	PrevAfter: function () {
		Bestseller.RenderOneSlide(0, (Bestseller.page_index + Bestseller.pageCount - 1) % Bestseller.pageCount);
		Bestseller.DeleteNextElement(1);
		$('#bestseller_slide ul').css('left', "-895px");
		Bestseller.animate = false;
		$("#BestSellPaging a").siblings("a").removeClass("on");
		$("#BestSellPaging a:eq(" + Bestseller.page_index + ")").addClass("on");
	},
	Tab: function () {


		if (Bestseller.animate) return;

		if ($(this).hasClass('on')) return;

		Bestseller.itemCount = Bestseller.list[$('#bestseller_tab li').index(this)].length;
		Bestseller.lastCount = Bestseller.itemCount % 5;

		if (Bestseller.lastCount > 0)
			Bestseller.pageCount = parseInt(Bestseller.itemCount / 5) + 1;
		else
			Bestseller.pageCount = Bestseller.itemCount / 5;



		Bestseller.pageing_index = 0;

		ControlClassHandle(this, 'li', 'on');
		/*if ($(this).hasClass('on')) return;

		$(this).siblings('li').removeClass('on');
		$(this).addClass('on');*/

		Bestseller.flag = true;
		Bestseller.tab_index = $('#bestseller_tab li').index(this);
		Bestseller.page_index = 0;
		Bestseller.RenderSlide();


		//ControlClassHandle(
		$("#BestSellPaging a").siblings("a").removeClass("on");
		$("#BestSellPaging a:eq(" + Bestseller.page_index + ")").addClass("on");
	},
	RenderSlide: function () {
		Bestseller.RenderOneSlide(1, Bestseller.page_index);
		Bestseller.RenderHideSlide();
	},
	RenderHideSlide: function () {
		Bestseller.RenderOneSlide(0, (Bestseller.page_index + Bestseller.pageCount - 1) % Bestseller.pageCount);
		Bestseller.RenderOneSlide(2, (Bestseller.page_index + 1) % Bestseller.pageCount);
	},
	RenderOneSlide: function (slide_index, page_index) {
		var buffer = '';
		var item;
		var i = 0;
		for (i = 0; i < 5 && (page_index * 5) + i < Bestseller.list[Bestseller.tab_index].length; i++) {
			item = Bestseller.list[Bestseller.tab_index][(page_index * 5) + i];

			buffer +=
				'<li class="' + ((i == 0) ? 'firstchild' : '') + '" title="' + item.gd_nm + '">' +
				'	<a class="thumb" href=' + item.href + ' target="_blank">';


			if (page_index == 0) {
				buffer +=
				'	<span class="num ff_thm">' + (i + 1) + '</span>';
			} else {
				buffer +=
				'	<span class="num ff_thm">' + (i + (5 * page_index) + 1) + '</span>';
			}
			if (Bestseller.isInit == true) {
				buffer += '		<img onerror="this.src=img_host+\'/image/no_image.gif\';" width="175" load="N" resizing="false" gd_src="' + item.img_src + '" src="' + loadImg + '" alt="' + item.gd_nm + '">';
				Bestseller.isInit = false;
			} else {
				buffer += '		<img  onerror="this.src=img_host+\'/image/no_image.gif\';" width="175" load="N" resizing="false" src="' + item.img_src + '" alt="' + item.gd_nm + '">';
			}
			buffer +=
				'	</a>' +
				'	<div class="detail" >' +
				'		<div class="info">';
			if (item.percent != "0") {
				buffer += '			<a class=" ellipsis" href="' + item.href + '" ><font color="yellow">' + item.percent + '</font></a>';
			}
			else {
				buffer += '<br/>';
			}
			buffer += '			<a class="subject ellipsis" href="' + item.href + '" >' + item.gd_nm + '</a>' +
				'		</div>' +
				'		<div class="price ff_thm"><strong>' + item.price + '</strong></div>' +
				'	</div>' +
				'</li>';
		}
		for (i; i < 5; i++) {
			buffer +=
				'<li>' +
				'	<a class="thumb">';

			buffer +=
				'	<span></span>';

			buffer +=
			//'		<img width="175" height="228">' +
				'	</a>' +
				'	<div>' +
				'		<div class="info">' +
				'			<a ></a>' +
				'			<a></a>' +
				'		</div>' +
				'		<div></div>' +
				'	</div>' +
				'</li>';
		}

		if (slide_index == 0) {
			//Left
			$("#bestseller_slide ul li:eq(0)").before(buffer);
		} else if (slide_index == 1) {
			//middle
			$('#bestseller_slide ul').html(buffer);
		} else {
			//right
			$('#bestseller_slide ul').append(buffer);
		}
	},
	DeletePreElement: function (icount) {
		$('#bestseller_slide li').slice(0, (5 * icount)).remove();
	},
	DeleteNextElement: function (icount) {
		$('#bestseller_slide li').slice(-5 * icount).remove();
	},
	PagingHandle: function () {

		if ($(this).hasClass('on')) return;

		ControlClassHandle(this, 'a', 'on');
		Bestseller.pageing_index = Bestseller.page_index;
		//Bestseller.page_index = $('#BestSellPaging a').index(this);

		if (Bestseller.animate) return;
		Bestseller.animate = true;

		var iPageIDX = 0;

		if (Bestseller.page_index > $('#BestSellPaging a').index(this)) {
			//alert(1);
			//---------
			//next
			iPageIDX = Bestseller.pageing_index - $('#BestSellPaging a').index(this);

			Bestseller.page_index = $('#BestSellPaging a').index(this);

			if (iPageIDX <= 0) return;
			Bestseller.pageing_index = (Bestseller.pageing_index - 1) % Bestseller.pageCount;

			if (Bestseller.pageing_index < 0) {
				Bestseller.pageing_index = Bestseller.pageCount - Bestseller.pageing_index;
			}

			if (iPageIDX >= 2) {

				for (var i = 0; i < iPageIDX; i++) {
					Bestseller.pageing_index = (Bestseller.pageing_index - 1);
					if (Bestseller.pageing_index < 0) {
						Bestseller.pageing_index = Bestseller.pageCount - Bestseller.pageing_index;
					}
					Bestseller.RenderOneSlide(0, (Bestseller.pageing_index) % Bestseller.pageCount);
				}
			} else if (iPageIDX == 1) {
				Bestseller.pageing_index = (Bestseller.pageing_index - 1);
				if (Bestseller.pageing_index < 0) {
					Bestseller.pageing_index = Bestseller.pageCount - Bestseller.pageing_index;
				}
				Bestseller.RenderOneSlide(0, (Bestseller.pageing_index) % Bestseller.pageCount);
			}

			$('#bestseller_slide ul').animate({ left: 0 + "px" }, 200, Bestseller.SetLeft);
			Bestseller.DeleteNextElement(iPageIDX);
			Bestseller.animate = false;
			$("#BestSellPaging a").siblings("a").removeClass("on");
			$("#BestSellPaging a:eq(" + ((Bestseller.page_index) % Bestseller.pageCount) + ")").addClass("on");

		} else if (Bestseller.page_index < $('#BestSellPaging a').index(this)) {
			//alert(2);
			//next
			iPageIDX = $('#BestSellPaging a').index(this) - Bestseller.pageing_index;

			Bestseller.page_index = $('#BestSellPaging a').index(this);

			if (iPageIDX == 0) return;

			Bestseller.pageing_index = (Bestseller.pageing_index + 1) % Bestseller.pageCount;

			if (iPageIDX >= 2) {

				for (var i = 0; i < iPageIDX; i++) {
					Bestseller.pageing_index = (Bestseller.pageing_index + 1);
					//alert("iPageIDX->" + iPageIDX);
					Bestseller.RenderOneSlide(2, (Bestseller.pageing_index) % Bestseller.pageCount);
				}
			} else if (iPageIDX == 1) {
				Bestseller.pageing_index = (Bestseller.pageing_index + 1);
				Bestseller.RenderOneSlide(2, (Bestseller.pageing_index) % Bestseller.pageCount);
			}


			Bestseller.DeletePreElement(iPageIDX);
			$('#bestseller_slide ul').animate({ left: (895 * 2 * -1) + "px" }, 200, Bestseller.SetLeft);

			Bestseller.animate = false;
			$("#BestSellPaging a").siblings("a").removeClass("on");
			$("#BestSellPaging a:eq(" + ((Bestseller.page_index) % Bestseller.pageCount) + ")").addClass("on");

		}
		Bestseller.animate = false;
		//alert(3);
		//Bestseller.RenderSlide();
	},
	SetLeft: function () {
		$('#bestseller_slide ul').css({ "left": "-895px" });
	}
}

function ControlClassHandle(ParentElement, targetElement, className) {
	if ($(ParentElement).hasClass(className)) return;
	$(ParentElement).siblings(targetElement).removeClass(className);
	$(ParentElement).addClass(className);
}

var NewGoods = {
	//page_index: 0,
	from: 0,
	to: 0,
	list: '',
	page_index: 0,
	beforElement: null,
	nextindex: null,
	beforindex: null,
	pageItem: 5,

	Init: function () {
		//alert(newGoodsList);
		if (typeof newGoodsList != undefined) NewGoods.list = newGoodsList; else NewGoods.list = new Array(); //from .cs
		$('#newProductTab li').mouseover(NewGoods.slideFun);
		$('#ulGoodsList li').mouseover(NewGoods.ZoomOut);
		$('#ulGoodsList li').mouseleave(NewGoods.ZoomIn);

		//alert(newGoodsList);
		NewGoods.to = 1;
		NewGoods.RenderSlide();
	},
	slideFun: function () {
		if ($(this).hasClass('on')) return;

		NewGoods.page_index = $('#newProductTab li').index(this);
		NewGoods.to = $('#newProductTab li').index(this) + 1;

		ControlClassHandle(this, 'li', 'on');
		NewGoods.RenderSlide();
		if (newSetTimeoutVarible > 0) clearTimeout(newSetTimeoutVarible);
		newSetTimeoutVarible = setTimeout("$('#ulGoodsList').animate({ left: (929 * NewGoods.page_index * -1) + 'px' }, 500);", image_display_duration);

	},
	ZoomOut: function () {

		if ($(this).hasClass('on')) return;

		var tabindex = NewGoods.page_index;
		//alert($('#newProductTab').index(this));

		if (tabindex < 0) {
			tabindex = 0;
		}
		ControlClassHandle(this, 'li', 'on');

		$(this).find('img').css("display", "none");
		//$(this).find('img').fadeOut();


		$(this).find('img').attr('width', '207');
		//$(this).find('img').attr('height', '207');
		$(this).find('img').fadeIn(100);

		for (var i = 0; i < $(this).find('a').children().length; i++) {
			$($(this).find('a').children("span")[i]).css("display", "");
		}
		//3
		$("#divNewgoods").css("overflow", "visible");

		if (tabindex > 0) {

			$("#ulGoodsList li:eq(" + ((tabindex * 5) - 1) + ")").css("visibility", "hidden");

			//$('#ulGoodsList li').index($('#newProductTab li').index(this) * 5 - 1).css("visibility", "hidden");
			NewGoods.beforindex = $("#ulGoodsList li:eq(" + (tabindex * 5 - 1) + ")");
		}

		if ($("#ulGoodsList li:eq(" + (tabindex * 5 + 5) + ")") != null && $("#ulGoodsList li:eq(" + (tabindex * 5 + 5) + ")") != null) {
			//alert($("#ulGoodsList li:eq(" + (tabindex * 5 + 5) + ")"));
			$("#ulGoodsList li:eq(" + (tabindex * 5 + 5) + ")").css("visibility", "hidden");

			NewGoods.nextindex = $("#ulGoodsList li:eq(" + (tabindex * 5 + 5) + ")");
		}
		NewGoods.beforElement = this;
	},
	ZoomIn: function () {

		$("#divNewgoods").css("overflow", "hidden");

		if (NewGoods.beforindex != null) {
			NewGoods.beforindex.css("visibility", "visible");
		}
		if (NewGoods.nextindex != null) {
			NewGoods.nextindex.css("visibility", "visible");
		}
		for (var i = 0; i < $(this).find('a').children().length; i++) {
			$($(this).find('a').children("span")[i]).css("display", "none");
		}
		$(NewGoods.beforElement).find('img').attr("width", "175");
		//$(NewGoods.beforElement).find('img').attr("height", "175");
		$(NewGoods.beforElement).removeClass("on");

	},
	RenderSlide: function () {

		var item;
		//alert(NewGoods.from + ":" + NewGoods.to);

		while (NewGoods.from < NewGoods.to) {
			var htmlBuffer = "";
			item = NewGoods.list[NewGoods.from];
			if (item == null) return;
			for (var i = 0; i < item.length; i++) {
				if (i == 0) {
					htmlBuffer += '<li class="firstchild">';
				} else {
					htmlBuffer += '<li>';
				}

				htmlBuffer += '<a  href="' + item[i].href + '" title="' + item[i].gd_nm + '" target="_qlink" onclick="return Util.newTab(this, event);">' + '<img resizing="false" onerror="this.src=img_host+\'/image/no_image.gif\';" src="' + item[i].img_src + '" width="175" alt="' + item[i].gd_nm + '">' +
								'<span class="bg" style="display:none;"></span>' +
								'<span class="detail"  style="display:none;">' +
									'<span class="subject">' + item[i].gd_nm + '</span>' +
									'<span class="price">¥<strong>' + (parseFloat(item[i].price) >= 100 ? Math.ceil(item[i].price) : item[i].price) + '</strong></span>' +
								'</span>' +
							  '</a>' +
							'</li>'
			}
			if (item.length < NewGoods.pageItem) {
				for (var i = item.length; i < NewGoods.pageItem; i++) {
					htmlBuffer += '<li></li>';
				}
			}
			$('#ulGoodsList').append(htmlBuffer);
			$('#ulGoodsList li').mouseover(NewGoods.ZoomOut);
			$('#ulGoodsList li').mouseleave(NewGoods.ZoomIn);
			NewGoods.from = (NewGoods.from + 1);
		}
	}
}

function GetNotice(nid, link_kind, link, rootDomain) {
	if (link_kind == "P") {
		var url = "";
		var pathname = window.location.pathname;
		if (rootDomain == undefined) {
			if (pathname == "/") {
				url = "gmkt.inc/Main/PopupNotice.aspx?nid=" + nid;
			}
			else {
				url = "Main/PopupNotice.aspx?nid=" + nid;
			}
		}
		else {
			url = rootDomain + "/Main/PopupNotice.aspx?nid=" + nid;
		}		
		//var url = Public.getWWWServerUrl("/Main/PopupNotice.aspx?nid=" + nid, false)
		window.open(url, "popup_notice", "top=100,left=100,Width=622, Height=511,scrollbars=no,resizable=yes");
	}
	else if (link_kind == "N") {
		if (Util.safeString(link) != "") window.open(link);
	}
	else {
		if (Util.safeString(link) != "") window.location.href = link;
	}
	return false;
}
 
function InitCategoryGroup() {

	$(".spot_temp li").hover(function () {
		if ($($(this).parents(".temp_ty")).attr("LIST") != "0") {
			$($(this).parents(".temp_ty").find("img")).css({ "opacity": ".75", "filter": "alpha(opacity=75)" });
			$($(this).parents(".temp_ty2").find("img")).css({ "opacity": ".75", "filter": "alpha(opacity=75)" });
			$("img", this).css({ "opacity": "1", "filter": "alpha(opacity=100)" });
			$(this).addClass("on");
		}
	}, function () {
		if ($(this).parents(".temp_ty").attr("LIST") != "0") {
			$(this).removeClass("on");
		}
	});

	$(".section_cate div.spot_temp div.temp_ty").mouseleave(CategoryDimMouseOut);
	$(".section_cate div.spot_temp div.temp_ty2").mouseleave(CategoryDimMouseOut);
}

function CategoryDimMouseOut() {

	if ($(this).attr("LIST") != "0") {
		for (var i = 0; i < $(this).find("img").length; i++) {
			$($(this).find("img")[i]).css({ "opacity": "1", "filter": "alpha(opacity=100)" });
		}
	}
}


