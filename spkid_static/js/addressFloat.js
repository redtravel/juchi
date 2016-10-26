jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options = $.extend({}, options);
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '; domain=.'+location.host.split('.').slice(-2).join('.');
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};


function stopBub(event) {
    if (event.stopPropagation) {
        event.stopPropagation();
    }
    else {
        event.cancelBubble = true;
    }
}
function addFloatHide(e) {
    var e = e || window.event;
    stopBub(e);
    $('#address_float').fadeOut(40);
}

/**
 * 选择区域
 * @param {type} region_id
 * @param {type} region_name
 * @returns {undefined}
 */
function change_region(region_id, region_name) {
    region_id = parseInt(region_id);
    if(typeof provider_shipping_fee_config[region_id] == 'undefined') {
        return;
    }
    var shipping_fee = provider_shipping_fee_config[region_id][0];
    var shipping_price = provider_shipping_fee_config[region_id][1];
    $('#loc_region').html(region_name);
    $('.peisong_address').html(region_name);
    $('#online_fee').html('运费：￥' + shipping_fee + '元');
    $('#free_shipping_price').html('（商家满￥' + shipping_price + '免邮）');
    $.cookie('curr_region', region_id);
	$('.p_logistics').show();
}
$(function() {
    region_id = $.cookie('curr_region');
	if(!region_id){
		$.ajax({
			url:'/user_api/get_current_region_id',
			data:{rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				region_id = result.region_id;
				var dom = $('.add_floatDiv dd a[region_id=' + region_id + ']');
				if (dom.length < 1)
					return;
				change_region(region_id, dom.html());
			}
		});
	}else{
		var dom = $('.add_floatDiv dd a[region_id=' + region_id + ']');
		if (dom.length < 1)
			return;
		change_region(region_id, dom.html());
	}
    $('.peisong_address').click(function(event) {
        var e = event || window.event;
        stopBub(e);
        if ($('#address_float').is(':hidden')) {

            $('#address_float').show();
        } else {
            addFloatHide(event);
        }
    });
    $('body').click(function(event) {
        addFloatHide(event);
    });
    $('#address_float .add_float_closeBtn').click(function(event) {
        addFloatHide(event);
    });
    $('.add_floatDiv dd a').bind("click", function(e) {
        var e = e || window.event;
        addFloatHide(e);
        change_region($(this).attr('region_id'), $(this).html());
    });
    $('#address_float').click(function(event) {
        var e = e || window.event;
        stopBub(e);
    });
});


