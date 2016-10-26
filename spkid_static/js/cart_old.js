var update_num_ing=0;
function update_num (rec_id,diff) {
    if(update_num_ing) return false;
    update_num_ing=1;
	var container = $('tr[id=cart_rec_'+rec_id+']');
	var num = parseInt($('span.product_num', container).html());
	if(isNaN(num)) {update_num_ing = 0; return false;} 
	num += diff;
	if (num<=0) {

update_num_ing = 0;
		return;
	};
	
	$.ajax({
		url:'/cart/update_cart',
		data:{rec_id:rec_id,num:num,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
            update_num_ing=0;
			if(result.msg) alert(result.msg);            
			if(result.err==1) return false;
			if(result.err==2) 
			{
				location.href=location.href;
				return;
			}
                update_cart_num();
			if (result.cart!=undefined) {
                                $('#cartNum_1').html(result.cart.product_num);
				$('.product_num', container).html(result.cart.product_num);
				$('.product_price', container).html(result.cart.product_price);
				$('.total_price', container).html(number_format(result.cart.product_price*result.cart.product_num,2));
			};
			if (result.cart_summary!=undefined)
			{
                            
				$('#summary_product_num').html(result.cart_summary.product_num);
				$('#summary_product_price').html('￥'+number_format(result.cart_summary.product_price,2));
				$('#summary_point').html(result.cart_summary.point);
                refresh_gifts(result.cart_summary.product_price);
                $('#_summary_product_num').html(result.cart_summary.product_num);
				$('#_summary_product_price').html('￥'+number_format(result.cart_summary.product_price,2)+'元');
				left_time = result.cart_summary.left_time;
			}
		}
	});
}

/**
 * 顶部更新购物车数量
 * @param {type} rec_id
 * @param {type} diff
 * @returns {Boolean}
 */
function update_num_pro (rec_id,diff) {
    if(update_num_ing) return false;
    update_num_ing=1;
    var container = $('li#cart_rec_'+rec_id);
    var container_flowcart = $('tr[id=cart_rec_'+rec_id+']');
	var num = parseInt($('#cartNum_1', container).html());
	if(isNaN(num)){update_num_ing = 0; return false;} 
	num += diff;
	if (num<=0) {
            update_num_ing = 0;
            return;
	};
	
	$.ajax({
		url:'/cart/update_cart',
		data:{rec_id:rec_id,num:num,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
            update_num_ing=0;
			if(result.msg) alert(result.msg);            
			if(result.err==1) return false;
			if(result.err==2) 
			{
				location.href=location.href;
				return;
			}
                update_cart_num();

			if (result.cart!=undefined) {
                                $('#cartNum_1', container).html(result.cart.product_num);
				$('.product_num', container_flowcart).html(result.cart.product_num);
				$('.product_price', container_flowcart).html(result.cart.product_price);
				$('.total_price', container_flowcart).html(number_format(result.cart.product_price*result.cart.product_num,2));
                                
			};
			if (result.cart_summary!=undefined)
			{
				$('#summary_product_num').html(result.cart_summary.product_num);
				$('#summary_product_price').html('￥'+number_format(result.cart_summary.product_price,2));
				$('#summary_point').html(result.cart_summary.point);
                refresh_gifts(result.cart_summary.product_price);
                $('#_summary_product_num').html(result.cart_summary.product_num);
				$('#_summary_product_price').html('￥'+number_format(result.cart_summary.product_price,2)+'元');
				left_time = result.cart_summary.left_time;
			}
		}
	});
}
function remove_cart(rec_id){
	if(!confirm('确定不购买该商品？')) return;
	$.ajax({
		url:'/cart/remove_from_cart',
		data:{rec_id:rec_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if(result.msg) alert(result.msg);
			if(result.err==1) return false;
			if(result.err==2) 
			{
				location.href=location.href;
				return;
			}
			$('tr[id=cart_rec_'+rec_id+']').remove();

			if (result.cart_summary!=undefined)
			{
                if(result.cart_summary.product_num<1) location.href=location.href;
				$('#summary_product_num').html(result.cart_summary.product_num);
				$('#summary_product_price').html('￥'+number_format(result.cart_summary.product_price,2));
				$('#summary_point').html(result.cart_summary.point);
                refresh_gifts(result.cart_summary.product_price);
                update_cart_num();
				left_time = result.cart_summary.left_time;
			}
		}
	});
}
function remove_cart_pro(rec_id){
	if(!confirm('确定不购买该商品？')) return;
	$.ajax({
		url:'/cart/remove_from_cart',
		data:{rec_id:rec_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if(result.msg) alert(result.msg);
			if(result.err==1) return false;
			if(result.err==2) 
			{
				location.href=location.href;
				return;
			}
			$('li#cart_rec_'+rec_id).remove();

			if (result.cart_summary!=undefined)
			{
				location.href=location.href;
			}
		}
	});
}
function load_city() {
    $(':input[name=city]')[0].options.length=1;
    $(':input[name=district]')[0].options.length=1;
    var parent_id =  $(':input[name=province]').val();
    if(!parent_id) return;
    $.ajax({
        url:'/region/load_region',
        data:{parent_id:parent_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
            sel = $(':input[name=city]')[0];
            for(i in result.data){
                sel.options.add(new Option(result.data[i].region_name,result.data[i].region_id));
            }
        }
    });
    return true;
} // End of load_city

function load_district() {
    $(':input[name=district]')[0].options.length=1;
    var parent_id =  $(':input[name=city]').val();
    if(!parent_id) return;
    $.ajax({
        url:'/region/load_region',
        data:{parent_id:parent_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
            if(result.data.length==0) $(':input[name=district]').css('display','none')
            sel = $(':input[name=district]')[0];
            for(i in result.data){
                sel.options.add(new Option(result.data[i].region_name,result.data[i].region_id));
            }
        }
    });
    return true;
} // End of load_district


// 检查地址表单
function check_address() {
    var movePhone=/^1[3,5,8][0-9]{9}/,
        postCode=/^\d{6}$/,
        tel=/(\d{2,5}-\d{7,8})/;
    if($('.address_block').css('display')=='none') return true;
    if(!$.trim($(':input[name=consignee]').val()))  $(':input[name=consignee]').addClass('err_input');
    if(!$.trim($(':input[name=address]').val())) $(':input[name=address]').addClass('err_input');
    if(!postCode.exec($.trim($(':input[name=zipcode]').val()))) $(':input[name=zipcode]').addClass('err_input');
    if(!movePhone.exec($.trim($(':input[name=mobile]').val()))) {
        $(':input[name=mobile]').addClass('err_input');
    }
    
    if(tel.exec($.trim($(':input[name=tel]').val())) || $.trim($(':input[name=tel]').val()) !=''){
        $(':input[name=tel]').addClass('err_input');
    }
    if(!$(':input[name=province]').val()) $(':input[name=province]').addClass('err_input');
    if(!$(':input[name=city]').val()) $(':input[name=city]').addClass('err_input');
    if($(':input[name=district]').css('display')!='none' && !$(':input[name=district]').val()){
        $(':input[name=district]').addClass('err_input');
    }
    return true;
} // End of check_address show_address_edit('185','roc','长岛路100号','200123','','13122641770','9_120_1055',0)

// 检查发票
function check_invoice(first_argument) {
    if($(':radio[name=need_invoice]:checked').val()==1){
       if(!$.trim($(':input[name=invoice_title]').val())) $(':input[name=invoice_title]').addClass('err_input'); 
    }
    return true;
} // End of check_invoice

function e_value(e) {
    v = $('#'+e).html();
    v = v.replace('￥','');
    if(!v) v=0;
    return parseFloat(v);
} // End of e_value


function check_use_balance () {
    if ($(':checkbox[name=use_balance]').attr('checked')) {
        var unpay_price = e_value('summary_unpay_price');
        var user_money = e_value('user_money');
        var balance = Math.min(unpay_price,user_money);
        balance = Math.max(balance,0.00);
        balance = number_format(balance,2);
        $(':input[name=balance]').attr('disabled',false).val(balance);

    }else{
        $(':input[name=balance]').val('0.00').attr('disabled',true);
    }
    reset_pay_list();
}

function check_balance () {
    //保证最大值不溢出，不为负
    var input_balance = parseFloat($(':input[name=balance]').val());
    if(isNaN(input_balance) || input_balance<0){
       $(':input[name=balance]').val('0.00');
       return; 
    }
    var unpay_price = e_value('summary_unpay_price');
    var user_money = e_value('user_money');
    var balance = Math.min(unpay_price,user_money);
    balance = Math.max(balance,0.00);
    if(input_balance>balance) input_balance = balance;
    $(':input[name=balance]').val(number_format(input_balance,2));
    reset_pay_list();
}

function reset_pay_list () {
    var unpay_price = e_value('summary_unpay_price');
    var balance = 0;
    if ($(':checkbox[name=use_balance]').attr('checked')) {
        balance = parseFloat($(':input[name=balance]').val());
        if(isNaN(balance)) balance = 0;
    };
    if(balance>= unpay_price){
		$('#is_pay_id').val('0');
		set_shipping_fee();
    }else{
		$('#is_pay_id').val('1');
    }
}

function init_checkout(){
    // 余额是否可以输入
    if (!$(':checkbox[name=use_balance]').attr('checked')){
        $(':input[name=balance]').attr('disabled',true);
    }
    reset_pay_list();
}

function set_shipping_fee() {
	var address_id = parseInt($(':radio[name=address_id]:checked').val());
	var province_id = 0;
    if(isNaN(address_id)) address_id=0;
	if(!address_id){
        province_id = $.trim($(':input[name=province]').val());
    }
	var pay_id = '';
	if($('#is_pay_id').val() == 0){
        pay_id = '5_';
    }
	if($('#is_pay_id').val() == 1){
		pay_id = $(':radio[name=pay_id]:checked').val();
	}
	if(address_id <= 0 && province_id <= 0) {
		return false;
	}
	if(!pay_id) {
		return false;
	}
    $.ajax({
        url:'/cart/set_shipping_fee',
        data:{address_id:address_id,province_id:province_id,pay_id:pay_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
            if(result.cart_summary){
                reset_cart_summary(result.cart_summary);
                check_balance();
                reset_pay_list();
            }
        }
    });
}


function get_shipping_fee() {
	var address_id = parseInt($(':radio[name=address_id]:checked').val());
	var province_id = 0;
    var district_id = 0;
    if(isNaN(address_id)) address_id=0;
	if(!address_id){
        province_id = $.trim($(':input[name=province]').val());
        district_id = $.trim($(':input[name=district]').val()); 
    }
	if(address_id <= 0 && province_id <= 0) {
		return false;
	}
	
    $.ajax({
        url:'/cart/get_shipping_fee',
        data:{address_id:address_id,province_id:province_id,district_id:district_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
			$('#shipping_fee_str').html(result.shipping_fee_str);
        }
    });
}

function pay_voucher (voucher_sn) {
    if (!voucher_sn) {
        voucher_sn = $.trim($(':input[name=voucher_sn]').val());
        if(voucher_sn=="请输入您的现金劵号码") voucher_sn='';
    };
    if (!voucher_sn) {
        alert('请输入现金券号');
        return false;
    };
	
	var address_id = parseInt($(':radio[name=address_id]:checked').val());
	var province_id = 0;
    if(isNaN(address_id)) address_id=0;
	if(!address_id){
        province_id = $.trim($(':input[name=province]').val());
    }
	var pay_id = 0;
	if($('#is_pay_id').val() == 0){
        pay_id = '5_';
    }
	if($('#is_pay_id').val() == 1){
		pay_id = $(':radio[name=pay_id]:checked').val();
	}
	
    $.ajax({
        url:'/cart/pay_voucher',
        data:{voucher_sn:voucher_sn,address_id:address_id,province_id:province_id,pay_id:pay_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
            if(result.html) $('#voucher_block').html(result.html);
            if(result.cart_summary){
                reset_cart_summary(result.cart_summary);
                check_balance();
                reset_pay_list();
            }
        }
    });
}

function unpay_voucher () {
	var address_id = parseInt($(':radio[name=address_id]:checked').val());
	var province_id = 0;
    if(isNaN(address_id)) address_id=0;
	if(!address_id){
        province_id = $.trim($(':input[name=province]').val());
    }
	var pay_id = 0;
	if($('#is_pay_id').val() == 0){
        pay_id = '5_';
    }
	if($('#is_pay_id').val() == 1){
		pay_id = $(':radio[name=pay_id]:checked').val();
	}
    $.ajax({
        url:'/cart/unpay_voucher',
        data:{address_id:address_id,province_id:province_id,pay_id:pay_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.msg) alert(result.msg);
            if(result.err) return false;
            if(result.html) $('#voucher_block').html(result.html);
            if(result.cart_summary){
                reset_cart_summary(result.cart_summary);
                check_balance();
                reset_pay_list();
            }
        }
    });
}

function reset_cart_summary(summary_cart){
    $('#summary_product_price').html('￥'+number_format(summary_cart.product_price,2));
    $('#summary_voucher').html('￥'+number_format(summary_cart.voucher,2));
    $('#summary_shipping_fee').html('￥'+number_format(summary_cart.shipping_fee,2));
    $('#summary_unpay_price').html('￥'+number_format(summary_cart.unpay_price+summary_cart.balance,2));
}


//提交订单的处理函数
function submit_cart() {
	//检查发票抬头
	if($(':radio[name=need_invoice]:checked').val() == 1 ){
		var invoice_title= $(':text[name=invoice_title]').val();
		if(invoice_title == '' || cnlength(invoice_title) < 1){
			alert("请填写发票抬头");
			return false;
		}
	}

    check_balance();//余额
    $('.err_input').each(function(i){$(this).removeClass('err_input')});
    var address_id = parseInt($(':radio[name=address_id]:checked').val());
    if(isNaN(address_id)) address_id=0;
    if(!address_id){
        check_address();
    }else{
        if($('#address_block').css('display')!='none'){
            alert('请先保存收货地址');
            return false;
        }
    }
    check_invoice();
    if($('.err_input').length>0) {
        $('.err_input:first').focus();
        return false;
    }
    if($('#is_pay_id').val() == 1){//$(
        if($(':radio[name=pay_id]:checked').length!=1){
            $(':radio[name=pay_id]:first').focus();
            alert('请选择支付方式');
            return false;
        }
    }
    
    // 收集数据，提交
    var data = {rnd:new Date().getTime(),address_id:address_id};
    if(!address_id){
        data['consignee'] = $.trim($(':input[name=consignee]').val());
        data['address'] = $.trim($(':input[name=address]').val());
        data['zipcode'] = $.trim($(':input[name=zipcode]').val());
        data['mobile'] = $.trim($(':input[name=mobile]').val());
        data['tel'] = $.trim($(':input[name=tel]').val());
        data['province'] = $.trim($(':input[name=province]').val());
        data['city'] = $.trim($(':input[name=city]').val());
        data['district'] = $.trim($(':input[name=district]').val());        
    }
    data['best_time'] = $(':radio[name=best_time]:checked').val();
    if(data['best_time']=='') data['best_time'] = $.trim($(':input[name=best_time_other]').val());
    if(data['best_time']==undefined) data['best_time']='';
    data['user_notice'] = $.trim($(':input[name=user_notice]').val());
    
    if($(':checkbox[name=use_balance]').attr('checked')){
        data['balance'] = $(':input[name=balance]').val();
    }else{
        data['balance'] = 0;
    }

    if($('#is_pay_id').val() == 1){
        data['pay_id'] = $(':radio[name=pay_id]:checked').val();
    }else{
        data['pay_id'] = 0;
    }
    if($(':radio[name=need_invoice]:checked').val()==1){
        data['invoice_title'] = $.trim($(':input[name=invoice_title]').val());
    }else{
        data['invoice_title'] = '';
    }
    $.ajax({
        url:'/cart/proc_checkout',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            if (result.msg) alert(result.msg);
            if (result.url) {location.href=base_url+result.url;};
            if (result.err) return false;
            if(result.order_id) location.href=base_url+'cart/success/'+result.order_id ;           
        }
    });
}

function load_address_form (address_id) {
    //点修改则使本行的单选框选中
    $("#address_"+address_id).find('input').attr('checked',true);
    $.ajax({
        url:'/cart/load_address_form',
        data:{address_id:address_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if (result.msg) alert(result.msg);
            if (result.err) return false;
            if(result.html) {
                $('#address_block').html(result.html).show();
                $('.address_btn_div').show();
            }
        }
    });
}

function cancel_address_form () {
    $('#address_block').html('').hide();
    $('.address_btn_div').hide();
	get_shipping_fee();
	set_shipping_fee();
}

function submit_address_form () {
    $('.err_input').each(function(i){$(this).removeClass('err_input')});
    check_address();
    if($('.err_input').length>0) {
        $('.err_input:first').focus();
        return false;
    }
    var address_id = $(':radio[name=address_id]:checked').val();
    var data={rnd:new Date().getTime(),address_id:address_id}
    data['address_id'] = address_id;
    data['consignee'] = $.trim($(':input[name=consignee]').val());
    data['address'] = $.trim($(':input[name=address]').val());
    data['zipcode'] = $.trim($(':input[name=zipcode]').val());
    data['mobile'] = $.trim($(':input[name=mobile]').val());
    data['tel'] = $.trim($(':input[name=tel]').val());
    data['province'] = $.trim($(':input[name=province]').val());
    data['city'] = $.trim($(':input[name=city]').val());
    data['district'] = $.trim($(':input[name=district]').val()); 
    $.ajax({
        url:'/cart/submit_address_form',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            if (result.msg) alert(result.msg);
            if (result.err) return false;
            if(result.html) $('ul.address').html(result.html);
            $('#address_block').html('').hide();          
        }
    });
}

function load_buy_buy_cart () {
    $.ajax({
        url:'/product_api/buy_buy_cart',
        data:{rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.err) return false;
            if(result.html) $('#buy_buy_block').html(result.html).css('display','');
        }
    });
}

function refresh_gifts (cart_price) {
    var gift_num=0;
    cart_price=parseFloat(cart_price);
    $('#cart_detail_gift div').each(function(){
        var limit_price = parseFloat($(this).attr('limit_price'));
        if(limit_price<=cart_price){
            $(this).css('display','block');
            gift_num += 1;
        }else{
            $(this).css('display','none');
        }
    });
    if(gift_num>0){
        $('#cart_detail_gift').css('display','');
    }else{
        $('#cart_detail_gift').css('display','none');
    }
}

function update_cart_num () {
    cart_num=getCookie('cart_num');
    if(cart_num) {
        $('#mainCartNum').html(cart_num + "件"); // 非团购页面购购车数量
        $('#mainCartNumTuan').html('('+cart_num + "件)"); // 团购页面购物车数量
    }
}
