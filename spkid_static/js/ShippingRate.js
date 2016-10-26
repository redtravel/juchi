$(function () {
	CtrShippingRate.initEventHandler();

	if ($("#ship_where").val() == "popup") {
		$("div#shipping_rate #detail_close_btn").click();
	}
});

var CtrShippingRate = {
	initEventHandler: function () {
		// see details layer
		$("div#shipping_rate").delegate(".btn_details", "click", function () {
			// 다른 layer 숨김 처리 & click 클래스 제거
			$("div.ly_wrap").not($(this).siblings("div.ly_wrap")).removeClass("click").find("div.ly_order").hide();

			if ($(this).siblings("div.ly_wrap").find("div.ly_order").length !== 0) {
				$(this).siblings("div.ly_wrap").toggleClass("click").find("div.ly_order").toggle();
			}

			if ($(this).attr("data-shipopt") == "true" && $("#ship_where").val() == "cart") {
				CtrShippingRate.getShippingOption(this);
			}
		});

		// see details close
		$("div#shipping_rate").delegate(".btn_close", "click", function () {
			$(this).parent("div.ly_order").hide();
			$(this).parents("div.ly_wrap").removeClass("click");
			return false;
		});

		// QExpress info icon
		$("div#shipping_rate .icon_info").mouseover(function () {
			$(this).siblings("div.ly_wrap").find("div.ly_info").show();
		}).mouseout(function () {
			$(this).siblings("div.ly_wrap").find("div.ly_info").hide();
		});

		// Detail ▼▲ 버튼
		$("div#shipping_rate #detail_close_btn").click(function () {
			$(this).toggleClass("close");
			$(this).siblings("div.details").toggle();
		});

		// img slider init
		$("div.slide_list div.slide_in ul.sliding").each(function () {
			if ($(this).find("li").length > 4) {
				$(this).parent("div.slide_in").siblings(".btn_next").addClass("on");
			}
		});

		// img slider btn_prev
		$("div.slide_list .btn_prev").click(function () {
			var cur_left = parseInt($(this).siblings("div.slide_in").find("ul.sliding").css("left"), 10);
			var li_cnt = $(this).siblings("div.slide_in").find("ul.sliding li").length;

			if (cur_left === 0) {
				return;
			}

			var new_left = cur_left + 70;
			$(this).siblings("div.slide_in").find("ul.sliding").css("left", new_left + "px");

			if (new_left === 0) {
				$(this).removeClass("on");
			}

			$(this).siblings(".btn_next").addClass("on");
		});

		// img slider btn_next
		$("div.slide_list .btn_next").click(function () {
			var cur_left = parseInt($(this).siblings("div.slide_in").find("ul.sliding").css("left"), 10);
			var li_cnt = $(this).siblings("div.slide_in").find("ul.sliding li").length;

			if (cur_left <= (4 - li_cnt) * 70) {
				return;
			}

			var new_left = cur_left - 70;
			$(this).siblings("div.slide_in").find("ul.sliding").css("left", new_left + "px");

			if (new_left <= (4 - li_cnt) * 70) {
				$(this).removeClass("on");
			}

			$(this).siblings(".btn_prev").addClass("on");
		});

		// 상품별 배송 옵션 변경
		$("div#shipping_rate .btn_optionchange").click(function () {
			$(this).siblings("div.g_layer_shipOption").toggle();
		});

		// 상품별 배송 옵션 닫기
		$("div.g_layer_shipOption .g_btn_iClose, div.g_layer_shipOption .g_btn_close").click(function () {
			$(this).parents("div.g_layer_shipOption").hide();
		});
	},
	getShippingOption: function (_this) {
		var $wrap,
			i, goodscode_arr, group_no_arr, param, ret, wrap_idx, uid;

		$wrap = $(_this).siblings("div.ly_wrap");
		wrap_idx = $("div#shipping_rate div.details div.ly_wrap").index($wrap);

		goodscode_arr = $wrap.find("div.cont tr td[data-goodscode]").map(function () {
			return $(this).attr("data-goodscode");
		}).get();

		group_no_arr = $wrap.find("div.cont tr td[data-delivery_group_no]").map(function () {
			return $(this).attr("data-delivery_group_no");
		}).get();

		for (i = 0; i < goodscode_arr.length; i++) {
			param = new RMSParam();
			param.add("goodscode", goodscode_arr[i]);
			param.add("target_currency", $("#ship_target_currency").val());
			param.add("global_order_type", $("#ship_global_order_type").val());

			uid = wrap_idx + "_" + i; // async callback에서 caller 구분용

			ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "GetGoodsDeliveryFeeDefaultOptionInfo" + "?uid" + uid, param.toJson(), CtrShippingRate.callbackGetShippingOption);
		}

		// callback을 보고 성공했을 때에만 하는게 더 맞지만..
		$(_this).attr("data-shipopt", "false");
	},
	callbackGetShippingOption: function (result, svc, method, xmlHttpasync) {
		var i, j, uid, wrap_idx, item_idx, temp_arr, transc_nm, delivery_option_name, delivery_option_names_arr, cur_option, cur_delivery_group_no, selected,
			$select_opt;

		if (result !== null && result.Rows !== undefined && result.Rows.length > 1) {
			uid = method.substr(method.indexOf("?uid")).replace("?uid", "");

			temp_arr = uid.split("_");

			wrap_idx = temp_arr[0];
			item_idx = temp_arr[1];

			// 버튼 노출
			$("div#shipping_rate div.details div.ly_wrap:eq(" + wrap_idx + ") a.btn_optionchange:eq(" + item_idx + ")").show();

			// 옵션 변경 layer 채워줌
			$select_opt = $("div#shipping_rate div.details div.ly_wrap:eq(" + wrap_idx + ") select:eq(" + item_idx + ")");
			cur_option = $("div#shipping_rate div.details td.rate:eq(" + wrap_idx + ")").attr("data-optioncode");
			cur_delivery_group_no = $("div#shipping_rate div.details td.rate:eq(" + wrap_idx + ")").attr("data-delivery_group_no");
			
			delivery_option_names_arr = $("#ship_delivery_option_names").val().split("|");

			for (i = 0; i < result.Rows.length; i++) {
				transc_nm = result.Rows[i].transc_nm;
				selected = "";

				if (result.Rows[i].delivery_fee_condition.trim() == "W") {
					transc_nm = MultiLang.findResource("ScriptPickupSeller");
				}

				if (transc_nm === "") {
					// delivery_option_code로 배송수단 이름을 가져옴
					for (j = 0; j < delivery_option_names_arr.length; j++) {
						delivery_option_name = delivery_option_names_arr[j].split("_");

						if (result.Rows[i].option_code == delivery_option_name[0]) {
							transc_nm = delivery_option_name[1];
							break;
						}
					}
				}

				if (cur_delivery_group_no == result.Rows[i].delivery_group_no) {
					selected = "selected";
				}
				$select_opt.append("<option value='" + result.Rows[i].delivery_group_no + "' title='" + result.Rows[i].delivery_fee + "' " + selected + ">" + transc_nm + "</option>");
			}

			CtrShippingRate.showSelectDelOptFee($select_opt);
		}
	},
	setDeliveryOption: function (_this, order_idx, pid) {
		var delivery_option_no = "0";

		delivery_option_no = $(_this).parent(".g_processBtns").prev("table").find("select").val();

		var param = new RMSParam();
		param.add("pid", pid);
		param.add("order_way_kind", "PAK");
		param.add("order_idx", order_idx);
		param.add("delivery_option_no", delivery_option_no);
		param.add("global_order_type", $("#ship_global_order_type").val());

		var ret = RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "SetDeliveryOption", param.toJson(), CtrShippingRate.callbackSetDeliveryOption);
	},
	callbackSetDeliveryOption: function (result, svc, method, xmlHttpasync) {
		if ($("#ship_where").val() == "cart") {
			//recalculaterSelectedItems();
			ControlUtil.getServerForm().method = "GET";
			ControlUtil.submitServerForm(Public.getOrderServerUrl("/Order/Cart.aspx?global_order_type=" + $("#ship_global_order_type").val(), false));
		}
	},
	showSelectDelOptFee: function (_this) {
		var surcharge = $(_this).find("option:selected").attr("title");
		$(_this).parent("td").next("td").text(PriceUtil.FormatCurrencySymbol(surcharge));
	}
}