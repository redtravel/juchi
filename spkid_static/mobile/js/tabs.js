;(function(w, app, d7){
	$$ = d7;
	/**
	* tagType 1=>'商品',2=>'礼包',3=>'课程'
	* tag_id 对应tagtype的id，如tag_type=1，则tag_id 为对应的product_id
	* comment_type 1=>'咨询',2=>'评价', 3=>'测评'
	* container 为对应显示liuyan的html 标签，请直接传$$(选择器)
	*/
	/*function get_liuyan(tagType, tagId, comment_type, container) {
	        if (!tagType || !tagId || !comment_type || !container) {
	            alert('数据错误');
	            return;
	        };

	        $$.ajax({
	        url : '/liuyan/liuyan_list',
	        type : 'POST',
	        dataType : "json",
	        data : {
	            comment_type : comment_type,
	            tag_type : tagType,
	            tag_id : tagId
	        },
	        success : function(data, status, xhr) {            
	            if (data && data.html) container.html(data.html);
	        },

	        error : function(xhr, status) {
	            alert('数据请求错误');
	        }

	    });
	}*/

	function get_liuyan(tagType, tagId, comment_type, container, callback, params) {        
	        if (!tagType || !tagId || !comment_type || !container) {
	            alert('数据错误');
	            return;
	        };        
	        $$.ajax({
	        url : '/liuyan/liuyan_list',
	        type : 'POST',
	        dataType : "json",
	        data : {
	            comment_type : comment_type,
	            tag_type : tagType,
	            tag_id : tagId
	        },
	        success : function(data, status, xhr) {  
	        	if (data.err == 0) {
	        		if (typeof callback == 'function') {
	        		    callback(data.html);
	        		};
	        	} else {
	        		myApp.alert('未知错误');
	        	} 
	        },

	        error : function(xhr, status) {
	            myApp.alert('数据请求错误');
	        }

	    });
	}

	function html_encode(str)   
	{   
	  var s = "";   
	  if (str.length == 0) return "";   
	  s = str.replace(/&/g, "&gt;");   
	  s = s.replace(/</g, "&lt;");   
	  s = s.replace(/>/g, "&gt;");   
	  s = s.replace(/ /g, "&nbsp;");   
	  s = s.replace(/\'/g, "&#39;");   
	  s = s.replace(/\"/g, "&quot;");   
	  s = s.replace(/\n/g, "<br>");   
	  return s;   
	}   
	 
	function html_decode(str)   
	{   
	  var s = "";   
	  if (str.length == 0) return "";   
	  s = str.replace(/&gt;/g, "&");   
	  s = s.replace(/&lt;/g, "<");   
	  s = s.replace(/&gt;/g, ">");   
	  s = s.replace(/&nbsp;/g, " ");   
	  s = s.replace(/&#39;/g, "\'");   
	  s = s.replace(/&quot;/g, "\"");   
	  s = s.replace(/<br>/g, "\n");   
	  return s;   
	}

	$$(document).on('ajaxStart', function (e) {
	    myApp.showIndicator();
	});
	$$(document).on('ajaxComplete', function () {
	    myApp.hideIndicator();
	});

	$$('.tab-link').on('click', function(e){
	    var index = $$(this).index();
	    var fun = $$(this).attr('data-fun');
	    $$('.tab-link').removeClass('active');
	    $$(this).addClass('active');
	    var popUpFunction = function(content) {
	    	$$('.popup-tabs-pannel .tabs-pannel-content').html(content);
	    	myApp.popup('.popup-tabs-pannel');
	    }

	    // if (fun == 'xiangqing') {
	    // 	popUpFunction(xiangqing_detail);
	    // 	return;
	    // };

	    // if (fun == 'peixunxiangqing') {
	    // 	popUpFunction(peixunxiangqing);
	    // 	return;
	    // };

	    // if (fun == 'laoshijieshao') {
	    // 	popUpFunction(laoshijieshao);
	    // 	return;
	    // };

	    // if (fun == 'jiaotongluxian') {
	    // 	popUpFunction(jiaotongluxian);
	    // 	return;
	    // };
	    
	    // if (fun == 'xiangguanpeixun') {
	    // 	popUpFunction('暂无相应内容');
	    // 	return;
	    // };


		// if (fun == 'ceping') {
		//     popUpFunction(ceping_shipin);
		//   	return;  
		// };

	    if (fun == 'shuoming') {
	    	var html = "<dl class='chanpingshuoming'>";
	    	
			if (product_additional.package_name) {
	    		html += "<dt>" + '包装显示名称：' + "</dt>"
	    		+ "<dd>" + product_additional.package_name + '</dd>'
	    	};	    	

	    	if (product_additional.product_weight && parseFloat(product_additional.product_weight)) {
	    		html += "<dt>" + '商品毛重：' + "</dt>"
	    		+ "<dd>" + product_additional.product_weight + 'g' + '</dd>'
	    	};
	    	
	    	if (product_additional.brand_name) {
	    		html += "<dt>" + '品牌：' + "</dt>"
	    		+ "<dd>" + product_additional.brand_name + '</dd>';
	    	};

	    	if (product_additional.medical1 && product_additional.medical2) {
	    		html += "<dt>" + '医械类别' + "</dt>"
	    		+ "<dd>" + product_additional.medical2 + ' ' + product_additional.medical1 + '</dd>'
	    	} else if(product_additional.medical1 == 0) {
	    		+ "<dd>" + '非医疗机械' + '</dd>'
	    	}
	    	
	    	if (product_additional.register_no) {
	    		html += "<dt>" + '注册证号：' + "</dt>"
	    		+ "<dd>" + product_additional.register_no + '</dd>'
	    	};

	    	if (product_additional.product_name) {
	    		html += "<dt>" + '产品名称：' + "</dt>"
	    		+ "<dd>" + product_additional.product_name + '</dd>';
	    	}
	    	
	    	if (product_additional.standard) {
	    		html += "<dt>" + '产品标准' + "</dt>"
	    		+ "<dd>" + product_additional.standard + '</dd>'
	    	};
	    	
	    	if (product_additional.property) {
	    		html += "<dt>" + '产品性能结构及组成' + "</dt>"
	    		+ "<dd>" + product_additional.property + '</dd>'
	    	};
	    	
	    	if (product_additional.scope) {
	    		html += "<dt>" + '适用范围' + "</dt>"
	    		+ "<dd>" + product_additional.scope + '</dd>'	
	    	};	    	
	    	
	    	html += "</dl>";
	    	$$('.popup-tabs-pannel .tabs-pannel-content').html(html);
	    	myApp.popup('.popup-tabs-pannel');
	    	return;
	    };

	    if (fun == 'pingjia') {
	    	get_liuyan(1, tag_id, 2, $$('#tab4'), popUpFunction, index);	
	    	return;

	    };

	    if (fun == 'xueyuanjieshao') {
	    	get_liuyan(3, tag_id, 2, $$('#tab4'), popUpFunction, index);	
	    	return;
	    };
	    
	});

  	
})(window, myApp, Dom7);
