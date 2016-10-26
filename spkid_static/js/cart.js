function onPageLoad() {
	Util.__openInnerPopup_multiPopup = true;

	updateDetailLayer();

	// 선택한 주소록의 주소로 배송불가한 상품들이 있는지 체크
	$("div#address_list div.lst li a[data-addrno=" + $("#cart_addr_no").val() + "]").click();

	// see details 내의 td에 font_weight: bold를 스크립트에서 제거(css 수정 요청 필요)	// 임시
	$(".ly_wrap .ly_order table").find("th, td").css("font-weight", "normal");
}

function initEventHandler() {
	Util.addEventHandler("selectAllTop", "onclick", selectAllTop_onclick);
	Util.addEventHandler("selectAllBottom", "onclick", selectAllBottom_onclick);
	Util.addEventHandler("check_cod", "onclick", COD_Order.selectCodChkBox);

	$(".orderList .btn_more").each(function () {
		$(this).bind("click", function (e) {
			$(".layer_dcDetail01").find(".layer_wrap01").addClass("g_disNone");
			$(".orderTable ul.list li").removeClass("click");

			if ($("div[name=dc_detail_layer]").eq($(this).attr("idx")).attr("class") == "layer_wrap01") {
				$("div[name=dc_detail_layer]").eq($(this).attr("idx")).addClass("g_disNone");
			} else {
				$("div[name=dc_detail_layer]").eq($(this).attr("idx")).removeClass("g_disNone");
				$(this).parents("li").addClass("click");
			}
			return false;
		});
	});

	$(".layer_dcDetail01 .g_close").each(function () {
		$(this).bind("click", function (e) {
			$(this).parents("#layer_dcDetail01").find(".layer_wrap01").addClass("g_disNone");
			$(this).parents("li").removeClass("click");
			return false;
		});
	});

	$(".orderList .btn_change").each(function () {
		$(this).bind("click", function (e) {
			$("#g_layer_shipOption .g_layer").addClass("g_disNone");

			if ($("div[name=optionLayer]").eq($(this).attr("idx")).attr("class") == "g_layer") {
				$("div[name=optionLayer]").eq($(this).attr("idx")).addClass("g_disNone");
			} else {
				$("div[name=optionLayer]").eq($(this).attr("idx")).removeClass("g_disNone");
			}
			return false;
		});
	});

	// GIFT
	$(".bul_gift").mouseover(function () {
		$(this).siblings(".ly_bnft").show();
		$(this).parent().addClass("hover");
	}).mouseout(function () {
		$(this).siblings(".ly_bnft").hide();
		$(this).parent().removeClass("hover");
	});

	//plus reward
	$(".bul_pqst").mouseover(function () {
		$(this).siblings(".ly_bnft").show();
		$(this).parent().addClass("hover");
	}).mouseout(function () {
		$(this).siblings(".ly_bnft").hide();
		$(this).parent().removeClass("hover");
	});

	$(".bul_pqpt").mouseover(function () {
		$(this).siblings(".ly_bnft").show();
		$(this).parent().addClass("hover");
	}).mouseout(function () {
		$(this).siblings(".ly_bnft").hide();
		$(this).parent().removeClass("hover");
	});

	$("#g_layer_shipOption .g_btn_iClose").each(function () {
		$(this).bind("click", function (e) {
			$("#g_layer_shipOption .g_layer").addClass("g_disNone");
			return false;
		});
	});

	$("#g_layer_shipOption .g_btn_close").each(function () {
		$(this).bind("click", function (e) {
			$("#g_layer_shipOption .g_layer").addClass("g_disNone");
			return false;
		});
	});

	$(".g_layer_order02 a.btn_close").bind("click", function () {
		$(".g_layer_order02").hide();
	});

	$(".select_lst3").hover(function () {
		$(this).addClass("hover");
		$(".lst", this).show();
	}, function () {
		$(this).removeClass("hover");
		$(".lst", this).hide();
	});

	$("div#item_price").delegate(".btn_close", "click", function () {
		$(this).parent("div.ly_order").hide().parent(".ly_wrap").removeClass("click");
		return false;
	});

	$("div#item_price").delegate(".btn_details", "click", function () {
		$("div.ly_wrap").not($(this).siblings("div.ly_wrap")).removeClass("click");
		$("div.ly_order").not($(this).siblings("div.ly_wrap").find("div.ly_order")).hide();

		if ($(this).attr("data-cartdiscount") == "true") {
			getCartDiscountLayer(this);
		}
		else if ($(this).siblings("div.ly_wrap").find("div.ly_order").length !== 0) {
			$(this).siblings("div.ly_wrap").toggleClass("click").find("div.ly_order").toggle();
		}
	});
}

function selectAllTop_onclick() {
	selectAll(1);
}

function selectAllBottom_onclick() {
	selectAll(2);
}

function selectAll(opt) {
	var objSelectOrder = $nget("select_order");
	var checkYn = false;

	if (objSelectOrder != null) {
		if (opt == 1) {
			checkYn = $get("selectAllTop").checked;
			$get("selectAllBottom").checked = checkYn;
		}
		else {
			checkYn = $get("selectAllBottom").checked;
			$get("selectAllTop").checked = checkYn;
		}


		for (i = 0; i < objSelectOrder.length; i++) {
			if (objSelectOrder[i].disabled == false)
				objSelectOrder[i].checked = checkYn;
		}
	}
}


function goOrderSub() {
	ControlUtil.getServerForm().method = "GET";
	var orderUrl = "";
	if ($get("global_order_type").value == "G")
		orderUrl = Public.getOrderServerUrl("/Order/us/BuyOrder.aspx", true);
	else
		orderUrl = Public.getOrderServerUrl("/Order/BuyOrder.aspx", true);

	// get 방식에서는 폼URL에 있는 QueryString이 넘어 가지 않는다.
	if (orderUrl.indexOf("SessionShare.aspx") >= 0) {
		var idx = orderUrl.indexOf("?nextUrl=");
		var nextUrl = "";
		if (idx >= 0) {
			nextUrl = unescape(orderUrl.substring(idx + 9, orderUrl.length));
			orderUrl = orderUrl.substring(0, idx);
		}

		$(ControlUtil.getServerForm()).append("<input type='hidden' name='nextUrl' value='" + nextUrl + "'/>");
	}

	ControlUtil.submitServerForm(orderUrl);
}


function noItem() {
	alert(MultiLang.findResource("ALERT_MSG03"));
	return;
}

function delete_cart(pid) {
	var order_idxs = "";
	var chk_all_yn = "Y";

	for (i = 0; i < $nget("select_order").length; i++) {
		if ($nget("select_order")[i].checked) {
			if (order_idxs != "")
				order_idxs += "|";

			order_idxs += $nget("select_order")[i].value;
		} else {
			chk_all_yn = "N";
		}
	}

	if (order_idxs != "") {
		if (chk_all_yn == "Y") {
			//order_idxs = "";
		}

		if (confirm(MultiLang.findResource("ALERT_MSG04"))) {
			var param = new RMSParam();
			param.add("Pid", pid);
			param.add("OrderWayKind", "PAK");
			param.add("OrderIdxs", order_idxs);
			param.add("global_order_type", $get("global_order_type").value);
			var ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "DeleteCartItem", param.toJson(), callbackAsyncService);
		} else {
			return;
		}
	} else {
		alert(MultiLang.findResource("ALERT_MSG05"));
		return;
	}
}

function delete_cart_item(pid, order_idxs) {
	if (confirm(MultiLang.findResource("ALERT_MSG04"))) {
		var param = new RMSParam();
		param.add("Pid", pid);
		param.add("OrderWayKind", "PAK");
		param.add("OrderIdxs", order_idxs);
		param.add("global_order_type", $get("global_order_type").value);
		var ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "DeleteCartItem", param.toJson(), callbackAsyncService);
	} else {
		return;
	}
}

function callbackAsyncService(result, svc, method, xmlHttpasync) {
	
	if ($get("global_order_type").value == "G")
		document.location.href = "./Cart.aspx?global_order_type=G";
	else
		document.location.href = "./Cart.aspx";
}

function popEditCartGoods(order_idx, basis_kind, ex_type) {
	if (basis_kind == "PCP") {
		alert(MultiLang.findResource("ALERT_MSG06"));
		return;
	}
	else if (ex_type == "C2C") {
		alert(MultiLang.findResource("ALERT_MSG07"));
		return;
	}
	var Obj = Util.openPopup('PopupEditCartGoods.aspx?select_order=' + order_idx, 383, 234, 'EditCartGoods')
}

//----------------------------------------- 배송비 옵션 정보 수정 -----------------------------------------------------------------------
function setDeliveryOption(order_idx, selectedIdx, pid, global_order_type) {
	var delivery_option_no = "0";

	delivery_option_no = $get("del_opt_select_" + selectedIdx).value;

	var param = new RMSParam();
	param.add("pid", pid);
	param.add("order_way_kind", "PAK");
	param.add("order_idx", order_idx);
	param.add("delivery_option_no", delivery_option_no);
	param.add("global_order_type", global_order_type);

	var ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "SetDeliveryOption", param.toJson(), callbackAsyncService);
}
//----------------------------------------- 배송비 옵션 정보 수정 -----------------------------------------------------------------------

//----------------------------------------- 해외배송비 가능 국가 리스트  -----------------------------------------------------------------------
//function searchOverseaList(delivery_group_no, delivery_bundle_no, idx, goodscode) {
//	var search_keyword = "";
//	var search_keyword = $get("oversea_search" + idx).value;

//	var param = new RMSParam();

//	param.add("delivery_group_no", delivery_group_no);
//	param.add("delivery_bundle_no", delivery_bundle_no);
//	param.add("search_keyword", search_keyword);
//	param.add("goodscode", goodscode);
//	param.add("global_order_type", $get("global_order_type").value);
//	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "GetOverseaDeliveryFeeInfo", param.toJson());

//	var oveseaInfoTxt = "";

//	oveseaInfoTxt += "<table summary=\"\" style=\"width:230px;\">\r\n";
//	oveseaInfoTxt += "	<colgroup>\r\n";
//	oveseaInfoTxt += "		<col width=\"134px\" />\r\n";
//	oveseaInfoTxt += "		<col width=\"95px\" />\r\n";
//	oveseaInfoTxt += "	</colgroup>\r\n";
//	oveseaInfoTxt += "	<tbody>\r\n";

//	if (ret != null) {
//		for (var i = 0; i < ret.length; i++) {
//			oveseaInfoTxt += "<tr>\r\n";
//			oveseaInfoTxt += "	<td>" + ret[i].nation_nm + "</td>\r\n";
//			oveseaInfoTxt += "	<td>" + PriceUtil.FormatCurrencySymbol(ret[i].oversea_delivery_fee) + "</td>\r\n";
//			oveseaInfoTxt += "</tr>\r\n";
//		}
//	}

//	oveseaInfoTxt += "	</tbody>\r\n";
//	oveseaInfoTxt += "</table>\r\n";
//	//	alert(oveseaInfoTxt);

//	$get("oversea_tbl_list" + idx).innerHTML = oveseaInfoTxt;
//}
//----------------------------------------- 해외배송비 가능 국가 리스트  -----------------------------------------------------------------------

// 배송비 detale 레이어에서 선택 옵션 가격 표시
function showSelectDelOptFee(optIndex, add_fee_use_yn) {
	if (add_fee_use_yn != "Y") {
		document.getElementById("td_del_opt_select_" + optIndex).innerHTML =
		document.getElementById("del_opt_select_" + optIndex)[document.getElementById("del_opt_select_" + optIndex).selectedIndex].title;
	}
}

//-------------------------------------------------- E-Coupono 관련 스크립트 ----------------------------------------------------------------
function ECouponHelpView() {
	window.open(Public.getWWWServerUrl('/popup/popupecouponhelp.aspx'), 'Integration_Search', 'top=10,left=10,width=450,height=870');
}
//-------------------------------------------------- E-Coupono 관련 스크립트 ----------------------------------------------------------------
function goGloalMyCoupon(goodscode, select_order, buy_cnt, chakbul_prepay_yn, coupon_nos) {
	Util.closeInnerPopup();

//	if ($get("hid_dis_coupon_usable_" + goodscode + "_" + select_order) != null && $get("hid_dis_coupon_usable_" + goodscode + "_" + select_order).value == "N") {
//		alert(MultiLang.findResource("ALERT_MSG08"));
//		return;
//	}

	var i = 0;
	var isTrue = true;
	var tmpType = "";
	var cost_basis_no = 0;
	var allPromotionDiscont = false;
	while (true) {
		if ($get("hid_dis_" + goodscode + "_" + select_order + "_" + i) == null) {
			break;
		}

		if ($($get("hid_dis_" + goodscode + "_" + select_order + "_" + i)).val() == "GOODS") {
			tmpType = $($get("hid_dis_" + goodscode + "_" + select_order + "_" + i)).attr("basistype");
			cost_basis_no = $($get("hid_dis_" + goodscode + "_" + select_order + "_" + i)).attr("cost_basis_no");

			if ($($get("hid_dis_" + goodscode + "_" + select_order + "_" + i)).attr("basistype") != "GD") {
				isTrue = false;
				break;
			}
		}

		i++;
	}

	if (coupon_nos.indexOf("etc") >= 0) {
		coupon_nos = coupon_nos.replace("etc", "");
		coupon_nos = coupon_nos.replace("", "*");
	}
	if (coupon_nos.indexOf("") >= 0) {
		coupon_nos = coupon_nos.replace("", "*");
	}

	//이너팝업이 열려있다면 닫기
	if ($("#div_popup").length > 0) {
		$('div[id^="div_popup"]').hide();
	}

	var global_order_type = $get("global_order_type").value;

	var url = Public.convertNormalUrl("~/MyCoupon/MyCouponBox.aspx?goodscode=" + goodscode + "&select_order=" + select_order + "&buy_cnt=" + buy_cnt + "&chakbul_prepay_yn=" + chakbul_prepay_yn + "&coupon_nos=" + coupon_nos + "&goods_dc_basis_type=" + tmpType + "&goods_dc_cost_no=" + cost_basis_no + "&where=cart" + "&global_order_type=" + global_order_type);
	//Util.openInnerPopup(url, 799, 626);

	if (!Public.isLogin()) //비로그인
		Util.openInnerPopup(url, 400, 370, false, -4, -3);
	else                   //로그인
		Util.openInnerPopup(url, 800, 578, false);
}

function goMyCoupon(goodscode, select_order, buy_cnt, chakbul_prepay_yn, coupon_nos) {
	if ($get("global_order_type").value == "G" && ($get("global_link_yn").value == "N" || false == Public.isLogin())) {
		Util.globalMemberForward("goGloalMyCoupon(" + goodscode + ", " + select_order + ", " + buy_cnt + ", '" + chakbul_prepay_yn + "', '" + coupon_nos + "')");
		return;
	}

	goGloalMyCoupon(goodscode, select_order, buy_cnt, chakbul_prepay_yn, coupon_nos);
}

goGlobalCartCoupon = function (global_check_yn) {
	//alert("global_check_yn=" + global_check_yn);
	Util.closeInnerPopup();

	var obj = $(".discount .btn_plused");
	var set_discount_qoo10_yn = "N";

	for (var i = 0; i < obj.length; i++) {
		if ($($(obj)[i]).attr("who_fee") == "GD") {
			set_discount_qoo10_yn = "Y";
			break;
		}
	}

	//구매자부담 셋트 할인이 된경우 Qoo10부담 쿠폰함 띄우지 못한다.
	if (set_discount_qoo10_yn == "Y") {
		alert(MultiLang.findResource("AlertCannotUseQoo10Coupon"));
		return;
	}

	var checked_order_index = "";
	var lists = $("dd.co_seller table.orderList").each(function () {
		if ($(this).find(":checkbox").attr("checked") == "checked") {
			checked_order_index += $(this).find(":checkbox:checked").val() + ",";
		}
	});

	if (global_check_yn == undefined)
		checked_order_index = "";

	//alert(checked_order_index);
	var global_order_type = $get("global_order_type").value;

	//var url = Public.convertNormalUrl("~/MyCoupon/MyCouponBox.aspx?goodscode=" + goodscode + "&select_order=" + select_order + "&buy_cnt=" + buy_cnt + "&chakbul_prepay_yn=" + chakbul_prepay_yn + "&coupon_nos=" + coupon_nos + "&goods_dc_basis_type=" + tmpType + "&goods_dc_cost_no=" + cost_basis_no);
	var url = Public.convertNormalUrl("~/MyCoupon/MyCouponBox.aspx?is_cart=Y&checked_order_index=" + checked_order_index + "&global_order_type=" + global_order_type);


	if (!Public.isLogin()) //비로그인
		Util.openInnerPopup(url, 400, 370, false, -4, -3);
	else                   //로그인
		Util.openInnerPopup(url, 800, 578, false);
}

function goMyCartCoupon() {
	if ($get("global_order_type").value == "G" && ($get("global_link_yn").value == "N" || false == Public.isLogin())) {
		Util.globalMemberForward("goGlobalCartCoupon");
		return;
	}

	goGlobalCartCoupon("Y");
}

function goMySellerCoupon(sell_cust_no) {

	var obj = $(".discount .btn_plused:[sell_cust_no=" + sell_cust_no + "]");
	var set_discount_seller_yn = "N";
	
	for (var i = 0; i < obj.length; i++) {
		if ($($(obj)[i]).attr("who_fee") == "ME") {
			set_discount_seller_yn = "Y";
			break;
		}
	}

	// 선택된 항목 체크
	var checked_order_index = "";
	var lists = $("div.sellerShop[seller_cust_no='" + sell_cust_no + "']").siblings("ul.list").children("li").each(function () {
		if ($(this).find(":checkbox").attr("checked") == "checked") {
			checked_order_index += $(this).find(":checkbox:checked").val() + ",";
		}
	});
	
	//판매자 셋트 할인이 된경우 seller_coupon띄우지 못한다.
	if (set_discount_seller_yn == "Y") {
		alert(MultiLang.findResource("AlertCannotUseSellerCoupon"));
		return;
	}
	//var url = Public.convertNormalUrl("~/MyCoupon/MyCouponBox.aspx?goodscode=" + goodscode + "&select_order=" + select_order + "&buy_cnt=" + buy_cnt + "&chakbul_prepay_yn=" + chakbul_prepay_yn + "&coupon_nos=" + coupon_nos + "&goods_dc_basis_type=" + tmpType + "&goods_dc_cost_no=" + cost_basis_no);
	var url = Public.convertNormalUrl("~/MyCoupon/MyCouponBox.aspx?is_cart=Y&param_seller_cust_no=" + sell_cust_no + "&checked_order_index=" + checked_order_index);
	
	if (!Public.isLogin()) //비로그인
		Util.openInnerPopup(url, 400, 370, false, -4, -3);
	else                   //로그인
		Util.openInnerPopup(url, 800, 578, false);
}



function GetPopupHeight() {

	var winHeight = $(window).height();
	var scrollHeight = $(document).scrollTop();
	var height = 220;

		alert(winHeight);
		alert(scrollHeight);
		alert(height);


	//return;
	var top = winHeight - scrollHeight;//  - height;
	return top;
//	var documentHeight = $(document).height();

//	var scrollHeight = $(document).scrollTop();

//	alert(winHeight);
//	alert(documentHeight);
//	alert(scrollHeight);

//	if (scrollHeight < 300)
//		scrollHeight = 200;
//	else {
//		scrollHeight = scrollHeight + (scrollHeight / 2.5);
//	}

	
//	//var scrollLeft = $(document).scrollLeft();

//	var documentHeight = $(document).height();


//	alert(scrollHeight);

//	

	//var windowHeight = $(window).height() / 2;

//	alert(scrollHeight);

//	return scrollHeight;
}

function cancelCoupon(goodscode, select_order, buy_cnt) {
	var ret = cancelCouponSub(goodscode, select_order, buy_cnt);
	var global_order_type = $get("global_order_type").value;

	if (ret != 0) {
		alert(MultiLang.findResource("alert_cancel_coupon_msg_2"));
	}
	else {
		document.location.href = Public.getOrderServerUrl("/Order/Cart.aspx?global_order_type=" + global_order_type, false);
	}
}

function cancelCouponSub(goodscode, select_order, buy_cnt) {
	var cust_no = "";

	if (Public.isLogin()) {
		cust_no = Public.getCustNo();
	}

	var param = new RMSParam();
	param.add("order_cnts", buy_cnt);
	param.add("cost_basis_nos", "");
	param.add("coupon_nos", "");
	param.add("order_idx", select_order);
	param.add("cust_no", cust_no);
	param.add("gd_no", goodscode);
	param.add("order_way_kind", "PAK");
	param.add("update_kind", "DEL");
	param.add("global_order_type", $get("global_order_type").value);
	param.add("cart_cost_basis_no", 0);
	param.add("cart_coupon_no", "");
	param.add("sell_cust_no", "");
	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "SetBasketCoupon", param.toJson());

	return ret;
}

//function dispCartSummaryDetail() {
//	if ($("#CartSummaryDetail").css("display") == "none") {
//		$("#CartSummaryDetail").show();
//		$("#detail_close_btn").addClass("close");
//	}
//	else {
//		$("#CartSummaryDetail").hide();
//		$("#detail_close_btn").removeClass("close");
//	}
//}


function openOverseaShippingRate(delivery_group_no, delivery_bundle_no, goodscode, index) {

	for (var i = 0; i < 30; i++) {
		if (i != parseInt(index)) {
			closeOverseaShippingRate(i);
		}
	}

	var paramText = "delivery_group_no=" + delivery_group_no + "&delivery_bundle_no=" + delivery_bundle_no + "&goodscode=" + goodscode + "&index=" + index + makeGlobalOrderTypeParam();
	//Order/Popup/OverseaListPopup.aspx?delivery_group_no=34825&delivery_bundle_no=1&goodscode=500157647
	//ajax는 한번만 호출되게 한다.
	if ($get("shipLayer_" + index).innerHTML == "") {
		RMSHelper.asyncCallWebObject(Public.convertNormalUrl("~/Order/Popup/OverseaListPopup.aspx?" + paramText), "GET", null, PageInit_Oversea_callback, index);
	}
	else {
		if ($get("shipLayer_" + index).style.display == "none")
			$get("shipLayer_" + index).style.display = "";
		else
			$get("shipLayer_" + index).style.display = "none";
	}
}

PageInit_Oversea_callback = function (result, svc, xmlHttp, index) {
	$get("shipLayer_" + index).innerHTML = result;
	$get("shipLayer_" + index).style.display = "";
}

function closeOverseaShippingRate(index) {
	if ($get("shipLayer_" + index) != undefined) {
		$get("shipLayer_" + index).style.display = "none";
	}
}

function openBundleShippingRate(delivery_group_no, delivery_bundle_no, index, auto_oversea_yn) {
	for (var i = 0; i < 30; i++) {
		if (i != parseInt(index)) {
			closeBundleShippingRate(i);
		}
	}

	var del_nation_cd = $get("del_nation_cd").value;

	var paramText = "delivery_group_no=" + delivery_group_no + "&delivery_bundle_no=" + delivery_bundle_no + "&index=" + index + "&del_nation_cd=" + del_nation_cd + "&auto_oversea_yn=" + auto_oversea_yn + makeGlobalOrderTypeParam();

	//ajax는 한번만 호출되게 한다.
	if ($get("bundleLayer_" + index).innerHTML == "") {
		RMSHelper.asyncCallWebObject(Public.convertNormalUrl("~/Order/Popup/DeliveryBundleDetailPopup.aspx?" + paramText), "GET", null, PageInit_Bundle_callback, index);
	}
	else {
		if ($get("bundleLayer_" + index).style.display == "none")
			$get("bundleLayer_" + index).style.display = "";
		else
			$get("bundleLayer_" + index).style.display = "none";
	}
}

PageInit_Bundle_callback = function (result, svc, xmlHttp, index) {
	$get("bundleLayer_" + index).innerHTML = result;
	$get("bundleLayer_" + index).style.display = "";
}

function closeBundleShippingRate(index) {
	if ($get("bundleLayer_" + index) != undefined) {
		$get("bundleLayer_" + index).style.display = "none";
	}
}

function openMileageDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailMileage.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openDiscountDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailDiscount.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 294, null);
}

function openPlusDiscountDetail() {
	var zip_code = "";
	var del_nation_cd = $get("del_nation_cd").value;
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailPlusDiscount.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + "&zipcode=" + zip_code + "&del_nation_cd=" + del_nation_cd + makeGlobalOrderTypeParam()), 390, 294, null);
}

function openShippingDetail() {
	var zip_code = "";
	var del_nation_cd = $get("del_nation_cd").value;
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailDelivery.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + "&zipcode=" + zip_code + "&del_nation_cd=" + del_nation_cd + makeGlobalOrderTypeParam()), 390, 294, null);
}

function openStampDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailStamp.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openDonationDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailDonation.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openVisitSellerDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailVisitSeller.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openChargeOnDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailChargeOnDelivery.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openOverseaDetail() {
	var zip_code = "";
	var del_nation_cd = $get("del_nation_cd").value;
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailOversea.aspx?zipcode=" + zip_code + "&del_nation_cd=" + del_nation_cd + "&order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function showPlusDetailPopup() {
	var zip_code = "";
	var del_nation_cd = $get("del_nation_cd").value;
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	var url = Public.convertNormalUrl("~/Order/Popup/PlusDiscountDetail.aspx?zipcode=" + zip_code + "&del_nation_cd=" + del_nation_cd + "&order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam(), false)
	Util.openInnerPopup(url, 820, 560);
}

function openGChanceDetail() {
	var order_way_kind = "PAK";
	var order_idxs = getSeletedOrderIdx();

	Util.closeInnerPopup();
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Order/Popup/CartDetailGChance.aspx?order_way_kind=" + order_way_kind + "&select_order=" + order_idxs + makeGlobalOrderTypeParam()), 390, 244, null);
}

function openPopupPlusDiscount(relation_group_no) {
	Util.__openInnerPopup_multiPopup = true;
	var p = Util.openInnerPopup(Public.convertNormalUrl("~/Popup/PopupPlusDiscountDetail.aspx?relation_group_no=" + relation_group_no + makeGlobalOrderTypeParam()), 610, 400);
}

function openPopupPlusGroup(goodscode) {
	Util.__openInnerPopup_multiPopup = true;
	var p = Util.openInnerPopup(Public.getOrderServerUrl("/Goods/PopupInnerPlusGroup.aspx?goodscode=" + goodscode), 980, 452, false, -2, 80);
}

function getSeletedOrderIdx() {
	var order_idxs = "";

	// 선택한 상품의 order_idx를 , 로 묶음(111,222,...)
	order_idxs = $("input[name=select_order]:checked").map(function () {
		return this.value;
	}).get().join(",");

//	for (i = 0; i < $nget("select_order").length; i++) {
//		if (order_idxs != "")
//			order_idxs += ",";

//		if ($nget("select_order")[i].checked == true)
//			order_idxs += $nget("select_order")[i].value;
//	}

	return order_idxs;
}

function recalculaterSelectedItems() {
	var select_order = $nget("select_order")
	var global_order_type = $get("global_order_type").value;

	if (select_order.length > 0) {
		var selectCheck = false;
		for (i = 0; i < select_order.length; i++) {
			if (select_order[i].checked) {
				selectCheck = true;
				break;
			}
		}

		if (selectCheck) {
			ControlUtil.getServerForm().method = "GET";
			ControlUtil.submitServerForm(Public.getOrderServerUrl("/Order/Cart.aspx?global_order_type=" + global_order_type, false));
		}
	}
}

function showEdit(order_idx, basis_kind, ex_type) {
	if (basis_kind == "PCP") {
		alert(MultiLang.findResource("ALERT_MSG06"));
		return;
	}
	else if (ex_type == "C2C") {
		alert(MultiLang.findResource("ALERT_MSG07"));
		return;
	}

	$("#cart_qty_" + order_idx).hide();
	$("#cart_edit_" + order_idx).show();
}

function goQtyCancel(order_idx) {
	$("#cart_qty_" + order_idx).show();
	$("#cart_edit_" + order_idx).hide();
}

function UpQty(order_idx) {
	var qty = $get("OrderCnt_" + order_idx).value;

	if (parseInt(qty)) {
		qty = parseInt(qty) + 1;
	} else {
		qty = 1;
	}

	$get("OrderCnt_" + order_idx).value = qty;
}

function DownQty(order_idx) {
	var qty = $get("OrderCnt_" + order_idx).value;

	if (parseInt(qty)) {
		qty = parseInt(qty) - 1;
	} else {
		qty = 1;
	}

	if (qty == 0) {
		qty = 1;
	}

	$get("OrderCnt_" + order_idx).value = qty;
}

function goQtyEdit(rec_id) {
        num = parseInt($("#OrderCnt_" + rec_id).val())||1;
        $.ajax({
            url: '/cart/update_cart',
            data: {rec_id: rec_id, num: num, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg)
                    alert(result.msg);
                if (result.err)
                    return false;
                location.href = location.href;
            }
        });
	
}

function goQtyEditVirtual(rec_id)
{
    num = parseInt($("#OrderCnt_" + rec_id).val())||1;
    location.href="/virtual/checkout/"+rec_id+"/"+num;
}


function chageSelectedNation(nation_cd) {
	$get("del_nation_cd").value = nation_cd;
	//$get("del_nation_area").innerHTML = "<dfn class=\"flag_" + getFlagName(nation_cd) + "\"><span>" + getCountryName(nation_cd) + "</span></dfn> <span>" + getCountryName(nation_cd) + "</span>";

	$(".select_lst3").removeClass("hover");
	$(".lst", ".select_lst3").hide();

	//ControlUtil.getServerForm().method = "GET";
	//ControlUtil.submitServerForm(Public.getOrderServerUrl("/Order/Cart.aspx", false));
}

function getFlagName(nation_cd) {
	var flag_nm = "";

	switch (nation_cd) {
		case "CN":
			flag_nm = "ch"; break;
		case "HK":
			flag_nm = "ho"; break;
		case "US":
			flag_nm = "us"; break;
		case "MY":
			flag_nm = "my"; break;
		case "JP":
			flag_nm = "ja"; break;
		case "ID":
			flag_nm = "id"; break;
		case "SG":
			flag_nm = "sg"; break;
		case "KR":
			flag_nm = "ko"; break;
		case "PH":
			flag_nm = "ph"; break;
		case "TW":
			flag_nm = "ta"; break;
		case "GB":
			flag_nm = "uk"; break;
		case "VN":
			flag_nm = "vi"; break;
		case "AU":
			flag_nm = "au"; break;
		case "CA":
			flag_nm = "ca"; break;
		case "TH":
			flag_nm = "th"; break;
	}

	return flag_nm;
}

function getCountryName(nation_cd) {
	var country_nm = "";

	switch (nation_cd) {
		case "CN":
			country_nm = "China"; break;
		case "HK":
			country_nm = "Hong Kong"; break;
		case "US":
			country_nm = "United States"; break;
		case "MY":
			country_nm = "Malaysia"; break;
		case "JP":
			country_nm = "Japan"; break;
		case "ID":
			country_nm = "Indonesia"; break;
		case "SG":
			country_nm = "Singapore"; break;
		case "KR":
			country_nm = "South Korea"; break;
		case "PH":
			country_nm = "Philippines"; break;
		case "TW":
			country_nm = "Taiwan"; break;
		case "GB":
			country_nm = "United Kingdom"; break;
		case "VN":
			country_nm = "Vietnam"; break;
		case "AU":
			country_nm = "Australia"; break;
		case "CA":
			country_nm = "Canada"; break;
		case "TH":
			country_nm = "THAILAND"; break;
	}

	return country_nm;
}

function OpenQDiscountLayerSub(prmOrIdx) {
	if (($get("q_discount_help_sub").className).indexOf("g_disNone") >= 0) {
		var tmpTop = 0;

		var winHeight = $(window).height();
		tmpTop = winHeight / 2 - 150 + ($(document).scrollTop());
		if (tmpTop < 0) {
			tmpTop = 10;
		}
		$get("q_discount_help_sub").style.top = tmpTop + "px";
		$($get("q_discount_help_sub")).removeClass("g_disNone");
	}
	else {
		CloseQDiscountLayerSub(prmOrIdx);
	}
}

function CloseQDiscountLayerSub(prmOrIdx) {
	$($get("q_discount_help_sub")).addClass("g_disNone");
}

function ApplyDiscountBtn_Click(prmCostBasisNo, prmType, prmOrderIdx, prmCostBasisNos, prmCouponNos, prmGdNo, prmCPUsed) {
	if (prmCPUsed) {
		if (!confirm(String.format(MultiLang.findResource("alert_discount_apply1"), getDiscountTypeText(prmType)))) {
			return;
		}
		else {
			cancelCouponSub(prmGdNo, prmOrderIdx, $get("OrderCnt_" + prmOrderIdx).value);
		}
	}

	var tmpCust_no = "";

	if (Public.isLogin()) {
		tmpCust_no = Public.getCustNo();
	}

	var param = new RMSParam();
	param.add("order_idx", prmOrderIdx);
	param.add("gd_no", prmGdNo);
	param.add("order_way_kind", "PAK");
	param.add("update_kind", "UP");
	param.add("disType", prmType);
	param.add("cost_basis_no", prmCostBasisNo);
	param.add("global_order_type", $get("global_order_type").value);
	
	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "SetBasketDiscountChange", param.toJson());

	document.location.reload();
}

function CancelDiscountBtn_Click(prmOrderIdx, prmGdNo) {
	var tmpCust_no = "";

	if (Public.isLogin()) {
		tmpCust_no = Public.getCustNo();
	}

	var param = new RMSParam();
	param.add("order_idx", prmOrderIdx);
	param.add("gd_no", prmGdNo);
	param.add("order_way_kind", "PAK");
	param.add("update_kind", "DEL");
	param.add("disType", "");
	param.add("cost_basis_no", 0);
	param.add("global_order_type", $get("global_order_type").value);

	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "SetBasketDiscountChange", param.toJson());

	location.reload();
}

function getDiscountTypeText(prmType) {
	var discountType = "";
	switch (prmType) {
		case "PD":
			discountType = MultiLang.findResource("Promotion Discount");
			break;
		case "QD":
			discountType = MultiLang.findResource("Q-Discount");
			break;
		case "TD":
			discountType = MultiLang.findResource("Time Sale");
			break;
		case "GD":
			discountType = MultiLang.findResource("Additional Off");
		default:
			break;
	}
	return discountType;
}

function makeGlobalOrderTypeParam() {
	return "&global_order_type=" + $get("global_order_type").value;
}

function show_seller_coupon(seller_domain) {
	alert('not ready yet');
}

function updateDetailLayer() {
	var item_name, item_discount, html,
		$ly_item_discount_detail = $("#ly_item_discount_detail");

	html = "";
	$("div[name=dc_detail_layer]").each(function () {
		var chkbox = $(this).parents("tr:eq(0)").find("td.checking input[type=checkbox]");
		if (chkbox.attr("checked") != "checked") {
			return true;
		}

		item_name = $(this).parents("td.discount").siblings("td.info").find(".title").text();
		item_discount = $(this).find("div.g_content p.g_total strong span[name=layer_dctotal]").text();

		html += "<tr><td>" + item_discount + "</td><td>" + item_name + "</td></tr>";
	});
	$ly_item_discount_detail.find("div.cont table tbody").html(html);
}

// 선택한 상품중 배송지 국가로 배송이 안 되는 상품이 있는지 확인(nation_cd = US, 글로벌 주문인 경우만)
function checkOverseaNation() {
	if (GMKT.ServiceInfo.nation != "US" && $get("global_order_type").value != "G") {
		// US, HK, MY 외 로컬 주문인 경우는 그냥 true 처리
		return true;
	}

	if ($("#cart_addr_no").val() === "0") {
		// US, 글로벌 주문인 경우 주소록을 선택하지 않아도 주문 가능
		return;
	}

	var order_idxs;

	// 선택한 상품의 order_idx를 , 로 묶음(111,222,...)
	order_idxs = $("input[name=select_order]:checked").map(function () {
		return this.value;
	}).get().sort().join(",");

	if (order_idxs != getDeliverableOrder()) {
		return false;
	}

	return true;
}

function getDeliverableOrder() {
	var order_idxs, param, svc_nation_cd;

	// 선택한 상품의 order_idx를 , 로 묶음(111,222,...)
	order_idxs = $("input[name=select_order]:checked").map(function () {
		return this.value;
	}).get().sort().join(",");

	// 비로그인인 경우는 주문페이지로 갈 수 있게 같은 order_idx를 그냥 리턴
	if (__PAGE_VALUE.IS_LOGIN === false) {
		return order_idxs;
	}

	if ($get("global_order_type").value == "G") {
		svc_nation_cd = "US";
	} else {
		svc_nation_cd = GMKT.ServiceInfo.nation;
	}

	param = new RMSParam();
	param.add("order_way_kind", "PAK");
	param.add("order_idxs", order_idxs);
	param.add("svc_nation_cd", svc_nation_cd);
	param.add("site_id", __PAGE_VALUE.SITEID);
	param.add("global_order_type", $get("global_order_type").value);
	param.add("del_nation_cd", $("#del_nation_cd").val());

	// del_nation_cd로 주문 가능한 상품의 order_idx가 ,로 묶어서 넘어옴(111,222,...)
	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "CheckOverseaDelivery", param.toJson());

	return ret.split(",").sort();
}

// 배송지 국가 변경시 해당 국가로 배송불가한 상품들은 체크해제
function updateCheckedOrderByNation() {	
	if (GMKT.ServiceInfo.nation != "US" && $get("global_order_type").value != "G") {
		// US, HK, MY 외 로컬 주문인 경우는 그냥 true 처리
		return ;
	}

	if ($("#cart_addr_no").val() === "0") {
		// US, 글로벌 주문인 경우 주소록을 선택하지 않아도 주문 가능
		return;
	}

	var is_include_not_delivable, not_delivable_arr,
		avail_order_idx = getDeliverableOrder();

	is_include_not_delivable = false;
	not_delivable_arr = new Array();

	$("input[name=select_order]").each(function () {
		if ($.inArray($(this).val(), avail_order_idx) == -1 && $(this).attr("checked") == "checked") {
			$(this).attr("checked", false);
			is_include_not_delivable = true;

			not_delivable_arr.push( $(this).attr("data-idx") );
		}
	});

	if (true === is_include_not_delivable) {
		alert(MultiLang.findResource("IncludedNotDeliverableOrder").replace("{0}", "[" + not_delivable_arr + "]"));

		recalculaterSelectedItems();
	}
}

function getCartDiscountLayer(_this) {
	var i, param, ret, html;

	param = new RMSParam();
	param.add("order_idxs", getSeletedOrderIdx());
	param.add("global_order_type", $get("global_order_type").value);
	param.add("del_nation_cd", $get("del_nation_cd").value);

	ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "GetCartDiscountJson", param.toJson(), function (result, svc, method, xmlHttpasync) {
		
		var cart_dc_info = $.parseJSON(result),
			$btn = $("a.btn_details[data-cartdiscount]"),
			$tbody = $btn.siblings("div.ly_wrap").find(".ly_order .cont tbody");

		
		if (cart_dc_info.item_list !== undefined) {
			for (i = 0; i < cart_dc_info.item_list.length; i++) {
				html = "<tr>";
				html += "  <td style='font-weight: normal;'>" + cart_dc_info.item_list[i].type + "</td>"; // 임시로 font-weight 강제 지정
				html += "  <td style='font-weight: normal;'>" + cart_dc_info.item_list[i].amount + "</td>";
				html += "  <td style='font-weight: normal;'>" + cart_dc_info.item_list[i].code + "</td>";
				html += "</tr>";

				$tbody.append(html);
			}
		}

		$btn.attr("data-cartdiscount", "false");
		$btn.siblings("div.ly_wrap").toggleClass("click").find("div.ly_order").toggle();
	});
}

// ----- (s) 주소록 관련 처리 -----
var OrderAddressBook = {};

OrderAddressBook.popFindAddress = function (where) {
	Util.openInnerPopup(Public.convertNormalUrl("~/My/AddressBook.aspx?where=" + where), 860, 560);
}

OrderAddressBook.setAddress = function (_this, key) {
	var $address_current = $("div.select_lst3 a.current span");

	$address_current.html($(_this).html());

	if (key === "0") {
		OrderAddressBook.clearAddress();
		return;
	}

	OrderAddressBook.callbackSetAddress(
		address_book[key].addr_no,
		address_book[key].addr_nm,
		address_book[key].recv_nm,
		address_book[key].tel_no,
		address_book[key].hp_no,
		Util.RemoveSpecialCharacter(address_book[key].addr_front),
		Util.RemoveSpecialCharacter(address_book[key].addr_last),
		address_book[key].nation_cd,
		address_book[key].zip_code,
		address_book[key].default_yn,
		key,
		address_book[key].jp_recv_nm,
		address_book[key].i_addr_no,
		address_book[key].addr_group_no,
		address_book[key].jp_recv_nm_first,
		address_book[key].jp_recv_nm_last,
		"",
		address_book[key].state,
		address_book[key].city,
		address_book[key].street
	//address_book[key].email
	);
}

OrderAddressBook.callbackSetAddress = function (addr_no, addr_name, rcv_nm, tel_no, hp_no, addr1, addr2, nation_isocode, zip_code, default_addr_yn, key, jp_recv_nm, i_addr_no, addr_group_no, jp_recv_nm_first, jp_recv_nm_last, email, state, city, street) {
	var $addressCountry = $("#addressCountry"),
		$address = $("#shippingAddress"),
		$del_nation_area = $("#del_nation_area"),
		$addr_no = $("#cart_addr_no");

	$addressCountry.parents("table.infoView").show();

	if ($addressCountry.find(".domestic").length === 0) {
		$addressCountry.text(OrderAddressBook.getCountryName(nation_isocode));
	}
	$address.text(addr1 + " " + addr2);
	$addr_no.val(addr_no);

	if (window.chageSelectedNation !== undefined) {	// 원래 함수에 오타가 있네..
		chageSelectedNation(nation_isocode, true);
	}

	if (window.updateCheckedOrderByNation !== undefined) {
		updateCheckedOrderByNation();
	}
}

OrderAddressBook.clearAddress = function () {
	var $addressCountry = $("#addressCountry"),
		$address = $("#shippingAddress"),
		$del_nation_area = $("#del_nation_area"),
		$addr_no = $("#cart_addr_no");

	$addressCountry.text("");
	$address.text("");
	$addr_no.val("0");

	$addressCountry.parents("table.infoView").hide();

	if (window.updateCheckedOrderByNation !== undefined) {
		updateCheckedOrderByNation();
	}
}

OrderAddressBook.refreshAddress = function () {
	// 주소록 정보를 json 데이터로 받아온다.
	var ret, param;

	param = new RMSParam();
	param.add("format", "json");
	param.add("count", 10);
	param.add("nation_cd", $("#del_nation_cd").val());
	param.add("global_order_type", $("#global_order_type").val());

	ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_MemberAjaxService.asmx"), "GetMemberAddress", param.toJson(), function (result, svc, method, xmlHttpasync) {
		try {
			var i, idx, address_order, key,
				keys = [],
				temp_address = $.parseJSON(result);

			address_book = temp_address;

			// 넘어온 순서대로 정렬하기 위해...
			for (key in temp_address) {
				if (temp_address.hasOwnProperty(key)) {
					idx = result.indexOf("addr_no\":" + key + ",");
					keys.push({ idx: idx, key: key });
				}
			}

			keys.sort(function (a, b) {
				return a.idx - b.idx;	// a > b 이면 양수, a < b 이면 음수 리턴
			});

			for (key in keys) {
				if (keys.hasOwnProperty(key)) {
					keys[key] = keys[key].key; // addr_no만 저장
				}
			}

			if (keys.length > 0) {
				address_order = keys.join(",");
			}

			OrderAddressBook.renderAddress(temp_address, address_order);
		}
		catch (err) {
		}
	});

	return false;
}

OrderAddressBook.renderAddress = function (address_list, address_order) {
	// 주소록을 화면에 다시 그려준다.

	// 중복 싫으니 모양은 안 이쁘지만 내부함수로..
	function getListItemHtml(address_list, key) {
		var temp_li = "";

		temp_li += "<li>";
		temp_li += "	<a href=\"#\" onclick=\"OrderAddressBook.setAddress(this, '" + address_list[key].addr_no + "'); return false;\" data-addrno=\"" + address_list[key].addr_no + "\" style=\"padding-left: 6px;\">";
		temp_li += "		<dfn class=\"flag_" + OrderAddressBook.getFlagName(address_list[key].nation_cd) + "\"><span>" + OrderAddressBook.getCountryName(address_list[key].nation_cd) + "</span></dfn>";
		temp_li += "		<span>" + address_list[key].addr_nm + "</span>";
		temp_li += "	</a>";
		temp_li += "</li>";

		return temp_li;
	}

	var i, li, key,
		$address_ul = $("#address_ul");

	$address_ul.children("li:not(:eq(0))").remove(); // 첫번째(select address) 제외하고 모두 삭제

	li = "";

	if (address_order === undefined) {
		// 순서 지정되지 않으면 key순으로 출력
		for (key in address_list) {
			if (address_list.hasOwnProperty(key)) {
				li += getListItemHtml(address_list, key);
			}
		}
	}
	else {
		address_order = address_order.split(",");

		for (i = 0; i < address_order.length; i++) {
			key = address_order[i];

			li += getListItemHtml(address_list, key);
		}
	}

	$address_ul.append(li);
}

OrderAddressBook.getFlagName = function(nation_cd) {
	var flag_nm = "";

	switch (nation_cd) {
		case "CN":
			flag_nm = "ch"; break;
		case "HK":
			flag_nm = "ho"; break;
		case "US":
			flag_nm = "us"; break;
		case "MY":
			flag_nm = "my"; break;
		case "JP":
			flag_nm = "ja"; break;
		case "ID":
			flag_nm = "id"; break;
		case "SG":
			flag_nm = "sg"; break;
		case "KR":
			flag_nm = "ko"; break;
		case "PH":
			flag_nm = "ph"; break;
		case "TW":
			flag_nm = "ta"; break;
		case "GB":
			flag_nm = "uk"; break;
		case "VN":
			flag_nm = "vi"; break;
		case "AU":
			flag_nm = "au"; break;
		case "CA":
			flag_nm = "ca"; break;
		case "TH":
			flag_nm = "th"; break;
		case "RU":
			flag_nm = "ru"; break;
	}

	return flag_nm;
}

OrderAddressBook.getCountryName = function (nation_cd) {
	var country_nm = "";

	switch (nation_cd) {
		case "CN":
			country_nm = "China"; break;
		case "HK":
			country_nm = "Hong Kong"; break;
		case "US":
			country_nm = "United States"; break;
		case "MY":
			country_nm = "Malaysia"; break;
		case "JP":
			country_nm = "Japan"; break;
		case "ID":
			country_nm = "Indonesia"; break;
		case "SG":
			country_nm = "Singapore"; break;
		case "KR":
			country_nm = "South Korea"; break;
		case "PH":
			country_nm = "Philippines"; break;
		case "TW":
			country_nm = "Taiwan"; break;
		case "GB":
			country_nm = "United Kingdom"; break;
		case "VN":
			country_nm = "Vietnam"; break;
		case "AU":
			country_nm = "Australia"; break;
		case "CA":
			country_nm = "Canada"; break;
		case "TH":
			country_nm = "THAILAND"; break;
		case "RU":
			country_nm = "Russia"; break;
	}

	return country_nm;
}
// ----- (e) 주소록 관련 처리 -----

//-- 상품수량 시작 --
function OrderCnt_onClick(obj) {
	if ($(obj).val() <= 1) {
		$(obj).val("");
	}

}
function OrderCnt_onBlur(obj) {
	if ($(obj).val() == "") {
		$(obj).val("1");
	}
}

//-- 상품수량 끝 --

var COD_Order = {
	GoOrder: function (sell_cust_no, type) {
		this.SelectItem(sell_cust_no, type);
		goOrder();
	},
	SelectItem: function (sell_cust_no, type) {
		if (type == "all") {
			//판매자 단위 전체상품을 후불결제로 체크
			$(".orderTable input[type=checkbox]").attr("checked", false);
			var allGItem = $("#COD_Grouping_tb .lst tr[seller_cust_no=" + sell_cust_no + "]").find("span.num");
			for (var i = 0; i < allGItem.length; i++) {
				$(".sellerShop[seller_cust_no=" + sell_cust_no + "]").parent(".co_seller").find(".orderList input[data-idx=" + $(allGItem).eq(i).attr("item_idx") + "]").attr("checked", true);
			}
		}
		else { //undefined
			// 판매자 상품 안에 구매자가 선택한 상품이있으면 선택한 상품만 후불결제로 체크
			var buyerSelect = $(".sellerShop[seller_cust_no=" + sell_cust_no + "]").parent(".co_seller").find(".orderList input:checked");
			if (buyerSelect.length > 0) {
				//다른 셀러의 상품 처리
				var deselectseller = $(".sellerShop[seller_cust_no!=" + sell_cust_no + "]").parent(".co_seller").find(".orderList input[type=checkbox]");
				$(deselectseller).attr("checked", false);

				// 판매자중에서 선택되어있지만 상품 필터에 걸리는 부분 셀렉트 제거
				for (var i = 0; i < buyerSelect.length; i++) {
					if ($("#COD_Grouping_tb .lst tr[seller_cust_no=" + sell_cust_no + "]").find("span[item_idx=" + $(buyerSelect).eq(i).attr("data-idx") + "]").length == 0) {
						$(buyerSelect).eq(i).attr("checked", false);
					}
				}
			}
			else {
				//판매자 단위 전체상품을 후불결제로 체크
				$(".orderTable input[type=checkbox]").attr("checked", false);
				var allGItem = $("#COD_Grouping_tb .lst tr[seller_cust_no=" + sell_cust_no + "]").find("span.num");
				for (var i = 0; i < allGItem.length; i++) {
					$(".sellerShop[seller_cust_no=" + sell_cust_no + "]").parent(".co_seller").find(".orderList input[data-idx=" + $(allGItem).eq(i).attr("item_idx") + "]").attr("checked", true);
				}
			}
		}
	},
	selectCodChkBox: function () {
		if ($get("check_cod").checked) {
			$("#COD_Grouping_tb").show();
		}
		else {
			$("#COD_Grouping_tb").hide();
		}
	}
}