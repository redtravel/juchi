/**************************************************************************************************
* File Name		: Util.js
* Discription	: 공용 유틸 메소드 파일
* Write	Date	: 2010-01-25
* Modify Date	: 2011-04-12
2011-08-25 jquery version up

* Writer		: ykums
* jquery-1.6.2.min.js/jquery-ui-1.8.16.custom.min.js/binder.js/handler.js/multilang.js/public.js/RMSHelper.js 통합
* 수정할 때 mobile_util.js도 체크해야 합니다.
**************************************************************************************************/
/*jQuery JavaScript Library v1.6.2*/

// 클라이언트 객체를 ID로 조회 하여 리턴한다.
function $get(element) {
	return document.getElementById(element);
}

// 서버 객체를 ID로 조회 하여 리턴한다.
function $sget(element) {
	return document.getElementById(__ServerControl[element]);
}
 
// 클라이언트 객체를 이름으로 조회 하여 리턴한다.
function $nget(element) {
	return document.getElementsByName(element);
}

// String 객체에 trim 메소드를 추가 합니다.
String.prototype.trim = function () {
	return this.replace(/(^\s*)|(\s*$)|($\s*)/g, "");

}
String.prototype.ltrim = function () {
	return this.replace(/^\s+/, "");
}
String.prototype.rtrim = function () {
	return this.replace(/\s+$/, "");
}
String.format = function (text) {
	//check if there are two arguments in the arguments list
	if (arguments.length <= 1) {
		//if there are not 2 or more arguments there's nothing to replace
		//just return the original text
		return text;
	}
	//decrement to move to the second argument in the array
	var tokenCount = arguments.length - 2;
	for (var token = 0; token <= tokenCount; token++) {
		//iterate through the tokens and replace their placeholders from the original text in order
		text = text.replace(new RegExp("\\{" + token + "\\}", "gi"), arguments[token + 1].toString().replace(/\$0/, "$$$$0"));
	}
	return text;
};


/* Public.js */
var isCurrent = true;
var Public = function () { };
Public.ErrorHandler = function (ex, url, line) {
	var funcname = "";
	if (typeof (ex) != "string") {
		if (ex.message != undefined)
			ex = ex.message;
		else if (ex.ExceptionType != undefined)
			ex = ex.Message;
		else
			ex = "unknown script error";
	}
	if (Public.ErrorHandler.caller != null) {
		try {
			funcname = Public.ErrorHandler.caller.toString();
			funcname = funcname.replace("function", "");
			funcname = funcname.replace(/(^\s*)|(\s*$)|($\s*)/g, "");
			var idx = funcname.indexOf(")");

			if (idx >= 0)
				funcname = funcname.substring(0, idx + 1);
			else
				funcname = funcname.substring(0, 25);

		} catch (e) { }
	}
	Public.WriteScriptError(ex, url, funcname, line);
	return false;
};

Public.__errorCount = 0;

Public.WriteScriptError = function (ex, url, funcname, line, logType, body) {
	try {
		var serverName = "";
		var serverIP = "";
		var clientIP = "";
		var login_id = "";
		var lang = "";
		var app_no = 1; //unknown

		// 2개이상의 오류는 기록 하지 않는다.
		if (++Public.__errorCount > 2)
			return;

		try {
			if (line == null || line == "" || parseInt(line) <= 1)
				return;
			// 우리 사이트가 아닌 스크립트는 로깅 하지 않음
			if (url != "" && !(url.indexOf("http") == 0 && (url.indexOf("qoo10.") > -1 || url.indexOf("m18.") > -1)))
				return;

			// 크롬에 Timer관련 메소드 이면 로깅 하지 않음
			if (funcname && (window.navigator.userAgent.indexOf("Chrome") >= 0 && funcname.indexOf("Timer") >= 0))
				return;

			if (ex.indexOf("Unable to get property 'jQuery") >= 0 && ex.indexOf("Uncaught TypeError: Cannot read property 'jQuery") >= 0 || ex.indexOf("Uncaught Error: SYNTAX_ERR: DOM Exception 12") >= 0 || ex.indexOf("NETWORK_ERR: XMLHttpRequest Exception 101") >= 0)
				return;

			// iphone 버그 제거
			if ((window.navigator.userAgent.indexOf("iPhone OS") >= 0 || window.navigator.userAgent.indexOf("iPad; CPU OS") >= 0) && (ex == "TypeError: 'undefined' is not a function" || ex == "TypeError: 'null' is not an object"))
				return;

			// firefox 버그
			if (window.navigator.userAgent.indexOf("Firefox") >= 0 && ex != null && (ex.indexOf("Permission denied to access property") >= 0 || ex == "ReferenceError: Components is not defined"))
				return;

			// Google bot network 제거
			if (window.navigator.userAgent.indexOf("Googlebot") >= 0)
				return;

			// iPhone App 관련 제거
			if (ex.indexOf("window.giosis.iphone") >= 0)
				return;

			if (window.navigator.userAgent.indexOf("Mac OS X") >= 0 && ex.indexOf("ReferenceError: Can't find variable") >= 0)
				return;

			if (ex == "Script error.")
				return;

			serverName = __PAGE_VALUE.SERVER_NAME;
			serverIP = __PAGE_VALUE.SERVER_IP;
			clientIP = __PAGE_VALUE.CLIENT_IP;
			login_id = __PAGE_VALUE.IS_LOGIN ? __PAGE_VALUE.LOGIN_ID : "";
			lang = GMKT.ServiceInfo.ClientLang;
			app_no = __PAGE_VALUE.APP_NO;

			if (!Util.isNumber(app_no))
				app_no = 1;
		} catch (ex1) { }

		var detailmessage = "Referer : " + document.referrer + "\r\n";
		detailmessage += "User Language : " + lang + "\r\n";
		detailmessage += "Client IP : " + clientIP + "\r\n";
		detailmessage += "User Agent : " + window.navigator.userAgent + "\r\n";
		detailmessage += "Login ID : " + login_id + "\r\n";
		detailmessage += "Domain : " + document.domain + "\r\n";
		detailmessage += "Server Name : " + serverName + "\r\n";
		detailmessage += "Server IP : " + serverIP + "\r\n";
		detailmessage += "Error File : " + url + "\r\n";
		detailmessage += "Error Function : " + funcname + "\r\n";
		detailmessage += "Error Line : " + line + "\r\n";

		try {
			var condt = new Date(GMKT.ServiceInfo.ServerTime);
			var nowdt = new Date();

			condt.setSeconds(condt.getSeconds() + 5);

			//if (condt < nowdt)
			//	return;

			detailmessage += "Connect Time : " + GMKT.ServiceInfo.ServerTime + "\r\n";
		}
		catch (ex3) {
			detailmessage += "Connect Time : ";
		}

		detailmessage += "Error Message : " + ex + "\r\n";
		if (body) detailmessage += body;

		try {
			if (window.console && window.console.log)
				window.console.log(detailmessage);
		}
		catch (ex2) { }

		var param = new RMSParam();
		param.add("logType", !logType ? "Error" : logType);
		param.add("contexturl", window.location.href);
		param.add("useragent", window.navigator.userAgent);
		param.add("server", serverName);
		param.add("appNo", app_no);
		param.add("location", funcname);
		param.add("message", ex);
		param.add("detailmessage", detailmessage);

		var callback = function (result, svc, methodName, xmlHttpasync) { }

		RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_AjaxLoggingService.asmx"), "WriteLog", param.toJson(), callback);
	} catch (ex) { }
};

window.onerror = Public.ErrorHandler;

Public.isLocalServer = function () {
	if (__PAGE_VALUE.IS_LOCAL_SERVER || window.location.href.indexOf("localhost") >= 0)
		return true;
	return false;
};

Public.isLocalServerOrg = function () {
	if (__PAGE_VALUE.IS_LOCAL_SERVER_ORG || window.location.href.indexOf("localhost") >= 0)
		return true;
	return false;
};
Public.isDev = function () {
	return Public.isLocalServer();
};
Public.isReal = function () {
	return !Public.isDev();
};
Public.getAppPath = function () {
	return __PAGE_VALUE.ROOT_PATH;
};
Public.getEventAppPath = function () {
	return __PAGE_VALUE.EVENT_ROOT_PATH;
};
Public.getOpenApiAppPath = function () {
	return __PAGE_VALUE.OPENAPI_PATH;
};
Public.getServicePath = function () {
	return __PAGE_VALUE.SERVICE_PATH;
};
Public.getCookieDomain = function () {
	return __PAGE_VALUE.COOKE_DOMAIN;
};
Public.isLogin = function () {
	
	return false;
};
Public.getCustNo = function () {
	return __PAGE_VALUE.CUST_NO;
};
Public.getLoginId = function () {
	return __PAGE_VALUE.LOGIN_ID;
};
Public.getMemberKind = function () {
	return __PAGE_VALUE.MEMBER_KIND;
};

Public.getServiceUrl = function (serviceName) {
	return Public.getServicePath() + serviceName;
};
Public.convertNormalUrl = function (path) {
	if (path.substr(0, 1) == "~")
		return Public.getAppPath() + path.substr(1, path.length - 1)
	return Public.getAppPath() + path;
};
Public.getWWWServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.WWW_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getMemberServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.MEMBER_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getLoginServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.LOGIN_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getCategoryServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.CATEGORY_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getSearchServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.SEARCH_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getMyServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.MY_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getOrderServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.ORDER_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getGoodsServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.GOODS_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getCouponServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.COUPON_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getEventServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.EVENT_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getEventContentServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.EVENT_CONTENT_SERVER, ssl) + Public.getEventAppPath() + path);
};
Public.getEventContentAppServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.WWW_SERVER, ssl) + Public.getEventAppPath() + path);
};
Public.getOpenAPIServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.OPENAPI_SERVER, ssl) + Public.getOpenApiAppPath() + path);
};
Public.getPGServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.PG_SERVER, ssl) + Public.getOpenApiAppPath() + path);
};
Public.getGlobalWWWServerUrl = function (path, ssl) {
	return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.QOO10_SERVER, ssl) + Public.getAppPath() + path);
};
Public.getMobileServerUrl = function (path, ssl) {
	if (path.indexOf("/") == 0 && path.toLowerCase().indexOf("/mobile/") != 0)
		path = UriUtil.safeJobinPath("/Mobile", path);

	if (!ssl)
		return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.WWW_SERVER, ssl) + Public.getAppPath() + path);
	else
		return Public.convertAutoSessionShareUrl(Public._getSafeUrl(__PAGE_VALUE.MY_SERVER, ssl) + Public.getAppPath() + path);
};
Public._getSafeUrl = function (url, ssl) {
	if (Public.isMultiSite() && Public.useCommonSSL()) {
		url = UriUtil.getPathQuery(url);
		url = Public._getCurrentDomain(ssl) + url;
	} else if (!Public.isLocalServerOrg()) {
		if (ssl) {
			url = url.replace("http://", "https://");
		}
	}

	return url;
};
Public._getCurrentDomain = function (ssl) {
	if (ssl) {
		if (Public.isLocalServerOrg()) {
			if (Public.isMultiSite() && Public.useCommonSSL())
				return "http://" + Public.getCommonSSLDomain();
			else
				return "http://" + Public.getCurrentHost();
		}
		else {
			if (Public.isMultiSite() && Public.useCommonSSL())
				return "https://" + Public.getCommonSSLDomain();
			else
				return "https://" + Public.getCurrentHost();
		}
	}
	else if (Public.isMultiSite() && Public.getShopDomain() != "") {
		return "http://" + Public.getShopDomain();
	}
	else if (window.location.port != 80 && window.location.port != 443)
		return "http://" + window.location.host + ":" + window.location.port;

	return "http://" + Public.getCurrentHost();
};
Public.goLoginPage = function () {
	var nextUrl = window.location.href;
	document.location.href = Public.getLoginServerUrl("/Login/Login.aspx?nextUrl=" + escape(nextUrl), true);
};
Public.goPopupLoginPage = function () {
	var nextUrl = window.location.href;
	document.location.href = Public.getLoginServerUrl("/Login/PopupLogin.aspx?nextUrl=" + escape(nextUrl));
};
Public.getImgPath = function () {
	if (window.location.protocol == "https:") {
		return __PAGE_VALUE.DP_SSL_IMAGE_PATH;
	}
	else {
		return __PAGE_VALUE.DP_IMAGE_PATH;
	}
};
Public.getStaticImgPath = function () {
	if (window.location.protocol == "https:") {
		return __PAGE_VALUE.STATIC_SSL_IMAGE_PATH;
	}
	else {
		return __PAGE_VALUE.STATIC_IMAGE_PATH;
	}
};
Public.getCurrentHost = function () {
	return window.location.host;
};
Public.getCurrentHostUrl = function () {
	return window.location.protocol + "//" + Public.getCurrentHost();
};
Public.isMultiSite = function () {
	return __PAGE_VALUE.IS_MULTISITE;
}
Public.getSiteId = function () {
	return __PAGE_VALUE.SITEID;
}
Public.getViewSiteId = function () {
	return __PAGE_VALUE.VIEW_SITEID;
}
Public.useCommonSSL = function () {
	if (__PAGE_VALUE.USE_COMMONSSL)
		return __PAGE_VALUE.USE_COMMONSSL;
	return false;
};
Public.getCommonSSLDomain = function () {
	if (__PAGE_VALUE.COMMON_SSL_DOMAIN)
		return __PAGE_VALUE.COMMON_SSL_DOMAIN;
	return "";
};
Public.getShopDomain = function () {
	if (__PAGE_VALUE.SHOP_DOMAIN)
		return __PAGE_VALUE.SHOP_DOMAIN;
	return "";
};
Public.convertAutoSessionShareUrl = function (nextUrl) {
	var currHost = Public.getCurrentHost();

	if (Public.isMultiSite() && Public.useCommonSSL() && currHost != Public.getCommonSSLDomain()) {
		var uriHost = UriUtil.getHost(nextUrl);

		if (currHost == uriHost || nextUrl.toLowerCase().indexOf("sessionshare.aspx") >= 0) {
			return nextUrl;
		}

		return Public.getSessionShareUrl(nextUrl);
	}

	return nextUrl;
};
Public.getSessionShareUrl = function (nextUrl) {
	if (Public.useCommonSSL())
		return Public.convertNormalUrl("~/Common/SessionShare.aspx?nextUrl=" + escape(nextUrl));
	else
		return nextUrl;
};
Public.getGoodsImagePath = function (img_contents_no, imgType, stillYN) {
	if (parseInt(img_contents_no) < 1) return Public.getAppPath() + "/Img/no_image.gif";

	var goodsImagePath1 = "";
	var goodsImagePath2 = "";
	var goodsImagePath3 = "";

	goodsImagePath1 = location.protocol == "https:" ? __PAGE_VALUE.GOODS_SSL_IMAGE_PATH : goodsImagePath1 = __PAGE_VALUE.GOODS_IMAGE_PATH;

	// 2013-11-06 khchoi : Device kind가 mobile /tablet 인 경우 모바일용 이미지로 처리
	if (GMKT.DeviceInfo.Kind == "Mobile") {
		if (imgType == "L") goodsImagePath2 = "/m_li/";
		else if (imgType == "M") goodsImagePath2 = "/m_mi/";
		else if (imgType == "M2") goodsImagePath2 = "/m_mi2/";
		else if (imgType == "M3") goodsImagePath2 = "/m_mi3/";
		else if (imgType == "M4") goodsImagePath2 = "/m_mi4/";
		else if (imgType == "S") goodsImagePath2 = "/m_si/";
		else if (imgType == "LS") goodsImagePath2 = "/m_lis/";
		else if (imgType == "A") goodsImagePath2 = "/m_ai/";
		else if (imgType == "AS") goodsImagePath2 = "/m_ais/";
		else if (imgType == "L_S") goodsImagePath2 = "/m_li_s/";
		else if (imgType == "M_S") goodsImagePath2 = "/m_mi_s/";
		else if (imgType == "M2_S") goodsImagePath2 = "/m_mi2_s/";
		else if (imgType == "M3_S") goodsImagePath2 = "/m_mi3_s/";
		else if (imgType == "M4_S") goodsImagePath2 = "/m_mi4_s/";
		else if (imgType == "S_S") goodsImagePath2 = "/m_si_s/";
		else if (imgType == "LS_S") goodsImagePath2 = "/m_lis_s/";
		else if (imgType == "A_S") goodsImagePath2 = "/m_ai_s/";
		else if (imgType == "AS_S") goodsImagePath2 = "/m_ais_s/";
	} else {
		if (imgType == "L") goodsImagePath2 = "/li/";
		else if (imgType == "M") goodsImagePath2 = "/mi/";
		else if (imgType == "M2") goodsImagePath2 = "/mi2/";
		else if (imgType == "M3") goodsImagePath2 = "/mi3/";
		else if (imgType == "M4") goodsImagePath2 = "/mi4/";
		else if (imgType == "S") goodsImagePath2 = "/si/";
		else if (imgType == "LS") goodsImagePath2 = "/lis/";
		else if (imgType == "A") goodsImagePath2 = "/ai/";
		else if (imgType == "AS") goodsImagePath2 = "/ais/";
		else if (imgType == "L_S") goodsImagePath2 = "/li_s/";
		else if (imgType == "M_S") goodsImagePath2 = "/mi_s/";
		else if (imgType == "M2_S") goodsImagePath2 = "/mi2_s/";
		else if (imgType == "M3_S") goodsImagePath2 = "/mi3_s/";
		else if (imgType == "M4_S") goodsImagePath2 = "/mi4_s/";
		else if (imgType == "S_S") goodsImagePath2 = "/si_s/";
		else if (imgType == "LS_S") goodsImagePath2 = "/lis_s/";
		else if (imgType == "A_S") goodsImagePath2 = "/ai_s/";
		else if (imgType == "AS_S") goodsImagePath2 = "/ais_s/";
	}


	if (stillYN != "N") {
		if (__PAGE_VALUE.FRONT_STILL_IMAGE && (imgType == "M" || imgType == "M2" || imgType == "M3" || imgType == "M4" || imgType == "S" || imgType == "A" || imgType == "AS") && imgType.indexOf("_S") < 0) {
			goodsImagePath2 = goodsImagePath2.substring(0, goodsImagePath2.length - 1) + "_s/";
		}
	}

	var gd_no = "" + img_contents_no;
	goodsImagePath3 = gd_no.substr(6, 3) + "/" + gd_no.substr(3, 3) + "/" + gd_no + ".jpg";

	return goodsImagePath1 + goodsImagePath2 + goodsImagePath3;
};

// DP 썸네일 이미지URL 리턴
Public.getDPThumbnailImageUrl = function (imgUrl, size) {
	if (!(imgUrl.indexOf("http://") == 0 || imgUrl.indexOf("https://") == 0)) {
		imgUrl = Public.getImgPath(imgUrl, imgUrl.indexOf("https://") == 0);
	}

	return imgUrl.replace("/GMKT.IMG/", "/GMKT.IMG/thumbnail/" + size + "/");
}


//SEO 상품 URL
//이건 상품번호까지 return하므로 param은 붙여줘야 함
Public.getGoodsVUrl = function (gd_no, gd_nm, brand_nm, add_info, param, new_link_yn, site_domain) {
    var path = "";
    var vpath = "";
    
    if (Util.safeString(new_link_yn) == "") new_link_yn = "N";

    if (new_link_yn == "Y" && Util.safeString(site_domain) != "") {
        path = site_domain + "/item/";
    }
    else {
        path = Public._getSafeUrl(__PAGE_VALUE.GOODS_SERVER, false) + "/item/";
    }

    vpath = (Util.safeString(brand_nm) == "" ? "" : (Public.getSEOVirtualPath(brand_nm, 0) + "-")) + Public.getSEOVirtualPath(gd_nm, 30) + (Util.safeString(add_info) == "" ? "" : ("-" + Public.getSEOVirtualPath(add_info, 0)));
    path = path + encodeURI(vpath) + "/" + gd_no + (Util.safeString(param) == "" ? "" : "?" + Util.safeString(param));

    return path;
}


//SEO 상품 Global Auction item URL (qoo10)
Public.getGlobalGoodsVUrl = function (gd_no, gd_nm, brand_nm, add_info, param) {
    return Public.getGoodsVUrl(gd_no, gd_nm, brand_nm, add_info, param, "Y", __PAGE_VALUE.QOO10_GOODS_SERVER);
}

//SEO 검색결과 URL
//?keyword 및 param 붙여야함
//param에 keyword밖에 없는 경우 param을 true로 보내면 전체 url return
Public.getSearchVUrl = function (keyword, param) {

	var path = "";
	var vpath = "";

	if (__PAGE_VALUE.VIEW_SITEID != "m18") {
		path = Public._getSafeUrl(__PAGE_VALUE.SEARCH_SERVER, false) + "/s/";
		vpath = Public.getSEOVirtualPath(keyword, 30);
		return path + encodeURI(vpath) + (param == true ? "?keyword=" + encodeURI(keyword) : "");
	} else {
		path = Public._getSafeUrl(__PAGE_VALUE.SEARCH_SERVER, false) + "/gmkt.inc/M18/List/Search.aspx";
		vpath = Public.getSEOVirtualPath(keyword, 30);
		return path + (param == true ? "?keyword=" + encodeURI(keyword) : "");
	}
}

//SEO용 가상 경로 text 정리 / 특수문자 없애고, -으로 구분 / length 0 이면 체크 안함
Public.getSEOVirtualPath = function (value, len) {
	value = value.replace(/[\u0000-\u002f\u003a-\u0040\u005b-\u0060\u007b-\u00bf\u1200-\u2e7f\u3000-\u3040\u3100-\u3130\u3190-\u31ef\u3200-\u33ff]/gi, '-').replace(/-{1,}/gi, '-');
	var values = value.split('-');
	var result = "";

	for (var i = 0; i < values.length; i++) {
		if (len > 0 && result.length > len) {
			break;
		}
		else {
			result += values[i] + "-";
		}
	}

	result = result.replace(/(^-*)|(-*$)/g, '');
	result = result.toUpperCase();
	if (len > 0 && result.length > (len + 10)) {
		result = result.substring(0, len + 10);
	}

	return result;
}


//Public.GetStaticImagePath(): 각 국가별 Static Image 서버 주소를 가져온다.
Public.GetStaticImagePath = function (url, isCommon) {
	var buffer = Public.getStaticImgPath();
	var lang = GMKT.ServiceInfo.ClientLang;
	if (url == undefined) { return buffer; }
	else
		url = Public.ChangeLangFolder(url, lang);

	if (__PAGE_VALUE.VIEW_SITEID == "m18" && (isCommon != true)) { //for M18
		url = url.replace("/cm/", "/cm/m18/");
		url = url.replace("/" + lang + "/", "/" + lang + "/m18/");
	}

	var buffer = buffer + url;
	return buffer;
}

//Public.GetStaticImagePath(): 각 국가별 Static Image 서버 주소를 가져온다.
Public.GetDPImagePath = function (url) {
	var buffer = Public.getImgPath();
	if (url == undefined) { return buffer; }
	else {
		
		if (url.indexOf("/") == 0) {
			buffer = buffer + url;
		}
		else {
			buffer = buffer + "/" + url;
		}
	}
	
	return buffer;
}

Public.ChangeLangFolder = function (path, lang) {
	if (path.indexOf("/") == 0)
		return "/qoo10/front" + path.replace("$lang$", lang);
	else
		return "/qoo10/front/" + path.replace("$lang$", lang);
}


// 로딩이미지 GET 공통 function
Public.GetLoadingImage = function (type) {
	if (__PAGE_VALUE.VIEW_SITEID == 'm18') {
		if (type == "80")
			return Public.GetStaticImagePath("/cm/common/image/loading_80.png");
		else if (type == "140")
			return Public.GetStaticImagePath("/cm/common/image/loading_140.png");
		else if (type == "400")
			return Public.GetStaticImagePath("/cm/common/image/loading_400.png");
		else if (type == "500")
			return Public.GetStaticImagePath("/cm/common/image/loading_500x417.png");
		else if (type == "160x208")
			return Public.GetStaticImagePath("/cm/common/image/loading_160x208.png");
		else if (type == "auction")
			return Public.GetStaticImagePath("/cm/common/image/thumb_no.png");
		else
			return Public.GetStaticImagePath("/cm/common/image/loading_280.png");

	} else {

		if (type == "80")
			return Public.GetStaticImagePath("/cm/common/image/Loading_80_still.gif?20121112");
		else if (type == "140")
			return Public.GetStaticImagePath("/cm/common/image/Loading_140_still.gif?20121112");
		else if (type == "400")
			return Public.GetStaticImagePath("/cm/common/image/Loading_400_still.gif?20121112");
		else if (type == "500")
			return Public.GetStaticImagePath("/cm/common/image/Loading_bigphoto.gif?20121112");
		else if (type == "160x208")
			return Public.GetStaticImagePath("/cm/common/image/Loading_160x208_still.gif?20121112");
		else if (type == "auction")
			return Public.GetStaticImagePath("/cm/common/image/thumb_no.png?20121112");
		else
			return Public.GetStaticImagePath("/cm/common/image/Loading_280_still.gif?20121112");
	}
}

Public.getPageNo = function () {
	return __PAGE_VALUE.PAGE_NO;
}

Public.getPageContextId = function () {
	if (window.__PAGE_VALUE && __PAGE_VALUE.PAGE_CONTEXT_ID)
		return __PAGE_VALUE.PAGE_CONTEXT_ID;

	return "";
} 

// Windows 앱을 통해 접속 했는지 여부
Public.isWindowsShoppingApp = function() {
	if (GMKT.DeviceInfo.BrowserName.indexOf("Qoo10") >= 0 || GMKT.DeviceInfo.DeviceName == "WindowsApp")
		return true;

	return false;
}
/* Public End */


//*****************************************************
// Util 클래스 저의
//*****************************************************

function Util() {
	var clickOpenSmartView = false;
}


// 쿠키값을 리턴합니다.
// path는 optional파라미터 입니다.
// path는 입력 하지 않거나 입력 하였다면 가상패스를 ~로 대체한 패스를 사용해 주세요.(~/Member/)
Util.getCookie = function (sCookieName, path) {
	if (path != undefined && path != null && path != "/") {
		var path2 = Public.convertNormalUrl(path);

		if (window.location.pathname.indexOf(path2) < 0) {
			if (!Util.__pathCookieValue || Util.__pathCookiePath == path) {
				var url = path2 + "PathCookie.cookie";
				var ret = RMSHelper.callWebObject(url, "GET", "");
				Util.__pathCookieValue = eval("(" + ret + ")");
				Util.__pathCookiePath = path;

				if (Util.__pathCookieValue[sCookieName])
					return Util.__pathCookieValue[sCookieName];
				else
					return "";

				if (Util.__pathCookieValue[sCookieName])
					return callbackFunc(Util.__pathCookieValue[sCookieName], sCookieName, path);
				else
					return callbackFunc("", sCookieName, path);
			}

			if (Util.__pathCookieValue && Util.__pathCookieValue[sCookieName])
				return Util.__pathCookieValue[sCookieName];
			else
				return "";
		}
		else {
			sCookieName = "PathCookie_" + sCookieName;
		}
	}

	var sName = sCookieName + "=", ichSt, ichEnd;
	var sCookie = document.cookie;
	var value = "";

	if (sCookie.length && (-1 != (ichSt = sCookie.indexOf(sName)))) {
		if (-1 == (ichEnd = sCookie.indexOf(";", ichSt + sName.length)))
			ichEnd = sCookie.length;

		value = unescape(sCookie.substring(ichSt + sName.length, ichEnd));
	}

	return value;
}


// 쿠키를 설정 합니다.
// domain, expiredays, path 파라미터는 옵셔널 파라미터 입니다.
// domain이 null이거나 입력하지 않을시 기본값은 시스템이 사용하는 쿠키 도메인 입니다.
// path는 입력 하지 않거나 입력 하였다면 가상패스를 ~로 대체한 패스를 사용해 주세요.(~/Member/)
Util.setCookie = function (sName, vValue, domain, expiredays, path) {
	if (domain == undefined || domain == null)
		domain = Public.getCookieDomain();

	var org_path = path;
	var sCookie = sName + "=" + escape(vValue);

	if (path == undefined || path == null)
		path = "/";
	else if (path != "/")
		path = Public.convertNormalUrl(path);

	if (window.location.pathname.indexOf(path) < 0) {
		Util.__pathCookiePath = org_path;

		var url = path + "PathCookie.cookie?name=" + sName + "&value=" + escape(vValue);

		var ret = RMSHelper.callWebObject(url, "GET", "");
		Util.__pathCookieValue = eval("(" + ret + ")");
		return;
	}
	else {
		sName = "PathCookie_" + sName;
	}

	if (domain != undefined && domain != null && domain != "")
		sCookie += ";domain=" + domain + ";path=" + path;
	else
		sCookie += ";path=" + path;
	// expiresdays ==0인 경우 24까지 유효한 쿠키
	if (expiredays != undefined && expiredays != null) {
		var today = new Date();
		if (expiredays == 0) {
			today = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
		} else {
			today.setDate(today.getDate() + expiredays);
		}
		sCookie += "; expires=" + today.toGMTString();
	}

	document.cookie = sCookie;
}

//localStorage 을 지원 하는 경우 localStorage 이용. 그렇지 않을 경우 Cookie 사용.
// isJSON : vValue 값이 JSON 형일 경우에 string으로 변환 시킴.
Util.setStorage = function (sName, vValue, isJSON) {
	var tmpValue = (isJSON != null && isJSON == true ? JSON.stringify(vValue) : vValue);
	try {
		if ('localStorage' in window && window['localStorage'] != null) {
			localStorage.setItem(sName, tmpValue);
		}
		else {
			Util.setCookie(sName, tmpValue);
		}
	}
	catch (ee) {
		Util.setCookie(sName, tmpValue);
	}
}

//localStorage 을 지원 하는 경우 localStorage 이용. 그렇지 않을 경우 Cookie 사용.
// isJSON : vValue 값을 JSON 형으로 변환 시킴.
Util.getStorage = function (sName, isJSON) {
	var tmpValue = null;
	try {
		if ('localStorage' in window && window['localStorage'] != null) {
			tmpValue = localStorage.getItem(sName);
		}
		else {
			tmpValue = Util.getCookie(sName);
		}
	}
	catch (ee) {
		tmpValue = Util.getCookie(sName);
	}

	if (tmpValue != null && isJSON != null && isJSON == true) {
		tmpValue = JSON.parse(tmpValue);
	}

	return tmpValue;
}

//localStorage 을 지원 하는 경우 localStorage 이용. 그렇지 않을 경우 Cookie 사용.
Util.removeStorage = function (sName) {
	try {
		if ('localStorage' in window && window['localStorage'] != null) {
			localStorage.removeItem(sName);
		}
		else {
			Util.setCookie(sName, "");
		}
	}
	catch (ee) {
		Util.setCookie(sName, "");
	}
}

// 로컬 호스트 인지를 리턴합니다.
Util.isLocalHost = function () {
	var url = document.location.href;

	if (url.indexOf("125.131") >= 0)
		return true;

	if (url.indexOf("localhost") < 0) {
		return false;
	}

	return true;
}


// 숫자를 ##,### 형태로 포맷팅 합니다.
Util.getNumberFormat = function (value) {
	var str = value + "";
	var len = str.length;
	var buf = "";

	for (var i = 0; i < len; i++) {
		if (i != 0 && i % 3 == 0)
			buf = "," + buf;
		buf = str.charAt(len - (i + 1)) + buf;
	}

	return buf;
}



// 안전한(null 제외) 스트링 문자를 리턴합니다.
Util.safeString = function (strValue) {
	return strValue == null ? "" : strValue;
}


// 안전한(null 제외) 숫자를 리턴합니다.
Util.safeInt = function (value, defValue) {
	if (value == null || value == undefined || value.length == 0)
		return defValue;

	return parseInt(value);
}


// 전화번호를 - 로 파싱 하여 idx번째의 번호를 리턴합니다.
Util.getTelNo = function (telno, idx) {
	return Util.safeParseTelNo(telno)[idx];
}


// 전화번호를 파싱하여 배열로 리턴 합니다.
Util.safeParseTelNo = function (telNo) {
	try {
		var arrTelNo = telNo.split("-");
		var tmp = "---".split('-');

		if (arrTelNo.length == 1) {
			if (telNo.indexOf("+") == 0) {
				tmp[0] = telNo.substr(0, 3).replace("+", "");
				telNo = telNo.substr(3);
			}

			if (telNo.length > 4 && telNo.length <= 8) {
				tmp[2] = telNo.substr(0, telNo.length - 4);
				tmp[3] = telNo.substr(telNo.length - 4, 4);
			}
			else if (telNo.length > 8) {
				tmp[1] = telNo.substr(0, telNo.length - 4 - 4);
				tmp[2] = telNo.substr(telNo.length - 4 - 4, 4);
				tmp[3] = telNo.substr(telNo.length - 4, 4);
			}

			return tmp;
		}
		else if (arrTelNo.length == 2) {
			if (arrTelNo[0].indexOf("+") == 0) {
				tmp[0] = arrTelNo[0].replace("+", "");
				tmp[2] = arrTelNo[1];
			}
			else {
				tmp[2] = arrTelNo[0];
				tmp[3] = arrTelNo[1];
			}

			return tmp;
		}
		else if (arrTelNo.length == 3) {
			if (arrTelNo[0].indexOf("+") == 0) {
				tmp[0] = arrTelNo[0].replace("+", "");
				tmp[2] = arrTelNo[1];
				tmp[3] = arrTelNo[2];
			}
			else {
				tmp[1] = arrTelNo[0];
				tmp[2] = arrTelNo[1];
				tmp[3] = arrTelNo[2];
			}

			return tmp;
		}
		else if (arrTelNo.length > 4) {
			tmp[0] = arrTelNo[0].replace("+", "");
			tmp[1] = arrTelNo[1];
			tmp[2] = arrTelNo[2];
			tmp[3] = arrTelNo[3];

			for (var i = 4; i < arrTelNo.length; i++) {
				tmp[3] += arrTelNo[i];
			}

			return tmp;
		}
		else if (arrTelNo.length == 4) {
			arrTelNo[0] = arrTelNo[0].replace("+", "");
			return arrTelNo;
		}
	}
	catch (ex) {
	}

	return "---".split('-');
}


// 표준에러에 대한 에러 스트링을 리턴합니다.
Util.getStdErrMsg = function (strTitle, objResult) {
	return strTitle + "\n" + "[" + objResult.ERR_NO + "]" + objResult.ERR_MSG;
}



// 화이트 스페이스여부를 리턴합니다.
Util.isWhiteSpace = function (inChar) {
	return (inChar == ' ' || inChar == '\t' || inChar == '\n');
}

// 스트링의 문자열을 치환 합니다.
Util.replace = function (source, str1, str2) {
	var idx = source.indexOf(str1);

	if (idx >= 0) {
		var ret = "";

		if (idx > 0) {
			ret = source.substring(0, idx);
		}

		ret += str2;

		if (idx + str1.length < source.length) {
			ret += source.substring(idx + str1.length);
		}

		return Util.replace(ret, str1, str2);
	}

	return source;
}


Util.isNumber = function (str) {
	if (str.length == 0)
		return false;

	for (var i = 0; i < str.length; i++) {
		if (!('0' <= str.charAt(i) && str.charAt(i) <= '9'))
			return false;
	}
	return true;
}


Util.isPhoneNumber = function (str) {
	if (str.length == 0)
		return false;

	for (var i = 0; i < str.length; i++) {
		if (!(('0' <= str.charAt(i) && str.charAt(i) <= '9') || str.charAt(i) == "-"))
			return false;
	}
	return true;
}



Util.isHangul = function (str) {
	var regexp = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힝]/;

	if (regexp.test(str))
		return true;

	return false;
}

//문자열에 태그 포함여부 조사
Util.isValidString = function (str) {
	//HTML 태그
	var regexp = /<\/?[^>]+(>|$)/g;
	if (str.replace(regexp, "") == str)
		return true;
	else
		return false;
}

// 스트링의 ' , " 을 지웁니다.
Util.RemoveSpecialCharacter = function (str) {

	str = str.replace(/"/g, '');
	str = str.replace(/'/g, '');
	str = str.replace(/\\/g, '');
	return str;
}

// 스트링의 전각 존재하면 true
Util.IsJungak = function (str) {
	var isJungak = false;

	for (var i = 0; i < str.length; ++i) {
		var c = str.charCodeAt(i);

		if (c < 256 || (c >= 0xff61 && c <= 0xff9f)) {	//반각문자임
			isJungak = false;
		} else {
			isJungak = true;
			break;
		}
	}
	return isJungak;
}

// 이벤트 핸들러를 등록 합니다.
// objid는 id거나 오브젝트 일수 있습니다.
Util.addEventHandler = function (objid, event, evnetfunc) {
	ControlUtil.addEventHandler(objid, event, evnetfunc);
}

// 서버객체의 이벤트 핸들러를 등록 합니다.
Util.addSEventHandler = function (objid, event, evnetfunc) {
	ControlUtil.addSEventHandler(objid, event, evnetfunc);
}



//페이지 리로드
Util.reloadPage = function () {
	window.location.reload();
}


//페이지 GO
Util.goPage = function (url) {
	window.location.href = url;
}

// 타사이트로의 이동 & 자동 연동가입
Util.loginForward = function (url, isNewWin) {
	if (url.indexOf("?") > 0)
		url = url + "&__langcd=" + GMKT.ServiceInfo.ClientLang;
	else
		url = url + "?__langcd=" + GMKT.ServiceInfo.ClientLang;

	if (Public.isLogin()) {
		var url = Public.getLoginServerUrl("/Login/FrontLoginPass.aspx?nextUrl=", true) + encodeURIComponent(url);
		if (isNewWin || isNewWin == undefined) {
			var newWin = window.open("about:blank");
			newWin.location.href = url;
		}
		else {
			window.location.href = url;
		}
	}
	else {
		Public.goLoginPage();
	}
}

// 글로벌 연동 & url 이동
Util.globalMemberForward = function (callbackFunc) {
	var url = window.location.protocol + "//" + window.location.host + "/gmkt.inc/Member/GlobalMemberPass.aspx?callbackFunc=" + callbackFunc;

	if (!Public.isLogin()) //비로그인
		Util.openInnerPopup(url, 450, 335, false, -2, -3);
	else                   //로그인
		Util.openInnerPopup(url, 750, 620);
}

// aspx의 서버폼을 리턴합니다.
Util.getServerForm = function () {
	return ControlUtil.getServerForm();
}


// aspx의 서버폼을 서브밋 합니다.
// action은 옵셔널 파라미터 입니다.(없을경우 현재 페이지로 서브밋 됩니다.)
Util.submitServerForm = function (action) {
	ControlUtil.submitServerForm(action);
}

//이벤트 응모
// options : {reload_yn : "Y", password : "", callback : "", reload_type : "", ext_val : "", innerPopup : "", reg_affiliate : ""}
// reload_yn = Y / N -> Reload 여부 결정
// reload_type =  Y  -> 부모창과 도메인이 같은 경우 바로 Reload 시키고 창 닫힘
//             = N   -> 부모창과 도메인이 다른 경우 공통 함수인 Util.forwardOpener를 사용하여 Reload (팝업된 응모창이 흰 창으로 바뀌고 나서 부모창이 Reload 됨)
// ext_val : reload_first --> 부모창을 먼저 리로딩함.
// reg_affiliate : Y -> 신규 고객대상 유치 이벤트의 경우 팝업로그인창의 회원가입탭을 default 로 보여주도록 함

Util.EventApply = function (eid, options, password, callback, reload_type, ext_val, innerPopup) {
	var reload_yn = "";
	var reg_affiliate = "";

	if (typeof (options) == "object") {
		reload_yn = options.reload_yn = undefined ? "" : options.reload_yn;
		password = options.password = undefined ? "" : options.password;
		callback = options.callback = undefined ? "" : options.callback;
		reload_type = options.reload_type = undefined ? "" : options.reload_type;
		ext_val = options.ext_val = undefined ? "" : options.ext_val;
		innerPopup = options.innerPopup = undefined ? "" : options.innerPopup;
		reg_affiliate = options.reg_affiliate = undefined ? "" : options.reg_affiliate;
	}
	else {
		reload_yn = options;
	}

	// 모바일 환경이면 모바일 이벤트 페이지를 열어 준다.
	if (window.MobileUtil && MobileUtil.EventApply) {
		return MobileUtil.EventApply(eid, reload_yn, password, callback, reload_type, ext_val);
	}

	var url = "/Event/EventApply.aspx?eid=" + eid; //"B9G46bPv7rs_g_3_";eid;

	if (reload_yn != "" && reload_yn != undefined)
		url += "&reload_yn=" + reload_yn;

	if (reload_type != "" && reload_type != undefined)
		url += "&reload_type=" + reload_type;

	if (password != "" && password != undefined)
		url += "&pass_word=" + password;

	if (callback != "" && callback != undefined)
		url += "&callback=" + callback;

	if (ext_val != "" && ext_val != undefined)
		url += "&ext_val=" + ext_val;

	if (reg_affiliate != "" && reg_affiliate != undefined)
		url += "&affiliate_member=" + reg_affiliate;		// IE bug;; &reg_ 인코딩문제

	if (innerPopup)
		Util.openInnerPopup(Public.getAppPath() + url, 450, 316);
	else
		Util.openPopup(Public.getAppPath() + url, 450, 316, "EventApply");
}

// 이토큰 응모
Util.EtokenApply = function (t_code, reload_yn, is_off, go_login, size) {

	var url = "/Event/ETokenApply.aspx?t_code=" + t_code;

	if (reload_yn != "" && reload_yn != undefined)
		url += "&reload_yn=" + reload_yn;

	if (is_off != "" && is_off != undefined)
		url += "&is_off=" + is_off;
	else
		url += "&is_off=Y"; //오프라인 용 8자리 코드가 디폴트 

	if (go_login != "" && go_login != undefined)
		url += "&go_login=" + go_login;
	else
		url += "&go_login=Y"; //로그인 필수가 디폴트

	if (size != "" && size != undefined)
		url += "&size=" + size;

	Util.openPopup(Public.getAppPath() + url, 450, 316, "EtokenApply");
};

Util.forwardOpener = function (url, ssl) {
	var openerUrl = Util.getCookie("__popup_opener_domain__");

	try {
		// 쿠키에 __popup_opener_domain__ 설정이 되어 있는 케이스(Util.openPopup() 메소드로 오픈한경우)
		// 이케이스에는 reload도 여기서 처리됨
		if (openerUrl && openerUrl.length > 0) {
			window.location.href = openerUrl + "/gmkt.inc/Common/SafeFowardOpener.aspx?fowardurl=" + escape(url);
			return;
		}
		else if (url.substr(0, 7) == "http://" || url.substr(0, 8) == "https://") {
			var idx = url.indexOf("/", 9);

			if (idx >= 0) {
				if (url.substr(0, idx) != window.location.href.substr(0, idx)) {
					var idx2 = url.indexOf("/", idx + 1);

					if (idx2 >= 0) {
						url = url.substr(0, idx2) + "/Common/SafeFowardOpener.aspx?fowardurl=" + escape(url);
						window.location.href = url;

						return false;
					}
				} else {
					window.opener.location.href = url;
				}
			}
		}
		// reload에 도메인이 지정되어있는 케이스
		else if (url.substr(0, 7) == "reload:") {
			if (ssl != undefined && ssl == true)
				url = "https://" + url.substr(7, url.length - 7) + "/gmkt.inc/Common/SafeFowardOpener.aspx?fowardurl=reload";
			else
				url = "http://" + url.substr(7, url.length - 7) + "/gmkt.inc/Common/SafeFowardOpener.aspx?fowardurl=reload";

			window.location.href = url;
			return;
		}
		// 도메인이 설정 되어 있지 않고 reload만 설정된케이스
		else if (url == "reload") {
			window.opener.location.reload();
		}
		// 이도 저도 아닌 케이스
		else {
			window.opener.location.href = url;
		}
	}
	catch (ex) {
		return false;
	}

	if (openerUrl && openerUrl != "")
		Util.setCookie("__popup_opener_domain__", "");

	window.close();
}


Util._savePopupOpenerDomain = function () {
	Util.setCookie("__popup_opener_domain__", window.location.protocol + "//" + window.location.host);
}

// 팝업을 오픈 합니다.
Util.openPopup = function (url, width, heigth, name, top, left, noreturn) {
	Util._savePopupOpenerDomain();

	if (top == undefined || top == null)
		top = (screen.height - heigth) / 3;
	else if (top == -1)
		top = 0;
	else if (top == -2)
		top = (screen.height / 2) - (heigth / 2);
	else if (top == -3)
		top = screen.height - heigth;

	if (left == undefined || left == null)
		left = (screen.width - width) / 2;
	else if (left == -1)
		left = 0;
	else if (left == -2)
		left = (screen.width / 2) - (width / 2);
	else if (left == -3)
		left = screen.width - width;

	var objPop = window.open(url, name, 'width=' + width + ', height=' + heigth + ', top=' + top + ',left=' + left + ', resize=no, scrollbars=no');
	if (objPop) objPop.focus();

	if (!noreturn)
		return objPop;
}



// 팝업을 오픈 합니다.
Util.openPopdown = function (url, width, heigth, name, top, left, noreturn) {
	Util._savePopupOpenerDomain();

	if (top == undefined || top == null)
		top = (screen.height - heigth) / 3;
	else if (top == -1)
		top = 0;
	else if (top == -2)
		top = (screen.height / 2) - (heigth / 2);
	else if (top == -3)
		top = screen.height - heigth;

	if (left == undefined || left == null)
		left = (screen.width - width) / 2;
	else if (left == -1)
		left = 0;
	else if (left == -2)
		left = (screen.width / 2) - (width / 2);
	else if (left == -3)
		left = screen.width - width;

	var objPop = window.open(url, name, 'width=' + width + ', height=' + heigth + ', top=' + top + ',left=' + left + ', resize=no, scrollbars=no');

	self.focus();
	window.setTimeout("window.focus();", 500);

	if (!noreturn)
		return objPop;
}


// 팝업을 오픈 합니다.
Util.openPopupScroll = function (url, width, heigth, name, top, left) {
	Util._savePopupOpenerDomain();

	if (top == undefined || top == null)
		top = (screen.height - heigth) / 3;
	else if (top == -1)
		top = 0;
	else if (top == -2)
		top = (screen.height / 2) - (heigth / 2);
	else if (top == -3)
		top = screen.height - heigth;

	if (left == undefined || left == null)
		left = (screen.width - width) / 2;
	else if (left == -1)
		left = 0;
	else if (left == -2)
		left = (screen.width / 2) - (width / 2);
	else if (left == -3)
		left = screen.width - width;

	var objPop = window.open(url, name, 'width=' + width + ', height=' + heigth + ', top=' + top + ',left=' + left + ', resize=no, scrollbars=yes');
	if (objPop) objPop.focus();

	return objPop;
}


Util.openNextPageInnerPopup = function (url, width, height) {
	Util.setCookie("next_page_popup", escape(url) + "_:::_" + width + "_:::_" + height);
}

Util.__openInnerPopup_closeDelegate = null;
Util.__openInnerPopup_owner = null;
Util.__openInnerPopup_multiPopup = false;
Util.__openInnerPopup_count = 0;
Util.__openInnerPopup_loading = true;
Util.__prev_url = ""; // 마지막 로딩된 url

Util.openInnerPopupPreview = function (url, width, height, isback, top, left, scroll, closeDelegate, closebutton, popupName, headerDisplay) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	if (closeDelegate)
		Util.__openInnerPopup_closeDelegate = closeDelegate;

	Util._savePopupOpenerDomain();

	if (isback == undefined || isback == null)
		isback = false;

	if (isback.toString() == "0") {
		Util.blindScreen(true, isback);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}
	//else if (isback == undefined || isback)
	else if (isback) {
		Util.blindScreen(true);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}

	if (top == undefined)
		top = -2;

	if (left == undefined)
		left = -2;

	var winHeight = $(window).height();
	var winWidth = $(window).width();

	if (left == -2)
		left = winWidth / 2 - width / 2;
	else if (left == -1)
		left = 0;
	else if (left == -3)
		left = winWidth / 2 - width / 2 - 220;


	var position_flag = "man";
	if (top == -2) {
		if (ie6)
			top = winHeight / 2 - height / 2 + ($(document).scrollTop());
		else
			top = winHeight / 2 - height / 2;

		position_flag = "auto";
	}
	else if (top == -1)
		top = 0;
	else if (top == -3)
		top = winHeight - height;
	else if (top == -4)
		top = winHeight / 2 - height / 2 + ($(document).scrollTop()) - height;

	if (left < 0) left = 0;
	if (top < 0) top = 0;

	var bg_pattern = ETC.GetStaticImageURLRoot("/en/front/popup/image/bg_pattern02.gif?e110823");
	var close_icon = ETC.GetStaticImageURLRoot("/en/front/popup/image/icon_popup.png?e110823");
	var loading_img = ETC.GetStaticImageURLRoot("/en/gsm/common/image/ajax-loader.gif?e110823");

	var position_style = "absolute";
	// ie6에서는 fixed가 작동 하지 않는다.
	if (position_flag == "auto" && !ie6) {
		position_style = "fixed";
	}

	if (scroll == undefined || scroll == null)
		scroll = "yes";

	if (closebutton == undefined || closebutton == null)
		closebutton = true;

	if (popupName == undefined || popupName == null)
		popupName = "";

	if (headerDisplay == undefined || headerDisplay == null)
		headerDisplay = true;

	if (typeof (url) == "string") {
		var divId = "div_popup";
		var frameId = "frame_popup_preview";
		var title_id = "title_popup";

		if ($("#div_popup").length > 0 && Util.__openInnerPopup_multiPopup) {
			divId += "_" + Util.__openInnerPopup_count;
			frameId += "_" + Util.__openInnerPopup_count;
			title_id += "_" + Util.__openInnerPopup_count;
		}

		if ($get("div_popup") == null || Util.__openInnerPopup_multiPopup || $get(frameId) == null) {
			if (!Util.__openInnerPopup_multiPopup && $get("div_popup") && $get(frameId) == null) {
				$("#div_popup").remove();
			}
			Util.__openInnerPopup_count++;

			var close_button_display = "";
			close_button_display = !closebutton ? "display:none;" : "display:block;";

			var header_display = ""
			header_display = !headerDisplay ? "display:none;" : "display:block;";


			if (url.indexOf("?") > 0) {
				url = url + "&title_id=" + title_id;
			}
			else {
				url = url + "?title_id=" + title_id;
			}

			// 2011-09-05 edited by ykums
			// 도메인이 다르면 로딩 이미지를 보여주지 않는다.
			var loading = Util.__openInnerPopup_loading;
			if (Util.__openInnerPopup_loading) {
				if (url.indexOf("http://") == 0 || url.indexOf("https://") == 0) {
					if (url.indexOf(window.location.protocol + "//" + window.location.host) != 0)
						loading = false;
				}
			}

			var strHtml = "<div class=\"innerPopWrap\" id=\"" + divId + "\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:" + innerPopup_zindex + "; width:" + width + "px;\" >"
				+ "<div class=\"head\" style=\"" + header_display + "\">"
				+ "	<h2><span id=\"" + title_id + "\">" + popupName + "</span></h2>"
				+ "		<div class=\"closePop\"><a href=\"javascript:Util.closeInnerPopup(null, '" + divId + "');\" style=\"" + close_button_display + "\">X</a></div>"
				+ "</div>";

			innerPopup_zindex++;

			if (loading) {
				strHtml +=
					"<div class=\"loading\">"
					+ "	<div class=\"bg\" style=\"height:" + height + "px;\"></div>"
					+ "	<div class=\"img\" style=\"height:" + height + "px;\"><img src=\"" + loading_img + "\"/></div>"
					+ "</div>";
				// 로딩 이미지는 어쨌든 1초후에 사라지도록 한다.
				setTimeout(function (e) { $(".loading").hide(); }, 1000);
			}

			strHtml +=
				"<div class=\"content\">"
				+ "<iframe id=\"" + frameId + "\" name=\"frame_popup_preview\" style=\"width:" + width + "px; height:" + height + "px; background-color:#fff;\" scrolling=\"" + scroll + "\" src=\"" + url + "\" frameborder=\"0\" ></iframe>"
				+ "</div>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#" + divId) && $("#" + divId).position()) {
						var left2 = $("#" + divId).position().left;
						var top2 = $("#" + divId).position().top;

						$("#" + divId).css("position", "absolute");
						$("#" + divId).css("left", left2);
						$("#" + divId).css("top", top2);
					}
				}, 1000);
			}
		}
		else {

			ControlUtil.displayObject("div_popup", true);

			if (url.indexOf("?") > 0) {
				url = url + "&title_id=title_popup";
			}
			else {
				url = url + "?title_id=title_popup";
			}

			if (popupName != "") {
				$("#" + title_id).html(popupName);
			}

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#div_popup").css("z-index", innerPopup_zindex);
			$("#div_popup").width(width);

			if (headerDisplay)
				$(".innerPopWrap .head").show();
			else
				$(".innerPopWrap .head").hide();

			if (closebutton)
				$(".closePop a").show();
			else
				$(".closePop a").hide();

			innerPopup_zindex++;

			if (this.__prev_url != url) {
				$("#frame_popup_preview").width(width);
				$("#frame_popup_preview").height(height);
				$("#frame_popup_preview").attr("scrolling", scroll);
				$get("frame_popup_preview").src = "";
				setTimeout(function () { $get("frame_popup_preview").src = url; }, 100);
			}

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	} // if (typeof (url) == "string") 
	else {
		if ($get("div_popup") == null) {
			var strHtml = "<div class=\"innerPopWrap\" id=\"div_popup\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:" + innerPopup_zindex + "; width:" + width + "px;\" >"
				+ "<div class=\"head\" style=\"" + header_display + "\">"
				+ "	<h2><span id=\"" + title_id + "\">" + popupName + "</span></h2>"
				+ "<div class=\"closePop\" style=\"" + close_button_display + "\"><a href=\"javascript:Util.closeInnerPopup();\" style=\"" + close_button_display + "\">X</a></div></div>"
				+ url.innerHTML
				+ "</div>";
			innerPopup_zindex++;

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			ControlUtil.displayObject("div_popup", true);

			if (url.html == undefined)
				$("#div_popup").html($(url).html());
			else
				$("#div_popup").html(url.html());

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#div_popup").css("z-index", innerPopup_zindex);
			innerPopup_zindex++;


			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}

	this.__prev_url = url;

	try {
		//drag 2010-08-16 최길형
		$(".innerPopWrap").draggable({ cursor: 'move' });

	}
	catch (ex) { }
}

Util.openInnerPopup = function (url, width, height, isback, top, left, scroll, closeDelegate, closebutton, popupName, headerDisplay, headerSize) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	if (closeDelegate)
		Util.__openInnerPopup_closeDelegate = closeDelegate;

	Util._savePopupOpenerDomain();

	if (isback == undefined || isback == null)
		isback = false;

	if (isback.toString() == "0") {
		Util.blindScreen(true, isback);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}
	else if (isback.toString() == "99") {	//bibit결제 페이지에서 닫혀지는 내용 막기위함
		Util.blindScreen(true);
	}
	//else if (isback == undefined || isback)
	else if (isback) {
		Util.blindScreen(true);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}

	if (top == undefined)
		top = -2;

	if (left == undefined)
		left = -2;

	var winHeight = $(window).height();
	var winWidth = $(window).width();

	if (left == -2)
		left = winWidth / 2 - width / 2;
	else if (left == -1)
		left = 0;
	else if (left == -3)
		left = winWidth / 2 - width / 2 - 220;


	var position_flag = "man";
	if (top == -2) {
		if (ie6)
			top = winHeight / 2 - height / 2 + ($(document).scrollTop());
		else
			top = winHeight / 2 - height / 2;

		position_flag = "auto";
	}
	else if (top == -1)
		top = 0;
	else if (top == -3)
		top = winHeight - height;
	else if (top == -4)
		top = winHeight / 2 - height / 2 + ($(document).scrollTop()) - height;

	if (left < 0) left = 0;
	if (top < 0) top = 0;

	var bg_pattern = ETC.GetStaticImageURLRoot("/en/front/popup/image/bg_pattern02.gif?e110823");
	var close_icon = ETC.GetStaticImageURLRoot("/en/front/popup/image/icon_popup.png?e110823");
	var loading_img = ETC.GetStaticImageURLRoot("/en/gsm/common/image/ajax-loader.gif?e110823");

	var position_style = "absolute";
	// ie6에서는 fixed가 작동 하지 않는다.
	if (position_flag == "auto" && !ie6) {
		position_style = "fixed";
	}

	if (scroll == undefined || scroll == null)
		scroll = "no";

	if (closebutton == undefined || closebutton == null)
		closebutton = true;

	if (popupName == undefined || popupName == null)
		popupName = "";

	if (headerDisplay == undefined || headerDisplay == null)
		headerDisplay = true;

	if (typeof (url) == "string") {
		var divId = "div_popup";
		var frameId = "frame_popup";
		var title_id = "title_popup";

		if ($("#div_popup").length > 0 && Util.__openInnerPopup_multiPopup) {
			divId += "_" + Util.__openInnerPopup_count;
			frameId += "_" + Util.__openInnerPopup_count;
			title_id += "_" + Util.__openInnerPopup_count;
		}

		if ($get("div_popup") == null || Util.__openInnerPopup_multiPopup || $get(frameId) == null) {
			if (!Util.__openInnerPopup_multiPopup && $get("div_popup") && $get(frameId) == null) {
				$("#div_popup").remove();
			}
			Util.__openInnerPopup_count++;

			var close_button_display = "";
			close_button_display = !closebutton ? "display:none;" : "display:block;";

			var header_display = ""
			header_display = !headerDisplay ? "display:none;" : "display:block;";


			if (url.indexOf("?") > 0) {
				url = url + "&title_id=" + title_id;
			}
			else {
				url = url + "?title_id=" + title_id;
			}

			// 2011-09-05 edited by ykums
			// 도메인이 다르면 로딩 이미지를 보여주지 않는다.
			var loading = Util.__openInnerPopup_loading;
			if (Util.__openInnerPopup_loading) {
				if (url.indexOf("http://") == 0 || url.indexOf("https://") == 0) {
					if (url.indexOf(window.location.protocol + "//" + window.location.host) != 0)
						loading = false;
				}
			}

			var strHtml = "<div class=\"innerPopWrap\" id=\"" + divId + "\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:" + innerPopup_zindex + "; width:" + width + "px;\" >"
				+ "<div class=\"head\" style=\"" + header_display + "\">"
				+ "	<h2><span id=\"" + title_id + "\" " + ((headerSize > 0) ? "style=\"font-size: " + headerSize + "px;\"" : "") + ">" + popupName + "</span></h2>"
				+ "		<div class=\"closePop\"><a href=\"javascript:Util.closeInnerPopup(null, '" + divId + "');\" style=\"" + close_button_display + "\">X</a></div>"
				+ "</div>";

			innerPopup_zindex++;

			if (loading) {
				strHtml +=
					"<div class=\"loading\">"
					+ "	<div class=\"bg\" style=\"height:" + height + "px;\"></div>"
					+ "	<div class=\"img\" style=\"height:" + height + "px;\"><img src=\"" + loading_img + "\"/></div>"
					+ "</div>";
				// 로딩 이미지는 어쨌든 1초후에 사라지도록 한다.
				setTimeout(function (e) { $(".loading").hide(); }, 1000);
			}

			strHtml +=
				"<div class=\"content\">"
				+ "<iframe id=\"" + frameId + "\" name=\"frame_popup\" style=\"width:" + width + "px; height:" + height + "px; background-color:#fff;\" scrolling=\"" + scroll + "\" src=\"" + url + "\" frameborder=\"0\" ></iframe>"
				+ "</div>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#" + divId) && $("#" + divId).position()) {
						var left2 = $("#" + divId).position().left;
						var top2 = $("#" + divId).position().top;

						$("#" + divId).css("position", "absolute");
						$("#" + divId).css("left", left2);
						$("#" + divId).css("top", top2);
					}
				}, 1000);
			}
		} // if ($get("div_popup") == null || Util.__openInnerPopup_multiPopup || $get(frameId) == null)
		else {
			//if (closebutton) {
			//	$("#div_popup > div").show();
			//} else {
			//	$("#div_popup > div").hide();
			//}

			ControlUtil.displayObject("div_popup", true);

			if (url.indexOf("?") > 0) {
				url = url + "&title_id=title_popup";
			}
			else {
				url = url + "?title_id=title_popup";
			}

			if (popupName != "") {
				$("#" + title_id).html(popupName);
			}

			if (headerSize > 0) {
				$("#" + title_id).css('font-size', headerSize);
			} else {
				$("#" + title_id).css('font-size', '');
			}

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#div_popup").css("z-index", innerPopup_zindex);
			$("#div_popup").width(width);

			if (headerDisplay)
				$(".innerPopWrap .head").show();
			else
				$(".innerPopWrap .head").hide();

			if (closebutton)
				$(".closePop a").show();
			else
				$(".closePop a").hide();

			innerPopup_zindex++;

			if (this.__prev_url != url) {
				$("#frame_popup").width(width);
				$("#frame_popup").height(height);
				$("#frame_popup").attr("scrolling", scroll);
				$get("frame_popup").src = url;
			}

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	} // if (typeof (url) == "string") 
	else {
		if ($get("div_popup") == null) {
			var strHtml = "<div class=\"innerPopWrap\" id=\"div_popup\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:" + innerPopup_zindex + "; width:" + width + "px;\" >"
				+ "<div class=\"head\" style=\"" + header_display + "\">"
				+ "	<h2><span id=\"" + title_id + "\" " + ((headerSize > 0) ? "style=\"font-size: " + headerSize + "px;\"" : "") + ">" + popupName + "</span></h2>"
				+ "<div class=\"closePop\" style=\"" + close_button_display + "\"><a href=\"javascript:Util.closeInnerPopup();\" style=\"" + close_button_display + "\">X</a></div></div>"
				+ url.innerHTML
				+ "</div>";
			innerPopup_zindex++;

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			ControlUtil.displayObject("div_popup", true);

			if (url.html == undefined)
				$("#div_popup").html($(url).html());
			else
				$("#div_popup").html(url.html());

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#div_popup").css("z-index", innerPopup_zindex);
			innerPopup_zindex++;


			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}

	this.__prev_url = url;

	try {
		//drag 2010-08-16 최길형
		$(".innerPopWrap").draggable({ cursor: 'move' });

	}
	catch (ex) { }
}

Util.openInnerPopup2 = function (url, width, height, isback, top, left, scroll, closeDelegate, closebutton) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	if (closeDelegate)
		Util.__openInnerPopup_closeDelegate = closeDelegate;

	Util._savePopupOpenerDomain();

	if (isback == undefined || isback == null)
		isback = false;

	if (isback.toString() == "0") {
		Util.blindScreen(true, isback);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}
	else if (isback.toString() == "99") {	//bibit결제 페이지에서 닫혀지는 내용 막기위함
		Util.blindScreen(true);
	}
	//else if (isback == undefined || isback)
	else if (isback) {
		Util.blindScreen(true);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}

	if (top == undefined)
		top = -2;

	if (left == undefined)
		left = -2;

	var winHeight = $(window).height();
	var winWidth = $(window).width();

	if (left == -2)
		left = winWidth / 2 - width / 2;
	else if (left == -1)
		left = 0;
	else if (left == -3)
		left = winWidth - width;

	var position_flag = "man";
	if (top == -2) {
		if (ie6)
			top = winHeight / 2 - height / 2 + ($(document).scrollTop());
		else
			top = winHeight / 2 - height / 2;

		position_flag = "auto";
	}
	else if (top == -1)
		top = 0;
	else if (top == -3)
		top = winHeight - height;

	if (left < 0) left = 0;
	if (top < 0) top = 0;

	var img = ETC.GetStaticImageURLRoot("/en/front/common/image/btn_ifClose.gif");

	var position_style = "absolute";
	// ie6에서는 fixed가 작동 하지 않는다.
	if (position_flag == "auto" && !ie6) {
		position_style = "fixed";
	}

	if (scroll == undefined || scroll == null)
		scroll = "no";

	if (closebutton == undefined || closebutton == null)
		closebutton = true;

	if (typeof (url) == "string") {
		var divId = "div_popup";
		var frameId = "frame_popup";

		if ($get("div_popup") == null || Util.__openInnerPopup_multiPopup || $get(frameId) == null) {
			if ($get("div_popup") && $get(frameId) == null) {
				$("#div_popup").remove();
			}

			Util.__openInnerPopup_count++;

			if ($("#div_popup").length > 0) {
				divId += "_" + Util.__openInnerPopup_count;
				frameId += "_" + Util.__openInnerPopup_count;
			}

			var close_button_display = "";

			if (!closebutton)
				close_button_display = "display:none";

			var strHtml = "<div id=\"" + divId + "\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:1000; border:2px solid #000; background-color:White\" >"
				+ "<iframe id=\"" + frameId + "\" name=\"frame_popup\" style=\"width:" + width + "px; height:" + height + "px; background-color:#fff;\" scrolling=\"" + scroll + "\" src=\"" + url + "\" frameborder=\"0\" ></iframe>"
				+ "<div style=\"position:absolute; right:-1px; bottom:-17px; width:58px; height:17px;" + close_button_display + "\"><a href=\"javascript:Util.closeInnerPopup(null, '" + divId + "');\"><img border=\"0\" src=\"" + img + "\" alt=\"close\" /></a></div>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#" + divId) && $("#" + divId).position()) {
						var left2 = $("#" + divId).position().left;
						var top2 = $("#" + divId).position().top;
						$("#" + divId).css("position", "absolute");
						$("#" + divId).css("left", left2);
						$("#" + divId).css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			if (closebutton) {
				$("#div_popup > div").show();
			}
			else {
				$("#div_popup > div").hide();
			}

			ControlUtil.displayObject("div_popup", true);

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#frame_popup").width(width);
			$("#frame_popup").height(height);
			$("#frame_popup").attr("scrolling", scroll);
			$get("frame_popup").src = url;

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}
	else {
		if ($get("div_popup") == null) {
			var strHtml = "<div id=\"div_popup\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:1000; border:2px solid #000;background-color:White; width:" + width + "px; height:" + height + "px;\" >"
				+ url.innerHTML
				+ "<div style=\"position:absolute; right:-1px; bottom:-17px; width:58px; height:17px;\"><a href=\"javascript:Util.closeInnerPopup();\"><img border=\"0\" src=\"" + img + "\" alt=\"close\" /></a></div>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			ControlUtil.displayObject("div_popup", true);

			if (url.html == undefined)
				$("#div_popup").html($(url).html());
			else
				$("#div_popup").html(url.html());

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}

	try {
		//drag 2010-08-16 최길형
		$("#div_popup").draggable({ cursor: 'move' });
	}
	catch (ex) { }
}

Util.openInnerPopup3 = function (url, width, height, isback, top, left, scroll, closeDelegate, closebutton) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	if (closeDelegate)
		Util.__openInnerPopup_closeDelegate = closeDelegate;

	Util._savePopupOpenerDomain();

	if (isback == undefined || isback == null)
		isback = false;

	if (isback.toString() == "0") {
		Util.blindScreen(true, isback);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}
	else if (isback.toString() == "99") {	//bibit결제 페이지에서 닫혀지는 내용 막기위함
		Util.blindScreen(true);
	}
	//else if (isback == undefined || isback)
	else if (isback) {
		Util.blindScreen(true);
		ControlUtil.addEventHandler("div_screen", "onclick", function () { Util.closeInnerPopup(); });
	}

	if (top == undefined)
		top = -2;

	if (left == undefined)
		left = -2;

	var winHeight = $(window).height();
	var winWidth = $(window).width();

	if (left == -2)
		left = winWidth / 2 - width / 2;
	else if (left == -1)
		left = 0;
	else if (left == -3)
		left = winWidth - width;

	var position_flag = "man";
	if (top == -2) {
		if (ie6)
			top = winHeight / 2 - height / 2 + ($(document).scrollTop());
		else
			top = winHeight / 2 - height / 2;

		position_flag = "auto";
	}
	else if (top == -1)
		top = 0;
	else if (top == -3)
		top = winHeight - height;

	if (left < 0) left = 0;
	if (top < 0) top = 0;

	var img = ETC.GetStaticImageURLRoot("/en/front/common/image/btn_ifClose.gif");
	
	var position_style = "absolute";
	// ie6에서는 fixed가 작동 하지 않는다.
	if (position_flag == "auto" && !ie6) {
		position_style = "fixed";
	}

	if (scroll == undefined || scroll == null)
		scroll = "no";

	if (closebutton == undefined || closebutton == null)
		closebutton = true;

	if (typeof (url) == "string") {
		var divId = "div_popup";
		var frameId = "frame_popup";

		if ($get("div_popup") == null || Util.__openInnerPopup_multiPopup || $get(frameId) == null) {
			if ($get("div_popup") && $get(frameId) == null) {
				$("#div_popup").remove();
			}

			Util.__openInnerPopup_count++;

			if ($("#div_popup").length > 0) {
				divId += "_" + Util.__openInnerPopup_count;
				frameId += "_" + Util.__openInnerPopup_count;
			}

			var close_button_display = "";

			if (!closebutton)
				close_button_display = "display:none";

			var strHtml = "<div id=\"" + divId + "\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:1000; border:0px solid #000; background-color:#E4E4E4\" >"
				+ "<iframe id=\"" + frameId + "\" name=\"frame_popup\" style=\"width:" + width + "px; height:" + height + "px; background-color:#E4E4E4;\" scrolling=\"" + scroll + "\" src=\"" + url + "\" frameborder=\"0\" ></iframe>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#" + divId) && $("#" + divId).position()) {
						var left2 = $("#" + divId).position().left;
						var top2 = $("#" + divId).position().top;
						$("#" + divId).css("position", "absolute");
						$("#" + divId).css("left", left2);
						$("#" + divId).css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			if (closebutton) {
				$("#div_popup > div").show();
			}
			else {
				$("#div_popup > div").hide();
			}

			ControlUtil.displayObject("div_popup", true);

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);
			$("#frame_popup").width(width);
			$("#frame_popup").height(height);
			$("#frame_popup").attr("scrolling", scroll);
			$get("frame_popup").src = url;

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}
	else {
		if ($get("div_popup") == null) {
			var strHtml = "<div id=\"div_popup\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:1000; border:2px solid #000;background-color:White; width:" + width + "px; height:" + height + "px;\" >"
				+ url.innerHTML
				+ "<div style=\"position:absolute; right:-1px; bottom:-17px; width:58px; height:17px;\"><a href=\"javascript:Util.closeInnerPopup();\"><img border=\"0\" src=\"" + img + "\" alt=\"close\" /></a></div>"
				+ "</div>";

			if (Util.__openInnerPopup_owner == null)
				Util.__openInnerPopup_owner = document.body;

			$(Util.__openInnerPopup_owner).append(strHtml);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
		else {
			ControlUtil.displayObject("div_popup", true);

			if (url.html == undefined)
				$("#div_popup").html($(url).html());
			else
				$("#div_popup").html(url.html());

			$("#div_popup").css("position", position_style);
			$("#div_popup").css("left", left);
			$("#div_popup").css("top", top);

			if (position_style == "fixed") {
				setTimeout(function () {
					if ($("#div_popup") && $("#div_popup").position()) {
						var left2 = $("#div_popup").position().left;
						var top2 = $("#div_popup").position().top;
						$("#div_popup").css("position", "absolute");
						$("#div_popup").css("left", left2);
						$("#div_popup").css("top", top2);
					}
				}, 1000);
			}
		}
	}

	try {
		//drag 2010-08-16 최길형
		$("#div_popup").draggable({ cursor: 'move' });
	}
	catch (ex) { }
}

// 스마트 탭
Util.closeSmartTab = function () {
	$("#quickSmartTab").css("display", "none");
	$("#quickQplay").css("display", "none");
}
// top_obj $객체로 넘겨주면 해당 포지션에 맞춰서 처리
Util.openSmartTab = function (url, top_obj, type) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	var top_obj_position = 0;
	if (top_obj)	// top_obj를 넘겨 받은 경우 top position을 계산해서 최종적으로 top 위치를 여기에 맞춰준다.
		top_obj_position = top_obj.offset().top + top_obj.height();

	var winHeight = $(window).height();
	var winWidth = $(window).width();
	if (type == undefined) type = "S";

	var width = 876, height = 531;
	if (type == "Q") {
		width = 900;
		height = 650;
	}

	var top;
	var left = winWidth / 2 - width / 2 - 2;
	var position_flag = "auto";

	if (ie6)
		top = winHeight / 2 - height / 2 + ($(document).scrollTop());
	else
		top = winHeight / 2 - height / 2;

	if (left < 0) left = 0;
	if (top < $("#content").offset().top) top = $("#content").offset().top;

	if (top_obj_position > 0) top = top_obj_position;

	var img = ETC.GetStaticImageURLRoot("/en/front/common/image/ajax-loader2.gif?20111015");

	var position_style = "absolute";
	// ie6에서는 fixed가 작동 하지 않는다.
	if (position_flag == "auto" && !ie6) {
		position_style = "fixed";
	}
	var divId = "quickSmartTab";
	var frameId = "iframe_smarttab";

	if (type == "Q") {
		divId = "quickQplay";
		frameId = "iframe_qplay";
	}

	if ((type == "S" && ($get("quickSmartTab") == null || $get(frameId) == null)) || (type == "Q" && ($get("quickQplay") == null || $get(frameId) == null))) {
		var strHtml = "";
		if (type == "S") {
			strHtml = "<div class=\"quickSmartTab\" id=\"quickSmartTab\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:100;\">"
			+ "<div id=\"quickSmartTabLoading\" class=\"quickSmartTabLoading\" style=\"width: " + (width - 2) + "px; height: " + (height - 2) + "px; display:;\">"
			+ "<div class=\"bg\"></div>"
			+ "<div class=\"img\"><img src=\"" + img + "\" alt=\"loading\"></div>"
			+ "</div>"
			+ "<iframe src=\"" + url + "\" id=\"iframe_smarttab\" scrolling=\"no\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" width=\"" + width + "\" height=\"" + height + "\" style=\"width: " + width + "px; height: " + height + "px;\"></iframe>"
			+ "</div>";
		}
		else {
			strHtml = "<div class=\"quickQplay\" id=\"quickQplay\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:100;\">"
			+ "<div id=\"quickQplayLoading\" class=\"quickQplayLoading\" style=\"width: " + (width - 2) + "px; height: " + (height - 2) + "px; display:;\">"
			+ "<div class=\"bg\"></div>"
			+ "<div class=\"img\"><img src=\"" + img + "\" alt=\"loading\"></div>"
			+ "</div>"
			+ "<iframe src=\"" + url + "\" id=\"iframe_qplay\" scrolling=\"no\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" width=\"" + width + "\" height=\"" + height + "\" style=\"width: " + width + "px; height: " + height + "px;\"></iframe>"
			+ "</div>";
		}

		$("#content").append(strHtml);

		if (type == "S")
			setTimeout(function (e) { $(".quickSmartTabLoading").hide(); }, 1000);
		else
			setTimeout(function (e) { $(".quickQplayLoading").hide(); }, 1000);

		if (position_style == "fixed") {
			setTimeout(function () {
				if ($("#" + divId) && $("#" + divId).position()) {
					var left2 = $("#content").width() / 2 - $("#" + divId).width() / 2;
					var top2 = $("#" + divId).position().top;
					if ($("#content").css("position") == "relative") {	// content div의 position이 relatiive일때는 top에서 content의 top을 빼야함
						top2 = top2 - $("#content").offset().top;
					}

					$("#" + divId).css("position", "absolute");
					$("#" + divId).css("left", left2);
					$("#" + divId).css("top", top2);
				}
			}, 500);
		}
	}
	else {	//닫았다가 다시  연경우에는 기존 화면 그대로 다시 보여주고 끝
		ControlUtil.displayObject(divId, true);

		if (type == "S") {
			$("#quickSmartTab").css("position", position_style);
			$("#quickSmartTab").css("left", left);
			$("#quickSmartTab").css("top", top);
			$("#quickSmartTabLoading").css("display", "");
		}
		else {
			$("#quickQplay").css("position", position_style);
			$("#quickQplay").css("left", left);
			$("#quickQplay").css("top", top);
			$("#quickQplayLoading").css("display", "");
		}

		//기존 화면 그대로 다시 보여주는부분 제거. (HyunSoo : 2012/01/09)
		//if ($get("iframe_smarttab").src.split("?")[1] != url.split("?")[1]) {
		if (type == "S")
			$get("iframe_smarttab").src = url;
		else
			$get("iframe_qplay").src = url;
		//}
		//else {
		//	$("#quickSmartTabLoading").css("display", "none");
		//}

		if (position_style == "fixed") {
			setTimeout(function () {
				if ($("#" + divId) && $("#" + divId).position()) {
					var left2 = $("#content").width() / 2 - $("#" + divId).width() / 2;
					var top2 = $("#" + divId).position().top;
					if ($("#content").css("position") == "relative") {
						top2 = top2 - $("#content").offset().top;
					}
					$("#" + divId).css("position", "absolute");
					$("#" + divId).css("left", left2);
					$("#" + divId).css("top", top2);
				}
			}, 500);
		}
	}
	try {
		//drag 2010-08-16 최길형
		$("#div_popup").draggable({ cursor: 'move' });
	}
	catch (ex) { }
}

Util.openSmartTab_Auto = function () {
	var qplay_btn = $(".btn_smartView");
	if (qplay_btn.length == 0) {
		qplay_btn = $(".btn_smartview");
	}

	if (qplay_btn.length > 0) {
		qplay_btn.click();
	}
}

Util.closeSellerQplay = function () {
	$("#quickSellerQplay").css("display", "none");
}
//셀러샵 전용 Qplay를 새 레이어로 띄움
Util.openSellerQplayAtNewLayer = function (url, top_obj, position_type) {
	var ie6 = jQuery.browser.msie && jQuery.browser.version == "6.0";

	var top_obj_position = 0;
	if (top_obj)	// top_obj를 넘겨 받은 경우 top position을 계산해서 최종적으로 top 위치를 여기에 맞춰준다.
		top_obj_position = top_obj.offset().top + top_obj.height();

	var winHeight = $(window).height();
	var winWidth = $(window).width();
	var width = 876, height = 531;
	var top;
	var left = 0;
	var position_flag = "auto";
	var position_style = "absolute";
	var img = ETC.GetStaticImageURLRoot("/en/front/common/image/ajax-loader2.gif?20111015");
	
	if (position_type == "layer") {
		top = parent.$("#quickSmartTab").css("top").replace("px", "");
	}
	else {
		top = $("#todealSmartTab").offset().top - $("#content").offset().top;
	}
	var divId = "quickSellerQplay";
	var frameId = "iframe_seller_Qplay";

	if (($get("quickSellerQplay") == null && parent.$get("quickSellerQplay") == null) || ($get(frameId) == null && parent.$get(frameId)) == null) {
		var strHtml = "<div class=\"quickSmartTab\" id=\"quickSellerQplay\" style=\"top:" + top + "px; left:" + left + "px; position:" + position_style + "; z-index:101;\">"
			+ "<div id=\"quickSellerQplayLoading\" class=\"quickSmartTabLoading\" style=\"width: 874px; display:;\">"
			+ "<div class=\"bg\"></div>"
			+ "<div class=\"img\"><img src=\"" + img + "\" alt=\"loading\"></div>"
			+ "</div>"
			+ "<iframe src=\"" + url + "\" id=\"iframe_seller_Qplay\" scrolling=\"no\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" width=\"" + width + "\" height=\"" + height + "\" style=\"width: " + width + "px; height: " + height + "px;\"></iframe>"
			+ "</div>";
		if (parent) {
			parent.$("#content").append(strHtml);
			setTimeout(function (e) { $(".quickSellerQplayLoading", parent.document).hide(); }, 1000);
		}
		else {
			$("#content").append(strHtml);
			setTimeout(function (e) { $(".quickSellerQplayLoading").hide(); }, 1000);
		}


	}
	else {
		//닫았다가 다시  연경우에는 기존 화면 그대로 다시 보여주고 끝.
		ControlUtil.displayObject(divId, true);
		$("#quickSellerQplay").css("position", position_style);
		$("#quickSellerQplay").css("top", top);

		if (type == "S")
			$("#quickSmartTabLoading").css("display", "");
		else
			$("#quickQplayLoading").css("display", "");

		if (parent) {
			if (parent.$get("iframe_seller_Qplay").src.split("?")[1] != url.split("?")[1]) {
				parent.$get("iframe_seller_Qplay").src = url;
			}
			else {
				parent.$("#quickSellerQplayLoading").css("display", "none");
				parent.$("#quickSmartTabLoading").css("display", "none");
			}
		}
		else {
			if ($get("iframe_seller_Qplay").src.split("?")[1] != url.split("?")[1]) {
				$get("iframe_seller_Qplay").src = url;
			}
			else {
				$("#quickSellerQplayLoading").css("display", "none");
			}
		}


	}
	try {
		$("#div_popup").draggable({ cursor: 'move' });
	}
	catch (ex) { }
}

Util.openSellerQplay = function (sell_cust_no, goodscode) {
	if (goodscode != null && goodscode != "" && goodscode != undefined) goodscode = "&goodscode=" + goodscode;
	else goodscode = "";
	var url = Public.convertNormalUrl("/SmartTab/default.aspx?sell_cust_no=") + sell_cust_no + goodscode;
	Util.openSmartTab(url);
}

Util.openSellerQplayInnerPopup = function (sell_cust_no, is_encoded) {
	if (is_encoded == undefined || is_encoded == null) is_encoded = false;
	var url = is_encoded == false ? Public.convertNormalUrl("/SmartTab/default.aspx?dp=list&seller_cust_no=") + encodeURIComponent(sell_cust_no)
					: Public.convertNormalUrl("/SmartTab/default.aspx?dp=list&seller_cust_no=") + sell_cust_no;
	Util.openSmartTab(url);
}

Util.resizeFromInnerPopup = function (width, height) {
	var width2 = parent.$("#frame_popup").width();
	var height2 = parent.$("#frame_popup").height();

	parent.$("#frame_popup").width(width2 + width);
	parent.$("#frame_popup").height(height2 + height);
}


Util.resizeToInnerPopup = function (width, height) {
	parent.$("#frame_popup").width(width);
	parent.$("#frame_popup").height(height);
}

Util.closeInnerPopup = function (frame, divId) {
	if (Util.__openInnerPopup_closeDelegate) {
		if (typeof (Util.__openInnerPopup_closeDelegate) == "string") {
			var frameId_index = "";
			if ($("#div_popup").length > 0 && Util.__openInnerPopup_count > 1) frameId_index += "_" + (Util.__openInnerPopup_count);
			eval(Util.__openInnerPopup_closeDelegate.replace("frame.", "$get('frame_popup" + frameId_index + "').contentWindow."))();
		}
		else
			Util.__openInnerPopup_closeDelegate();
	}

	if (!divId) {
		divId = "div_popup";
	}

	if (frame != undefined && frame != null) {
		frame.document.getElementById(divId).style.display = "none";

		if (frame.document.getElementById("div_screen") != null)
			frame.document.getElementById("div_screen").style.visibility = "hidden";
	}
	else {
		if ($('div[id^="div_popup"]').length > 1 && divId == "div_popup") {
			$('div[id^="div_popup"]').hide();
		}
		else {
			ControlUtil.displayObject(divId, false);
		}

		if ($get("div_screen") != null)
			Util.blindScreen(false);
	}
}

Util.OpenQuickView = function (gd_no) {
	Util.openInnerPopup(Public.convertNormalUrl("~/Goods/QuickView.aspx?goodscode=" + gd_no), 680, 395, false);
	return false;
}

Util.OpenQuickViewCallBack = function (gd_no, callback) {
	Util.openInnerPopup(Public.convertNormalUrl("~/Goods/QuickView.aspx?goodscode=" + gd_no + "&callback=" + callback + "&type=cb"), 680, 395, false);
	return false;
}

Util.OpenQuickReview = function (gd_no, global_yn) {
	if (global_yn != undefined && global_yn == "Y")
		Util.openInnerPopup(Public.convertNormalUrl("~/Goods/QuickReview.aspx?goodscode=" + gd_no + "&global_order_type=G"), 680, 395, false);
	else
		Util.openInnerPopup(Public.convertNormalUrl("~/Goods/QuickReview.aspx?goodscode=" + gd_no), 680, 395, false);
	return false;
}

Util.OpenPreview = function (gd_no, prmWidth, prmHeight, prmTitle) {
	if (prmWidth == null) {
		prmWidth = 920;
	}

	if (prmHeight == null) {
		prmHeight = 600;
	}

	if (prmTitle == null) {
		prmTitle = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "PreviewTitle");
		prmTitle = (prmTitle == null || prmTitle == "" ? "<i class=\"ic_preview\"></i>Preview" : prmTitle);
	}

	Util.openInnerPopupPreview(Public.convertNormalUrl("~/Goods/Goods.aspx?css_mode=preview&goodscode=" + gd_no), prmWidth, prmHeight, false, null, null, true, null, null, prmTitle, null);
}

Util.blindScreen = function (onoff, alpha) {
	var iWidth = $(document).width();
	var iHeight = $(document).height();
	var bDisp = false;

	if ($get("div_screen") == null) {
		bDisp = true;

		var img = alpha == 0 ? ETC.GetStaticImageURLRoot("/en/front/common/image/bg_alpha0.png") : ETC.GetStaticImageURLRoot("/en/front/common/image/bg_layer.png");

		var html = "<div id=\"div_screen\" style=\"width:100%; height:" + iHeight + "px; position:absolute; top:0; left:0; z-index:999; font-family:'MS PGothic'; line-height:16px; background:url('" + img + "')\"></div>";
		$(document.body).append(html);
	}

	if (onoff) {
		if (!bDisp) {
			$("#div_screen").height(iHeight);
		}

		$("#div_screen").show();
	}
	else {
		$("#div_screen").hide();
	}
}

// Set Category Combobox 
Util.SetCategory = function (class_cd, type, box_id) {
	var param = new RMSParam();

	param.add("Type", type);
	param.add("ClassCd", class_cd);

	var ret = RMSHelper.callWebMethod(Public.getServiceUrl("swe_CategoryAjaxService.asmx"), "GetCategoryList2", param.toJson());

	$("#" + box_id).get(0).length = 1;
	for (var i = 0; i < ret.Rows.length; i++) {
		$("#" + box_id).get(0).options[i + 1] = new Option(ret.Rows[i]["cd_nm"], ret.Rows[i]["cd"]);
	}
}

// PopupLogin창을 오픈한다.
Util.openLoginPopup = function (nextUrl) {
	var obj = Util.openPopup(Public.getLoginServerUrl("/Login/PopupLogin.aspx") + "?ReturnUrl=" + nextUrl, 430, 450, "PopupLogin", 200, 200);
}

Util.isValidEmail = function (email) {
	if (email.lastIndexOf(' ') > 0) {
		return false;
	}

	var regText = /^([\.0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,6}$/;
	return regText.test(email);
}

//Smart Window
Util.OpenSmarWindowEZCompare = function () {
	$("#quickInfo .group_wing .btn_mylst").click();
}

// list에 포함된 quickview와 ezcompare click event bind 공통 함수

// .options class하위의 quick과 ez클래스에 바인딩됨///////////////////////////
Util.SetDIvEzEvent = function () {
	if (parent) {
		$(".smtopt .quick" || ".smtoptwrap .quick" || ".details .quick").bind("click", function (e) {
			if ($(this).children("a").attr("tradway") == "T2")
				parent.Util.OpenQuickView($(this).children("a").attr("goodscode"));
			else
				parent.Util.OpenPreview($(this).children("a").attr("goodscode"));

			return false;
		});
	}
	else {
		$(".smtopt .quick" || ".smtoptwrap .quick" || ".details .quick").bind("click", function (e) {
			if ($(this).children("a").attr("tradway") == "T2")
				Util.OpenQuickView($(this).children("a").attr("goodscode"));
			else
				Util.OpenQuickView($(this).children("a").attr("goodscode"));

			return false;
		});
	}
}


//글로벌쪽에서 사용
Util.GlobalDivEzEvent = function () {

	$("#global_goods_list .thumb_area").hover(function () {
		$(".smtoptwrap", this).show();
	}, function () {
		$(".smtoptwrap", this).hide();
	});

	//quick view
	$("#global_goods_list .thumb_area .smtoptwrap").children("div.smtopt").find('.quick').bind("click", function (e) {
		if ($(this).children("a").attr("tradway") == "T2")
			Util.OpenQuickView($(this).children("a").attr("goodscode"));
		else
			Util.OpenPreview($(this).children("a").attr("goodscode"));

		return false;
	});
}

Util.SetDivItemListCommon = function () {
	/* 
	리스트 옵션보기 
	bd_list01 : SearchResult.ascx (item list)
	SearchQuery.aspx (스마트서치 포커스 아이템)
	AuctionResult.ascs (auction item list)
	GoodsList.ascx (plus item list)
	ViewToday.ascx (TodaysView.aspx / ViewToday.aspx)
	PopupCoShipping.aspx
	*/
	if ($(".bd_list01").length > 0) { //old
		$(".bd_list01 .smtopt.smtopt_lst").hover(function () {
			$(this).css("display", "");
			//$(this).addClass("smtopt_off");
			$(this).removeClass("smtopt_off");
			$(this).find(".new").css("display", "");

			if ($(this).find(".added").css("display") == "none") {
				$(this).find(".ez").css("display", "");
			} else {
				$(this).find(".ez").css("display", "none");
			}

		}, function () {
			//$(this).removeClass("smtopt_off");
			$(this).addClass("smtopt_off");
			$(this).find(".ez").css("display", "none");
			$(this).find(".new").css("display", "none");

			return false;
		});
	}

	if ($(".bd_lst").length > 0) { //new
		$(".bd_lst .smtopt.smtopt_lst").hover(function () {
			$(this).css("display", "");
			//$(this).addClass("smtopt_off");
			$(this).removeClass("smtopt_off");
			
			//m18 모든창 띄움
			if(__PAGE_VALUE.VIEW_SITEID != "m18")
				$(this).find(".new").css("display", "");

			if ($(this).find(".added").css("display") == "none") {
				$(this).find(".ez").css("display", "");
			} else {
				$(this).find(".ez").css("display", "none");
			}

		}, function () {
			//$(this).removeClass("smtopt_off");
			$(this).addClass("smtopt_off");
			$(this).find(".ez").css("display", "none");

			//m18 모든창 띄움
			if (__PAGE_VALUE.VIEW_SITEID != "m18")
				$(this).find(".new").css("display", "none");

			return false;
		});
	}
}

Util.setPanoramaMouseOverEvent = function () {
	///새롭게 Gallay 형에서 추가되서 Add하는거 by Kim Young Hee
	//$("[class^='bd_gallery'] li .thumb_area").hover(function () {
	$("[class^='bd_gallery'] li .thumb_area, [class^='bd_glr'] li .thumb_area, [class^='bd_glr'] li .thumb_area").hover(function () {
		if ($(this).attr("option_view_pop_yn") == "N") {
			$(this).removeClass(" hover");
			$(".smtoptwrap", this).hide();
		} else {
			$(this).addClass(" hover");
			$(".smtoptwrap", this).show();

			$attribute_adult_item = $(this).parent().attr("Adult_NA");
			if ($attribute_adult_item != "NA") {
				//기본움직이는이미지노출
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src").replace("_s", "") });
			}
		}

		//조건부 배송에 배송비가 있을때
		shippingMouseOver($(this));

		return false;
	}, function () {
		$(this).removeClass(" hover");
		$(".smtoptwrap", this).hide();

		$attribute_adult_item = $(this).parent().attr("Adult_NA");
		if ($attribute_adult_item != "NA") {
			//정지이미지로원복
			var imgObj = $(this).find("img");
			imgObj.attr({ "src": imgObj.attr("gd_src") });
		}
		return false;
	});

	//새롭게 추가된 Panorama 형식 EventHandler
	$("[class^='bd_panorama'] li").hover(function () { //old
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

	$("div.bd_pnrm li").hover(function () { //new
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

Util.MouseoverDeliveryFee = function () {
	//배송비 표시 레이어 체크
	$("[class^='bd_gallery'] li .info .info_lt .shipping, [class^='bd_glr'] li .info .info_lt .shipping, [class^='bd_glr'] li .info .etc .shipping, [class^='bd_glr'] li .etc .shipping").hover(function () {
		var optionCheckDIv = $(this).children("div").hasClass("ly_sp");
		if (optionCheckDIv != undefined) {
			$(this).addClass(" hover");
		}
	}, function () {
		var optionCheckDIv = $(this).children("div").hasClass("ly_sp");

		if (optionCheckDIv != undefined) {
			$(this).removeClass(" hover");
		}
	});
}

Util.MouseoverListDeliveryInfo = function () {
	//배송비 표시 레이어 체크
	$("[class^='bd_lst'] td .shipping").bind("mouseover", function (e) {
		var goodscode = $(this).parent().parent().parent().parent().parent().attr("goodscode");

		var param = new RMSParam();
		param.add("gdno", goodscode);
		param.add("svc_nation_cd", GMKT.ServiceInfo.nation);

		var result = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "GetDeliveryGoodsForList", param.toJson());
		var shippingFromStr = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "Shipping From");

		if (result != null && result != "" && result.length > 0) {
			var deliveryDefaultCode = result[2]; //배송방식

			var deliveryOptionType = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "NO_script");

			if (deliveryDefaultCode.trim() == "RM") {
				deliveryOptionType = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "RM_script");
			}
			else if (deliveryDefaultCode.trim() == "EX") {
				deliveryOptionType = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "EX_script");
			}
			else if (deliveryDefaultCode.trim() == "TC") {
				deliveryOptionType = MultiLang.findCommonResource("Master/DefaultMasterPage.master", "TC_script");
			}

			$(this).attr("title", shippingFromStr + " : " + result[0] + "\r\n" + (result[1] != "" ? result[1] + "\r\n" : "") + "(" + deliveryOptionType + ")");
		}
	});
}

//방문수령 가능 레이어 띄우는거 
Util.MouseoverPayBack = function () {
	$("[class^='bd_lst'] dl .shipping .pckup, [class^='bd_gallery'] li .info .info_lt .pckup, [class^='bd_glr'] li .info .opt .info_lt .shipping .pckup, [class^='bd_glr'] li .info .info_lt .pckup, [class^='bd_glr'] li .info .info_lt .pckup").hover(function () {
		var optionCheckDIv = $(this).children("div").hasClass("ly");

		if (optionCheckDIv != undefined) 
			$(this).children("div").show();
		
	}, function () {
		var optionCheckDIv = $(this).children("div").hasClass("ly");

		if (optionCheckDIv != undefined)
			$(this).children("div").hide();
	});
}

function shippingMouseOver(obj) {
	var shippingDiv = obj.children("div .details").children("div .info").children("div .shipping");

	$(shippingDiv).hover(function () {
		if ($(shippingDiv).children("div").hasClass("ly_sp")) {
			$(shippingDiv).addClass(" hover");
		}
	}, function () {
		if ($(shippingDiv).children("div").hasClass("ly_sp")) {
			$(shippingDiv).removeClass(" hover");
		}
	});
}

Util.setEZclickEvent = function () {
	$('div.smtopt').find('.ez').bind("click", function (e) {
		Util.AddToEZView($(this).children("a").attr("goodscode"), $(this).children("a").attr("img_contents_no"), $(this).children("a").attr("sm_price"), $(this).children("a").attr("Adult_NA"));
		$(this).css("display", "none");
		$(this).parent("div").find(".added").css("display", "");
		if (typeof (MyListItems) != "undefined") {
			try {
				if (MyListItems != null && MyListItems.Init != null) {
					MyListItems.Init();
				}
			} catch (ee) {
			}
		}

		return false;
	});
}

// clicked 아이템 처리
Util.setClickedViewEvent = function () {
	if ($('.clicked').length > 0) {
		/* Clicked Item이 있으면 태그를 노출시킵니다. */
		$('.clicked').children('div.state[name="clicked"]').show();

		/* Clicked Item tag와 테두리 강조 style 삭제 action */
		$('.clicked').find('a.btn_close').bind("click", function () {
			$(this).parent().parent().removeClass("clicked");
			$(this).parent().hide();
			return false;
		});
	}
}

Util.GetLastViewGoods = function () {
	var prevItem = Util.getCookie("lastViewGoods");

	if (prevItem == "")
		return;
	else {
		//1) lastviewed 아이템 체크
		var goodscode = 0;
		goodscode = prevItem.split(':')[0];

		if (goodscode != "") {
			if ($('#content').attr('class') == 'w_980') { //new
				// LIST Type
				if ($('dd[goodscode]').length > 0) {
					$('dd[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastviewed > clicked
					$('dd[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
					$('dd[goodscode=' + goodscode + ']').addClass("lastviewed");
					$('dd[goodscode=' + goodscode + ']').children('div.state[name="lastviewed"]').show();

					Util.setCookie("lastViewGoods", "");
				}
				// gallery / panorama
				if ($('li[goodscode]').length > 0) {
					$('li[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastviewed > clicked
					$('li[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
					$('li[goodscode=' + goodscode + ']').addClass("lastviewed");
					$('li[goodscode=' + goodscode + ']').children('div.state[name="lastviewed"]').show();

					Util.setCookie("lastViewGoods", "");
				}

				//3) 클래스 제거
				$('li.lastviewed').find('a.btn_close').bind("click", function () {
					$(this).parent().parent().removeClass("lastviewed");
					$(this).parent().hide();
					return false;
				});

			} else { //old
				// LIST Type
				if ($('dd[goodscode]').length > 0) {
					$('dd[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastViewed > clicked
					$('dd[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
					$('dd[goodscode=' + goodscode + ']').addClass("lastViewed");
					$('dd[goodscode=' + goodscode + ']').children('div.state[name="lastViewed"]').show();

					Util.setCookie("lastViewGoods", "");
				}
				// gallery / panorama
				if ($('li[goodscode]').length > 0) {
					$('li[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastViewed > clicked
					$('li[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
					$('li[goodscode=' + goodscode + ']').addClass("lastViewed");
					$('li[goodscode=' + goodscode + ']').children('div.state[name="lastViewed"]').show();

					Util.setCookie("lastViewGoods", "");
				}

				//3) 클래스 제거
				$('li.lastViewed').find('a.btn_close').bind("click", function () {
					$(this).parent().parent().removeClass("lastViewed");
					$(this).parent().hide();
					return false;
				});
			}
		}
	}
}

/////////////////////////////////////////////////////////////////////////////////////////


Util.SetEzEvent = function () {
	if (parent) {
		$(".options .quick").bind("click", function (e) {
			$(this).parent("ul").find(".ez").css("display", "none");
			$(this).parent("ul").find(".added").css("display", "");
			parent.Util.OpenQuickView($(this).children("a").attr("goodscode"));
			return false;
		});
	}
	else {
		$(".options .quick").bind("click", function (e) {
			$(this).parent("ul").find(".ez").css("display", "none");
			$(this).parent("ul").find(".added").css("display", "");
			Util.OpenQuickView($(this).children("a").attr("goodscode"));
			return false;
		});
	}
}

// 상품 리스트 공통 처리 영역
// 마우스오버시 option 조회 / clicked item / last viewed item
Util.SetItemListCommon = function () {
	/* 
	리스트 옵션보기 
	bd_list01 : SearchResult.ascx (item list)
	SearchQuery.aspx (스마트서치 포커스 아이템)
	AuctionResult.ascs (auction item list)
	GoodsList.ascx (plus item list)
	ViewToday.ascx (TodaysView.aspx / ViewToday.aspx)
	PopupCoShipping.aspx
	*/
	if ($(".bd_list01").length > 0) {
		$(".bd_list01 .options").hover(function () {
			$(this).addClass("options_on")
					.css("display", "");

			$(this).find(".new").css("display", "");

			if ($(this).find(".added").css("display") == "none") {
				$(this).find(".ez").css("display", "");
			} else {
				$(this).find(".ez").css("display", "none");
			}
		}, function () {
			$(this).removeClass("options_on");
			$(this).find(".ez").css("display", "none");
			$(this).find(".new").css("display", "none");

			return false;
		});
	}

	/* 
	board : Gallery형 옵션보기 
	bd_gallery04 : SearchResult.ascx (item gallery)
	SearchQuery.aspx (스마트서치 포커스 아이템)
	AuctionResult.ascs (auction item gallery)
	bd_gallery01 : GoodsList.ascx (plus item gallery)
	PlusShop.aspx (plus item)
	bd_gallery06 : BestSellers/Default.aspx (베셀)
	Category/Group.aspx (그룹페이지)
	BestSeller.ascs(투딜 베셀)
	*/
	if ($(".bd_gallery04, .bd_gallery01, .bd_gallery06, .bd_gallery6, .galleryList02, .bd_glr4").length > 0) {
		$(".bd_gallery04 .thumb, .bd_gallery01 .thumb, .bd_gallery06 .thumb, .bd_gallery6 .thumb, .galleryList02 .thumb, .bd_glr4 .thumb").hover(function (e) {
			$(this).children(".options").addClass("options_on")
										.css("display", "");

			// added 표시 된 경우 ez 아이콘 숨김
			if ($(this).find(".added").css("display") != "none") {
				$(this).find(".ez").css("display", "none");
			}

			$attribute_adult_item = $(this).parent().attr("Adult_NA");
			if ($attribute_adult_item != "NA") {
				//기본움직이는이미지노출
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src").replace("_s", "") });
			}
			return false;
		}, function (e) {
			$(this).children(".options").removeClass("options_on")
										.css("display", "none");

			$attribute_adult_item = $(this).parent().attr("Adult_NA");
			if ($attribute_adult_item != "NA") {
				//정지이미지로원복
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src") });
			}
			return false;
		});
	}

	/*
	board : 파노라마형 옵션보기 
	bd_panorama01 : SearchResult.ascx (panorama 4단)
	bd_panorama01_01 : SearchResult.ascx (panorama 3단)
	bd_panorama1 : SearchResult.ascx (panorama 3단, 4단)
	bd_gallery03, bd_gallery03_row4, bd_gallery03_row3, bd_gallery03_mix4 : BestSellers/TodaysDeal.aspx (리스트:기획전 유형)
	Special.aspx (기획전 유형)
	*/
	if ($(".bd_panorama1, .bd_panorama01, .bd_panorama01_01, .bd_gallery03 .thumb, .bd_gallery03_row4 .thumb,.bd_gallery03_row3 .thumb,.bd_gallery03_mix4 .thumb").length > 0) {
		$(".bd_panorama1 li[goodscode], .bd_panorama01 li[goodscode], .bd_panorama01_01 li[goodscode], .bd_gallery03 .thumb, .bd_gallery03_row4 .thumb,.bd_gallery03_row3 .thumb,.bd_gallery03_mix4 .thumb").hover(function () {
			$(this).addClass("selected");
			$(this).find(".options").addClass("options_on")
									.css("display", "");

			// added 표시 된 경우 ez 아이콘 숨김
			if ($(this).find(".added").css("display") != "none") {
				$(this).find(".ez").css("display", "none");
			}

			$attribute_adult_item = $(this).attr("Adult_NA");

			if ($attribute_adult_item != "NA") {
				//기본움직이는이미지노출
				var imgObj = $(this).find("img");
				imgObj.attr({ "src": imgObj.attr("gd_src").replace("_s", "") });
			}
		}, function () {
			$(this).removeClass("selected");
			$(this).find(".options").removeClass("options_on")
									.css("display", "none");
			//정지이미지로원복
			var imgObj = $(this).find("img");
			imgObj.attr({ "src": imgObj.attr("gd_src") });
			return false;
		});
	}

	// clicked 아이템 처리
	if ($('.clicked').length > 0) {
		/* Clicked Item이 있으면 태그를 노출시킵니다. */
		$('.clicked').children('div.state[name="clicked"]').show();

		/* Clicked Item tag와 테두리 강조 style 삭제 action */
		$('.clicked').find('a.btn_close').bind("click", function () {
			$(this).parent().parent().removeClass("clicked");
			$(this).parent().hide();
			return false;
		});
	}

	//last viewed 아이템 처리
	///쿠키에서 이제까지 본 리스트를 가져와 last viwed item 강조를 구현한다.
	///
	///1) 쿠키에서 last viewed item을 가져온다
	///2) Traversing을 통해 해당 li에 .addClass('lastViewed') (=css를 통한 강조 구현)
	///3) last viewed item 삭제 action이 발생할 경우 삭제 구현
	var prevItem = Util.getCookie("lastViewGoods");

	if (prevItem == "")
		return;
	else {
		//1) lastviewed 아이템 체크
		var goodscode = 0;
		goodscode = prevItem.split(':')[0];
		if (goodscode != "") {
			// LIST Type
			if ($('dd[goodscode]').length > 0) {
				$('dd[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastViewed > clicked
				$('dd[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
				$('dd[goodscode=' + goodscode + ']').addClass("lastViewed");
				$('dd[goodscode=' + goodscode + ']').children('div.state[name="lastViewed"]').show();

				Util.setCookie("lastViewGoods", "");
			}
			// gallery / panorama
			if ($('li[goodscode]').length > 0) {
				$('li[goodscode=' + goodscode + ']').removeClass("clicked"); //우선순위priority 경쟁에서 lastViewed > clicked
				$('li[goodscode=' + goodscode + ']').children('div.state[name="clicked"]').hide();
				$('li[goodscode=' + goodscode + ']').addClass("lastViewed");
				$('li[goodscode=' + goodscode + ']').children('div.state[name="lastViewed"]').show();

				Util.setCookie("lastViewGoods", "");
			}

			//3) 클래스 제거
			$('li.lastViewed').find('a.btn_close').bind("click", function () {
				$(this).parent().parent().removeClass("lastViewed");
				$(this).parent().hide();
				return false;
			});
		}
	}
}

//
// EZ View에 추가
//
var ezSetTimeoutVariable = 0;
Util.AddToEZView = function (goodscode, img_contents_no, sm_price, adult_na, ani_effect) {
	var d = new Date();
	var ez_ec = $("#quickInfo li.ez a strong").html();
	var result = 0;

	// 로그인 된 경우 DB에 저장하고 ez_ec 카운트 증가 / 로그인 되지 않은 경우 ezview에 추가할 때 카운트 증가 
	// 카운트 변경이 있을 때 result -1로 변경

	if (Public.isLogin()) {
		var param = new RMSParam();
		param.add("gd_no", goodscode);

		result = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "SetViewTodayGoods", param.toJson());

		if (result < 0) {
			ez_ec = (!isNaN(ez_ec)) ? ++ez_ec : 1;
		}

	} else {
		var ezview = Util.getCookie("ezview");
		if (ezview.length < 10) ezview = "";
		if (ezview != "") {
			if (ezview.indexOf(goodscode) < 0) {
				if (ezview.split(",") >= 50)
					ezview = ezview.substring(0, ezview.lastIndexOf(","));

				ezview = goodscode + ":" + img_contents_no + ":" + sm_price + "," + ezview;
				Util.setCookie("ezview", ezview); //Util.AddToEZView()

				ez_ec = (!isNaN(ez_ec)) ? ++ez_ec : 1;
				result = -1;
			}
		}
		else {
			ezview = goodscode + ":" + img_contents_no + ":" + sm_price;
			Util.setCookie("ezview", ezview); //Util.AddToEZView()

			ez_ec = (!isNaN(ez_ec)) ? ++ez_ec : 1;
			result = -1;
		}
	}

	if (typeof (MyListItems) != "undefined") {
		try {
			if (MyListItems != null && MyListItems.Init != null) {
				MyListItems.Init();
			}
		}
		catch (ee) {
		}
	}
}

Util.AddToWishList = function (cust_no, group_no, gd_no, login_id, callback) {
	if (cust_no == "") return -99;	// cust_no가 없는 경우 -99 return;

	var param = new RMSParam();
	param.add("group_id", group_no);
	param.add("gd_no", gd_no);

	var result = RMSHelper.callWebMethod(Public.getServiceUrl("swe_GoodsAjaxService.asmx"), "SetWishListItem", param.toJson());

	//	추가했을 때 카운트 증가, 추가/수정된 경우 애니메이션
	if (result >= 0) {
		var ez_ws = Util.getCookie("ez_ws");
		if (result == 0) { // 신규 추가일때만 카운트 증가
			ez_ws = (!isNaN(ez_ws)) ? ++ez_ws : 1;
			Util.setCookie("ez_ws", ez_ws);
		}
	}

	if (result == 1) result = 0; // 1의 경우도 성공으로 처리하기 위해서 return은 0으로 (update 된 경우임)

	if (typeof (callback) == "function") callback(result)
	else return result;
}


// Follow Shop 추가 공통
Util.__AddToFavoriteSeller = function (sell_cust_no, callback) {	//기획전등에 사용하기 위한 용도의 function
	if (!Public.isLogin()) {
		Public.goLoginPage();
		return;
	}
	var buy_cust_no = Public.getCustNo();
	sell_cust_no = decodeURIComponent(sell_cust_no);

	Util.AddToFavoriteSeller(buy_cust_no, sell_cust_no);

	if (typeof (callback) == "function")
		callback();
}

Util.AddToFavoriteSeller = function (buy_cust_no, sell_cust_no, callback) {
	if (buy_cust_no == undefined) {
		if (!Public.isLogin()) {
			Public.goLoginPage();
			return;
		}
	}

	var param = new RMSParam();
	param.add("cust_no", buy_cust_no);
	param.add("sell_cust_no", sell_cust_no);

	RMSHelper.asyncCallWebMethod(Public.getServiceUrl("swe_MiniShopAjaxService.asmx"), "AddToFavoriteSeller", param.toJson(), function (result, svc, methodName, xmlHttpasync) {
		if (typeof (callback) == "function")
			callback(result, svc, methodName, xmlHttpasync);
	});
}

Util.AddToCart = function (gd_no, order_cnt, sell_price, inventory_seq_no, sel_no, sel_noT_s, selvalueT_s, global_order_type, callback) {
	Util.AddToCart2(gd_no, order_cnt, sell_price, inventory_seq_no, sel_no, sel_noT_s, selvalueT_s, "", "", "", global_order_type, "0", callback);
}

// open_smart_cart 추가 (Y인 경우 스마트 카트가 열림)
Util.AddToCart2 = function (gd_no, order_cnt, sell_price, inventory_seq_no, sel_no, sel_noT_s, selvalueT_s, pos_shop_cd, pos_class_cd, pos_class_kind, global_order_type, cost_basis_no, callback, open_smart_cart, coupon_no, cart_coupon_yn, cart_coupon_no, cart_cost_basis_no, multi_inventory_yn, multi_inventory_seq_no, delivery_option_no, multi_items) {
	if (cart_coupon_yn == null) {
		cart_coupon_yn = "N";
		cart_coupon_no = "";
		cart_cost_basis_no = "0";
	}

	var param = new RMSParam();
	param.add("order_way_kind", "PAK");
	param.add("gd_no", gd_no);
	param.add("order_cnts", order_cnt);
	param.add("sell_price", sell_price);
	param.add("inventory_seq_no", inventory_seq_no);
	param.add("sel_no", sel_no);
	param.add("sel_noT_s", sel_noT_s);
	param.add("sel_valueT_s", selvalueT_s);
	param.add("pos_shop_cd", pos_shop_cd);
	param.add("pos_class_cd", pos_class_cd);
	param.add("pos_class_kind", pos_class_kind);
	param.add("global_order_type", global_order_type);
	param.add("cost_basis_no", cost_basis_no);
	param.add("coupon_no", coupon_no);
	param.add("cart_coupon_yn", cart_coupon_yn);
	param.add("cart_coupon_no", cart_coupon_no);
	param.add("cart_cost_basis_no", cart_cost_basis_no);
	param.add("multi_inventory_yn", multi_inventory_yn);
	param.add("multi_inventory_seq_no", multi_inventory_seq_no);
	param.add("multi_delivery_option_no", delivery_option_no);
	param.add("multi_items", multi_items);

	var result = RMSHelper.callWebMethod(Public.getServiceUrl("swe_OrderAjaxService.asmx"), "InsertCart2", param.toJson());

	if (result != null && result.ResultCode == 0) {
		var ez_ct, ez_cart_items
			, ez_ct_global, ez_cart_items_global;

		ez_ct = Util.getCookie("ez_ct");
		ez_cart_items = Util.getCookie("ez_cart_items");

		ez_ct_global = Util.getCookie("ez_ct_global");
		ez_cart_items_global = Util.getCookie("ez_cart_items_global");

		if (global_order_type == "G") {
			ez_ct_global = (!isNaN(ez_ct_global)) ? ++ez_ct_global : 1;
			ez_cart_items_global += gd_no + ",";
		} else {
			ez_ct = (!isNaN(ez_ct)) ? ++ez_ct : 1;
			ez_cart_items += gd_no + ",";
		}

		Util.setCookie("ez_ct", ez_ct);
		Util.setCookie("ez_cart_items", ez_cart_items);

		Util.setCookie("ez_ct_global", ez_ct_global);
		Util.setCookie("ez_cart_items_global", ez_cart_items_global);

		// 스마트 윈도우 열어주는 기준으로 effect 구분
		if (open_smart_cart == "Y") {
			Util.OpenSmartWindow($("#quickInfo a.btn_cart"), "cartOn", Public.convertNormalUrl("~/order/smart/smartcart.aspx?type=addcart&global_order_type=") + global_order_type);
		} else {
			// 카트의 카운트가 늘어날 때 날개의 숫자를 변경하면서 이벤트 효과
			$("#quickInfo a.btn_cart").html("<span class=\"num fs_10 ff_ara\" >" + ez_ct + ez_ct_global + "</span>");
			Util.RightWingEffect($("#quickInfo a.btn_cart"));
		}

		if (typeof (callback) == "function")
			callback(result);
		else
			return result;
	}
	else {
		alert(result.ResultMsg);
		return result;
	}
}

var _ani_add_tid = new Array(), _ani_remove_tid = new Array();
Util.RightWingEffect = function (target) {
	try {
		var i = 0;
		var blink_times = 7;
		var time_to_blink = 400;
		while (i < blink_times * 2) {
			clearTimeout(_ani_add_tid[i]);
			clearTimeout(_ani_remove_tid[i]);
			_ani_add_tid[i] = setTimeout(function () { target.addClass("on"); }, time_to_blink * i);
			_ani_remove_tid[i] = setTimeout(function () { target.removeClass("on"); }, time_to_blink * (i + 1));
			i = i + 2;
		}
	} catch (e) { }

}

var innerPopup_zindex = 1000;


Util.OpenSmarWindowFollowShop = function () {
	if ($("#quickSmartView") == null) return;
	$("#quickInfo .list .follow").click();
}

//Smart Window
Util.SmartWindowInit = function () {
	if (Public.isLogin() && $("#quickInfo div.qPost a.message").html() != "0") $("#quickInfo div.qPost a.message").show();
	/* 날개배너 A링크 Selected */

	// QPost
	$("#quickInfo .qPost").bind("click", function (e) {
		$("#quickSmartView").hide();
		$("#quickSmartView").removeClass();
		$("#quickInfo a").removeClass("on");
		$(this).children("p").children("a").addClass("on"); //safe
	});

	// Cart
	$("#quickInfo a.btn_cart").bind("click", function (e) {
		var global_order_type = "L",
			$global_order_type = $("#global_order_type");

		if ($global_order_type.length !== 0 && ($global_order_type.val() == "L" || $global_order_type.val() == "G")) {
			global_order_type = $global_order_type.val();
		}

		Util.OpenSmartWindow($(this), "cartOn", Public.convertNormalUrl("~/order/smart/smartcart.aspx?global_order_type=" + global_order_type));
	});
	//EZ Compare
	$("#quickInfo .group_wing a.btn_mylst").bind("click", function (e) {
		Util.OpenSmartWindow($(this), "ezOn", Public.convertNormalUrl("~/Smart/EZView.aspx"));
	});
	//Wish List
	$("#quickInfo .list .wishlist").bind("click", function (e) {
		Util.OpenSmartWindow($(this), "wishOn", Public.convertNormalUrl("~/Smart/WishList.aspx"));
	});

	//Follow Shop
	$("#quickInfo .list .follow").bind("click", function (e) {
		Util.OpenSmartWindow($(this), "followOn", Public.convertNormalUrl("~/Smart/FollowShop.aspx"));
	});
}

Util.OpenSmartWindow = function (obj, className, src, alwaysOn) {
	if ($("#quickSmartView").attr("class") == className && alwaysOn != true) {
		Util.CloseSmartWindow();
	}
	else {

		$("#quickInfo .list ul li, #quickInfo .btn_cart, #quickInfo .group_wing .btn_mylst, #view_today .fs_10").removeClass("on");

		$("#quickSmartView").removeClass();
		$("#quickSmartView").addClass(className);
		$("#quickSmartView").show();
		$("#quickSmartView").css("z-index", innerPopup_zindex);
		innerPopup_zindex++;

		obj.addClass("on");

		if (className == "cartOn" || $("#quickSmartView iframe").attr("src") != src) {
			$("#quickSmartLoading").show(); // now, hide the loading image
			$("#quickSmartView iframe").attr({ "src": src });
		}
		else {
			if (className == "ezOn")
				$get("iframe_smartwindow").contentWindow.EZ.init();
			if (className == "wishOn")
				$get("iframe_smartwindow").contentWindow.WishList.init();
			if (className == "followOn")
				$get("iframe_smartwindow").contentWindow.FollowShop.init();
			if (className == "cartOn") {
				$get("iframe_smartwindow").contentWindow.Cart.init();
			} if (className == "vtOn")
				$get("iframe_smartwindow").contentWindow.EZ.init();
		}

		Util.clickOpenSmartView = true;

		$("body").click(function () {
			if (!Util.clickOpenSmartView) {
				Util.CloseSmartWindow();
			}
			setTimeout("Util.setClickOpenSmartView(false)", 100);
		});

	}
}

Util.CloseSmartWindow = function () {
	$("#quickSmartView").hide();
	$("#quickSmartView").removeClass();
	$("#quickInfo a.btn_cart, #quickInfo .group_lst a, #quickInfo .group_wing .btn_mylst").removeClass("on");

	if (typeof (MyListItems) != "undefined") {
		try {
			if (MyListItems != null && MyListItems.Init != null) {
				MyListItems.Init();
			}
		} catch (ee) {
		}
	}

	Util.clickOpenSmartView = false;
	$("body").unbind("click");
}

Util.setClickOpenSmartView = function (val) {
	Util.clickOpenSmartView = val;
}

Util.ResetSmartWindowPosition = function (type) {
	if (type == "ezCompareOn") {
		$("#quickSmartView").css("left", "-734px");
		$("#quickSmartView").css("width", "721px");
		$("#quickSmartLoading").css("width", "717px");
		$("#quickSmartView iframe").css("width", "717px");
		$("#quickSmartView iframe").css("height", "496px");
	} else {
		$("#quickSmartView").css("left", "-458px");
		$("#quickSmartView").css("width", "445px");
		$("#quickSmartLoading").css("width", "441px");
		$("#quickSmartView iframe").css("width", "441px");
		$("#quickSmartView iframe").css("height", "496px");
	}
}


Util.convDataTableToHashTable = function (dt) {
	var ret = new Array();

	for (var i = 0; i < dt.Rows.length; i++) {
		ret[dt.Rows[i]["field_nm"]] = dt.Rows[i]["field_value"];
	}

	return ret;
}

Util.getSafeHashData = function (data, field) {
	if (data[field])
		return data[field];

	return "";
}


Util.giosisUrlEncode = function (data) {
	return data.replace(/_g_/g, "_g_8_").replace(/\+/g, "_g_1_").replace(/\//g, "_g_2_").replace(/=/g, "_g_3_").replace(/&/g, "_g_4_").replace(/</g, "_g_5_").replace(/>/g, "_g_6_").replace(/@/g, "_g_7_");
}

Util.giosisUrlDecode = function (data) {
	return data.replace(/_g_1_/g, "+").replace(/_g_2_/g, "/").replace(/_g_3_/g, "=").replace(/_g_4_/g, "&").replace(/_g_5_/g, "<").replace(/_g_6_/g, ">").replace(/_g_7_/g, "@").replace(/_g_8_/g, "_g_");
}

//
// 아래 다섯개의 창안에서만 새창이 열리게 컨트롤한다.
// target="special" / "best" / "goods" / "minishop" / "todayssale"
//
Util.openNewLink = function (obj) {
	window.location.href = obj.href;
	/*
	var url = obj.href;
	var target = obj.target;
	if($.browser.msie || $.browser.mozilla){
	Util.newTab(url,target);
	return false;
	}
	else if($.browser.safari) {
	window.open(url, target);
	return false;
	}
	else{
	obj.target="";
	return true;
	}
	*/
}

// target 이름을 판단하여 링크 이벤트 추가 여부
// target="_q-name" 으로 들어오면 "name"만 구분함
// opera는 적용안됨.
// target="_qlink"로 통일 (HyunSoo)
Util.setOpenNewLinkEvent = function () {
	var new_link_tag = $("a[target|='_qlink']");
	var new_link_tag_length = new_link_tag.length;
	var attr_name = "target";
	var attr_value = "_blank";

	//m18로 제한뒀던 부분을 제한을 품
	//if (__PAGE_VALUE.VIEW_SITEID == "m18") {
	//	attr_name = "target";
	//	attr_value = "_blank";
	//}
	//	else if (($.browser.msie && parseInt($.browser.version) > 7) || $.browser.mozilla || $.browser.safari || $.browser.opera) {
	//		attr_name = "onclick";
	//		attr_value = "return Util.newTab(this, event);";
	//	}
	for (var i = 0; i < new_link_tag_length; i++) {
		new_link_tag.eq(i).attr(attr_name, attr_value);
	}
}

// 크롬 및 오페라은 window.open만 실행
// 그외는 target이름으로 window 존재 여부 체크
// IE/opera의 팝업 차단에 대비하기 위해서, exsitingWindow가 null(즉 팝업이 막힌 경우) href에 url을 줘서 링크 되도록 처리함.
var __special_new_tab = false, __brand_new_tab = false;
Util.newTab = function (obj, event) {
		$(obj).attr("target", "_blank");
		return true;
	//URL跳转处理
	var url = $(obj).attr("href");
	var tabname = Util.getUrlTargetName(url);

	/* shop을 제외하곤 한번 열린거는 안열어준다.*/
	if (tabname == "brand") {
		if (__brand_new_tab) {
			$(obj).removeAttr("target");
			return true;
		} else {
			__brand_new_tab = true;
		}
	}
	if (tabname == "special") {
		if (__special_new_tab) {
			$(obj).removeAttr("target");
			return true;
		} else {
			__special_new_tab = true;
		}
	}

	// 처음 진입 페이지에서만 새창 열기 동작
	// shop을 제외하고는 referrer가 qoo10인 페이지들에서는 새창 열기 없이 자기 창에서 링크가 열린다.
	if (document.referrer.indexOf(__PAGE_VALUE.COOKE_DOMAIN) > 0) {
		// 미니샵용으로 열어준것도 새창 열기 없음
		if (window.name.indexOf("_shop") > 0) {
			$(obj).removeAttr("target");
			return true;
		}

		if (tabname == "shop") {
			tabname = window.name + "_shop";
		}
		else {
			$(obj).removeAttr("target");
			return true;
		}
	}

	// 브랜드는 열리는 탭의 name은 shop으로..
	if (tabname == "brand")
		tabname = "shop";

	//if (window.name == tabname) { window.name = ""; tabname = ""; }
	if (event.ctrlKey == 1) { //do not handdle popups when control key was pressed
		return true;
	}
	else if ($.browser.safari) {
		$(obj).attr("target", tabname);
		return true;
	}
	else {
		if (tabname == "") {
			$(obj).removeAttr("target");
			return true;
		}
		if (!window.popups) window.popups = {};
		if (window.popups[tabname]) {
			try {
				window.popups[tabname].close();
				window.popups[tabname] = window.open(url, tabname);
			}
			catch (e) {
				window.location.href = url;
				return true;
			};
		} else {
			window.popups[tabname] = window.open(url, tabname);
		}
		return false;
	}
}
//URL로 새창열기 창의 타겟이름을 정함
Util.getUrlTargetName = function (url) {
	var tabname = "";
	url = url.toLowerCase();
	if (url.indexOf("/sp/") >= 0 || url.indexOf("/special/special.aspx") >= 0) {
		tabname = "special";
	}
	else if (url.indexOf("search/brand.aspx") >= 0) {
		tabname ="brand"
	}
	else if (url.indexOf("/shop/") >= 0 || url.indexOf("/minishop/") >= 0) {
		tabname = "shop";
	}
	return tabname;
}

var overReviewCnt = false, overReviewTimeout = null;
Util.mouseOverReviewCnt = function (gd_no, global_yn) {
	overReviewCnt = true;
	overReviewTimeout = setTimeout("Util.openQuickViewByReviewCnt(" + +gd_no + ", '" + global_yn + "')", 400);
}
Util.mouseOutReviewCnt = function() 
{
		clearTimeout(overReviewTimeout);
		overReviewCnt = false;
}
Util.openQuickViewByReviewCnt = function(gd_no, global_yn) 
{
	if (overReviewCnt)
	Util.OpenQuickReview(gd_no, global_yn);
}

Util.shoppingAppLink = function (url, type) {
	try {
		MobileUtil.shoppingAppLink("qoo10").send(url, type);
	} catch (e) {
		throw "Not Mobile";
	}
}

Util.qstyleAppLink = function (url, type) {
	try {
		MobileUtil.shoppingAppLink("qstyle").send(url, type);
	} catch (e) {
		throw "Not Mobile";
	}
}

/*****************************************************************************/

var UriUtil = function () { }

UriUtil.getHost = function (url) {
	var idx = url.indexOf("://");
	if (idx >= 0) {
		url = url.substring(idx + 3, url.length);
	}

	idx = url.indexOf(":");
	if (idx < 0)
		idx = url.indexOf("/");

	if (idx > 0)
		url = url.substring(0, idx);

	return url;
};

UriUtil.getPathQuery = function (url) {
	if (url.indexOf("http://") == 0 || url.indexOf("https://") == 0) {
		var idx2 = url.indexOf("/", 10);
		if (idx2 >= 0)
			url = url.substring(idx2, url.length);
		else
			url = "";
	}

	return url;
};


UriUtil.safeJobinPath = function (path1, path2) {
	if (path1[path1.length - 1] == "/")
		path1 = path1.substring(0, path1.length - 1);

	if (path2[0] != "/")
		path2 = "/" + path2;

	return path1 + path2;
};


UriUtil.parseQueryString = function (key, default_, url) {
	if (!url)
		url = window.location.href;

	if (!default_) default_ = "";

	key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
	var qs = regex.exec(url);

	if (qs == null)
		return default_;
	else
		return qs[1];
};


//*****************************************************
// 컨트롤 유틸 클래스 정의
//*****************************************************
var ControlUtil = function () { }


// Util.getObjectToIEClientCoords 메소드에서 리턴하는 클래스 타입
ControlUtil.Position = function (x, y) {
	this.X = x;
	this.Y = y;
}

// 클라이언트 객체의 포지션을 리턴 합니다.
// jQuery로 변경(기존메소드 호환성 유지)
ControlUtil.getObjectToIEClientCoords = function (strObjName) {
	var x, y, elem1;

	if (typeof (strObjName) == "string")
		elem1 = $("#" + strObjName);
	else {
		if (strObjName.val == undefined)
			elem1 = $(strObjName);
		else
			elem1 = strObjName;
	}

	//var position = elem1.position();
	var position = elem1.offset();
	x = position.left;
	y = position.top + elem1.height();

	return new ControlUtil.Position(x, y);
}

// 오브젝트를 디스플레이 하거나 감춘다.
ControlUtil.displayObject = function (objid, displayFlag) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	obj.style.display = displayFlag ? "" : "none";
}

ControlUtil.visibleObject = function (objid, visibleFlag) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	obj.style.visibility = visibleFlag ? "visible" : "hidden";
}

// 오브젝트의 디스플레이 속성을 토글 한다.
ControlUtil.toggleDisplayObject = function (objid) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	if (obj.style.display == "none")
		obj.style.display = displayFlag = "";
	else
		obj.style.display = displayFlag = "none";
}

ControlUtil.disableObject = function (objid, flag) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	obj.disabled = flag;
	obj.style.background = flag ? "#d3d3d3" : "#ffffff";
}

ControlUtil.focusObject = function (objid) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	obj.focus();
}

// 이벤트 핸들러를 등록 합니다.
// objid는 id거나 오브젝트 일수 있습니다.
ControlUtil.addEventHandler = function (objid, event, evnetfunc) {
	if (objid == null)
		return;

	var obj = null;

	if (typeof (objid) == "string")
		obj = $get(objid);
	else
		obj = objid;

	if (obj == null)
		return;

	Handler.add(obj, event, evnetfunc);
}

// 서버객체의 이벤트 핸들러를 등록 합니다.
ControlUtil.addSEventHandler = function (objid, event, evnetfunc) {
	var obj = $sget(objid);
	if (obj == null)
		return;

	Handler.add(obj, event, evnetfunc);
}

// aspx의 서버폼을 리턴합니다.
ControlUtil.getServerForm = function () {
	return document.forms["aspnetForm"];
}

// aspx의 서버폼을 서브밋 합니다.
// action은 옵셔널 파라미터 입니다.(없을경우 현재 페이지로 서브밋 됩니다.)
ControlUtil.submitServerForm = function (action) {
	var form = ControlUtil.getServerForm();

	if (action != undefined && action != null) {
		form.action = action;
		$get("__VIEWSTATE").value = "";
	}

	form.submit();
}

ControlUtil.setEnterKeyEvent = function (id, func) {
	ControlUtil.addEventHandler(id, "onkeypress",
		function (e) {
			var ev = e;

			if (window.event) {
				ev = window.event;
			}

			if (ev.keyCode == 13) {
				func();
			}
		}
	);
}


/* DateUtil 시작*/
var DateUtil = function () { };

/**
* 현재의 날짜를 숫자 8짜리 포맷으로 반환 (예 : 20070515)
* @return String 오늘 날짜
*/
DateUtil.GetCurrentDate = function () {
	var cur_date = GMKT.ServiceInfo.ServerTime;
	return DateUtil.FormatDate(cur_date);
};

/**
* 현재의 날짜를 숫자 8짜리 포맷으로 반환 (예 : 20070515)
* @return String 오늘 날짜
*/
DateUtil.GetCurrentTime = function () {
	var cur_date = GMKT.ServiceInfo.ServerTime;
	return DateUtil.FormatDate(cur_date);
};

//현재 시간 기준으로 시간이 사이에 있는지 체크
DateUtil.CheckTime = function (from, to) {
	from = (((from.split("-").join("")).split("/").join("")).split(" ").join("")).split(":").join("");
	to = (((to.split("-").join("")).split("/").join("")).split(" ").join("")).split(":").join("");

	var now_date = new Date(GMKT.ServiceInfo.ServerTime);
	var from_date = new Date(from.substr(0, 4), from.substr(4, 2) - 1, from.substr(6, 2), from.substr(8, 2), from.substr(10, 2), from.substr(12, 2));
	var to_date = new Date(to.substr(0, 4), to.substr(4, 2) - 1, to.substr(6, 2), to.substr(8, 2), to.substr(10, 2), to.substr(12, 2));

	if (now_date.getTime() > from_date.getTime() && now_date.getTime() < to_date.getTime())
		return true;
	else
		return false;
}


/**
* 지정날짜에 추가일을 더한 포맷 날짜 문자열을 얻는다
* 
* @param {int} addValue 추가할 날짜 수
* @param {String} addType "yy", "mm", "dd" 중 한가지를 지정하며, 각 지정에 따라 연, 월, 일을 증가시킨다
* @param {String} nDate 기준 날짜, 지정되지 않을 경우 현재 날짜를 기준으로 한다
* @return String
*/
DateUtil.DateAdd = function (addValue, addType, nDate) {
	var dt = DateUtil.ReturnDateTypeValue(nDate);

	switch (addType.toLowerCase()) {
		case "yy":
			{// year
				dt.setFullYear(dt.getFullYear() + parseInt(addValue));
				break;
			}

		case "mm":
			{// month
				dt.setMonth(dt.getMonth() + parseInt(addValue));
				break;
			}
		case "dd":
			{// day
				dt.setDate(dt.getDate() + parseInt(addValue));
				break;
			}
	}

	return DateUtil.FormatDate(dt);
};

/* 날짜 --> Date형으로 return */
DateUtil.ReturnDateTypeValue = function (strDate) {
	var mmm = { "JAN": "01", "FEB": "02", "MAR": "03", "APR": "04", "MAY": "05", "JUN": "06", "JUL": "07", "AUG": "08", "SEP": "09", "OCT": "10", "NOV": "11", "DEC": "12" };
	var tmpDateString;

	if (strDate == null) {
		strDate = DateUtil.GetCurrentDate();
	}

	if (typeof (strDate) == "object") // DateTime 형식이라면
	{
		dt = strDate;
	}
	else {
		strDate = strDate.replace(/-/g, '').replace(/\//g, '').replace(/:/g, '').replace(/ /g, '').replace(/,/g, '');

		if (strDate.length == 8 || strDate.length == 14) // "yyyymmdd" or "yyyymmddhhmmss"
		{
			tmpDateString = strDate.substring(0, 4) + "/" + strDate.substring(4, 6) + "/" + strDate.substring(6, 8);
			dt = new Date(tmpDateString);
		}
		else if (strDate.length == 9 || strDate.length == 15) // "MMMddyyyy" or"MMMddyyyyhhmmss"
		{
			tmpDateString = strDate.substring(5, 9) + "/" + mmm[strDate.substring(0, 3).toUpperCase()] + "/" + strDate.substring(3, 5);
			dt = new Date(tmpDateString);
		}
	}
	return dt;
};

//*****************************************************************************************
// [선택] 지정날짜(Datetime)을 해당 포맷으로 전환한다.
// 인자
// nDate : 날짜, null일경우 오늘 날짜를 서버에서 가져온다
// nformat : 날짜포맷 예) yyyy년 MM월 dd일
// DateFormat은 config에 지정한 내용으로 고정
//*****************************************************************************************
DateUtil.FormatDate = function (nDate) {
	var dt = DateUtil.ReturnDateTypeValue(nDate);
	var MMM = { "01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr", "05": "May", "06": "Jun", "07": "Jul", "08": "Aug", "09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec" };
	var format = GMKT.ServiceInfo.DateFormat.replace(" HH:mm:ss", "");

	var y, m, d;
	y = dt.getFullYear();
	m = dt.getMonth() + 1;
	d = dt.getDate();

	m = m < 10 ? "0" + m : m;
	d = d < 10 ? "0" + d : d;

	format = format.replace("yyyy", y);
	format = format.replace("MMM", MMM[m]);
	format = format.replace("MM", m);
	format = format.replace("dd", d);

	return format;
};

/**
* 날짜의 차이를 계산하여 두 날짜 사이의 간격(단위:day)을 숫자로 반환
* 
* @param {String} date1 yyyymmdd or mmm dd,yyyy 무방함
* @param {String} date2 yyyymmdd or mmm dd,yyyy 무방함
* @return String
*/
DateUtil.DateDiff = function (date1, date2) {
	var sDate = DateUtil.ReturnDateTypeValue(date1);
	var eDate = DateUtil.ReturnDateTypeValue(date2);

	var timeSpan = (eDate - sDate) / 86400000;
	var daysApart = Math.abs(Math.round(timeSpan));

	return daysApart;
};

/**
* 날짜의 차이를 계산하여 두 날짜 사이의 간격(단위:day)을 숫자로 반환
* 
* @param {String} date1 yyyymmdd or mmm dd,yyyy 무방함
* @param {String} date2 yyyymmdd or mmm dd,yyyy 무방함
* @return String
*/
DateUtil.DateDiff2 = function (date1, date2) {
	var sDate = DateUtil.ReturnDateTypeValue(date1);
	var eDate = DateUtil.ReturnDateTypeValue(date2);

	var timeSpan = (eDate - sDate) / 86400000;
	var daysApart = Math.round(timeSpan);

	return daysApart;
};
/* DateUtil 종료 */


/* PriceUtil 시작*/
var PriceUtil = function () { };

/* 국가별 Currency Formatting */
PriceUtil.FormatCurrency = function (money) {
	var sign, cents, roundFloat;
	roundFloat = 0.50000000001

	money = money.toString().replace(/\$|\,/g, '');

	if (isNaN(money))
		money = "0";

	sign = (money == (money = Math.abs(money)));
	money = Math.floor(money * 100 + roundFloat);

	cents = money % 100;
	money = Math.floor(money / 100).toString();

	if (cents < 10) cents = "0" + cents;

	for (var i = 0; i < Math.floor((money.length - (1 + i)) / 3); i++)
		money = money.substring(0, money.length - (4 * i + 3)) + ',' + money.substring(money.length - (4 * i + 3));

	if (GMKT.ServiceInfo.nation == "JP" || GMKT.ServiceInfo.nation == "ID") {
		return (((sign) ? '' : '-') + money);
	}
	else if (GMKT.ServiceInfo.nation == "MY" || GMKT.ServiceInfo.nation == "CN") {	// MY, CN의 경우 소수 있는 경우만 보여준다.
		if (cents == 0)
			return (((sign) ? '' : '-') + money);
		else
			return (((sign) ? '' : '-') + money + "." + cents);
	}
	else {
		return (((sign) ? '' : '-') + money + "." + cents);
	}
};

PriceUtil.FormatCurrencySymbol = function (money) {

	var money = this.FormatCurrency(money);

	if (GMKT.ServiceInfo.nation == "JP" || GMKT.ServiceInfo.nation == "CN")	//JP, HK, CN의 경우엔 currency symbol이 뒤에 오도록
		return money + GMKT.ServiceInfo.currency;
	else {
		return ((money.indexOf("-") >= 0) ? '-' : '') + GMKT.ServiceInfo.currency + money.replace('-', '');
	}

	return money;
};

PriceUtil.FormatCurrencyRegionSymbol = function (money) {

    var money = this.FormatCurrency(money);

    if (GMKT.ServiceInfo.region == "JP" || GMKT.ServiceInfo.region == "CN")	//JP, CN의 경우엔 currency symbol이 뒤에 오도록
        return money + GMKT.ServiceInfo.currency;
    else if (GMKT.ServiceInfo.region == "HK")   //HK, MY는 Service.currency가 USD로 되어 있어서 별도 처리
    {
        return ((money.indexOf("-") >= 0) ? '-' : '') + "HK$" + money.replace('-', '');
    }
    else if (GMKT.ServiceInfo.region == "MY")	//HK, MY는 Service.currency가 USD로 되어 있어서 별도 처리
    {
        return ((money.indexOf("-") >= 0) ? '-' : '') + "RM" + money.replace('-', '');
    }
    else {
        return ((money.indexOf("-") >= 0) ? '-' : '') + GMKT.ServiceInfo.currency + money.replace('-', '');
    }

    return money;
};

PriceUtil.FormatCurrencyCode = function (money, currency_code) {
	var d_svc_nation = { "SGD": "S$", "JPY": "円", "MYR": "RM", "IDR": "Rp", "KRW": "원", "USD": "US$", "CNY": "元", "HKD": "HK$", "RMB": "元", "PHP": "P", "TWD": "元" }

	var currency = "";
	for (var key in d_svc_nation) {
		if (key == currency_code)
			currency = d_svc_nation[key];
	}

	var sign, cents, roundFloat;
	roundFloat = 0.50000000001

	money = money.toString().replace(/\$|\,/g, '');

	if (isNaN(money))
		money = "0";

	sign = (money == (money = Math.abs(money)));
	money = Math.floor(money * 100 + roundFloat);

	cents = money % 100;
	money = Math.floor(money / 100).toString();

	if (cents < 10) cents = "0" + cents;

	for (var i = 0; i < Math.floor((money.length - (1 + i)) / 3); i++)
		money = money.substring(0, money.length - (4 * i + 3)) + ',' + money.substring(money.length - (4 * i + 3));

	var disp_money = "";

	if (currency_code == "SGD" || currency_code == "USD" || currency_code == "HKD" || currency_code == "PHP")
		disp_money = (((sign) ? '' : '-') + currency +money + "." + cents);
	else if (currency_code == "MYR") {	// MY의 경우 소수 있는 경우만 보여준다.
		if (cents == 0)
			disp_money = currency + (((sign) ? '' : '-') + money);
		else
			disp_money = currency + (((sign) ? '' : '-') + money + "." + cents);
	}
	else if (currency_code == "RMB" || currency_code == "CNY") {
		if (cents == 0)
			disp_money = (((sign) ? '' : '-') + money) + currency;
		else
			disp_money = (((sign) ? '' : '-') + money + "." + cents) + currency;
	}
	else {
		if (currency_code == "JPY" || currency_code == "KRW" || currency_code == "TWD")
			disp_money = (((sign) ? '' : '-') + money) + currency;
		else
			disp_money = currency + (((sign) ? '' : '-') + money);
	}

	return disp_money;
};

/* 일반 숫자값 Formatting */
PriceUtil.FormatNumber = function (value) {
	value = value.toString().replace(/\$|\,/g, '');
	for (var i = 0; i < Math.floor((value.length - (1 + i)) / 3); i++)
		value = value.substring(0, value.length - (4 * i + 3)) + ',' + value.substring(value.length - (4 * i + 3));

	return value;
}

PriceUtil.PriceCuttingService = function (value, nation) {

	var format = GMKT.ServiceInfo.money_format;
	var digits;
	var rtnTmp;

	value = Number(value);

	if (nation == "ID") {
		digits = -1;
	}
	else if (format.indexOf(".") == -1) {
		digits = 0;
	}
	else {
		digits = format.substr(format.indexOf(".") + 1, format.length).length;
	}

	if (digits < 0 || value < 1) {
		rtnTmp = PriceUtil.Round(value, digits);
	}
	else {
		rtnTmp = value.toFixed(digits);
	}

	return rtnTmp;
}


PriceUtil.PriceCuttingCode = function (value, currency) {

	var digits;
	var rtnTmp;

	if (currency == undefined)
		currency = GMKT.ServiceInfo.currencyCode;

	value = Number(value);

	if (currency == "IDR")
		digits = -1;
	else if (currency == "SGD" || currency == "MYR" || currency == "USD" || currency == "CNY" || currency == "HKD")
		digits = 2;
	else
		digits = 0;

	if (digits < 0 || value < 1) {
		rtnTmp = PriceUtil.Round(value, digits);
	}
	else {
		rtnTmp = value.toFixed(digits);
	}

	return rtnTmp;
}

/// 가격 끝자리 처리(config의 서비스 국가별로 가격 자리수 조정 ) - 국가코드입력 & 반올림여부 입력
/// IDR는 10에서 반올림, 버림, 올림 처리 함.(기존은 1의 자리에서 반올림 함)
/// JP는 소수 첫째 자리에서 반올림, 버림, 올림 처리 함.
/// 그외 국가는 소수 둘째 자리에서 반올림, 버림, 올림 처리 함.(기존은 소수 셋째 자리에서 반올림 함)
PriceUtil.PriceCutting2Currency = function (price, currency_cd, nStyle) {
	var cutVal = 1;
	var cutPo = 1;
	var cutcount = 0;

	if (currency_cd == "")
		currency_cd = GMKT.ServiceInfo.viewCurrencyCode;

	if (currency_cd == "IDR" || currency_cd == "KRW") {
		if (nStyle == 0) //ID는 10에서 반올림.
			return Math.round(price / 100, 0) * 100;
		else if (nStyle == 1) //ID는 10에서 버림
		{
			price = (price / 100);
			return Math.round(price - cutVal * 0.5, 0) * 100;
		}
		else //ID는 10에서 올림
		{
			price = (price / 100);
			return Math.round(price + cutVal * 0.5, 0) * 100;
		}
	}
	else {
		if (currency_cd == "SGD" || currency_cd == "MYR" || currency_cd == "USD" || currency_cd == "CNY" || currency_cd == "HKD") {
			cutcount = 1;
		}

		for (i = 0; i < cutcount; i++) { cutVal /= 10; cutPo *= 10 }

		if (nStyle == 0) // 반올림
			return Math.round(price * cutPo, cutcount) / cutPo;
		else if (nStyle == 1) //버림
			return Math.round((price - cutVal * 0.5) * cutPo, cutcount) / cutPo;
		else // 올림
			return Math.round((price + cutVal * 0.5) * cutPo, cutcount) / cutPo;
	}
}

PriceUtil.Round = function (valuenum, digits) {
	var sourceDouble = valuenum;    // 5 단위에서 반올림
	return Math.round(sourceDouble * Math.pow(10, digits)) / Math.pow(10, digits);
}

/* Config에 정의된 money_format에 맞춰서 관련된 반올림 정리 : 통화 관련된건 이걸 사용한다. */
PriceUtil.PriceCutting = function (value) {
	var nation = GMKT.ServiceInfo.nation;

	return PriceUtil.PriceCuttingService(value, nation);
}

PriceUtil.GetMoney = function (money) {
	money = money.toString().replace(/\S\$|RM|Rp|\$|\US\$|\HK\$|\元|\,/g, '');
	return parseFloat(money);
}

PriceUtil.ChangeFormatToNum = function (sValue) {
	return sValue.toString().replace(/\$|\,/g, '');
}

PriceUtil.AddCurrencySymbol = function (amt, currency) {
	if (currency == undefined) { currency = PriceUtil.GetCurrencySymbol(); }

	if (currency == '원' || currency == '円' || currency == '元')	//'원', '円', '元'의 경우엔 currency symbol이 뒤에 오도록
		return amt + currency;
	else
		return currency + amt;
}

PriceUtil.GetNationCurrency = function (svc_nation_cd) {
	var d_svc_nation = { "SG": "SGD", "JP": "JPY", "MY": "MYR", "ID": "IDR", "KR": "KRW", "US": "USD", "CN": "CNY", "HK": "HKD", "RM": "RMB" }

	var currency = "";
	for (var key in d_svc_nation) {
		if (key == svc_nation_cd)
			currency = d_svc_nation[key];
	}
	return currency;
}

PriceUtil.GetCurrencySymbol = function (currency_cd) {
	if (currency_cd == undefined) { currency_cd = GMKT.ServiceInfo.viewCurrencyCode; }
	var d_svc_nation = { "SGD": "S$", "JPY": "円", "MYR": "RM", "IDR": "Rp", "KRW": "원", "USD": "US$", "CNY": "元", "HKD": "HK$", "RMB": "元", "PHP": "P", "TWD": "元" }

	var currency = "";
	for (var key in d_svc_nation) {
		if (key == currency_cd)
			currency = d_svc_nation[key];
	}
	return currency;
}
/* PriceUtil 종료*/


/* dreamweaver용 swap 스크립트 상세에 하도 사용을 해대서 util에 등록함. */
function MM_swapImgRestore() { //v3.0
	var i, x, a = document.MM_sr; for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) x.src = x.oSrc;
}
function MM_preloadImages() { //v3.0
	var d = document; if (d.images) {
		if (!d.MM_p) d.MM_p = new Array();
		var i, j = d.MM_p.length, a = MM_preloadImages.arguments; for (i = 0; i < a.length; i++)
			if (a[i].indexOf("#") != 0) { d.MM_p[j] = new Image; d.MM_p[j++].src = a[i]; } 
	}
}

function MM_findObj(n, d) { //v4.01
	var p, i, x; if (!d) d = document; if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
		d = parent.frames[n.substring(p + 1)].document; n = n.substring(0, p);
	}
	if (!(x = d[n]) && d.all) x = d.all[n]; for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
	for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
	if (!x && d.getElementById) x = d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
	var i, j = 0, x, a = MM_swapImage.arguments; document.MM_sr = new Array; for (i = 0; i < (a.length - 2); i += 3)
		if ((x = MM_findObj(a[i])) != null) { document.MM_sr[j++] = x; if (!x.oSrc) x.oSrc = x.src; x.src = a[i + 2]; }
}
/* swap 스크립트 end*/


/* RMSHelper.js */
if (!window.JSON) { window.JSON = { parse: function (sJSON) { return eval("(" + sJSON + ")"); }, stringify: function (vContent) { if (vContent instanceof Object) { var sOutput = ""; if (vContent.constructor === Array) { for (var nId = 0; nId < vContent.length; sOutput += this.stringify(vContent[nId]) + ",", nId++); return "[" + sOutput.substr(0, sOutput.length - 1) + "]"; } if (vContent.toString !== Object.prototype.toString) { return "\"" + vContent.toString().replace(/"/g, "\\$&") + "\""; } for (var sProp in vContent) { sOutput += "\"" + sProp.replace(/"/g, "\\$&") + "\":" + this.stringify(vContent[sProp]) + ","; } return "{" + sOutput.substr(0, sOutput.length - 1) + "}"; } return typeof vContent === "string" ? "\"" + vContent.replace(/"/g, "\\$&") + "\"" : String(vContent); } }; }

var RMSHelper = function () { };
RMSHelper.getXMLHTTP = function () {
	if (window.XMLHttpRequest) return new XMLHttpRequest();
	var versions = [
	"MSXML2.XMLHTTP.6.0",
	"MSXML2.XMLHTTP.4.0",
	"Microsoft.XMLHTTP",
	"MSXML2.XMLHTTP.5.0",
	"MSXML2.XMLHTTP.3.0",
	"MSXML2.XMLHTTP"
	];
	for (var i = 0; i < versions.length; i++) {
		try {
			var oXMLHTTP = new ActiveXObject(versions[i]);
			return oXMLHTTP;
		}
		catch (e) { };
	}
	throw new Error("No XMLHTTP");
}
RMSHelper.callWebMethod = function (serviceName, methodName, argument) {
	var svc = serviceName + "/" + methodName;
	if (!argument) argument = "";

	var xmlHttp = RMSHelper.getXMLHTTP();
	xmlHttp.open("POST", svc, false);
	xmlHttp.setRequestHeader('Content-Type', 'application/json');

	xmlHttp.send(argument);
	
	var result = null;
	try {
		result = eval("(" + xmlHttp.responseText + ")");
	}
	catch (ex) { }

	if (result && result.ExceptionType != undefined) {
		//alert(result.Message);
		throw result;
	}

	if (result && result.d != undefined)
		return result.d;
	return result;
}
RMSHelper.asyncCallWebMethod = function (serviceName, methodName, argument, callBackFunction) {
	var svc = serviceName + "/" + methodName;
	var xmlHttpasync = null;

	xmlHttpasync = RMSHelper.getXMLHTTP();

	if (!argument) argument = "";
	xmlHttpasync.open("POST", svc, true);
	xmlHttpasync.setRequestHeader('Content-Type', 'application/json');

	//var xhrTimeout = setTimeout(function () {
	//	xmlHttpasync.abort();
	//	alert("Request timed out");
	//}, 2000);

	// ie8이상?
	//xmlHttpasync.timeout = 2000;
	//xmlHttpasync.ontimeout = function () { alert("Timed out!!!"); }

	xmlHttpasync.onreadystatechange = function () {
		//0: request not initialized 
		//1: server connection established
		//2: request received 
		//3: processing request 
		//4: request finished and response is ready

		if (xmlHttpasync.readyState == 4) {
			// clearTimeout(xhrTimeout);
			var result = null;
			
			try {
				result = eval("(" + xmlHttpasync.responseText + ")");
			} catch (ex) { }

			try {
				if (result && result.ExceptionType != undefined) {
					//alert(result.Message);
					throw result;
				}
				if (result && result.d != undefined)
					result = result.d;
			} catch (ex) { }

			callBackFunction(result, svc, methodName, xmlHttpasync);
		}
	};

	xmlHttpasync.send(argument);
}

RMSHelper.callWebObject = function (url, httpMethod, postData) {
	var svc = url;

	// ios 6.0에서 발생하는 캐싱 문제를 해결 하기 위해 추가
	if (svc.indexOf("?") >= 0)
		svc += "&";
	else
		svc += "?";

	svc += "___cache_expire___=" + new Date().getTime();

	var xmlHttp = RMSHelper.getXMLHTTP();

	if (httpMethod == "POST") {
		xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xmlHttp.open("POST", svc, false);
		xmlHttp.send(postData);
	}
	else {
		xmlHttp.open("GET", svc, false);
		xmlHttp.send();
	}

	return xmlHttp.responseText;
}

RMSHelper.asyncCallWebObject = function (url, httpMethod, postData, callBackFunction, statObject) {
	var svc = url;

	// ios 6.0에서 발생하는 캐싱 문제를 해결 하기 위해 추가
	if (svc.indexOf("?") >= 0)
		svc += "&";
	else
		svc += "?";

	svc += "___cache_expire___=" + new Date().getTime();

	var xmlHttp = RMSHelper.getXMLHTTP();

	if (httpMethod == "POST") {
		xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xmlHttp.open("POST", svc, true);
		xmlHttp.onreadystatechange = function () {
			if (xmlHttp.readyState == 4) {
				callBackFunction(xmlHttp.responseText, svc, xmlHttp, statObject);
			}
		};
		xmlHttp.send(postData);
	}
	else {
		xmlHttp.open("GET", svc, true);
		xmlHttp.onreadystatechange = function () {
			if (xmlHttp.readyState == 4) {
				callBackFunction(xmlHttp.responseText, svc, xmlHttp, statObject);
			}
		};
		xmlHttp.send();
	}
}

var RMSParam = function () {
	this._pl = new Array();
}
RMSParam.prototype.add = function (name, value) {
	this._pl[name] = value;
	return this;
}
RMSParam.prototype.toXml = function () {
	var xml = "";
	for (var p in this._pl) {
		if (typeof (this._pl[p]) != "function")
			xml += "<" + p + ">" + this._pl[p].toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") + "</" + p + ">";
	}
	return xml;
}
RMSParam.prototype.toJson = function () {
	var query = "";	

	for (var p in this._pl) {
		if (typeof (this._pl[p]) != "function") {
			if (query.length > 0)
				query += ",";

			if (this._pl[p] == null)
				query += "\"" + p + "\"" + ":null";
			else
				query += "\"" + p + "\"" + ":" + JSON.stringify(this._pl[p]);
				//query += "\"" + p + "\"" + ":\"" + this._pl[p].toString().replace(/\\/g, "\\\\").replace(/"/g, "\\\"") + "\"";
		}
	}

	// ios 6.0에서 발생하는 캐싱 문제를 해결 하기 위해 추가
	if (query.length > 0)
		query += ",";
	query += "\"___cache_expire___\":\"" + new Date().getTime() + "\""

	return "{" + query + "}";
}

/* RMSHelper.js End*/

/* MultiLang.js */
var MultiLang = function () { }
MultiLang.findResource = function (resourceId, arg1, arg2, arg3) {
	var val = MultiLang._findResource(resourceId, false);
	if (arg3 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString()).replace('{2}', arg3.toString());
	}
	else if (arg2 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString());
	}
	else if (arg1 != undefined) {
		val = val.toString().replace('{0}', arg1.toString());
	}
	return val;
};

MultiLang.findResourceByNation = function (resourceId, arg1, arg2, arg3) {
	var val = MultiLang._findResource(resourceId, true, true);

	if (arg3 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString()).replace('{2}', arg3.toString());
	}
	else if (arg2 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString());
	}
	else if (arg1 != undefined) {
		val = val.toString().replace('{0}', arg1.toString());
	}

	return val;
};

MultiLang.findResourceForceByNation = function (resourceId, arg1, arg2, arg3) {
	var val = MultiLang._findResource(resourceId, true, false);

	if (arg3 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString()).replace('{2}', arg3.toString());
	}
	else if (arg2 != undefined) {
		val = val.toString().replace('{0}', arg1.toString()).toString().replace('{1}', arg2.toString());
	}
	else if (arg1 != undefined) {
		val = val.toString().replace('{0}', arg1.toString());
	}

	return val;
};


MultiLang._findResource = function (resourceId, bNationResource, bRegionResource) {
	if (bRegionResource)
		resourceId += "__" + GMKT.ServiceInfo.region;
	else if (bNationResource)
		resourceId += "__" + GMKT.ServiceInfo.nation;

	if (__ClientResource != undefined) {
		var val = __ClientResource[resourceId.toLowerCase()];

		if (val != undefined)
			return val;
	}
	var msg = "Resource('" + resourceId.toLowerCase() + "') not found";
	Public.WriteScriptError(msg, window.location.href, "MultiLang._findResource", "", "Warn");
	//alert(msg);
	return "";
};
MultiLang.findCommonResource = function (categoryid, resourceId) {
	return MultiLang._findCommonResource(categoryid, resourceId, false);
};

MultiLang.findCommonResourceByNation = function (categoryid, resourceId) {
	return MultiLang._findCommonResource(categoryid, resourceId, true, true);
};
MultiLang.findCommonResourceForceByNation = function (categoryid, resourceId) {
	return MultiLang._findCommonResource(categoryid, resourceId, true, false);
};
MultiLang._findCommonResource = function (categoryid, resourceId, bNationResource, bRegionResource) {
	if (bRegionResource)
		resourceId += "__" + GMKT.ServiceInfo.region;
	else if (bNationResource)
		resourceId += "__" + GMKT.ServiceInfo.nation;

	if (__ClientResource != undefined) {
		var val = __ClientResource["__" + categoryid.toLowerCase() + "__" + resourceId.toLowerCase()];

		if (val != undefined)
			return val;
	}
	var msg = "Resource('" + "__" + categoryid.toLowerCase() + "__" + resourceId.toLowerCase() + "') not found";
	Public.WriteScriptError(msg, window.location.href, "MultiLang._findCommonResource", "", "Warn");
	//alert(msg);
	return "";
};
MultiLang.findScriptResource = function (resourceId) {
	return MultiLang._findScriptResource(resourceId, false);
};
MultiLang.findScriptResourceByNation = function (resourceId) {
	return MultiLang._findScriptResource(resourceId, true, true);
};
MultiLang.findScriptResourceForceByNation = function (resourceId) {
	return MultiLang._findScriptResource(resourceId, true, false);
};
MultiLang._findScriptResource = function (resourceId, bNationResource, bRegionResource) {
	if (bRegionResource)
		resourceId += "__" + GMKT.ServiceInfo.region;
	else if (bNationResource)
		resourceId += "__" + GMKT.ServiceInfo.nation;

	if (__ClientResource != undefined) {
		var val = eval(__ClientResource[resourceId.toLowerCase()]);
		if (val != undefined)
			return val;
	}

	var msg = "Resource('" + resourceId + "') not found";
	Public.WriteScriptError(msg, window.location.href, "MultiLang._findScriptResource", "", "Warn");
	return null;
};
MultiLang.addResource = function (resourceId, text) {
	__ClientResource[resourceId.toLowerCase()] = text;
};
/* MultiLang.js End */


/* Binder.js */
SelectBoxBinder = function () { };
SelectBoxBinder.bindingJson = function (objSelect, json, valuekey, textkey, valueBinder, textBinder) {
	if (json.__type == "System.Data.DataTable") {
		for (var i = 0; i < json.Rows.length; i++) {
			var valueData = json.Rows[i][valuekey];
			var textData = json.Rows[i][textkey];
			if (valueBinder != undefined && valueBinder != null) {
				valueData = valueBinder(valuekey, valueData, json, i);
			}
			if (textBinder != undefined && textBinder != null) {
				textData = textBinder(valuekey, textData, json, i);
			}
			SelectBoxBinder.addOption(objSelect, textData, valueData);
		}
	}
	else {
		for (var i = 0; i < json.length; i++) {
			var valueData = json[i][valuekey];
			var textData = json[i][textkey];
			if (valueBinder != undefined && valueBinder != null) {
				valueData = valueBinder(valuekey, valueData, json, i);
			}
			if (textBinder != undefined && textBinder != null) {
				textData = textBinder(valuekey, textData, json, i);
			}
			SelectBoxBinder.addOption(objSelect, textData, valueData);
		}
	}
};
SelectBoxBinder.addOption = function (objSelect, text, value) {
        if(objSelect == null) return;
	var opt = new Option(text, value);
	objSelect.options.add(opt);
	return opt;
};
SelectBoxBinder.clear = function (objSelect, rowIndex, delGroup) {
	if (!delGroup && $(objSelect).children("optgroup") != null && $(objSelect).children("optgroup").length > 0) {
		delGroup = true;
	}

	if (delGroup) {
		while (objSelect.firstChild) {
			objSelect.removeChild(objSelect.firstChild);
		}
	}
	else {
		if (!rowIndex) {
			rowIndex = 0;
		}

		objSelect.options.length = rowIndex;
	}
}
/* Binder.js End */

/* Handler.js */
var Handler = {};
if (document.addEventListener) {
	Handler.add = function (element, eventType, handler) {
		if (typeof element == "string") element = document.getElementById(element);

		if (eventType.indexOf("on") != 0)
			element.addEventListener(eventType, handler, false);
		else
			element.addEventListener(eventType.substr(2, eventType.length - 2), handler, false);
	};

	Handler.remove = function (element, eventType, handler) {
		if (typeof element == "string") element = document.getElementById(element);

		if (eventType.indexOf("on") != 0)
			element.removeEventListener(eventType, handler, false);
		else
			element.removeEventListener(eventType.substr(2, eventType.length - 2), handler, false);
	};
}
else if (document.attachEvent) {
	Handler.add = function (element, eventType, handler) {
		if (typeof element == "string") element = document.getElementById(element);

		if (!element) return;

		if (Handler._find(element, eventType, handler) != -1) return;

		var wrappedHandler = function (e) {
			if (!e) e = window.event;

			var event = {
				_event: e,   // In	case we	really want	the	IE event object
				type: e.type, 		// Event type
				target: e.srcElement, // Where the event happened
				currentTarget: element, // Where we're handling	it
				relatedTarget: e.fromElement ? e.fromElement : e.toElement,
				eventPhase: (e.srcElement == element) ? 2 : 3,

				// Mouse coordinates
				clientX: e.clientX, clientY: e.clientY,
				screenX: e.screenX, screenY: e.screenY,

				// Key state
				altKey: e.altKey, ctrlKey: e.ctrlKey,
				shiftKey: e.shiftKey, charCode: e.keyCode,

				// Event management	functions
				stopPropagation: function () { this._event.cancelBubble = true; },
				preventDefault: function () { this._event.returnValue = false; }
			}

			if (Function.prototype.call)
				handler.call(element, event);
			else {
				// If we don't have	Function.call, fake	it like	this
				element._currentHandler = handler;
				element._currentHandler(event);
				element._currentHandler = null;
			}
		};

		if (eventType.indexOf("on") != 0)
			element.attachEvent("on" + eventType, wrappedHandler);
		else
			element.attachEvent(eventType, wrappedHandler);

		var h = {
			element: element,
			eventType: eventType,
			handler: handler,
			wrappedHandler: wrappedHandler
		};

		var d = element.document || element;
		var w = d.parentWindow;

		var id = Handler._uid();  // Generate a	unique property	name
		if (!w._allHandlers) w._allHandlers = {};  // Create object	if needed
		w._allHandlers[id] = h; // Store the handler info in this object

		if (!element._handlers) element._handlers = [];
		element._handlers.push(id);

		if (!w._onunloadHandlerRegistered) {
			w._onunloadHandlerRegistered = true;
			w.attachEvent("onunload", Handler._removeAllHandlers);
		}
	};

	Handler.remove = function (element, eventType, handler) {
		var i = Handler._find(element, eventType, handler);
		if (i == -1) return;  // If	the	handler	was	not	registered,	do nothing

		var d = element.document || element;
		var w = d.parentWindow;

		var handlerId = element._handlers[i];
		var h = w._allHandlers[handlerId];

		if (eventType.indexOf("on") != 0)
			element.detachEvent("on" + eventType, h.wrappedHandler);
		else
			element.detachEvent(eventType, h.wrappedHandler);

		element._handlers.splice(i, 1);
		delete w._allHandlers[handlerId];
	};

	Handler._find = function (element, eventType, handler) {
		var handlers = element._handlers;
		if (!handlers) return -1;  // if no	handlers registered, nothing found

		var d = element.document || element;
		var w = d.parentWindow;

		for (var i = handlers.length - 1; i >= 0; i--) {
			var handlerId = handlers[i]; 	// get handler id
			var h = w._allHandlers[handlerId]; // get handler info
			// If handler info matches type	and	handler	function, we found it.
			if (h.eventType == eventType && h.handler == handler)
				return i;
		}
		return -1; // No match	found
	};

	Handler._removeAllHandlers = function () {
		var w = this;

		for (id in w._allHandlers) {
			var h = w._allHandlers[id];
			if (h.eventType.indexOf("on") != 0)
				h.element.detachEvent("on" + h.eventType, h.wrappedHandler);
			else
				h.element.detachEvent("on" + h.eventType, h.wrappedHandler);

			delete w._allHandlers[id];
		}
	};

	Handler._counter = 0;
	Handler._uid = function () { return "h" + Handler._counter++; };
};
/* Handler.js End */

/*  UI Effect Start */
var Effect = function () { };
// Dissolve 효과
Effect.Dissolve = function (obj, src, speed, callback) {
	if (speed = undefined) speed = 500; // default 속도

	obj.fadeTo(speed, 0, function () {
		if (src != undefined) obj.attr("src", src); // 이미지 url이 있는 경우
		obj.fadeTo(speed, 1)
	});

	if (typeof (callback) == "function") callback();
}
/*  UI Effect End */

var ETC = function () { };

//SetServerTimeTic(): GMKT.ServiceInfo.ServerTime을 1초마다 갱신한다.
ETC.SetServerTimeTic = function () {
	var now = new Date(GMKT.ServiceInfo.ServerTime);
	now.setTime(now.getTime() + 1000);
	GMKT.ServiceInfo.ServerTime = now;
	setTimeout(ETC.SetServerTimeTic, 1000);
}

//ssl에 맞는 컨텐츠로 replace
ETC.GetSSLContents = function (contents) {
	// ssl인 경우 처리
	if (window.location.protocol == "https:") {
		contents = contents.replace(/http:\/\/static.image-gmkt.com/gi, __PAGE_VALUE.STATIC_SSL_IMAGE_PATH);
		contents = contents.replace(/http:\/\/dp.image-gmkt.com/gi, __PAGE_VALUE.DP_SSL_IMAGE_PATH);
		contents = contents.replace(/http:\/\/gd.image-gmkt.com/gi, __PAGE_VALUE.GOODS_SSL_IMAGE_PATH);

		if (GMKT.ServiceInfo.nation == "CN") {
			contents = contents.replace(/http:\/\/static.qoo10.cn/gi, __PAGE_VALUE.STATIC_SSL_IMAGE_PATH);
			contents = contents.replace(/http:\/\/dp.qoo10.cn/gi, __PAGE_VALUE.DP_SSL_IMAGE_PATH);
			contents = contents.replace(/http:\/\/gd.qoo10.cn/gi, __PAGE_VALUE.GOODS_SSL_IMAGE_PATH);

			contents = contents.replace(/http:\/\/static.image-qoo10.cn/gi, __PAGE_VALUE.STATIC_SSL_IMAGE_PATH);
			contents = contents.replace(/http:\/\/dp.image-qoo10.cn/gi, __PAGE_VALUE.DP_SSL_IMAGE_PATH);
			contents = contents.replace(/http:\/\/gd.image-qoo10.cn/gi, __PAGE_VALUE.GOODS_SSL_IMAGE_PATH);
}
	}

	return contents;
}

// 배너 이미지 url 정리 - CN인 경우 dp 이미지 url을 바꿔준다.
ETC.LinkChange = function (url) {
	// CN인 경우 dp.image-gmkt.com을 CN용 주소로 변경
	if (GMKT.ServiceInfo.nation == "CN") {
		url = url.replace(/http:\/\/dp.image-gmkt.com/gi, __PAGE_VALUE.DP_IMAGE_PATH);
	}

	return url;
}

// satic 이미지 root로부터 url을 가져온다.
ETC.GetStaticImageURLRoot = function (url) {
	var buffer = Public.getStaticImgPath();

	if (url.indexOf("/") == 0) {
		buffer = buffer + url;
	}
	else {
		buffer = buffer + '/' + url;
	}
	return buffer;
}

//GetStaticImageURL(): 각 국가별 Static Image 서버 주소를 가져온다.
ETC.GetStaticImageURL = function (url) {
	var buffer = Public.getStaticImgPath() + '/' + GMKT.ServiceInfo.ClientLang + '/';
	if (url == undefined) { return buffer; }

	//tail
	var now = new Date(GMKT.ServiceInfo.ServerTime);
	var year = now.getFullYear() + '';
	year = year.substring(2, 4);
	var month = now.getMonth() + 1;
	if (month < 10) { month = '0' + month; } else { month = month + ''; }
	var date = now.getDate();
	if (date < 10) { date = '0' + date; } else { date = date + ''; }
	var tail = '?' + year + month + date;

	var buffer = buffer + url + tail;
	return buffer;
}

//GetStaticImageURLQoo10(): 각 국가별 Static Image 서버 주소를 가져온다.
ETC.GetStaticImageURLQoo10 = function (url) {
	var buffer = Public.getStaticImgPath() + '/qoo10/front/' + GMKT.ServiceInfo.ClientLang + '/';
	if (url == undefined) { return buffer; }

	//tail
	var now = new Date(GMKT.ServiceInfo.ServerTime);
	var year = now.getFullYear() + '';
	year = year.substring(2, 4);
	var month = now.getMonth() + 1;
	if (month < 10) { month = '0' + month; } else { month = month + ''; }
	var date = now.getDate();
	if (date < 10) { date = '0' + date; } else { date = date + ''; }
	var tail = '?' + year + month + date;

	var buffer = buffer + url + tail;
	return buffer;
}

ETC.GetDatePeriod = function (end_dt, type, start_dt) { //type: 'DAY', 'HOUR', 'MINUTE', 'SECOND'
	start_dt = (start_dt == undefined) ? new Date(GMKT.ServiceInfo.ServerTime) : new Date(start_dt);
	type = (type == undefined) ? 'DAY' : type.toUpperCase();
	if (end_dt == undefined) { return false; } else { end_dt = new Date(end_dt); } //required

	var compare = end_dt - start_dt;
	if (type == 'DAY') {
		return compare / (1000 * 60 * 60 * 24);

	} else if (type == 'HOUR') {
		return compare / (1000 * 60 * 60);

	} else if (type == 'MINUTE') {
		return compare / (1000 * 60);

	} else {
		return compare / 1000;
	}
}

ETC.GetDate = function (date) {
	if (date == undefined) {
		date = new Date(GMKT.ServiceInfo.ServerTime);

	} else if (date.length >= 22) { //case: /Date(-62135596800000)/
		date = new Date(eval(date.replace(/\//gi, '')));

	} else if (date.length == 14) { //case: 20121206230000
		var year = date.substring(0, 4);
		var month = date.substring(4, 6);
		var day = date.substring(6, 8);
		var hour = date.substring(8, 10);
		var min = date.substring(10, 12);
		var sec = date.substring(12, 14);

		date = year + '/' + month + '/' + day + ' ' + hour + ':' + min + ':' + sec; //format(for iOS): <yyyy/mm/dd hh:mm:ss>
		date = new Date(date);

	} else { //common case
		date = new Date(date);
	}

	return date;
}

ETC.GetShuffleArray = function (array) {
	var count = array.length;
	var shuffle_array = new Array();

	var index_list = new Array();
	for (var i = 0; i < count; i++) { index_list.push(i); }

	while (index_list.length > 0) {
		var index = Math.floor(Math.random() * index_list.length);
		var item = array[index_list[index]];
		shuffle_array.push(item);
		index_list.splice(index, 1);
	}

	return shuffle_array;
}

//*****************************************************
// ExchangeUtil 클래스 정의 
//*****************************************************
var Exchange_price_type = [{ "SELL_PRICE": 2, "DEAL_PRICE": 2, "DISCOUNT_PRICE": 1, "GROUP_PRICE": 2, "DELIVERY_FEE": 2, "BASIS_MONEY": 2}];
var ExchangeUtil = function () {
	var s_currency_cd = "";
	var t_currency_cd = "";
	var s_unit = 1;
	var t_unit = 1;
	var e_rate = 1;
	var ie_rate = 1;
	var oe_rate = 1;
}

ExchangeUtil.Init = function (source_currency_cd, target_currency_cd) {
	if (ExchangeUtil.s_currency_cd != "" && ExchangeUtil.s_currency_cd == source_currency_cd)
		return;

	if (ExchangeUtil.t_currency_cd != "" && ExchangeUtil.t_currency_cd == target_currency_cd)
		return;

	try {
		for (i = 0; i < exchange_info.length; i++) {
			if (source_currency_cd == exchange_info[i].sc_cd && target_currency_cd == exchange_info[i].tc_cd) {
				ExchangeUtil.s_currency_cd = exchange_info[i].sc_cd;
				ExchangeUtil.t_currency_cd = exchange_info[i].tc_cd;
				ExchangeUtil.s_unit = exchange_info[i].s_u;
				ExchangeUtil.t_unit = exchange_info[i].t_u;
				ExchangeUtil.e_rate = exchange_info[i].e_r;
				ExchangeUtil.ie_rate = exchange_info[i].ie_r;
				ExchangeUtil.oe_rate = exchange_info[i].oe_r;
				break;
			}
		}
	} catch (e) {
		return;
	}
}


ExchangeUtil.CalculateExchangeRate = function (money, source_currency_cd, target_currency_cd, nStyle) {
	try {
		if (exchange_info == undefined) return money;

		if (source_currency_cd == undefined || source_currency_cd == "")
			source_currency_cd = GMKT.ServiceInfo.currencyCode;

		if (target_currency_cd == undefined || target_currency_cd == "")
			target_currency_cd = GMKT.ServiceInfo.viewCurrencyCode;

		//소스와 타겟이 같으면 그냥 return
		if (source_currency_cd == target_currency_cd)
			return money;

		if (target_currency_cd == "RMB")
			target_currency_cd = "CNY";

		ExchangeUtil.Init(source_currency_cd, target_currency_cd);

		var exchanged_money = (money / ExchangeUtil.s_unit) * (ExchangeUtil.ie_rate * ExchangeUtil.t_unit);

		return PriceUtil.PriceCutting2Currency(exchanged_money, target_currency_cd, Exchange_price_type[0][nStyle]);
	} catch (e) {
		return money;
	}
}
