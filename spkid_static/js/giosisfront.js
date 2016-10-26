/*
GGI Front 통합 JS - 개발용 v1.0
Choi Kil Hyoung [다음 js를 통합함] ackeyword.js / Layout 관련
* 수정할 때 mobile_util.js도 체크해야 합니다.
*/
/* aspx내에 존재하는 내용 */



var __afterLoginProc = false;

function __afterPopupLogin(cartcount) {
	if ($get("login_view") && !__afterLoginProc && Public.isLogin()) { 
		var logout_html = "<a href=" + Public.getLoginServerUrl("/Login/Logout.aspx", true) + ">" + MultiLang.findResource("__master/defaultmasterpage.master__sign out") + "</a>";
		var editinfo_html = "<a href=" + Public.getLoginServerUrl("/My/EditMember.aspx", true) + ">" + MultiLang.findResource("__master/defaultmasterpage.master__edit info") + "</a>";

		__afterLoginProc = true;
		$("#login_view").html(logout_html);
		$("#login_view2").html(editinfo_html);

		if (cartcount > 0) {
			$("#quickInfo a.btn_cart").html("<span class=\"num fs_10 ff_ara\" >" + cartcount + "</span>");

//			if (__PAGE_VALUE.VIEW_SITEID == "m18") {
//				$(".cart a").html(String.format(MultiLang.findResource("__m18/index.aspx__cart {0} items "), cartcount));
//			}
		}
	}
}
function __afterLogout() {
	return;
	if ($get("logout_view") && Util.getCookie(GMKT.ServiceInfo.LoginCookieName) == "") {
		var login_html = "<a href=" + Public.getLoginServerUrl("/Login/Login.aspx", true) + ">" + MultiLang.findResource("__master/defaultmasterpage.master__sign in") + "</a>";
		var regist_html = "<a href=" + Public.getLoginServerUrl("/Member/RegisterMember.aspx", true) + ">" + MultiLang.findResource("__master/defaultmasterpage.master__register") + "</a>";

		$("#logout_view").html(login_html);
		$("#logout_view2").html(regist_html);

		$("#quickInfo a.btn_cart").html("");
		$("#quickInfo a.btn_qbox").html("");

		if (__PAGE_VALUE.VIEW_SITEID == "m18") {
			$(".cart a").html(String.format(MultiLang.findResource("__m18/index.aspx__cart {0} items "), 0));
		}
	}
}

function doSearch() {
	if ($("hid_key_inflow_check") != null && $("#hid_key_inflow_check").val() != undefined && $get("hid_key_inflow_check").value == "F") {
		Util.setCookie("SearchInflowCheck", "KC");
	}
	else {
		//Search관련 유입경로 쿠키추가
		if ($("#hid_main_inflow_path") != null && $("#hid_main_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "MS");
		else if ($("#hid_sub_inflow_path") != null && $("#hid_sub_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "SS");
		else
			Util.setCookie("SearchInflowCheck", "KC");
	}


	if ($get("search____keyword").value.trim() != "") {
		document.search_____form.keyword.value = $get("search____keyword").value;
		$get("search_____form").action = Public.getSearchVUrl($get("search____keyword").value);

		if ($get("search___connect_url").value != "") {
			window.location.href = $get("search___connect_url").value;
			$get("search___connect_url").value = "";
		} else {
			$get("search_____form").submit();
		}
	}
}

var addImg_layer_obj;
function StillboxHoverEvent() {
	if ($(".ly_stillbox") != null) {
		$(".ly_stillbox").hover(function () {
			clearTimeout(addImg_layer_obj);
			$(this).css({ "display": "" });
		}, function () {
			addImg_layer_obj = setTimeout(ly_stillbox_hide, 100);
		});
	}

	//버튼 색 활성화
	$(".ly_stillbox .btn_prev").bind("mouseover", function (e) {
		$(this).children("span").addClass("on");
	});
	$(".ly_stillbox .btn_prev").bind("mouseout", function (e) {
		$(this).children("span").removeClass("on");
	});
	$(".ly_stillbox .btn_next").bind("mouseover", function (e) {
		$(this).children("span").addClass("on");
	});
	$(".ly_stillbox .btn_next").bind("mouseout", function (e) {
		$(this).children("span").removeClass("on");
	});

}

function ly_stillbox_hide() {
	$(".ly_stillbox").fadeOut(200);
}

function AdditionalImgGallery(obj, board_type_class, clasgubun) {
	var btn_disable = 554
	var btn_disable_plus = 22;
	var additional_with = 84;
	var obj_top_num = 237;   //107
	var obj_left_num = 9;
	var add_obj_left_num;
	var num_btn_disable = 6;
	var layer_width = 504;
	var Additionallayer_with = 0;

	if ($("#bestWrap").offset() == null) {
		btn_disable = 722;
		obj_top_num = 237;
		obj_left_num = 8;
		num_btn_disable = 8;
		layer_width = 672;
		btn_disable_plus = 32;
	} else {
		obj_top_num = 218;
	}
	//check brand bar
	//if ($("#brand_bar_tag_3").offset() != null)
	//	obj_top_num += $("#brand_bar_tag_3").height() + 10;
	//if ($("#header .gnb_bar").length > 0)
	//	obj_top_num += $("#header .gnb_bar").height();
	var additional_img_box = $(".ly_stillbox");

	if ((clasgubun != null && clasgubun != undefined && clasgubun != "") || $(obj).attr("globalCheck") == "Y") {
		additional_img_box.addClass('ly_stillbox_glb');
	} else
		additional_img_box.removeClass('ly_stillbox_glb');

	//if (additional_img_box.attr("goodscode") != $(obj).attr("goodscode")) {
	//	if ($(obj).attr("goodscode") != undefined && $(obj).attr("goodscode") != "") {
	//추가이미지 가져오기
	var AddImg;
			var param = new RMSParam();
			param.add("gd_no", $(obj).attr("goodscode"));

			if ($(obj).attr("globalCheck") == "Y")
				AddImg = RMSHelper.callWebMethod(Public.getServiceUrl("swe_CategoryAjaxService.asmx"), "GetGlobalGoodsAddtionalImg", param.toJson());
			else
				AddImg = RMSHelper.callWebMethod(Public.getServiceUrl("swe_CategoryAjaxService.asmx"), "GetGoodsAddtionalImg", param.toJson());

			if (AddImg != null && AddImg.Rows.length	 > 0) {
				var add_contents_no = AddImg.Rows[0]["contents_no"];
				var img_url_head = Public.getGoodsImagePath(add_contents_no, "AS", "Y");
				img_url_head = img_url_head.substring(0, img_url_head.lastIndexOf('/') + 1);
				var img_url_tail = AddImg.Rows[0]["add_image_lists"].split(',');

				//갯수 계산
				var real_li_cnt = parseInt($(obj).attr("img_cnt"));
				if (real_li_cnt > img_url_tail.length + 1)
					real_li_cnt = img_url_tail.length + 1;

				//갯수 계산
				var real_img_cnt = parseInt($(obj).attr("img_cnt"));
				if (real_img_cnt > img_url_tail.length + 1)
					real_img_cnt = img_url_tail.length + 1;

				//Qplay 열기
				var qplay_url = "";
				if ($("#Qplay_area").attr("href") != undefined)
					qplay_url = $("#Qplay_area").attr("href") + "&goodscode=" + $(obj).attr("goodscode") + "&dp=max";
				else if ($(obj).attr("qplay_href") != undefined)
					qplay_url = $(obj).attr("qplay_href") + "&goodscode=" + $(obj).attr("goodscode") + "&dp=max";
				else
					qplay_url = $(obj).attr("qplay_href") + "&goodscode=" + $(obj).attr("goodscode") + "&dp=max";

				var global_order_type = $(obj).attr("globalCheck") == "Y" ? "G" : ""; 
				
				//li를 만듬
				var li_html = "";
				for (var i = 0; i < real_img_cnt; i++) {
					if (__PAGE_VALUE.VIEW_SITEID == "DEFAULT" || __PAGE_VALUE.VIEW_SITEID == "m18")
						li_html += "<a class=\"thumb\" href=\"" + qplay_url + "&ai_no=" + i + "\" onclick=\"openGoodsImageEnlargeView('" + $(obj).attr("goodscode") + "', " + i + ", '" + global_order_type + "'); return false; \"></a>";
					else
						li_html += "<a class=\"thumb\" style=\"cursor:default;\" onclick=\"return false; \"></a>";
				}

				additional_img_box.children("div").html(li_html);

				//기본이미지 1개 앞에 넣기
				var basic_img_src = Public.getGoodsImagePath($(obj).attr("img_contents_no"), "LS_S", "Y");
				var basic_img_tag = "<img style=\"display:none; width:78px; height:78px;\" src=\"" + basic_img_src + "\" onerror=\"this.src='/gmkt.inc/Img/no_image.gif';\" onload=\"$(this).css('display','block')\" >";
				additional_img_box.children("div").children("a").eq(0).html(basic_img_tag);

				//이미지 태그를 li안에 집어 넣음
				var img_tag = "";
				for (var i = 0; i < img_url_tail.length && i < real_img_cnt - 1; i++) {
					img_tag = "<img style=\"display:none; width:78px; height:78px;\" src=\"" + img_url_head + img_url_tail[i] + "\" onerror=\"this.src='/gmkt.inc/Img/no_image.gif';\" onload=\"$(this).css('display','block')\" >";
					additional_img_box.children("div").children("a").eq(i + 1).html(img_tag);
				}

				//버튼 활성화
				if (parseInt(real_img_cnt) > num_btn_disable) {
					Additionallayer_with = btn_disable + btn_disable_plus;
					additional_img_box.children("div").attr("style", "width:" + layer_width + "px;");
					additional_img_box.children(".btn_prev").show();
					additional_img_box.children(".btn_next").show();
				}
				else {
					Additionallayer_with = (parseInt(real_img_cnt) * additional_with) + btn_disable_plus;
					additional_img_box.children("div").removeAttr("style");
					additional_img_box.children(".btn_prev").hide();
					additional_img_box.children(".btn_next").hide();
				}
		//	}
		//}
		additional_img_box.attr("goodscode", $(obj).attr("goodscode"));
	}

	var now_location = location.pathname;
	var board_left = 0;
	var board_width = 0;
	var obj_top = 0;
	var obj_left = 0;
	var back = 0;
	var move = 0;
	var wrap_top = 0;

	if (additional_img_box.parent().offset() != null)
		board_left = additional_img_box.parent().offset().left;

	if (additional_img_box.parent().width() != null)
		board_width = additional_img_box.parent().width();

	if ($(obj).offset() != null) {
		obj_top = $(obj).offset().top - obj_top_num;

		//미니샵일경우
		if (now_location.toLowerCase().indexOf("/minishop/") >= 0 || now_location.toLowerCase().indexOf("/shop/") >= 0)
			obj_top = obj_top + 97;

		obj_left = $(obj).offset().left - board_left - obj_left_num;
	}

	if ($("#wrap").offset() != null)
		wrap_top = $("#wrap").offset().top;

	if ($("#bestWrap").offset() == null) {
		if (board_type_class == "bd_gallery04") {
			obj_top = $(obj).offset().top - obj_top_num;
			obj_left -= 8;

			if (parseInt(Additionallayer_with) > parseInt(board_width) - parseInt(obj_left)) {
				//미니샵일경우
				if (now_location.toLowerCase().indexOf("/minishop/") >= 0 || now_location.toLowerCase().indexOf("/shop/") >= 0)
					obj_top = $(obj).offset().top - 138;

				back = parseInt(obj_left) + parseInt(Additionallayer_with) - parseInt(board_width);
				move = parseInt(obj_left) - parseInt(back);
			}
			else {
				move = obj_left;
				if (now_location.toLowerCase().indexOf("/minishop/") >= 0 || now_location.toLowerCase().indexOf("/shop/") >= 0)
					obj_top = $(obj).offset().top - 138;
			}
		}
		else if (board_type_class == "bd_gallery6") {
			obj_top = $(obj).offset().top - 115;
			obj_left = $(obj).offset().left - $("#groupWrap").offset().left - 9;

			if (parseInt($(obj).offset().left) + parseInt(Additionallayer_with) - 1 > parseInt(board_left) + parseInt(board_width)) {
				back = parseInt($(obj).offset().left) + parseInt(Additionallayer_with) - parseInt(board_left) - parseInt(board_width) - 9;
				move = parseInt(obj_left) - parseInt(back);
			}
			else
				move = obj_left;
		} else if (board_type_class == "bd_list01") { //old
			obj_top = $(obj).offset().top - obj_top_num + 78;

			//미니샵일경우
			if (now_location.toLowerCase().indexOf("/minishop/") >= 0 || now_location.toLowerCase().indexOf("/shop/") >= 0)
				obj_top = $(obj).offset().top - 55;
			move = 92;

		} else if (board_type_class == "bd_lst") { //new
			obj_top = $(obj).offset().top - obj_top_num + 78 + 20 - 1 + 80;

			//미니샵일경우
			if (now_location.toLowerCase().indexOf("/minishop/") >= 0 || now_location.toLowerCase().indexOf("/shop/") >= 0)
				obj_top = $(obj).offset().top - 55;
			move = 92 + 20 - 1;

		}
		else {
			if (now_location.toLowerCase().indexOf("/globalqshop/") >= 0)
				obj_top = $(obj).offset().top - 55;

			if (parseInt(Additionallayer_with) > parseInt(board_width) - parseInt(obj_left)) {
				back = parseInt(obj_left) + parseInt(Additionallayer_with) - parseInt(board_width);
				move = parseInt(obj_left) - parseInt(back) + 5;
			}
			else
				move = obj_left;
		}
	} else {
		if (now_location.toLowerCase().indexOf("/globalqshop/") >= 0)
			obj_top = $(obj).offset().top - 55;

		if (parseInt($(obj).offset().left) + parseInt(Additionallayer_with) - 1 > parseInt(board_left) + parseInt(board_width)) {
			back = parseInt($(obj).offset().left) + parseInt(Additionallayer_with) - parseInt(board_left) - parseInt(board_width) - 9;
			move = parseInt(obj_left) - parseInt(back);
		}
		else
			move = obj_left

	}

	var contents_offet_top = 0;
	if (__PAGE_VALUE.VIEW_SITEID == "m18") {

		contents_offet_top = 12;
		//if (location.pathname.toLowerCase().indexOf("special") >= 0)
		//	contents_offet_top = contents_offet_top - 130;
	}

	
	additional_img_box.css("top", (obj_top - wrap_top - contents_offet_top) + "px");
	additional_img_box.css("left", move + "px");

	clearTimeout(addImg_layer_obj);
	additional_img_box.fadeIn(200);
}

Util.add_image_mouseover = function (obj) {
	if ($(obj).attr("img_cnt") == "0") return;
	var bestWrapObj = $("#bestWrap").offset();
	var board_type_class = $(obj).attr("class");
	var classGubun = $(obj).attr("classgubun");

	if (board_type_class == undefined || board_type_class == null)
		board_type_class = "";

	if ($(obj).attr("add_img") != "none") {
		clearTimeout(addImg_layer_obj);
		AdditionalImgGallery($(obj), board_type_class, classGubun);
		$attribute_adult_item = $(this).parent().parent().attr("Adult_NA");
	}
}

function add_image_mouseover(obj) {
	if ($(obj).parent().attr("img_cnt") == "0") return;
	var bestWrapObj = $("#bestWrap").offset();
	var board_type_class = $(obj).attr("class");
	var classGubun = $(obj).attr("classgubun");

	if (board_type_class == undefined || board_type_class == null)
		board_type_class = "";

	if ($(obj).attr("add_img") != "none") {
		clearTimeout(addImg_layer_obj);
		AdditionalImgGallery($(obj).parent().parent(), board_type_class, classGubun);
		$attribute_adult_item = $(this).parent().parent().attr("Adult_NA");

//		if ($attribute_adult_item != "NA") {
//			//기본움직이는이미지노출
//			var imgObj = $(obj).find("img");

//			imgObj.attr({ "src": imgObj.attr("gd_src").replace("_s", "") });
//		}
	}
}

Util.add_image_mouseout = function (obj) {
	if ($(obj).attr("add_img") != "none") {
		$attribute_adult_item = $(this).parent().attr("Adult_NA");
		if ($attribute_adult_item != "NA") {
			//정지이미지로원복
			var imgObj = $(obj).find("img");
			imgObj.attr({ "src": imgObj.attr("gd_src") });
		}
		addImg_layer_obj = setTimeout(ly_stillbox_hide, 100);
	}
}

function add_image_mouseout(obj) {
	if ($(obj).attr("add_img") != "none") {
		$attribute_adult_item = $(this).parent().attr("Adult_NA");
		if ($attribute_adult_item != "NA") {
			//정지이미지로원복
			var imgObj = $(obj).find("img");
			imgObj.attr({ "src": imgObj.attr("gd_src") });
		}
		addImg_layer_obj = setTimeout(ly_stillbox_hide, 100);
	}
}

/* 상품명 위에 mouseover시 quickview 자동 실행 */
var isQuickviewLayerClick = false;
var AutoQuickView = {
	t_autoqv: null,
	onmouseover: function (gd_no, delay) {
		delay = ((delay === undefined) || (delay === null) || (isNaN(delay))) ? 1000 : delay; //1000ms=1s
		this.t_autoqv = setTimeout(
			function () {
				Util.OpenQuickView(gd_no);
				$('#div_popup').bind('click', function () { isQuickviewLayerClick = true; });
				$('body').bind('click', AutoQuickView.BodyOnClick);
			}, delay);
	},
	onmouseout: function () {
		clearTimeout(this.t_autoqv);
	},

	BodyOnClick: function () {
		if (isQuickviewLayerClick == false) { //onclick out of quickview layer(#div_popup)
			$('#div_popup, body').unbind();
			Util.closeInnerPopup(null, 'div_popup'); //close quickview layer
		}

		isQuickviewLayerClick = false;
	}
};

function openGoodsImageEnlargeView(gd_no, img_index, global_order_type) {
	var url = Public.getCurrentHostUrl() + "/gmkt.inc/List/ListEnlargeImage.aspx" + "?gd_no=" + gd_no;
	if (img_index != undefined && img_index != "")
		url += "&img_index=" + img_index;

	if (global_order_type != undefined && global_order_type != "")
		url += "&global_order_type=" + global_order_type;

	Util.closeInnerPopup();
	Util.openInnerPopup(url, 671, 641, null, null, null, null, null, false, null, false);
}


function doChangeSearch() {
	if ($get("search____keyword").value.trim() == "") {
		return false;
	}

	if ($("hid_key_inflow_check") != null && $("hid_key_inflow_check").val() != undefined && $get("hid_key_inflow_check").value == "F") {
		Util.setCookie("SearchInflowCheck", "KC");
	}
	else {
		//Search관련 유입경로 쿠키추가
		if ($("#hid_main_inflow_path") != null && $("#hid_main_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "MS");
		else if ($("#hid_sub_inflow_path") != null && $("#hid_sub_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "SS");
		else
			Util.setCookie("SearchInflowCheck", "KC");
	}

	$get("search_____form").action = Public.getSearchVUrl($get("search____keyword").value);
	return true;
}

function onFavoriteTitle(URL, title) {
	if (URL == "" || URL == null)
		URL = Public._getSafeUrl(__PAGE_VALUE.WWW_SERVER, false);

	if (window.opera) { // opera
		var elem = document.createElement('a');
		elem.setAttribute('href', URL);
		elem.setAttribute('title', title);
		elem.setAttribute('rel', 'sidebar');
		elem.click();
	}
	else if (document.all) // ie
		window.external.AddFavorite(URL, title);
	else if (window.sidebar) // firefox
		window.sidebar.addPanel(title, URL, "");
	else if (navigator.appName == "Netscape") {
		alert(MultiLang.findCommonResource("Master/DefaultMasterPage.master", "ChromeBookMark"));
	}
}

function onFavorite(URL) {
	var pageTitle = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "PageTitle");

	onFavoriteTitle(URL, pageTitle);
}

function shareUrl(sns_cd, url, title, msg) {

	var refer_url, refer_title;
	if (url != "" && url != undefined)
		refer_url = encodeURIComponent(url);
	else
		refer_url = encodeURIComponent(document.location.href);

	if (title != "" && title != undefined)
		refer_title = encodeURI(title);
	else
		refer_title = encodeURI(document.title);

	if (msg != undefined)
		msg == msg;
	else
		msg == "";

	var url = Public.getWWWServerUrl("/SNS/PopupShareSNS.aspx?title=", false) + refer_title + "&share_url=" + refer_url + "&sns_cd=" + sns_cd + "&msg=" + msg;
	Util.openPopup(url, 470, 750, "sns_share");
}

//Qplay 쉐어하기
function QplayShareUrl(sns_cd, url, title, dp) {
	var refer_url, refer_title;
	if (url != "" && url != undefined)
		refer_url = encodeURIComponent(url);

	if (title != "" && title != undefined)
		refer_title = encodeURI(title);
	else
		refer_title = encodeURI(document.title);

	var url = Public.getWWWServerUrl("/SNS/PopupShareQplay.aspx?title=", false) + refer_title + "&share_url=" + refer_url + "&sns_cd=" + sns_cd + "&dp=" + dp;
	Util.openPopup(url, 930, 430, "sns_share");
}


//stop flash & play pics
function flashControl() {
	if ((Util.getCookie("stillimage") == "N" || Util.getCookie("stillimage") == "")
		&& window.location.href.indexOf("stillimage=Y") < 0) {	// full에서 still image로..
		Util.setCookie("stillimage", "Y");
		var url = null;
		if (window.location.href.indexOf("stillimage=") >= 0) {
			if (window.location.href.indexOf("stillimage=Y") >= 0)
				url = window.location.href.replace("stillimage=Y", "");
			else if (window.location.href.indexOf("stillimage=N") >= 0)
				url = window.location.href.replace("stillimage=Y", "");
		}

		if (url != null)
			window.location.href = url;
		else
			window.location.reload();

	} else {	// still에서 full image로..
		Util.setCookie("stillimage", "N");

		var url = null;
		if (window.location.href.indexOf("stillimage=") >= 0) {
			if (window.location.href.indexOf("stillimage=Y") >= 0)
				url = window.location.href.replace("stillimage=Y", "");
			else if (window.location.href.indexOf("stillimage=N") >= 0)
				url = window.location.href.replace("stillimage=Y", "");
		}

		if (url != null)
			window.location.href = url;
		else
			window.location.reload();
	}
}
function initFlashControl() {
	if (Util.getCookie("stillimage") == "Y") {
		flashControl();
	}
}

/* 상단 카테고리 이동*/
function __goCategory(gdlc_cd) {
	window.location.href = Public.getLoginServerUrl("/Category/Default.aspx") + "?gdlc_cd=" + gdlc_cd;
}
/* aspx End*/

/* ACKeyword.js */
var ACKeyword = function () { };
var ACidx = -1; // [0,9] current index
var ACmax = 0; // list.length - 1
var ACTotalidx = 0;
var html_out = "";
var ACKeywordOver = false;
var ACKeywordTimer;
var ACKeywordLast;

ACKeyword.over = function () {
	clearTimeout(ACKeywordTimer);
	ACKeywordOver = true;
}

ACKeyword.hide = function (delay, overcheck) {
	ACKeywordOver = false;
	if (overcheck == undefined)
		overcheck = false;

	ACKeywordTimer = setTimeout("ACKeyword.hidelayer(" + overcheck + ")", delay);
	ACidx = -1;
}

ACKeyword.hidelayer = function (overcheck) {
	if (overcheck && ACKeywordOver)
		return;

	$get("ac_layer").style.display = "none";
}

var recent_keyword_list = {};
var recent_keyword_count = 0;
ACKeyword.show = function () {
	recent_keyword_list = {};
	recent_keyword_count = 0;

	ACKeywordOver = true;
	if ($get("search____keyword").value.trim() == "") //2011-07-07. youk항상 열리도록한다.
	{
		var recent_keyword = Util.getCookie("recentKeyword");

		$get("ac_layer").style.display = "";
		html_out = "";
		var recent_keywrod_split = recent_keyword.split('_');
		var i = 0;

		if (recent_keywrod_split.length > 0 && recent_keyword != "") {
			$("#ac_list").attr({ "style": "display:" });
			//캐싱 키워드 7개까지만 노출
			for (i = 0; i < recent_keywrod_split.length && i < 7; i++) {
				var keyword = recent_keywrod_split[i];
				recent_keyword_list[keyword] = '';
				recent_keyword_count++;

				html_out += String.format("<li id='ACliidx{0}' onmouseover='ACKeyword.mouseover({0})' onmousemove='ACKeyword.mousemove({0})'><a id='ACidx{0}' title=\"{3}\" href=\"javascript:ACKeyword.doSearch(\'{1}\', \'{4}\');\">{2}</a></li>", i, recent_keywrod_split[i], recent_keywrod_split[i], recent_keywrod_split[i], "");
			}

			ACTotalidx = i;
		}
		else {
			$("#ac_list").attr({ "style": "display:none;" });
		}

		$get("ac_list").innerHTML = html_out;

		//추천키워드 노출
		ACKeywordRecommand.Call();

	}
	else {
		ACKeyword.Call();
	}
	ACidx = -1;
}

ACKeyword.onkeyup = function (e) {
	var code;
	if (!e) e = window.event
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;

	//자동검색으로 바로 들어왔는지 아님 검색어로 검색해서 들어왔는지 판단
	if ($("#hid_key_inflow_check") != null && $("#hid_key_inflow_check").val() != undefined)
		$get("hid_key_inflow_check").value = "T";


	//enter 입력인 경우 검색 되도록
	if ($get("ac_layer").style.display == "none" && code != 13) {
		ACKeyword.show();
		ACKeyword.Call();
		return;
	}
	try {
		if (code != 38 && code != 40 && code != 13 && code != 27 && code != 229) ACKeyword.Call();
		if (code == 38 || code == 40) { // ↑ or ↓
			if ($get("ACidx" + ACidx)) $get("ACliidx" + ACidx).style.backgroundColor = "";

			var rst = "";

			// ↑
			if (code == 38) {
				ACidx = (ACidx > 0) ? --ACidx : ACmax;
				rst = $get("ACidx" + ACidx).innerHTML;
				$get("ac_total_view").href = Public.getSearchVUrl(rst) + "?keyword=" + eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')");
				ACKeywordTotal.Call(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
				ACKeywordTotal.CallPlusItem(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
			}
			// ↓
			else if (code == 40) {
				ACidx = (ACidx < ACmax) ? ++ACidx : 0;
				rst = $get("ACidx" + ACidx).innerHTML;
				$get("ac_total_view").href = Public.getSearchVUrl(rst) + "?keyword=" + eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')");
				ACKeywordTotal.Call(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
				ACKeywordTotal.CallPlusItem(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
			}

			if ($get("ACidx" + ACidx)) $get("ACliidx" + ACidx).style.backgroundColor = "#e0edff";
			if ($get("ACidx" + ACidx)) {
				$get("search____keyword").value = $get("ACidx" + ACidx).title;
				if ($("#bottom_search____keyword") != undefined) $get("bottom_search____keyword").value = $get("ACidx" + ACidx).title;
			}

		}
		else if (code == 27) ACKeyword.hide(0); // Esc
	} catch (ex) { }
}

ACKeyword.onkeydown = function (e) {
	ACKeyword.show();
}

ACKeyword.Call = function () {
	if (ACKeywordLast == $get("search____keyword").value)
		return;

	$get("search___connect_url").value = "";
	if ($get("search____keyword").value.match(/\S/gi) == null) return;
	if ($get("search____keyword").value.indexOf("\\") >= 0) return;

	var param = new RMSParam();
	param.add("keyword", $get("search____keyword").value);
	RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_SearchAjaxService.asmx"), "Search ", param.toJson(), ACKeyword.callBack);

	ACKeywordLast = $get("search____keyword").value;
	return;
};

ACKeyword.callBack = function (result, svc, methodName, xmlHttpasync) {
	var i;
	try { ACmax = result.ResultList.length; } catch (ex) { }
	try {
		var rst = "";
		var rst_mat = "";
		var rst_mat_auto = "";
		var keyw = $get("search____keyword").value;
		var keyw_spn = "";
		var keyw_spn_auto = "";
		var patt1 = eval("/" + keyw + "/i");
		var patt1_auto = "";
		var keyw_auto_change = "";
		html_out = "";
		if (keyw != result.auto_change) keyw_auto_change = result.auto_change;

		$get("search____keyword_auto_change").value = result.auto_change;

		if (ACmax > 10) ACmax = 10;

		if (ACmax == 0) {
			$("#ac_layer").css('display', 'none');
			$("#ac_total_preview").css('display', 'none');
		}
		else {
			$("#ac_layer").css('display', '');
			$("#ac_total_preview").css('display', '');

			for (i = 0; i < ACmax; ++i) {
				rst = result.ResultList[i];
				rst_mat = rst.match(patt1);
				keyw_spn = rst_mat != null ? String.format("<span>{0}</span>", rst_mat) : rst; //2012-09-07, Kim Tae Jong
				rst = eval("rst.replace(/(^" + keyw + "|" + keyw + "$)/i, '" + keyw_spn + "')");
				if (keyw_auto_change != "") {
					patt1_auto = eval("/" + keyw_auto_change + "/i");
					rst_mat_auto = rst.match(patt1_auto);
					keyw_spn_auto = rst_mat_auto != null ? String.format("<span>{0}</span>", rst_mat_auto) : rst; //2012-08-17, Youk
					rst = eval("rst.replace(/(^" + keyw_auto_change + "|" + keyw_auto_change + "$)/i, '" + keyw_spn_auto + "')");
				}

				html_out += String.format("<li id='ACliidx{0}' onmouseover='ACKeyword.mouseover({0})' onmousemove='ACKeyword.mousemove({0})'><a id='ACidx{0}' title=\"{3}\" href=\"javascript:ACKeyword.doSearch(\'{1}\', \'{4}\');\">{2}</a></li>", i, result.ResultList[i], rst, result.ResultList[i], keyw_auto_change);
			}
			$("#ac_list").attr({ "style": "border:none;" });
			$("#ac_recomm").attr({ "style": "display:none;" });

			$get("ac_list").innerHTML = html_out;

			//instant Search 검색어 자동완성 검색어의 첫번째 데이터로 기본 조회
			var rst1 = $get("ACidx0").innerHTML;
			$get("ac_total_view").href = Public.getSearchVUrl(rst1) + "?keyword=" + encodeURI(eval("rst1.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
			ACKeywordTotal.Call(eval("rst1.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
			ACKeywordTotal.CallPlusItem(keyw);
		}
	} catch (ex) { }
};

ACKeyword.doSearch = function (keyword, auto_change, isListresult) {
	if (keyword == "" || keyword == null) keyword = $get("search____keyword").value;

	document.search_____form.keyword.value = keyword;
	document.search_____form.keyword_auto_change.value = auto_change;
	document.search_____form.isListresult = isListresult;

	if (__PAGE_VALUE.VIEW_SITEID == "m18") { }
	else $get("search_____form").action = Public.getSearchVUrl(keyword);

	if (isListresult == "best") {
		Util.setCookie("SearchInflowCheck", "KC");
	} else {
		//Search관련 유입경로 쿠키추가
		if ($("#hid_main_inflow_path") != null && $("#hid_main_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "MS");
		else if ($("#hid_sub_inflow_path") != null && $("#hid_sub_inflow_path").val() != undefined)
			Util.setCookie("SearchInflowCheck", "SS");
		else
			Util.setCookie("SearchInflowCheck", "KC");
	}

	$get("search_____form").submit();
};

var mousemove_flag = 0;
var mouseover_idx = 100;

ACKeyword.mouseover = function (idx) {
	var i = 0;
	ACidx = idx;

	if (mousemove_flag == 1) {
		var rst = $get("ACidx" + idx).innerHTML;
		$get("ac_total_view").href = Public.getSearchVUrl(rst) + "?keyword=" + encodeURI(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
		ACKeywordTotal.Call(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
		ACKeywordTotal.CallPlusItem(eval("rst.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));

		for (i = 0; i < ACmax; ++i) $get("ACliidx" + i).style.backgroundColor = (i == ACidx) ? "#e0edff" : "white";
		mousemove_flag = 0;
	}
};

ACKeyword.mousemove = function (idx) {
	if (idx == 100 || idx != mouseover_idx) {
		mousemove_flag = 1;
		mouseover_idx = idx;
	}
};

ACKeyword.search_onfocus = function (obj) {
	$(obj).css('text-align', 'left');
	$(obj).val($(obj).val());
	ACKeyword.setCaretPosition(obj, $(obj).val().length);

	$(obj).removeClass('g_adsInput');
	if ($get("search____keyword").value.trim() != "" && $get("search____keyword_ad").value == $get("search____keyword").value) {
		obj.value = "";
		$get("search___connect_url").value = '';
	}
};

ACKeyword.setCaretPosition = function (obj, pos) {
	if (obj.setSelectionRange) {
		obj.focus();
		obj.setSelectionRange(pos, pos);
	}
	else if (obj.createTextRange) {
		var range = obj.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
};

//서브페이지
ACKeyword.mousedown = function () {
	if ($("#topNotiBanner").height() > 0)
		$('#ac_layer').css({ 'top': ($('#search____keyword').offset().top - 3), 'left': 305 });
	else
		$('#ac_layer').css({ 'top': ($('#search____keyword').offset().top + 23), 'left': 305 });
};

/* ACKeyword.js End */
var ACKeywordRecommand = function () { };

ACKeywordRecommand.Call = function () {
	var param = new RMSParam();
	param.add("svc_nation_cd", __PAGE_VALUE.VIEW_SITEID == "m18" ? "me" : GMKT.ServiceInfo.nation);
	param.add("device_cd", "front");
	RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_CategoryAjaxService.asmx"), "GetKeywordPointIndexList2", param.toJson(), ACKeywordRecommand.callBack);
	return;
};

ACKeywordRecommand.callBack = function (result, svc, methodName, xmlHttpasync) {
	html_out = "";

	try { ACmax = result.Rows.length; } catch (ex) { }
	try {
		if (ACmax <= 0) { return false; } //validation
		$("#ac_recomm").show();

		//order and shuffle: by display(D, Y)
		var keyword_list_D = new Array();
		var keyword_list_Y = new Array();
		var list = result.Rows;

		for (var i = 0; i < list.length; i++) { //split
			var item = list[i];
			var display = item.display;

			if (display == 'D') { keyword_list_D.push(item); }
			else if (display == 'Y') { keyword_list_Y.push(item); }
		}

		keyword_list_D = ETC.GetShuffleArray(keyword_list_D); //shuffle
		keyword_list_Y = ETC.GetShuffleArray(keyword_list_Y);
		list = keyword_list_D.concat(keyword_list_Y); //and order

		//while: max_recommend_keyword
		var max_recommend_keyword = (10 - recent_keyword_count); //pair: isContain
		var i = 0, j = 0;
		while (i < list.length && j < max_recommend_keyword) {
			//isContain
			var keyword = list[i].keyword;
			var isContain = (isNaN(recent_keyword_list[keyword])) ? false : true;

			if (isContain == false) {
				var index = j + recent_keyword_count;
				var li_class = (j == 0) ? 'best' : '';
				var li_id = 'ACliidx' + index;
				var a_id = 'ACidx' + index;

				html_out +=
					'<li class="' + li_class + '" id="' + li_id + '" onmouseover="ACKeyword.mouseover(' + index + ')" onmousemove="ACKeyword.mousemove(' + index + ');">' +
					'	<a id="' + a_id + '" title="' + keyword + '" href="javascript:ACKeyword.doSearch(\'' + keyword + '\', \'\',\'best\');">' + keyword + '</a>' +
					'</li>';

				j++; //while
				ACTotalidx++;
				if (ACmax <= ACTotalidx) { break; }
			}

			i++; //while
		}
		ACmax = ACTotalidx;
		ACTotalidx = 0;

		$get("ac_recomm").innerHTML = html_out;

		//instant Search 검색어 자동완성 검색어의 첫번째 데이터로 기본 조회
		if ($get("ACidx0") != null) {
			var rst1 = $get("ACidx0").innerHTML;
			$get("ac_total_view").href = Public.getSearchVUrl(rst1) + "?keyword=" + eval("rst1.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')");
			ACKeywordTotal.Call(eval("rst1.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
			ACKeywordTotal.CallPlusItem(eval("rst1.replace(/(<(\"[^\"]*\"|\'[^\']*\'|[^\'\">])*>)/ig, '')"));
		}
	} catch (ex) { }
};

/* ACKeywordRecommand.js End */
var ACKeywordTotal = function () { };

ACKeywordTotal.Call = function (keyword) {
	var multisite_id = __PAGE_VALUE.VIEW_SITEID == "m18" ? __PAGE_VALUE.VIEW_SITEID : "DEFAULT";
	var param = new RMSParam();
	param.add("keyword", keyword);
	param.add("pageSize", "3");
	param.add("target_server_kind", GMKT.DeviceInfo == "Bot" ? "B" : "A");
	param.add("multisite_id", multisite_id);
	param.add("global_yn", "");
	param.add("gmkt_lang", GMKT.ServiceInfo.ClientLang.replace(/-/g, "_"));
	RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_SearchAjaxService.asmx"), "TotalSearchSimple", param.toJson(), ACKeywordTotal.callBack);
};

ACKeywordTotal.callBack = function (result, svc, methodName, xmlHttpasync) {
	var html_out, i
	var resultcnt = 0;
	var img_url = "";
	var goods_url = "";
	html_out = "";

	try { resultcnt = result.ResultList.length; } catch (ex) { }
	try {

		if (resultcnt == 0)
			$("#ac_total_preview").css('display', 'none');
		else
			$("#ac_total_preview").css('display', '');

		for (var i = 0; i < resultcnt; i++) {
			var idx = i;

			if (idx == 0 && $("#ac_total_link0").attr("existPlus") == "Y")
				continue;

			if ($("#ac_total_link0").attr("existPlus") == "Y" && $("#ac_total_link0").attr("gdNo") == result.ResultList[idx]["GD_NO"])
				idx = 0;

			$("#ac_total_link0").attr("existPlus", "N");
			$("#ac_total_link0").attr("gdNo", "");

			img_url = Public.getGoodsImagePath(result.ResultList[idx]["IMG_CONTENTS_NO"], "S");
			goods_url = Public.getGoodsServerUrl("/goods/goods.aspx?goodscode=" + result.ResultList[idx]["GD_NO"], false);

			$get("ac_total_link" + i).innerHTML = result.ResultList[idx]["GD_NM"];
			$get("ac_total_link" + i).href = goods_url;
			$get("ac_total_img" + i).innerHTML = "<p><a href=\"" + goods_url + "\"><img width=\"65\" height=\"65\" src=\"" + img_url + "\" /></a></p>";

			var aution_kind = "";
			var sell_price = 0;

			aution_kind = result.ResultList[idx]["AUCTION_KIND"];
			sell_price = parseFloat(result.ResultList[idx]["SELL_PRICE_VIEW"]);

			//Price(경매/일반)
			if (result.ResultList[idx]["TRAD_WAY"] == "T2") { // auction items
				ACKeywordTotal.GetBidPrice(result.ResultList[idx]["SUCC_BID_POSS_PRICE"], result.ResultList[idx]["SELL_PRICE"], aution_kind, i);
			}
			else { // general items
				var dealprice, discount_price;

				if (result.ResultList[idx]["DEAL_PRICE"] != null)
					dealprice = parseFloat(result.ResultList[i]["DEAL_PRICE"]);
				else
					dealprice = 0;

				if (result.ResultList[idx]["DISCOUNT_PRICE"] != null)
					discount_price = parseFloat(result.ResultList[idx]["DISCOUNT_PRICE"]);
				else
					discount_price = 0;

				//Price Info Setting
				ACKeywordTotal.GetSellPrice(dealprice, sell_price, discount_price, i);
			}

			var icon_info = "";
			var gd_kind = result.ResultList[idx]["GD_KIND2"];
			var client_lang = GMKT.ServiceInfo.ClientLang;

			if (gd_kind == 14 || gd_kind == 15 || gd_kind == 16 || gd_kind == 17)
				icon_info = icon_info + "<img src=" + ETC.GetStaticImageURLQoo10("common/image/icon_premium.png") + " alt=\"Premium\" /> ";

			//상품아이콘 및 브랜드 노출
			if (result.ResultList[idx]["TRAD_WAY"] == "T2") { // auction items
				if (result.ResultList[idx]["CHARITY_ITEM_YN"] == "Y") {
					icon_info = icon_info + "<img src=" + ETC.GetStaticImageURLQoo10("common/image/icon_atcrt.png") + " alt=\"CharityAuction\" />";
				}
				else {
					if (aution_kind == "A03")
						icon_info = icon_info + "<img src=" + ETC.GetStaticImageURLQoo10("common/image/icon_atlk.png") + " alt=\"Lucky\" />";
					else
						icon_info = icon_info + "<img src=" + ETC.GetStaticImageURLQoo10("common/image/icon_at.png") + " alt=\"Auction\" />";
				}
			}
			else {
				icon_info = icon_info + "<em>[" + result.ResultList[idx]["NICKNAME"] + "]</em>";
			}
			$get("ac_total_nick" + i).innerHTML = icon_info;
		}

	} catch (ex) { }
};

ACKeywordTotal.CallPlusItem = function (keyword) {
	if (ACKeywordLast == keyword)
		return;

	if (keyword != "") {
		param = new RMSParam();
		param.add("keyword", keyword);
		RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_MainAjaxService.asmx"), "GetPlusKewordItem ", param.toJson(), ACKeywordTotal.callBackPlusItem);
	}
}

ACKeywordTotal.callBackPlusItem = function (result, svc, methodName, xmlHttpasync) {
	var html_out, i
	var img_url = "";
	var goods_url = "";
	html_out = "";

	$("#ac_total_link0").attr("existPlus", "N");

	try {
		if (result["GD_NO"] != "" && result["GD_NO"] != null) {
			$("#ac_total_link0").attr("existPlus", "Y");
			$("#ac_total_link0").attr("gdNo", result["GD_NO"]);

			img_url = Public.getGoodsImagePath(result["IMG_CONTENTS_NO"], "S");
			goods_url = Public.getGoodsServerUrl("/goods/goods.aspx?goodscode=" + result["GD_NO"], false);

			$get("ac_total_link0").innerHTML = result["GD_NM"];
			$get("ac_total_link0").href = goods_url;
			$get("ac_total_img0").innerHTML = "<p><a href=\"" + goods_url + "\"><img width=\"65\" height=\"65\" src=\"" + img_url + "\" /></a></p>";

			var aution_kind = "";
			var sell_price = 0;

			aution_kind = result["AUCTION_KIND"];
			sell_price = parseFloat(result["SELL_PRICE_VIEW"]);

			//Price(경매/일반)
			if (result["TRAD_WAY"] == "T2") { // auction items
				ACKeywordTotal.GetBidPrice(result["SUCC_BID_POSS_PRICE"], result["SELL_PRICE"], aution_kind, i);
			}
			else { // general items
				var dealprice, discount_price;

				if (result["DEAL_PRICE"] != null)
					dealprice = parseFloat(result["DEAL_PRICE"]);
				else
					dealprice = 0;

				if (result["DISCOUNT_PRICE"] != null)
					discount_price = parseFloat(result["DISCOUNT_PRICE"]);
				else
					discount_price = 0;

				//Price Info Setting
				ACKeywordTotal.GetSellPrice(dealprice, sell_price, discount_price, 0);
			}

			var icon_info = "";
			var gd_kind = result["GD_KIND2"];
			var client_lang = GMKT.ServiceInfo.ClientLang;

			icon_info = icon_info + "<img src=" + ETC.GetStaticImageURLQoo10("common/image/icon_plus.png") + " alt=\"Plus\" /> ";

			$get("ac_total_nick0").innerHTML = icon_info;
		}

	} catch (ex) { }
}

ACKeywordTotal.GetBidPrice = function (bid, buy_now_price, aution_kind, i) {
	var price_info = "";
	if (aution_kind == "A03")
		price_info = price_info + "<em>" + MultiLang.findCommonResource("Master/DefaultMasterPage.master", "Max. Bid") + "</em><strong>" + PriceUtil.FormatCurrencySymbol(bid) + "</strong>";
	else
		price_info = price_info + "<em>" + MultiLang.findCommonResource("Master/DefaultMasterPage.master", "Current") + "</em><strong>" + PriceUtil.FormatCurrencySymbol(bid) + "</strong>";

	if (buy_now_price > 0)
		price_info = price_info + "<em>" + MultiLang.findCommonResource("Master/DefaultMasterPage.master", "Buy Now") + "</em><strong>" + PriceUtil.FormatCurrencySymbol(buy_now_price) + "</strong>";

	$get("ac_total_price" + i).innerHTML = price_info;
};

ACKeywordTotal.GetSellPrice = function (dealprice, sell_price, discount_price, i) {
	var price_info = "";

	//품절(Sold Out)
	if (sell_price < 0) {
		$get("ac_total_price" + i).innerHTML = "Sold Out";
	}
	else {
		if (dealprice > 0 && dealprice > sell_price)
			price_info = price_info + "<del>" + PriceUtil.FormatCurrencySymbol(dealprice) + "</del>";

		if (discount_price > 0) {
			price_info = price_info + "<span>" + PriceUtil.FormatCurrencySymbol(sell_price) + "<em>(" + PriceUtil.FormatCurrencySymbol(discount_price) + "▼)</em></span>";
			price_info = price_info + "<strong>" + PriceUtil.FormatCurrencySymbol(sell_price - discount_price) + "</strong>";
		}
		else {
			price_info = price_info + "<strong>" + PriceUtil.FormatCurrencySymbol(sell_price) + "</strong>";
		}

		$get("ac_total_price" + i).innerHTML = price_info;
	}
};

/* ACKeywordTotal.js End */

/* Layout Start */
var Layout = function () { };
/* Shop All Categories */
Layout.shopAllCate = function () {
	//list_todeal
	var list_todeal_count = $('#list_todeal ul li').index($('#list_todeal ul li:last')) + 1;
	var index_prev = 0;
	var index_next = 0;

	function list_todeal_roll() { //검색창 우측 투딜 롤링
		index_prev = index_next;
		index_next = (index_next + 1) % list_todeal_count;

		$('#list_todeal ul li:eq(' + index_prev + ')').fadeOut("slow");
		$('#list_todeal ul li:eq(' + index_next + ')').show();
	}

	if (list_todeal_count > 1) { //2개 이상일 경우에 롤링한다.
		setInterval(list_todeal_roll, 5 * 1000);
	}
}
/* END Shop All Categories */

/* Footer */
Layout.Footer = function () {
	$(function () {
		$(".familysite .inner").click(function () {
			$(".familysite .layer").css("visibility", "visible");
		});
	});
	$(".familysite .layer").mouseover(function () {
		$(this).css("visibility", "visible");
	}).mouseout(function () {
		$(this).css("visibility", "hidden");
	});
	$(".familysite .layer li").mouseover(function () {
		$(this).addClass("selected");
	}).mouseout(function () {
		$(this).removeClass("selected");
	});
};
/* Footer */

/* Layer Content */
Layout.layerContent = function (doID) {
	if (!document.getElementsByTagName) return false;
	if (!document.getElementById) return false;

	var showing = document.getElementById(doID);

	if (showing.style.display == "" || showing.style.display == "none") {
		showing.style.display = "block";

		showing.onmouseover = function () {
			showing.style.display = "block";
		}
		showing.onfocus = function () {
			showing.style.display = "block";
		}
		showing.onblur = function () {
			showing.style.display = "none";
		}
		showing.onmouseout = function () {
			showing.style.display = "none";
		}
	} else {
		showing.style.display = "none";
	}
};
/* END Layer Content */

//partners logo rolling
Layout.partnersRolling = function () {
	$partners_count = $('#partners_ul li').length;

	$hide_li = function (index) { $('#partners_ul li:eq(' + index + ')').hide(); }
	$show_li = function (index) { $('#partners_ul li:eq(' + index + ')').show(); }
	$index_loop = function () { $partners_index = ($partners_index + 1 + $partners_count) % $partners_count; }

	$rolling_partners = function () {
		$hide_li($partners_index);
		$index_loop();
		$show_li($partners_index);
	}

	if ($partners_count > 1) {
		$partners_index = 0;
		setInterval($rolling_partners, 4 * 1000);
	} else if ($partners_count == 1) {
		$('#partners_ul li').show();
	}
}

/* Quick Menu */
// Internet Explorer 6 and below
var IE6 = jQuery.browser.msie && jQuery.browser.version == "6.0"; // false/*@cc_on || @_jscript_version < 5.7@*/; --> XP3에서는 요게 안먹는듯해서 jquery로 변경.

// Mobile
var isMobile = navigator.userAgent.match(/Android/i) != null
Layout.setRightWing = function () {
//	var target = $("#quickInfo");
//	if (__PAGE_VALUE.VIEW_SITEID == "lotte") $('#quickInfo .inner').css({ "top": "-100px" });
//	else {
//		if (Layout.isMain())
//			if (IE6)
//				$('#quickInfo .inner').css({ "top": "-46px" });
//	}
//	if (IE6 || isMobile)
//		Layout.initMoving(target);
};

// ie6, ipad
// 공통 moving 함수로
// target : 움직일 element $('#id') / topLimit : 상단 기준(없을때는 gnb top) / btmLimit : 하단 스크롤 기준 (없으면 무시)
Layout.initMoving = function (target, topLimit, btmLimit) {
	var currentPosition = parseInt(target.css("top"));
	try {
		if (topLimit == undefined) topLimit = $("#gnb_menu").position().top;
	} catch (ex) { }


	$(window).scroll(function () {
		try {
			var position = $(window).scrollTop();
			if ($("#gnb_menu").offset().top > $("#gnb_menu").position().top)
				position = position - ($("#gnb_menu").offset().top - $("#gnb_menu").position().top); //상단 gnb가 있는 경우 scroll 조정

			// 현재 스크롤바의 위치값을 반환합니다.
			if (position > topLimit) {
				if (btmLimit != undefined && btmLimit < position) currentPosition = btmLimit;
				else currentPosition = position + 6;
			}
			else currentPosition = topLimit;
			$("#quickInfo").stop().animate({ "top": currentPosition + "px" }, 300); //jquery animate를 이용해서 날개 배너 scroll처리

		} catch (e) {

		}
	});
};
// else
if (!IE6 && !isMobile) {
	$(window).scroll(function () {
		try {
			var gnb_top = $get("gnb_menu") != null ? $("#gnb_menu").offset().top : 0; //youk, 2011-08-29, Main의 gnbMenu추가
			$(window).scrollTop() > gnb_top ? $('#quickInfo .g_fixed').css({ "position": "fixed", "top": "6px" }) : $('#quickInfo .g_fixed').css({ "position": "", "top": gnb_top + "px" });

			//Smart Window Position
			var yPosition = $(document).scrollTop();
			var windowHeight = $('body').height() - $("#quickInfo").height();

			// 창 사이즈가 한계를 넘어갈 때 스크롤이 계속 내려가지 않도록 처리
			if (windowHeight <= yPosition) {
				yPosition = windowHeight;
			}

			var comparePosition = (Layout.isMain() && __PAGE_VALUE.VIEW_SITEID != "m18") ? 80 : 100;
			if (yPosition > comparePosition) {
				$('#quickInfo .inner').css({ "top": yPosition - 125 });
				//$('#quickInfo .inner .group_ctrl').fadeIn("slow");
			}
			else {
				//$('#quickInfo .inner').css({ "top": (Layout.isMain() && __PAGE_VALUE.VIEW_SITEID != "m18") ? "-46px" : "0px" });
				if (__PAGE_VALUE.VIEW_SITEID == "m18")
					$('#quickInfo .inner').css({ "top": "0px" });
				else if (__PAGE_VALUE.VIEW_SITEID == "lotte")
					$('#quickInfo .inner').css({ "top": "-100px" });
				else if (Layout.isMain())
					$('#quickInfo .inner').css({ "top": "-46px" });
				else
					$('#quickInfo .inner').css({ "top": "0px" });

				//$('#quickInfo .inner .group_ctrl').fadeOut("slow");
			}
		} catch (e) {
		}
	});
}

// 화면의 현재 scroll 위치 좌표 구하기
Layout.getNowScroll = function () {
	var de = document.documentElement;
	var b = document.body;
	var now = {};

	now.X = document.all ? (!de.scrollLeft ? b.scrollLeft : de.scrollLeft) : (window.pageXOffset ? window.pageXOffset : window.scrollX);
	now.Y = document.all ? (!de.scrollTop ? b.scrollTop : de.scrollTop) : (window.pageYOffset ? window.pageYOffset : window.scrollY);

	return now;
}

//날개 한페이지씩 스크롤 버튼 처리
Layout.moveUp = function () {
	var window_height = $(window).height();
	var scrollTop = $(window).scrollTop();
	$('html, body').animate({ scrollTop: scrollTop - window_height }, 400);
}

Layout.moveDown = function () {
	var window_height = $(window).height();
	var scrollTop = $(window).scrollTop();
	$('html, body').animate({ scrollTop: scrollTop + window_height }, 400);

}

Layout.moveTop = function () {
	var window_height = $(window).height();
	var scrollTop = $(window).scrollTop();
	$('html, body').animate({ scrollTop: 0 }, 400);
}

Layout.moveBottom = function () {
	var window_height = $(document).height();
	var scrollTop = $(window).scrollTop();
	$('html, body').animate({ scrollTop: $(document).height() }, 400);

}

Layout.isMain = function () {
	var ismain = false;
	try {
		return $get("hid_main_page_flag").value == "true";
	} catch (ex) {
		return false;
	}
	return false;
}
/* END Quick Menu*/

/* Layout End*/


/* Countdown Timer 

var TargetDate = "12/31/2020 5:00 AM";
var DisplayFormat = "%%H%% : %%M%% : %%S%%";
var FinishMessage = "ENDED";

var cd1 = new Countdown("HHMMSS", "temp1", TargetDate, DisplayFormat, FinishMessage);
var cd2 = new Countdown("HHMMSS", "temp2", TargetDate, DisplayFormat, FinishMessage);

*/
var countdown_event = new Array();
var Countdown = function () { };
Countdown.Create = function (DisplayType, TargetID, TargetDate, DisplayFormat, FinishMessage, CountStepper, CountActive, LeadingZero) {
	if (countdown_event[TargetID] != undefined) { clearTimeout(countdown_event[TargetID]); }

	if (typeof (DisplayType) == "undefined") DisplayType = "HHMMSS";
	if (typeof (TargetID) == "undefined") TargetID = "countdown";
	if (typeof (TargetDate) == "undefined") TargetDate = "12/31/2020 5:00 AM";
	if (typeof (DisplayFormat) == "undefined") DisplayFormat = "%%D%% Days, %%H%% : %%M%% : %%S%%";
	if (typeof (FinishMessage) == "undefined") FinishMessage = "Ended";
	if (typeof (CountStepper) != "number") CountStepper = -1;
	if (typeof (CountActive) == "undefined") CountActive = true;
	if (typeof (LeadingZero) == "undefined") LeadingZero = true;

	CountStepper = Math.ceil(CountStepper);
	if (CountStepper == 0) CountActive = false;
	var dthen = new Date(TargetDate);
	var dnow = new Date(GMKT.ServiceInfo.ServerTime);

	//if (GMKT.ServiceInfo.nation == "JP") dnow.setTime(dnow.getTime() + (1 * 60 * 60 * 1000));

	if (DisplayType == "TXT")
		Countdown.CountBackTxt(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero, DisplayFormat, FinishMessage);
	else if (DisplayType == "HHMMSS")
		Countdown.CountBackHHMMSS(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if (DisplayType == "SPAN")
		Countdown.CountBackSPAN(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if (DisplayType == "CURRENT")
		Countdown.CountBackNOW(Math.floor((CountStepper > 0 ? new Date(dnow) : new Date(dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if (DisplayType == "MobileMain")
		Countdown.CountBackMobileMain(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if (DisplayType == "MobileIndex6")
		Countdown.CountBackMobileIndex6(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if (DisplayType == "LuckyPrice")
		Countdown.CountBackMobileLuckyPrice(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
	else if(DisplayType == "DailyDealTimer")
		Countdown.CountDailyDealMain(Math.floor((CountStepper > 0 ? new Date(dnow - dthen) : new Date(dthen - dnow)).valueOf() / 1000), TargetID, (Math.abs(CountStepper) - 1) * 1000 + 990, CountActive, CountStepper, LeadingZero);
};

Countdown.calc = function (secs, num1, num2, leadingzero) {
	s = ((Math.floor(secs / num1)) % num2).toString();
	if (leadingzero && s.length < 2) s = "0" + s;
	return "<b>" + s + "</b>";
}

Countdown.CountBackTxt = function (secs, id, period, active, countstepper, leadingzero, DisplayFormat, FinishMessage) {
	if (secs < 0) {
		$get(id).innerHTML = FinishMessage;
		return;
	}
	var DisplayStr = DisplayFormat.replace(/%%D%%/g, Countdown.calc(secs, 86400, 100000, leadingzero));
	DisplayStr = DisplayStr.replace(/%%H%%/g, Countdown.calc(secs, 3600, 24, leadingzero));
	DisplayStr = DisplayStr.replace(/%%M%%/g, Countdown.calc(secs, 60, 60, leadingzero));
	DisplayStr = DisplayStr.replace(/%%S%%/g, Countdown.calc(secs, 1, 60, leadingzero));

	$get(id).innerHTML = DisplayStr;
	if (active) countdown_event[id] = setTimeout("Countdown.CountBackTxt(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "', '" + DisplayFormat + "', '" + FinishMessage + "')", period);
}

Countdown.CountBackHHMMSS = function (secs, id, period, active, countstepper, leadingzero) {
	var ids = id.split(",");

	for (i = 0; i < ids.length; i++) {
		var _id = ids[i];
		if (secs < 0) {
			$get("hh_" + _id).innerHTML = "00";
			$get("mm_" + _id).innerHTML = "00";
			$get("ss_" + _id).innerHTML = "00";
			active = false;
			return;
		}

		$get("hh_" + _id).innerHTML = Countdown.calc(secs, 3600, 24, leadingzero);
		$get("mm_" + _id).innerHTML = Countdown.calc(secs, 60, 60, leadingzero);
		$get("ss_" + _id).innerHTML = Countdown.calc(secs, 1, 60, leadingzero);
	}

	if (active) countdown_event[id] = setTimeout("Countdown.CountBackHHMMSS(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountBackMobileIndex6 = function (secs, id, period, active, countstepper, leadingzero) {
	var hh, mm, ss;

	if (secs < 0) {
		hh = "00";
		mm = "00";
		ss = "00";
		active = false;
	}
	else {
		hh = Countdown.calc(secs, 3600, 24, leadingzero);
		mm = Countdown.calc(secs, 60, 60, leadingzero);
		ss = Countdown.calc(secs, 1, 60, leadingzero);
	}

	$('#' + id).html(hh + ':' + mm + ':' + ss);

	if (active) countdown_event[id] = setTimeout("Countdown.CountBackMobileIndex6(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountBackMobileLuckyPrice = function (secs, id, period, active, countstepper, leadingzero) {
	var dd, hh, mm, ss;

	if (secs < 0) {
		dd = 0;
		hh = "00";
		mm = "00";
		ss = "00";
		active = false;
	} else {
		dd = (Math.floor(secs / (60 * 60 * 24)) % 365);
		hh = (Math.floor(secs / (60 * 60)) % 24); if (hh < 10) { hh = '0' + hh; }
		mm = (Math.floor(secs / 60) % 60); if (mm < 10) { mm = '0' + mm; }
		ss = (secs % 60); if (ss < 10) { ss = '0' + ss; }
	}

	var day = __ClientResource["__master/basemasterpage.master__day"];
	var days = __ClientResource["__master/basemasterpage.master__days"];
	var html = (dd == 0) ? hh + ' : ' + mm + ' : ' + ss :
				(dd == 1) ? dd + day + ' ' + hh + ':' + mm + ':' + ss :
				(dd >= 10) ? dd + days :
				(dd > 1) ? dd + days + ' ' + hh + ':' + mm + ':' + ss :
				'';

	$('#' + id).html(html);

	if (active) countdown_event[id] = setTimeout("Countdown.CountBackMobileLuckyPrice(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountBackMobileMain = function (secs, id, period, active, countstepper, leadingzero) {
	var hh, mm, ss;

	if (secs < 0) {
		hh = "00";
		mm = "00";
		ss = "00";
		active = false;
	}
	else {
		hh = Countdown.calc(secs, 3600, 24, leadingzero);
		mm = Countdown.calc(secs, 60, 60, leadingzero);
		ss = Countdown.calc(secs, 1, 60, leadingzero);
	}

	$('#' + id).html('<span class="ic ic_time2"></span> <span id="hh_timesale">' + hh + '</span> : <span id="mm_timesale">' + mm + '</span> : <span id="ss_timesale">' + ss + '</span>');

	if (active) countdown_event[id] = setTimeout("Countdown.CountBackMobileMain(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountDailyDealMain = function (secs, id, period, active, countstepper, leadingzero) {
	

	var hh, mm, ss;
	var hh_a, hh_b, mm_a, mm_b, ss_a, ss_b;

	if (secs < 0) {
		hh_a = "0";
		hh_b = "0";
		mm_a = "0";
		mm_b = "0";
		ss_a = "0";
		ss_b = "0";

		active = false;
		return;
	}
	else {
		hh = Countdown.calc(secs, 3600, 24, leadingzero);
		mm = Countdown.calc(secs, 60, 60, leadingzero);
		ss = Countdown.calc(secs, 1, 60, leadingzero);
		hh_a = hh.substr(3, 1);
		hh_b = hh.substr(4, 1);
		mm_a = mm.substr(3, 1);
		mm_b = mm.substr(4, 1);
		ss_a = ss.substr(3, 1);
		ss_b = ss.substr(4, 1);
	}

	$get("hh_" + id).innerHTML = '<em>' + hh_a + '</em><em>' + hh_b + '</em>';
	$get("mm_" + id).innerHTML = '<em>' + mm_a + '</em><em>' + mm_b + '</em>';
	$get("ss_" + id).innerHTML = '<em>' + ss_a + '</em><em>' + ss_b + '</em>';

	if (active) countdown_event[id] = setTimeout("Countdown.CountDailyDealMain(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountBackSPAN = function (secs, id, period, active, countstepper, leadingzero) {
	if (secs < 0) {
		$get("hh_" + id).innerHTML = "<span>0</span><span>0</span>";
		$get("mm_" + id).innerHTML = "<span>0</span><span>0</span>";
		$get("ss_" + id).innerHTML = "<span>0</span><span>0</span>";
		active = false;
		return;
	}

	var hh = Countdown.calc(secs, 3600, 24, leadingzero);
	var mm = Countdown.calc(secs, 60, 60, leadingzero);
	var ss = Countdown.calc(secs, 1, 60, leadingzero);
	var hh_a = hh.substr(3, 1);
	var hh_b = hh.substr(4, 1);
	var mm_a = mm.substr(3, 1);
	var mm_b = mm.substr(4, 1);
	var ss_a = ss.substr(3, 1);
	var ss_b = ss.substr(4, 1);

	$get("hh_" + id).innerHTML = '<span>' + hh_a + '</span><span>' + hh_b + '</span>';
	$get("mm_" + id).innerHTML = '<span>' + mm_a + '</span><span>' + mm_b + '</span>';
	$get("ss_" + id).innerHTML = '<span>' + ss_a + '</span><span>' + ss_b + '</span>';

	if (active) countdown_event[id] = setTimeout("Countdown.CountBackSPAN(" + (secs + countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}

Countdown.CountBackNOW = function (secs, id, period, active, countstepper, leadingzero) {
	if (secs < 0) {
		$get("hh_" + id).innerHTML = "00";
		$get("mm_" + id).innerHTML = "00";
		$get("ss_" + id).innerHTML = "00";
		active = false;
		return;
	}

	var d = new Date();

	$get("hh_" + id).innerHTML = (d.getHours().toString().length < 2) ? "<b>" + "0" + d.getHours() + "</b>" : "<b>" + d.getHours() + "</b>";
	$get("mm_" + id).innerHTML = (d.getMinutes().toString().length < 2) ? "<b>" + "0" + d.getMinutes() + "</b>" : "<b>" + d.getMinutes() + "</b>";
	$get("ss_" + id).innerHTML = (d.getSeconds().toString().length < 2) ? "<b>" + "0" + d.getSeconds() + "</b>" : "<b>" + d.getSeconds() + "</b>";
	if (active) countdown_event[id] = setTimeout("Countdown.CountBackNOW(" + (secs - countstepper) + ", '" + id + "', '" + period + "', '" + active + "', '" + countstepper + "', '" + leadingzero + "')", period);
}
/* Countdown Timer End */


/*	Delay Loading of Images
- 缓加载图片
*/

var DelayImageLoading = function () { };
var __load_close = "N"; //scroll에서 DOM select 안하도록 전역으로 체크

var DelayImageLoading_CustomCheckObj; 	// 페이지별 체크 Object
var DelayImageLoading_CustomCallback; 	// CustomCheckObj 가 있을 경우 실행할 Function

DelayImageLoading.Init = function (callBackFunction) {
	// 리스트 상품 이미지 동적 랜더링 스크립트
	var __scrollRender = false;
	var window_height = $(window).height();

	// 동적 렌더링 처리 판별
	try {
		// 동적 렌더링 처리 판별
		if ($.browser.msie || $.browser.safari || $.browser.mozilla) {
			__scrollRender = true;
		}
		else if ($.browser.opera) {
			if (navigator.userAgent.indexOf("Mini") == -1) {
				__scrollRender = true;
			}
		}
	} catch (e) { }

	$(window).resize(function () {
		window_height = $(window).height();
	});

	// image rendering
	$(document).ready(function (e) {
		var height;
		var imgHtml;
		var window_height = $(window).height();

		try {
			if (window.FB && FB.init) {	// FaceBook App인 경우 false
				__scrollRender = false;
			}
		}
		catch (e) { }

		// load 값이 N인 image에 대해서 image rendering을 조정/
		$("img[load='N'][gd_src]").each(function () {
			if (__scrollRender == true) {
				height = $(this).height();

				//이미지의 위치가 스크롤 top 위치에서 윈도우 사이즈만큼에 위치할 때 이미지를 로딩한다.
				if (($(window).scrollTop() - 1 * height) <= $(this).offset().top && $(this).offset().top < ($(window).scrollTop() + window_height + 1 * height)) {	//스크롤 위치 + window 높이 + 1줄 rendering
					$(this).attr({ "load": "Y" });

					if (($(this).attr("gd_src").indexOf("/mi/") > 0 || $(this).attr("gd_src").indexOf("/mi_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4/") > 0) && $(this).attr("resizing") != "false") {
						DelayImageLoading.ImageResize($(this));
					}
					else {
						$(this).attr({ "src": $(this).attr("gd_src") });
					}
				}
				else if ($(this).offset().top > ($(window).scrollTop() + window_height + 1 * height)) {
					return false;
				}
			}
			else {
				if (($(this).attr("gd_src").indexOf("/mi/") > 0 || $(this).attr("gd_src").indexOf("/mi_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4/") > 0) && $(this).attr("resizing") != "false") {
					DelayImageLoading.ImageResize($(this));
				}
				else {
					$(this).attr({ "load": "Y" });
					this.src = $(this).attr("gd_src");
				}
			}
		});

		// scroll 될 때 image rendering
		if (__scrollRender == true) {
			$(window).scroll(function () {
				if (__load_close == "N") {
					try {
						$("img[load='N']").each(function () {
							//이미지의 위치가 스크롤 top 위치에서 윈도우 사이즈만큼에 위치할 때 이미지를 로딩한다.
							if (($(window).scrollTop() - 1 * height) <= $(this).offset().top && $(this).offset().top < ($(window).scrollTop() + window_height + 1 * height)) {	//스크롤 위치 + window 높이 + 1줄 rendering
								$(this).attr({ "load": "Y" });

								if (($(this).attr("gd_src").indexOf("/mi/") > 0 || $(this).attr("gd_src").indexOf("/mi_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4/") > 0) && $(this).attr("resizing") != "false") {
									DelayImageLoading.ImageResize($(this));
								}
								else {
									$(this).attr({ "src": $(this).attr("gd_src") });
								}
							}
							else if ($(this).offset().top > ($(window).scrollTop() + window_height + 1 * height)) { //큰거 나오면 each 탈출
								return false;
							}
						});
						if ($("img[load='N']").length == 0) {
							__load_close = "Y";
						}
					} catch (e) {
					}
				}
				//페이지별로 체크하는 부분이 있을 경우 Callback Function 실행
				if (DelayImageLoading_CustomCheckObj != undefined && DelayImageLoading_CustomCheckObj.length > 0) {
					eval(DelayImageLoading_CustomCallback + "();");
				}
			});
		}

		// execute callback function
		if (callBackFunction != "" && callBackFunction != null) eval(callBackFunction + "();");
	});
};

// 이미지 비율이 다른 경우 resize 처리(mi인 경우에만 처리됨)
// 2012-11-12 CKH 용도가 비율 다른 이미지일 때 css 처리를 통해서 하단 overflow로 가려지도록 바뀜
DelayImageLoading.ImageResize = function (obj, _parent_width, _parent_height) {
	var newImg = new Image();
	src = $(obj).attr("gd_src");

	if (src == undefined) return;

	newImg.onload = function () {
		var height_rate = 1;
		var width_rate = 1;
		var max_height = (_parent_width == undefined ? parseInt($(obj).css("height")) : _parent_width);
		var max_width = (_parent_height == undefined ? parseInt($(obj).css("width")) : _parent_height);

		if (max_height == 0) max_height = max_width;

		var height = newImg.height;
		var width = newImg.width;
		var image_rate = height / width;

		if (height != width) {
			if (width > max_width) {
				width_rate = width / max_width;
				width = max_width;
				height = Math.round(height / width_rate);
			}

		}
		$(obj).removeAttr("height");
		$(obj).removeAttr("width");
		$(obj).css("width", "100%");
		$(obj).css("height", "");

		// 이미지가 slot보다 작을때 세로 위치 가운데로 조정
		if (height < max_height && (image_rate) < 1) {
			$(obj).css({ "position": "absolute", "left": "0", "top": Math.floor((max_height - height) / 2) });
			$(obj).parent("a").css({ "position": "relative", "display": "block", "height": max_height, "width": max_width, "overflow": "hidden" });
		}

		$(obj).attr({ "src": newImg.src });
	}

	newImg.src = src;
};



DelayImageLoading.MobileImageResize = function (obj, _parent_width, _parent_height) {
	var src = $(obj).attr("gd_src") == undefined ? $(obj).attr("src") : $(obj).attr("gd_src");
	if (src == undefined) return;

	var newImg = new Image();
	newImg.src = src;
	newImg.onload = function () {
		// 이미지 영역의 사이즈
		var margin = 10;
		var box_width = $(obj).width();
		var box_height = $(obj).height();
		var box_ratio = box_width / box_height;

		// 이미지의 사이즈
		var image_width = newImg.width;
		var image_height = newImg.height;
		var image_ratio = image_width / image_height;

		//render
		var width = 0, height = 0, top = 0, left = 0;
		if (image_ratio > box_ratio) {
			width = box_width;
			height = box_height * (box_ratio / image_ratio);
			top = (box_height - height) / 2;

		} else if (box_ratio > image_ratio) {
			height = box_height;
			width = box_width * (image_ratio / box_ratio);
			left = (box_width - width) / 2;

		} else {
			$(obj).attr({ "src": newImg.src });
			return;
		}

		$(obj).attr({ "style": '' });
		$(obj).css({ "width": width, "height": height });
		$(obj).css({ "margin-left": left, "margin-right": left });
		$(obj).css({ "margin-top": top, "margin-bottom": top });
		$(obj).attr({ "src": newImg.src });
	}
}

// 모바일 용 DelayImageLoading.Init
// DelayImageLoading.MobileInit은 스크롤 후에 이미지 처리로 분리사용
DelayImageLoading.MobileScrollInit = {
	scrollTop_past: -1,
	scrollTop_before: -1,

	Init: function () {
		//MobileListB하고 이중사용을 막기 위해
		if (MobileListB.Parameter.dispType != "") return;

		DelayImageLoading.MobileScrollInit.scrollTop_past = DelayImageLoading.MobileScrollInit.scrollTop_before = $(window).scrollTop();

		setInterval(DelayImageLoading.MobileScrollInit.Observer, 100);
		if (MobileUtil.isIOS()) { //trick: setInterval() doesn't work when scrolling the window on iOS.
			window.addEventListener("touchmove", DelayImageLoading.MobileScrollInit.Observer, false);
		}
	},
	Observer: function () {
		var scrollTop_past = DelayImageLoading.MobileScrollInit.scrollTop_past;
		var scrollTop_before = DelayImageLoading.MobileScrollInit.scrollTop_before;
		var scrollTop = $(window).scrollTop();

		if (scrollTop_before == scrollTop) {
			DelayImageLoading.MobileScrollInit.ScrollStop();

		} else { //if (scrollTop_before != scrollTop) {
			DelayImageLoading.MobileScrollInit.ScrollMove();
		}

		DelayImageLoading.MobileScrollInit.scrollTop_past = DelayImageLoading.MobileScrollInit.scrollTop_before;
		DelayImageLoading.MobileScrollInit.scrollTop_before = scrollTop;
	},
	ScrollStop: function () {
		DelayImageLoading.MobileInit();
		if (typeof ScrollStop == 'function') { ScrollStop(); }
	},
	ScrollMove: function () {
		if (typeof ScrollMove == 'function') { ScrollMove(); }
	}
}

DelayImageLoading.MobileInit = function () { //$(window).scroll event 대신 사용
	var window_height = $(window).height();

	$("img[load='N']").each(function () {
		var height = $(this).height();

		//이미지의 위치가 스크롤 top 위치에서 윈도우 사이즈만큼에 위치할 때 이미지를 로딩한다.
		if (($(window).scrollTop() - 1 * height) <= $(this).offset().top && $(this).offset().top < ($(window).scrollTop() + window_height + 1 * height)) {	//스크롤 위치 + window 높이 + 1줄 rendering
			var gd_src = $(this).attr("gd_src");
			var width = Number($(this).attr("width"));
			var resizing = $(this).attr("resizing");
			var obj = $(this);

			$(this).attr({ "load": "Y" });

			var isSetImageRatio =
				((gd_src > '') && //validation
				(gd_src.indexOf("/mi/") > 0 || gd_src.indexOf("/mi_s/") > 0 || gd_src.indexOf("/mi4_s/") > 0 || gd_src.indexOf("/mi4/") > 0) &&
				(resizing != 'false'))

			if (isSetImageRatio) {
				DelayImageLoading.MobileImageResize(obj);
			}
			else {
				$(obj).attr({ "src": gd_src });
			}
		}
		else if ($(this).offset().top > ($(window).scrollTop() + window_height + 1 * height)) { //큰거 나오면 each 탈출
			return false;
		}
	});
};

DelayImageLoading.ManuallChangeImg = function () {
	try {
		__load_close = "N";

		$("img[load='N']").each(function () {
			var height = $(this).height();
			var window_height = $(window).height();

			//이미지의 위치가 스크롤 top 위치에서 윈도우 사이즈만큼에 위치할 때 이미지를 로딩한다.
			if (($(window).scrollTop() - 1 * height - 300) <= $(this).offset().top && $(this).offset().top < ($(window).scrollTop() + window_height + 1 * height + 400)) {	//스크롤 위치 + window 높이 + 1줄 rendering
				$(this).attr({ "load": "Y" });
				if (($(this).attr("gd_src").indexOf("/mi/") > 0 || $(this).attr("gd_src").indexOf("/mi_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4_s/") > 0 || $(this).attr("gd_src").indexOf("/mi4/") > 0) && $(this).attr("resizing") != "false") {
					DelayImageLoading.ImageResize($(this));
				}
				else {
					$(this).attr({ "src": $(this).attr("gd_src") });
				}
			}
			else if ($(this).offset().top > ($(window).scrollTop() + window_height + 1 * height)) { //큰거 나오면 each 탈출
				return false;
			}
		});

	} catch (e) {
	}
}

// 새창에서 링크 띄움
function openLinkToNewWindow(id, class_nm) {
	$("a[href]").live("click", function (e) {
		if ($(this).attr("onclick") == null && $(this).attr("href").length > 20 && $(this).attr("href").indexOf("http://") == 0 && $(this).attr("autonewwindow") != "off") {
			window.open($(this).attr("href"));
			return false;
		}
	});
}

//스크롤을 내릴때 아이템을 밑으로 append.
var AjaxAppendItem = {
	scrollAppendPage: 1,
	IsAppendable: true,
	IsNowAppending: false,
	appendPageSize: 60,
	appendTotalSize: 120,
	appendTotalPage: 2,
	brandPageGubun: '',
	Init: function () {
		//페이징 제거 (일단 스크립트에서 감춤으로 처리)
		$(".pagingInfo .ctrl").hide();
		$("#quickPaging").hide();
		$("#dispType").val();
		this.appendTotalPage = this.appendTotalSize / this.appendPageSize;

		AjaxAppendItem.setRePaging();

		//현재의 페이지
		this.scrollAppendPage = ($("#curPage").val() != undefined ? (parseInt($("#curPage").val()) - 1) * this.appendTotalPage + 1 : 1);
		this.IsAppendable = true;

		$(window).scroll(function () {
			var scroll_top_append = $(window).scrollTop();
			var window_height_append = $(window).height();
			var scroll_height_append = document.body.scrollHeight;

			//윈도우창 아래로 스크롤이 어느정도 내려 왔을때 append 함 
			if ((scroll_height_append - scroll_top_append) < (window_height_append + 200) && AjaxAppendItem.IsNowAppending == false)
				AjaxAppendItem.getItemHTML();
		});
	},
	getItemHTML: function () {
		if (this.IsAppendable && $("#search_result_item_list").length > 0) {
			$("#append_loading_div").show(); //로딩이미지 show
			this.IsNowAppending = true;
			this.scrollAppendPage++;
			var queryString = this.makeSearchQueryString();
			var brandCheck;

			if (this.brandPageGubun != '')
				brandCheck = "&brand=Y";
			else
				brandCheck = "";

			var url = Public.getCurrentHostUrl() + "/gmkt.inc/Search/SearchResultAjaxTemplate.aspx" + queryString + "&curPage=" + this.scrollAppendPage + "&pageSize=" + this.appendPageSize + brandCheck;

			//비동기			
			RMSHelper.asyncCallWebObject(url, "post", null, AjaxAppendItem.getHtmlCallBack);

			//동기
			//			var html = RMSHelper.callWebObject(url, "post", "");
			//			//결과값이 없으면 더이상 검색하지 않음 
			//			if (html.trim() == "")
			//				this.IsAppendable = false;
			//			else {
			//				// 전역변수 __load_close : 이미지 로딩을 계속 함
			//				__load_close = "N";
			//				$("#search_result_item_list").append(html);
			//			}
		}
	},
	makeSearchQueryString: function () {
		//Submit 되는 파라미터들중 curPage는 제외함
		var input_list = $("#search_rst_gform input[name]");

		var queryString = "?";
		for (var i = 0; i < input_list.length; i++) {
			if ($(input_list[i]).attr("name").toLowerCase() != "curpage" && $(input_list[i]).attr("name").toLowerCase() != "pagesize" && $(input_list[i]).attr("value") != "" && ($(input_list[i]).attr("type") == "hidden" || $(input_list[i]).attr("type") == "text")) {
				if (i > 0) queryString += "&";

				if ($(input_list[i]).attr("name").toLowerCase() == "keyword" || $(input_list[i]).attr("name").toLowerCase() == "brandnm")
					queryString += $(input_list[i]).attr("name") + "=" + encodeURIComponent($(input_list[i]).attr("value"));
				else
					queryString += $(input_list[i]).attr("name") + "=" + $(input_list[i]).attr("value");
			}
		}
		return queryString;
	},
	getHtmlCallBack: function (result, svc, xmlHttp) {
		//결과값이 없으면 더이상 검색하지 않음 
		if (result.trim() == "")
			AjaxAppendItem.IsAppendable = false;
		else {
			//한페이지에 AjaxAppendItem 으로 붙는 아이템 갯수 제한
			if (AjaxAppendItem.scrollAppendPage % AjaxAppendItem.appendTotalPage == 0) AjaxAppendItem.IsAppendable = false;
			// 전역변수 __load_close : 이미지 로딩을 계속 함
			__load_close = "N";
			$("#search_result_item_list").append(result);
			//새로추가된 영역에 대해 binding 처리한다. (Youk, 2012-12-29)
			SearchResultBoardBinding.Init();
		}
		$("#append_loading_div").hide(); //로딩이미지 show
		AjaxAppendItem.IsNowAppending = false;
	},
	setRePaging: function () {
		var pageNod = $("#pageing_list a");
		for (var i = 0; i < pageNod.length; i++) {
			if ($(pageNod).eq(i).attr("value") != "") {
				$(pageNod).eq(i).attr("value", (parseInt($(pageNod).eq(i).attr("value")) - 1) * this.appendTotalPage + 1);
			}
			else {
				$(pageNod).eq(i).attr("href", "#");
				$(pageNod).eq(i).attr("value", "");
			}
		}
	}
}

/*
 SearhResult.ascx에서 호출되는 js binding 처리 스크립트 가져옴.
 appending시 호출되고, SearchResult에서도 아래가 호출되도록 일치시켜야 한다.
 (Youk, 2012-12-31)
*/
var SearchResultBoardBinding = {
	Init: function () {

		///새롭게 Gallay 형에서 추가되서 Add하는거 by Kim Young Hee
		$("[class^='bd_gallery'] li .thumb_area, [class^='bd_glr'] li .thumb_area").unbind();
		$("[class^='bd_gallery'] li .thumb_area, [class^='bd_glr'] li .thumb_area").hover(function () {
			$(this).addClass(" hover");
			$(".smtoptwrap", this).show();
		}, function () {
			$(this).removeClass(" hover");
			$(".smtoptwrap", this).hide();
		});

		//quick view
		$('div.smtopt').find('.quick').live("click", function (e) {
			Util.OpenQuickView($(this).children("a").attr("goodscode"));
			return false;
		});

		//새롭게 추가된 Panorama 형식
		$("[class^='bd_pnrm'] li, [class^='bd_panorama'] li").unbind();
		$("[class^='bd_pnrm'] li, [class^='bd_panorama'] li").hover(function () {
			$(this).addClass("on selected");
			$(".options", this).addClass("smtopt_area_on");
			$(".options", this).show();
			$attribute_adult_item = $(this).parent().attr("Adult_NA");

			if ($attribute_adult_item != "NA") {
				//기본움직이는이미지노출
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src").replace("_s", "") });
			}

			return false;

		}, function () {
			$(this).removeClass("on selected");
			$(".options", this).hide();
			$(".options", this).removeClass("options_on");

			$attribute_adult_item = $(this).parent().attr("Adult_NA");
			if ($attribute_adult_item != "NA") {
				//정지이미지로원복
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src") });
			}
			return false;
		});
	}
}


/**
Mouse Scroll Control event
(Youk, 2013-07-25)
i.e.
	MouseWheelControl.Init();
	MouseWheelControl.ScrollDown = function () {
		//..
	}
	MouseWheelControl.ScrollUp = function () {
		//..
	}
*/
var MouseWheelControl = {
	Init: function () {
		if (window.addEventListener)
		/** DOMMouseScroll is for mozilla. */
			window.addEventListener('DOMMouseScroll', MouseWheelControl.Wheel, false);
		/** IE/Opera. */
		window.onmousewheel = document.onmousewheel = MouseWheelControl.Wheel;
	},
	Wheel: function (event) {
		/** Event handler for mouse wheel event.
		*/
		var delta = 0;
		if (!event) /* For IE. */
			event = window.event;
		if (event.wheelDelta) { /* IE/Opera. */
			delta = event.wheelDelta / 120;
		} else if (event.detail) { /** Mozilla case. */
			delta = -event.detail / 3;
		}

		if (delta)
			MouseWheelControl.Handle(delta);
		
		if (event.preventDefault)
			event.preventDefault();
		event.returnValue = false;
	},
	Handle: function (delta) {
		if (delta > 0) {
			// scroll up
			MouseWheelControl.ScrollUp();
		} else if (delta < 0) {
			// scroll down
			MouseWheelControl.ScrollDown();
		}
	},
	ScrollUp: function () {
	},
	ScrollDown: function () {
	},
	EnableScroll: function () { //다시 scroll되도록 할때.
		if (window.removeEventListener) {
			window.removeEventListener('DOMMouseScroll', MouseWheelControl.Wheel, false);
		}
		window.onmousewheel = document.onmousewheel = document.onkeydown = null;
	}
}
