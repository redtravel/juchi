//$(function () {
//	ctrShippingAddress.init();
//});

var ctrShippingAddress = {
	init: function (initAddressSelect) {
		ctrShippingAddress.initEventHandler();

		if (initAddressSelect === undefined || initAddressSelect === true) {
			ctrShippingAddress.updateUIByNation(true);
		} else {
			ctrShippingAddress.updateUIByNation(false);
		}
	},

	initEventHandler: function () {
		// custom event가 필요한 경우 여기에서 정의
		var $wrapper = $(ctrShippingAddress.config.element_map.wrapper);
	},

	updateUIByNation: function (initAddressSelect) {
		// 국가코드에 따라 UI 보임/숨김 처리
		var cur_nation_cd,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$address_select = $(ctrShippingAddress.config.element_map.address_selectbox_area, $wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
			$zipcode_sel = $(ctrShippingAddress.config.element_map.zipcode + "_sel", $wrapper),
			$zipcode_search_btn = $(ctrShippingAddress.config.element_map.zipcode_search_btn, $wrapper),
			$in_eng = $(ctrShippingAddress.config.element_map.in_eng, $wrapper),
			$in_eng_label = $(ctrShippingAddress.config.element_map.in_eng_label, $wrapper),
			$address_guide = $(ctrShippingAddress.config.element_map.address_guide, $wrapper),
			$address_guide_zipcode = $(ctrShippingAddress.config.element_map.address_guide_zipcode, $wrapper),
			$address_guide_selectbox = $(ctrShippingAddress.config.element_map.address_guide_selectbox, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		cur_nation_cd = $nation_cd.val();

		if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.no_search_btn) !== -1) {
			$address_front.attr("readonly", false).removeClass("gsm_readonly").addClass("gsm_textType");
		} else {
			$address_front.attr("readonly", true).addClass("gsm_readonly").removeClass("gsm_textType");
		}

		$address_guide.hide();

		// 주소 selectbox 보임/숨김
		if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.address_front_with_selectbox) !== -1) {
			$address_select.show();
			$address_guide_selectbox.show();
			$address_front.parent("dd").show();

			if (initAddressSelect !== undefined && true === initAddressSelect) {
				ctrShippingAddress.initAddressSelect();
			} else {
				ctrShippingAddress.initAddressSelect("nosub"); // state 만 init
			}
		} else {
			$address_select.hide();
			$address_guide_zipcode.show();
			$address_front.parent("dd").show();
		}

		if (false === ctrShippingAddress.config.show_address_guide || $.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.no_search_btn) !== -1) {
			$address_guide.hide();
		}

		// 우편번호 보임/숨김 & Readonly
		if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.no_zipcode) !== -1) {
			$zipcode.hide();
			$zipcode_sel.hide();
		} else {
			if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.zipcode_selectable) !== -1) {
				$zipcode.hide();
				$zipcode_search_btn.hide();
				$zipcode_sel.show();
			} else {
				$zipcode.show();
				$zipcode_search_btn.show();
				$zipcode_sel.hide();
			}

			if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.zipcode_editable) !== -1) {
				$zipcode.attr("readonly", false).removeClass("gsm_readonly");
			} else {
				$zipcode.attr("readonly", "readonly").addClass("gsm_readonly");
			}
		}

		// selectbox 영문 보임/숨김
		if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.address_select_en) === -1) {
			$in_eng.attr("checked", false).hide();
			$in_eng_label.hide();
		} else {
			$in_eng.show();
			$in_eng_label.show();
		}

		if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.no_search_btn) !== -1) {
			$zipcode_search_btn.hide();
		} else {
			$zipcode_search_btn.show();
		}

		// CN인 경우 특별 처리들
		if (cur_nation_cd == "CN") {
			// 1) 우편번호 위치(for CN) - 붙이는 순서에 유의
			$wrapper.find("dt._zipcode").insertBefore($wrapper.find("dd._hidden"));
			$wrapper.find("dd._zipcode").insertBefore($wrapper.find("dd._hidden"));

			// 2) address_front와 last를 한 줄에 표시
			$wrapper.find("dd#address_front_wrap input").css("width", "");
			$wrapper.find("dd#address_last_wrap").css("float", "right").css("width", "79%").insertBefore($wrapper.find("dd#address_front_wrap"));

			$wrapper.find("._zipcode").hide();
		} else {
			$wrapper.find("dd._zipcode").prependTo($wrapper);
			$wrapper.find("dt._zipcode").prependTo($wrapper);

			$wrapper.find("dd#address_front_wrap input, dd._address_last input").css("width", "427px");
			$wrapper.find("dd#address_last_wrap").css("float", "none").insertAfter($wrapper.find("dd#address_front_wrap"));

			$wrapper.find("._zipcode").show();
		}
	},

	initAddressSelect: function (option) {
		var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		$sel_state.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
		$sel_city.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
		$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));

		if (typeof (AddressSearcher) != "undefined") {
			AddressSearcher.get_address_list($nation_cd.val(), "", "state", ctrShippingAddress.callback.address_list, option);
		}
	},

	updateAddressSelect: function () {
		// state, city, street 값에 따라 업데이트 시킬 때(state 목록은 새로 검색 안함)
		var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		$sel_state.val($state.val());
		$sel_city.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
		$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));

		if ($sel_state.is(":visible") === false) {
			return;
		}

		if (typeof (AddressSearcher) != "undefined") {
			AddressSearcher.get_address_list($nation_cd.val(), $state.val(), "city", ctrShippingAddress.callback.address_list);
		}
	},

	getValidZipcode: function () {
		// state, city, street 정보가 있으면 해당 값으로 valid한 zipcode를 가져와 hidden_zipcode 정보를 업데이트 시켜준다.
		var nation_cd,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
			$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
			$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
			$hidden_zipcode = $(ctrShippingAddress.config.element_map.hidden_zipcode, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		nation_cd = $nation_cd.val();

		AddressSearcher.get_address_detail(nation_cd, "", $state.val(), $city.val(), $street.val(), ctrShippingAddress.callback.get_valid_zipcode, "validzipcode");
	},

	clearAddress: function () {
		var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$address_last = $(ctrShippingAddress.config.element_map.address_last, $wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
			$street = $(ctrShippingAddress.config.element_map.street, $wrapper);

		$address_front.val("");
		//$address_last.val("");

		$sel_state.val("");
		$sel_city.val("");
		$sel_street.val("");

		$state.val("");
		$city.val("");
		$street.val("");
	},

	writeDirectFrontAddress: function () {
		// 직접입력 모드
		var cur_nation_cd,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$address_select = $(ctrShippingAddress.config.element_map.address_selectbox_area, $wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		cur_nation_cd = $nation_cd.val();

		if (cur_nation_cd == "SG") {
			$address_front.val("Singapore");
		} else if (cur_nation_cd == "CN") {
			// nothing to do
		} else if ($.inArray(cur_nation_cd, ctrShippingAddress.config.nation_cd.no_search_btn) !== -1) {
			$address_front.attr("readonly", false).removeClass("gsm_readonly").addClass("gsm_textType").show();
			$address_front.parent("dd").show();
			$address_select.hide();
		} else {
			$address_front.parent("dd").hide();
			$address_select.show();
		}

		$zipcode.attr("readonly", false).removeClass("gsm_readonly");

		if ($sel_state.length <= 1) {
			ctrShippingAddress.initAddressSelect();
		}
	},

	showEN: function () {
		var $nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

		if (typeof (AddressSearcher) != "undefined") {
			AddressSearcher.get_address_list($nation_cd.val(), "", "state", ctrShippingAddress.callback.address_list);
		}
	},

	searchAddress: function () {
		if (window.searchAddress !== undefined) {
			searchAddress();
		}
	},

	checkAddressForOrder: function () {
		var nation_cd, address_front, address_temp,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
			$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
			$is_zipcode_valid_yn = $(ctrShippingAddress.config.element_map.is_zipcode_valid_yn, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd)
			;

		nation_cd = $nation_cd.val();

		if (nation_cd == "CN") {
			// 셀렉트박스를 모두 선택해야함
			if ($sel_state.val() === "" || $sel_city.val() === "" || $sel_street.val() === "") {
				return "InvalidRegionCN";
			}
		} else if (nation_cd == "JP") {
			// Sagawa인 경우, 우편번호지의 주소와 front 주소가 일치해야함
			if ($is_zipcode_valid_yn.val() == "N") {
				return "Valid";
			} else {
				address_front = $.trim($address_front.val());
				address_temp = $state.val() + $city.val() + $street.val().replace(/（[^\x00-\x2E\x3A-\x40\x5B-\x5E\x60\x7B-\x7F]+）$/, ""); // street에 붙은 번지수는 떼어낸다.

				// 공백을 제거하고 비교
				if (address_front.replace(/\s/g, "") != address_temp.replace(/\s/g, "")) {
					return "NotMatch";
				} else {
					return "Valid";
				}
			}
		}

		return "Valid";
	},

	focusEmptyRegion: function () {
		var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper)
			;

		if ($sel_state.val() === "") {
			$sel_state.focus();
			return;
		}

		if ($sel_city.val() === "") {
			$sel_city.focus();
			return;
		}

		if ($sel_street.val() === "") {
			$sel_street.focus();
			return;
		}
	},

	updateAddressFront: function () {
		var nation_cd,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$address_last = $(ctrShippingAddress.config.element_map.address_last, $wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
			$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd)
		;

		nation_cd = $nation_cd.val();

		if (nation_cd == "JP") {
			// 번지 제외하고 표시
			$address_front.val($state.val() + $city.val() + $street.val().replace(/（[^\x00-\x2E\x3A-\x40\x5B-\x5E\x60\x7B-\x7F]+）$/, ""));
		} else {
			$address_front.val($state.val() + $city.val() + $street.val());
		}

		$address_last.val("");
	},

	eventHandlers: {
		zipcode_keyup: function (pThis) {
			// 사용자가 우편번호란에서 키를 입력할 때.
			var timeout = ctrShippingAddress.global_var.timeout,
				zipcode_delay = ctrShippingAddress.config.zipcode_delay,
				cur_zipcode = $(pThis).val();

			// 우편번호가 변경되었는지 확인하고 약간의 딜레이를 주어
			// 키입력이 끝났을 때 우편번호 유효성을 검사하도록 한다.
			if (cur_zipcode != ctrShippingAddress.global_var.last_zipcode) {
				if (timeout) {
					clearTimeout(timeout);
				}

				ctrShippingAddress.global_var.timeout = setTimeout(function () {
					var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
						$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper);

					$zipcode.trigger("change");
				}, zipcode_delay);
			} else if (timeout) {
				clearTimeout(timeout);
			}
		},
		zipcode_changed: function (pThis, params) {
			// 사용자가 우편번호 수정을 완료했을 때
			var zipcode, opt,
				$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
				$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

			zipcode = $zipcode.val();
			ctrShippingAddress.global_var.last_zipcode = zipcode;
			opt = "zipcode";

			// 우편번호 검색창에서 넘어오는 경우(valid한 우편번호만 존재) or 우편번호의 valid 여부만 업데이트 하고 싶은 경우
			if (params.length > 1 && params[1] == "indirectly") {
				opt = "validzipcode";
			}

			if (zipcode !== "" && typeof (AddressSearcher) != "undefined") {
				AddressSearcher.get_address_detail($nation_cd.val(), zipcode, "", "", "", ctrShippingAddress.callback.address_detail, opt);
			}

			if (typeof (ShippingFeeCalculator) != "undefined") {
				ShippingFeeCalculator.calculate(zipcode);
			}
		},
		zipcode_sel_changed: function (pThis) {
			var $wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
				$zipcode_sel = $(ctrShippingAddress.config.element_map.zipcode + "_sel", $wrapper);

			$zipcode.val($zipcode_sel.val());
		},
		address_select_changed: function (pThis) {
			// selectbox의 값이 변경된 경우
			var sel_group, key, group, nation_cd,
			$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
			$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
			$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
			$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
			$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
			$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
			$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
			$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
			$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
			$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

			group = "";
			sel_group = $(pThis).attr("data-group"); // 어떤 selectbox가 변경됐는지 구분
			nation_cd = $nation_cd.val();

			// 하위 단계의 selectbox 를 초기화 시켜준다. key, group으로 검색을 통해 selectbox 목록을 채워줌
			if (sel_group == "state") {
				$sel_city.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
				$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));

				key = $(pThis).val();
				group = "city";

				$state.val(key);
				$city.val("");
				$street.val("");
				$zipcode.val("");
			} else if (sel_group == "city") {
				$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));

				key = $(pThis).val();
				group = "street";

				$city.val(key);
				$street.val("");
				$zipcode.val("");
			} else if (sel_group == "street") {
				key = $(pThis).val();
				$street.val(key);

				if ($.inArray(nation_cd, ["MY", "JP", "ID", "KR", "SG"]) == -1) {
					$zipcode.val("");
				} else {
					if ($street.val() !== "") {
						$zipcode.val("");
					}

					if ($zipcode.val() === "") {
						ctrShippingAddress.searchAddress();
					}
				}

				AddressSearcher.get_address_detail(nation_cd, "", $state.val(), $city.val(), $street.val(), ctrShippingAddress.callback.address_detail, "selectbox");
			}

			$address_front.val($.trim($state.val() + " " + $city.val() + " " + $street.val())).trigger("change");

			if (group !== "" && key !== "" && typeof (AddressSearcher) != "undefined") {
				AddressSearcher.get_address_list(nation_cd, key, group, ctrShippingAddress.callback.address_list);
			}
		},
		address_front_changed: function (pThis) {
			// CN의 경우 address_front가 바뀌면 input box의 크기를 변경해줘야 함..
			var front_len, last_len,
				$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
				$address_last = $(ctrShippingAddress.config.element_map.address_last, $wrapper),
				$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

			if ($nation_cd.val() != "CN") {
				return;
			}

			front_len = $address_front.val().length;
			last_len = 0;

			if (front_len < 5 || front_len > 80) {
				front_len = 30;
			} else {
				front_len = (front_len * 2.5 > 70) ? 70 : front_len * 2.2;
			}

			last_len = 100 - front_len;

			if (last_len < 50) {
				last_len = last_len * 0.7;
			}

			$address_front.css("width", front_len + "%");
			$address_last.css("width", last_len + "%");
			$address_last.parent("dd").css("width", last_len - 2.2 + "%");
		}
	},

	callback: {
		address_detail: function (result, svc, methodName, xmlHttpasync) {
			// 입력된 우편번호가 수정된 경우. 우편번호에 해당하는 주소를 검색하여 유효성 검사 & Address front 표시
			var i, AddrLen, rst, in_eng, nation_cd,
				$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
				$zipcode_sel = $(ctrShippingAddress.config.element_map.zipcode + "_sel", $wrapper),
				$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
				$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
				$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
				$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
				$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
				$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
				$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
				$in_eng = $(ctrShippingAddress.config.element_map.in_eng, $wrapper),
				$is_zipcode_valid_yn = $(ctrShippingAddress.config.element_map.is_zipcode_valid_yn, $wrapper),
				$hidden_zipcode = $(ctrShippingAddress.config.element_map.hidden_zipcode, $wrapper),
				$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

			nation_cd = $nation_cd.val();
			in_eng = $in_eng.is(":checked");

			try { AddrLen = result.ResultList.length; } catch (ex) { }
			try {
				if (AddrLen > 0) {
					rst = result.ResultList[0];

					$is_zipcode_valid_yn.val("Y");
					$hidden_zipcode.val(rst.ZIPCODE);

					if ($state.val() === "") {
						// state, city, street에 값이 없으면 validzipcode 라도 정보를 넣어준다.
						$state.val(rst.STATE);
						$city.val(rst.CITY);
						$street.val(rst.STREET);
					}

					// validzipcode 옵션이 있으면 address front를 업데이트 하지 않고 끝낸다.
					if (svc !== undefined && svc.indexOf("validzipcode") !== -1) {
						return;
					}

					if (rst.STATE === "" && rst.CITY === "" && rst.STREET === "") {
						ctrShippingAddress.clearAddress();
					} else {
						if (in_eng) {
							$address_front.val($.trim(rst.STATE_EN + " " + rst.CITY_EN + " " + rst.STREET_EN));
						} else {
							$address_front.val($.trim(rst.STATE + " " + rst.CITY + " " + rst.STREET));
						}

						if (nation_cd == "SG") {
							if (in_eng) {
								$address_front.val($.trim(rst.BLDG_EN + " " + rst.NUMBER_EN + " " + $address_front.val()));
							}
							else {
								$address_front.val($.trim(rst.BLDG + " " + rst.NUMBER + " " + $address_front.val()));
							}
						}

						$address_front.trigger("change");

						$state.val(rst.STATE);
						$city.val(rst.CITY);
						$street.val(rst.STREET);
					}

					if (svc !== undefined) {
						if (svc.indexOf("?zipcode") !== -1) {
							// zipcode changed 에서 호출됐다면 selectbox 업데이트
							ctrShippingAddress.updateAddressSelect();
						}
						else if (svc.indexOf("?selectbox") !== -1) {
							// selecbox changed 에서 호출됐다면 우편번호 업데이트 & 배송비 재계산
							if (svc.indexOf("keepZipcode") == -1) {
								$zipcode.val(rst.ZIPCODE);
							}

							ctrShippingAddress.global_var.last_zipcode = rst.ZIPCODE;
							if (typeof (ShippingFeeCalculator) !== "undefined") {
								ShippingFeeCalculator.calculate(rst.ZIPCODE);
							}

							if ($zipcode_sel.is(":visible")) {
								$zipcode_sel.find("option:not(:first)").remove();
								//우편번호가 selectbox인 경우
								for (i = 0; i < AddrLen; i++) {
									$zipcode_sel.append(String.format("<option value='{0}'>{0}</option>", result.ResultList[i].ZIPCODE));
								}

								$zipcode_sel.val($zipcode.val());
							}

							// selectbox 값 기준으로 주소 설정
							$state.val($sel_state.val());
							$city.val($sel_city.val());
							$street.val($sel_street.val());
							$address_front.val($.trim($state.val() + " " + $city.val() + " " + $street.val())).trigger("change");
						}
					}
				}
				else {
					$is_zipcode_valid_yn.val("N");
					$hidden_zipcode.val("");

					// 주소록에서 주소를 선택한 경우, 우편번호는 invalid 하지만 유효한 state, city, street 값이 있을 수 있다. 이 정보로 select box 업데이트
					ctrShippingAddress.updateAddressSelect();
					ctrShippingAddress.getValidZipcode();
				}
			} catch (ex) { }
		},
		get_valid_zipcode: function (result, svc, methodName, xmlHttpasync) {
			var AddrLen, rst,
				$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$hidden_zipcode = $(ctrShippingAddress.config.element_map.hidden_zipcode, $wrapper);

			try { AddrLen = result.ResultList.length; } catch (ex) { }
			try {
				if (AddrLen > 0) {
					rst = result.ResultList[0];

					$hidden_zipcode.val(rst.ZIPCODE);

					if (typeof (ShippingFeeCalculator) != "undefined") {
						ShippingFeeCalculator.calculate(rst.ZIPCODE);
					}
				}
			}
			catch (ex) { }
		},
		address_list: function (result, svc, methodName, xmlHttpasync) {
			// 셀렉트 박스를 채움
			var ADDRLen, i, nation_cd, in_eng, state, city, street, cur_group,
				rst, rst_title, rst_short, rst_LC, rst_EN,
				cn_statelist_chiness, cn_stetelist_english, jp_statelist_japaness, jp_stetelist_english, id_statelist_indonessian, id_stetelist_english,
				$wrapper = $(ctrShippingAddress.config.element_map.wrapper),
				$zipcode = $(ctrShippingAddress.config.element_map.zipcode, $wrapper),
				$zipcode_sel = $(ctrShippingAddress.config.element_map.zipcode + "_sel", $wrapper),
				$address_front = $(ctrShippingAddress.config.element_map.address_front, $wrapper),
				$in_eng = $(ctrShippingAddress.config.element_map.in_eng, $wrapper),
				$sel_state = $(ctrShippingAddress.config.element_map.sel_state, $wrapper),
				$sel_city = $(ctrShippingAddress.config.element_map.sel_city, $wrapper),
				$sel_street = $(ctrShippingAddress.config.element_map.sel_street, $wrapper),
				$state = $(ctrShippingAddress.config.element_map.state, $wrapper),
				$city = $(ctrShippingAddress.config.element_map.city, $wrapper),
				$street = $(ctrShippingAddress.config.element_map.street, $wrapper),
				$nation_cd = $(ctrShippingAddress.config.element_map.out_of_wrapper.nation_cd);

			nation_cd = $nation_cd.val();
			in_eng = $in_eng.is(":checked");

			state = $state.val();
			city = $city.val();
			street = $street.val();

			if (result.ResultList.length > 0 && (result.ResultList[0].CITY === null && result.ResultList[0].STREET === null)) {
				// CN과 JP는 순서 때문에 하드코딩
				if (nation_cd == "CN") {
					cn_statelist_chiness = [{ "Key": "北京", "Value": 321 }, { "Key": "上海", "Value": 276 }, { "Key": "天津", "Value": 300 }, { "Key": "重庆", "Value": 905 }, { "Key": "河北", "Value": 1296 }, { "Key": "山西", "Value": 972 }, { "Key": "河南", "Value": 2176 }, { "Key": "辽宁", "Value": 1563 }, { "Key": "吉林", "Value": 945 }, { "Key": "黑龙江", "Value": 1430 }, { "Key": "内蒙古", "Value": 1173 }, { "Key": "江苏", "Value": 2133 }, { "Key": "山东", "Value": 2517 }, { "Key": "安徽", "Value": 1823 }, { "Key": "浙江", "Value": 1557 }, { "Key": "福建", "Value": 1080 }, { "Key": "湖北", "Value": 1454 }, { "Key": "湖南", "Value": 1942 }, { "Key": "广东", "Value": 2930 }, { "Key": "广西", "Value": 1491 }, { "Key": "江西", "Value": 1580 }, { "Key": "四川", "Value": 2807 }, { "Key": "海南", "Value": 366 }, { "Key": "贵州", "Value": 1081 }, { "Key": "云南", "Value": 1521 }, { "Key": "西藏", "Value": 987 }, { "Key": "陕西", "Value": 1064 }, { "Key": "甘肃", "Value": 1009 }, { "Key": "青海", "Value": 160 }, { "Key": "宁夏", "Value": 197 }, { "Key": "新疆", "Value": 960}];
					cn_stetelist_english = [{ "Key": "Beijing", "Value": 321 }, { "Key": "Shanghai", "Value": 276 }, { "Key": "Tianjin", "Value": 300 }, { "Key": "Chongqing", "Value": 905 }, { "Key": "Hebei Province", "Value": 1296 }, { "Key": "Shanxi Province", "Value": 972 }, { "Key": "Henan Province", "Value": 2176 }, { "Key": "Liaoning Province", "Value": 1563 }, { "Key": "Jilin Province", "Value": 945 }, { "Key": "Heilongjiang Province", "Value": 1430 }, { "Key": "The Inner Mongolia Autonomous Region", "Value": 1173 }, { "Key": "Jiangsu Province", "Value": 2133 }, { "Key": "Shandong Province", "Value": 2517 }, { "Key": "Anhui Province", "Value": 1823 }, { "Key": "Zhejiang Province", "Value": 1557 }, { "Key": "Fujian Province", "Value": 1080 }, { "Key": "Hubei Province", "Value": 1454 }, { "Key": "Hunan Province", "Value": 1942 }, { "Key": "Guangdong Province", "Value": 2930 }, { "Key": "The Guangxi Zhuang Autonomous Region", "Value": 1491 }, { "Key": "Jiangxi Province", "Value": 1580 }, { "Key": "Sichuan Province", "Value": 2807 }, { "Key": "Hainan Province", "Value": 366 }, { "Key": "Guizhou Province", "Value": 1081 }, { "Key": "Yunnan Province", "Value": 1521 }, { "Key": "The Tibet Autonomous Region", "Value": 987 }, { "Key": "Shaanxi Province", "Value": 1064 }, { "Key": "Gansu Province", "Value": 1009 }, { "Key": "Qinghai Province", "Value": 160 }, { "Key": "The Ningxia Hui Autonomous Region", "Value": 197 }, { "Key": "The Xinjiang Uygur Autonomous Region", "Value": 960}];
					result.DumyData[0].ResultList = cn_statelist_chiness;
					result.DumyData[1].ResultList = cn_stetelist_english;
				}

				if (nation_cd == "JP") {
					jp_statelist_japaness = [{ "Key": "北海道", "Value": 8242 }, { "Key": "青森県", "Value": 2510 }, { "Key": "岩手県", "Value": 1937 }, { "Key": "宮城県", "Value": 3329 }, { "Key": "秋田県", "Value": 2156 }, { "Key": "山形県", "Value": 1946 }, { "Key": "福島県", "Value": 3925 }, { "Key": "茨城県", "Value": 2856 }, { "Key": "栃木県", "Value": 1830 }, { "Key": "群馬県", "Value": 1499 }, { "Key": "埼玉県", "Value": 2929 }, { "Key": "千葉県", "Value": 3581 }, { "Key": "東京都", "Value": 3734 }, { "Key": "神奈川県", "Value": 2281 }, { "Key": "新潟県", "Value": 5396 }, { "Key": "富山県", "Value": 3250 }, { "Key": "石川県", "Value": 2539 }, { "Key": "福井県", "Value": 2257 }, { "Key": "山梨県", "Value": 943 }, { "Key": "長野県", "Value": 1685 }, { "Key": "岐阜県", "Value": 3359 }, { "Key": "静岡県", "Value": 2932 }, { "Key": "愛知県", "Value": 7518 }, { "Key": "三重県", "Value": 2473 }, { "Key": "滋賀県", "Value": 1843 }, { "Key": "京都府", "Value": 6662 }, { "Key": "大阪府", "Value": 3783 }, { "Key": "兵庫県", "Value": 5215 }, { "Key": "奈良県", "Value": 1932 }, { "Key": "和歌山県", "Value": 1598 }, { "Key": "鳥取県", "Value": 1396 }, { "Key": "島根県", "Value": 1180 }, { "Key": "岡山県", "Value": 2188 }, { "Key": "広島県", "Value": 2152 }, { "Key": "山口県", "Value": 1800 }, { "Key": "徳島県", "Value": 1426 }, { "Key": "香川県", "Value": 710 }, { "Key": "愛媛県", "Value": 1738 }, { "Key": "高知県", "Value": 1693 }, { "Key": "福岡県", "Value": 3280 }, { "Key": "佐賀県", "Value": 871 }, { "Key": "長崎県", "Value": 1892 }, { "Key": "熊本県", "Value": 1893 }, { "Key": "大分県", "Value": 1840 }, { "Key": "宮崎県", "Value": 874 }, { "Key": "鹿児島県", "Value": 1457 }, { "Key": "沖縄県", "Value": 794}];
					jp_stetelist_english = [{ "Key": "HOKKAIDO", "Value": 8242 }, { "Key": "AOMORI", "Value": 2510 }, { "Key": "IWATE", "Value": 1937 }, { "Key": "MIYAGI", "Value": 3329 }, { "Key": "AKITA", "Value": 2156 }, { "Key": "YAMAGATA", "Value": 1946 }, { "Key": "FUKUSHIMA", "Value": 3925 }, { "Key": "IBARAKI", "Value": 2856 }, { "Key": "TOCHIGI", "Value": 1830 }, { "Key": "GUMMA", "Value": 1499 }, { "Key": "SAITAMA", "Value": 2929 }, { "Key": "CHIBA", "Value": 3581 }, { "Key": "TOKYO", "Value": 3734 }, { "Key": "KANAGAWA", "Value": 2281 }, { "Key": "NIIGATA", "Value": 5396 }, { "Key": "TOYAMA", "Value": 3250 }, { "Key": "ISHIKAWA", "Value": 2539 }, { "Key": "FUKUI", "Value": 2257 }, { "Key": "YAMANASHI", "Value": 943 }, { "Key": "NAGANO", "Value": 1685 }, { "Key": "GIFU", "Value": 3359 }, { "Key": "SHIZUOKA", "Value": 2932 }, { "Key": "AICHI", "Value": 7518 }, { "Key": "MIE", "Value": 2473 }, { "Key": "SHIGA", "Value": 1843 }, { "Key": "KYOTO", "Value": 6662 }, { "Key": "OSAKA", "Value": 3783 }, { "Key": "HYOGO", "Value": 5215 }, { "Key": "NARA", "Value": 1932 }, { "Key": "WAKAYAMA", "Value": 1598 }, { "Key": "TOTTORI", "Value": 1396 }, { "Key": "SHIMANE", "Value": 1180 }, { "Key": "OKAYAMA", "Value": 2188 }, { "Key": "HIROSHIMA", "Value": 2152 }, { "Key": "YAMAGUCHI", "Value": 1800 }, { "Key": "TOKUSHIMA", "Value": 1426 }, { "Key": "KAGAWA", "Value": 710 }, { "Key": "EHIME", "Value": 1738 }, { "Key": "KOCHI", "Value": 1693 }, { "Key": "FUKUOKA", "Value": 3280 }, { "Key": "SAGA", "Value": 871 }, { "Key": "NAGASAKI", "Value": 1892 }, { "Key": "KUMAMOTO", "Value": 1893 }, { "Key": "OITA", "Value": 1840 }, { "Key": "MIYAZAKI", "Value": 874 }, { "Key": "KAGOSHIMA", "Value": 1457 }, { "Key": "OKINAWA", "Value": 794}];
					result.DumyData[0].ResultList = jp_statelist_japaness;
					result.DumyData[1].ResultList = jp_stetelist_english;
				}

				if (nation_cd == "ID" && result.ResultList.length > 0 && (result.ResultList[0].CITY === undefined && result.ResultList[0].STREET === undefined)) {
					id_statelist_indonessian = [{ "Key": "Jakarta", "Value": 490 }, { "Key": "Jawa Barat", "Value": 1520 }, { "Key": "Jawa Timur", "Value": 1782 }, { "Key": "Jawa Tengah", "Value": 1831 }, { "Key": "Sumatera Utara", "Value": 977 }, { "Key": "Sumatera Barat", "Value": 874 }, { "Key": "Sumatera Selatan", "Value": 442 }, { "Key": "Yogyakarta", "Value": 227 }, { "Key": "Sulawesi Selatan", "Value": 743 }, { "Key": "Sulawesi Utara", "Value": 420 }, { "Key": "Sulawesi Tenggara", "Value": 265 }, { "Key": "Sulawesi Tengah", "Value": 218 }, { "Key": "Sulawesi Barat", "Value": 57 }, { "Key": "Riau", "Value": 277 }, { "Key": "Bali", "Value": 218 }, { "Key": "Kalimantan Selatan", "Value": 393 }, { "Key": "Kalimantan Barat", "Value": 317 }, { "Key": "Kalimantan Timur", "Value": 369 }, { "Key": "Kalimantan Tengah", "Value": 271 }, { "Key": "Nusa Tenggara Barat", "Value": 215 }, { "Key": "Nusa Tenggara Timur", "Value": 456 }, { "Key": "Aceh", "Value": 392 }, { "Key": "Bangka-Belitung", "Value": 183 }, { "Key": "Banten", "Value": 442 }, { "Key": "Bengkulu", "Value": 260 }, { "Key": "Gorontalo", "Value": 131 }, { "Key": "Jambi", "Value": 197 }, { "Key": "Kepulauan Riau", "Value": 130 }, { "Key": "Lampung", "Value": 338 }, { "Key": "Maluku", "Value": 142 }, { "Key": "Maluku Utara", "Value": 75 }, { "Key": "Papua", "Value": 274 }, { "Key": "Papua Barat", "Value": 100}];
					id_stetelist_english = [{ "Key": "Jakarta", "Value": 490 }, { "Key": "West Java", "Value": 1520 }, { "Key": "East Java", "Value": 1782 }, { "Key": "Central Java", "Value": 1831 }, { "Key": "North Sumatra", "Value": 977 }, { "Key": "West Sumatra", "Value": 874 }, { "Key": "South Sumatra", "Value": 442 }, { "Key": "Yogyakarta", "Value": 227 }, { "Key": "South Sulawesi", "Value": 743 }, { "Key": "North Sulawesi", "Value": 420 }, { "Key": "South East Sulawesi", "Value": 265 }, { "Key": "Central Sulawesi", "Value": 218 }, { "Key": "West Sulawesi", "Value": 57 }, { "Key": "Riau", "Value": 277 }, { "Key": "Bali", "Value": 218 }, { "Key": "South Kalimantan", "Value": 393 }, { "Key": "West Kalimantan", "Value": 317 }, { "Key": "East Kalimantan", "Value": 369 }, { "Key": "Central Kalimantan", "Value": 271 }, { "Key": "West Nusa Tenggara", "Value": 215 }, { "Key": "East Nusa Tenggara", "Value": 456 }, { "Key": "Aceh", "Value": 392 }, { "Key": "Bangka-Belitung", "Value": 183 }, { "Key": "Banten", "Value": 442 }, { "Key": "Bengkulu", "Value": 260 }, { "Key": "Gorontalo", "Value": 131 }, { "Key": "Jambi", "Value": 197 }, { "Key": "Riau Islands", "Value": 130 }, { "Key": "Lampung", "Value": 338 }, { "Key": "Maluku", "Value": 142 }, { "Key": "North Maluku", "Value": 75 }, { "Key": "Papua", "Value": 274 }, { "Key": "West Papua", "Value": 100}];
					result.DumyData[0].ResultList = id_statelist_indonessian;
					result.DumyData[1].ResultList = id_stetelist_english;
				}

			}

			try { ADDRLen = result.DumyData[0].ResultList.length; } catch (ex) { }
			try {
				rst_LC = "";
				rst_EN = "";

				cur_group = "";

				for (i = 0; i < ADDRLen; ++i) {
					rst_LC = result.DumyData[0].ResultList[i].Key;
					try { rst_EN = result.DumyData[1].ResultList[i].Key; } catch (ex) { }

					rst = rst_LC;

					if (rst !== "") {
						rst_title = "";
						rst_short = "";

						if (in_eng) {
							rst_title = rst_EN;
						} else {
							rst_title = rst_LC;
						}

						if (result.ResultList[0].STREET === undefined) {
							if (rst_title.length > 19) {
								rst_short = rst_title.substring(0, 18) + "..";
							} else {
								rst_short = rst_title;
							}
						} else {
							if (rst_title.length > 21) {
								rst_short = rst_title.substring(0, 20) + "..";
							} else {
								rst_short = rst_title;
							}
						}

						// select box에 요소 추가
						if (result.ResultList[0].CITY === null && result.ResultList[0].STREET === null) {
							cur_group = "state";

							if (i === 0) {
								$sel_state.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
								$sel_city.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
								$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
							}

							$sel_state.append(String.format("<option value='{0}' title='{1}' {3}>{2}</option>", rst, rst_title, rst_short, (state == rst) ? "selected" : ""));
						}
						else if (result.ResultList[0].CITY !== null) {
							cur_group = "city";

							$sel_city.append(String.format("<option value='{0}' title='{1}' {3}>{2}</option>", rst, rst_title, rst_short, (city == rst) ? "selected" : ""));
							$sel_street.html(String.format("<option value=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "Select")));
						}
						else if (result.ResultList[0].STREET !== null) {
							cur_group = "street";

							$sel_street.append(String.format("<option value='{0}' title='{1}' {3}>{2}</option>", rst, rst_title, rst_short, (street == rst) ? "selected" : ""));

							if (i == ADDRLen - 1 && $.inArray(nation_cd, ctrShippingAddress.config.nation_cd.no_zipcode) == -1) {	// Street 맨뒤에 선택안함 추가
								$sel_street.append(String.format("<option value='' title=''>{0}</option>", MultiLang.findCommonResource("Control/Order/ShippingAddress.ascx", "NotSelect")));
							}
						}
					}

					if (result.ResultList[0].CITY !== null && ADDRLen == 1 && city === "") {
						$sel_city.val(rst);
						$city.val(rst);

						if (typeof (AddressSearcher) != "undefined") {
							AddressSearcher.get_address_list(nation_cd, rst, "street", ctrShippingAddress.callback.address_list);
						}
					}
				} // end of for

				// select box와 address front 가 같이 보이는 경우 address front 업데이트
				if ($.inArray(nation_cd, ctrShippingAddress.config.nation_cd.address_front_with_selectbox) !== -1) {
					if ($state.val() !== "") {
						$address_front.val($.trim($state.val() + " " + $city.val() + " " + $street.val())).trigger("change");
					}
				}

				if (svc.indexOf("?nosub") == -1) {
					// 하위 레벨의 값이 있다면 자동완성을 위해 호출
					if (state !== "" && cur_group == "state") {
						AddressSearcher.get_address_list(nation_cd, state, "city", ctrShippingAddress.callback.address_list);
					} else if (city !== "" && cur_group == "city") {
						AddressSearcher.get_address_list(nation_cd, city, "street", ctrShippingAddress.callback.address_list);
					}
				}
			} catch (ex) { }
		}
	},

	config: {
		zipcode_delay: 550,
		show_address_guide: false,
		nation_cd: {	// CN, HK, ID, JP, KR, MO, MY, PH, SG, TW, US + 추가됨(AU, CA, VN, TH, GB)	<- 가능하면 이 순서 맞춰야 아래에서 빠진 국가 찾기 쉬울 듯
			// 주소와 selectbox가 같이 보이는 국가
			address_front_with_selectbox: ["CN"],

			// 우편번호 숨김처리할 국가
			no_zipcode: ["CN", "HK"],

			// 우편번호 직접 입력 가능한 국가
			zipcode_editable: ["CN", "AU", "CA", "VN", "TH", "GB", "US", "RU"],

			// 우편번호가 셀렉트 박스인 국가
			zipcode_selectable: [],

			// street 입력 가능 국가
			street_enable: ["CN", "HK", "ID", "JP", "KR", "MO", "MY", "PH"],

			// 주소 selectbox 영문 제공 국가
			address_select_en: ["HK", "JP", "KR", "MO", "TW", "PH"],

			// 검색 버튼 비노출 
			no_search_btn: ["AU", "CA", "VN", "TH", "GB", "US", "RU"]
		},
		element_map: {
			wrapper: "#shipping_address",

			address_selectbox_area: "#address_selectbox_area",
			address_front: "#address_front",
			address_last: "#address_last",
			sel_state: "#sel_state_detail",
			sel_city: "#sel_city_detail",
			sel_street: "#sel_street_detail",
			state: "#state",
			city: "#city",
			street: "#street",
			in_eng: "#in_eng",
			in_eng_label: "#in_eng_label",
			address_guide: ".address_guide",
			address_guide_zipcode: "#address_guide_zipcode",
			address_guide_selectbox: "#address_guide_selectbox",

			zipcode: "#zipcode",
			hidden_zipcode: "#hidden_zipcode",
			is_zipcode_valid_yn: "#is_zipcode_valid_yn",
			zipcode_search_btn: "#zipcode_search_btn",

			out_of_wrapper: {
				// 국가 변경시 nation_cd 저장하는 hidden input
				nation_cd: "#nation_cd"
			}
		}
	},
	global_var: {
		last_zipcode: "",
		timeout: false
	}
};

var AddressSearcher = {
	get_address_detail: function (nation, zipcode, state, city, street, callbackFunc, extraParam) {
		if (zipcode !== undefined && zipcode !== "") {
			zipcode = zipcode.replace(/-|\s/, "");
		}

		if (nation === "" && zipcode === "" && state === "" && city === "" && street === "") {
			return;
		}

		if (extraParam === undefined) {
			extraParam = "";
		}

		var param = new RMSParam();
		param.add("zipcode", zipcode);
		param.add("keyword", "");
		param.add("nation", nation);
		param.add("state", state);
		param.add("city", city);
		param.add("street", street);
		RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_SearchAjaxService.asmx"), "AddressSearch?" + extraParam, param.toJson(), callbackFunc);
	},
	get_address_list: function (nation_cd, keyword, group_by, callbackFunc, extraParam) {
		var ret, param = new RMSParam();

		if (group_by === "") {
			group_by = "state";
		}

		if (extraParam === undefined) {
			extraParam = "";
		}

		param.add("keyword", keyword);
		param.add("group_by", group_by);
		param.add("nation", nation_cd);

		RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_SearchAjaxService.asmx"), "AddressSearchByGrouping?" + extraParam, param.toJson(), callbackFunc);
	}
};

var ShippingFeeCalculator = {
	in_calculate: false,
	last_zipcode: "",
	calculate: function (zipcode) {
		var in_calculate = ShippingFeeCalculator.in_calculate,
			last_zipcode = ShippingFeeCalculator.last_zipcode;

		if (zipcode === undefined) {
			return;
		}

		if (true === in_calculate && zipcode === last_zipcode) {
			return;
		}

		if (window.calcSzDeliveryFee !== undefined) {
			in_calculate = true;
			last_zipcode = zipcode;

			calcSzDeliveryFee(zipcode);

			setTimeout(function () {
				ShippingFeeCalculator.in_calculate = false;
			}, 2000);
		}
	}
};
