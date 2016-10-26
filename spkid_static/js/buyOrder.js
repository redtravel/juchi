function submit_address_form() {
    $('.err_input').each(function(i) {
        $(this).removeClass('err_input')
    });
    check_address();
    if ($('.err_input').length > 0) {
        $('.err_input:first').focus();
        return false;
    }
    var address_id = $(':radio[name=address_id]:checked').val();
    var data = {rnd: new Date().getTime(), address_id: address_id}
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
        url: '/cart/submit_address_form',
        data: data,
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.html)
                $('ul.address').html(result.html);
            $('#address_block').html('').hide();
            init_price();
        }
    });
}

function check_address() {
    var movePhone = /^1[3,5,8][0-9]{9}/,
            postCode = /^\d{6}$/,
            tel = /(\d{2,5}-\d{7,8})/;
    if ($('.address_block').css('display') == 'none')
        return true;
    if (!$.trim($(':input[name=consignee]').val()))
        $(':input[name=consignee]').addClass('err_input');
    if (!$.trim($(':input[name=address]').val()))
        $(':input[name=address]').addClass('err_input');
    if (!postCode.exec($.trim($(':input[name=zipcode]').val())))
        $(':input[name=zipcode]').addClass('err_input');
    if (!movePhone.exec($.trim($(':input[name=mobile]').val()))) {
        $(':input[name=mobile]').addClass('err_input');
    }

    if (!tel.exec($.trim($(':input[name=tel]').val())) && $.trim($(':input[name=tel]').val()) != '') {
        $(':input[name=tel]').addClass('err_input');
    }
    if (!$(':input[name=province]').val())
        $(':input[name=province]').addClass('err_input');
    if (!$(':input[name=city]').val())
        $(':input[name=city]').addClass('err_input');
    if ($(':input[name=district]').css('display') != 'none' && !$(':input[name=district]').val()) {
        $(':input[name=district]').addClass('err_input');
    }
    return true;
}

function cancel_address_form() {
    $('#address_block').html('').hide();
    $('.address_btn_div').hide();
    init_price();
}

function get_shipping_fee() {
    var address_id = parseInt($(':radio[name=address_id]:checked').val());
    var province_id = 0;
    var district_id = 0;
    if (isNaN(address_id))
        address_id = 0;
    if (!address_id) {
        province_id = $.trim($(':input[name=province]').val());
        district_id = $.trim($(':input[name=district]').val());
    }
    if (address_id <= 0 && province_id <= 0) {
        return false;
    }

    $.ajax({
        url: '/cart/get_shipping_fee',
        data: {address_id: address_id, province_id: province_id, district_id: district_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            $('#shipping_fee_str').html(result.shipping_fee_str);
        }
    });
}

function set_shipping_fee() {
    var address_id = parseInt($(':radio[name=address_id]:checked').val());
    var province_id = 0;
    if (isNaN(address_id))
        address_id = 0;
    if (!address_id) {
        province_id = $.trim($(':input[name=province]').val());
    }
    var pay_id = '';
    if ($('#is_pay_id').val() == 0) {
        pay_id = '5_';
    }
    if ($('#is_pay_id').val() == 1) {
        pay_id = $(':radio[name=pay_id]:checked').val();
    }
    if (address_id <= 0 && province_id <= 0) {
        return false;
    }
    if (!pay_id) {
        return false;
    }
    $.ajax({
        url: '/cart/set_shipping_fee',
        data: {address_id: address_id, province_id: province_id, pay_id: pay_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.cart_summary) {
                reset_cart_summary(result.cart_summary);
                check_balance();
                reset_pay_list();
            }
        }
    });
}

function load_city() {
    $(':input[name=city]')[0].options.length = 1;
    $(':input[name=district]')[0].options.length = 1;
    var parent_id = $(':input[name=province]').val();
    if (!parent_id)
        return;
    $.ajax({
        url: '/region/load_region',
        data: {parent_id: parent_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            sel = $(':input[name=city]')[0];
            for (i in result.data) {
                sel.options.add(new Option(result.data[i].region_name, result.data[i].region_id));
            }
        }
    });
    return true;
}

function load_district() {
    $(':input[name=district]')[0].options.length = 1;
    var parent_id = $(':input[name=city]').val();
    if (!parent_id)
        return;
    $.ajax({
        url: '/region/load_region',
        data: {parent_id: parent_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.data.length == 0) {
                $(':input[name=district]').css('display', 'none');
            } else {
                $(':input[name=district]').css('display', 'inline-block');
            }
            sel = $(':input[name=district]')[0];
            for (i in result.data) {
                sel.options.add(new Option(result.data[i].region_name, result.data[i].region_id));
            }
        }
    });
    return true;
}



function load_address_form(address_id) {
    //点修改则使本行的单选框选中
    $("#address_" + address_id).find('input').attr('checked', true);
    $.ajax({
        url: '/cart/load_address_form',
        data: {address_id: address_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.html) {
                $('#address_block').html(result.html).show();
                $('.address_btn_div').show();
            }
        }
    });
    init_price();
}



function use_balance()
{

    init_price();

}

function use_alipay()
{
    init_price();
    init_payment()

}

function use_unionpay()
{
    init_price();
    init_payment();
}

function init_price() {
    // 区域运费
    var province_id = $(':radio:checked[name=address_id]').attr('province_id');
    var shipping_fee_total = 0;
    var balance_payment_amount = 0;
    var cart_amount = 0;
    for (i in cart_summary['product_list'])
    {
        var provider = cart_summary['product_list'][i];
        var provider_id = provider['provider_id'];
        var product_price = provider['product_price'];
        var voucher = provider['voucher'];
        var shipping_fee_config =  provider['shipping_fee_config'];
        var voucher_payment_amount = voucher ? parseFloat(voucher['payment_amount']) : 0;
        var shipping_fee = 0;
        var free_shipping_price = 0;
        if(province_id>0){
            if(typeof (shipping_fee_config[province_id]) == 'undefined'){
                shipping_fee = region_shipping_fee[province_id][0];
                free_shipping_price = region_shipping_fee[province_id][1];
            }else{
                shipping_fee = shipping_fee_config[province_id][0];
                free_shipping_price = shipping_fee_config[province_id][1];
            }
        }
        if (provider['product_price'] >= free_shipping_price) {
            shipping_fee = 0; // 如果达到免邮标准
        }
        shipping_fee_total += shipping_fee;
        var subtotal = product_price - voucher_payment_amount + shipping_fee;
        $('#product_price_' + provider_id).html('合计金额 : ' + product_price + '元');
        $('#shipping_fee_' + provider_id).html('运费 : ' + shipping_fee + '元');
        $('#subtotal_' + provider_id).html('总合计 : ' + subtotal + '元');
        if (voucher) {
            $('#voucher_' + provider_id).html('现金券抵扣 : ' + voucher_payment_amount + '元');
        }
    }
    var cart_total = cart_summary['product_price'] - cart_summary['voucher'] + shipping_fee_total;
    $('#shipping_fee').html(shipping_fee_total + "元");
    $('#product_total').html(cart_total + '元');
    if ($('#chk_use_balance').attr('checked')) {
        balance_payment_amount = parseFloat($('#chk_use_balance').val());
    }
    cart_amount = Math.max(cart_total - balance_payment_amount, 0);
    $('#cart_amount').html(cart_amount);
    if(cart_amount<=0){
        $('#tr_alipay').hide();
        $('.pay_way').hide();
    }else{
        $('#tr_alipay').show();
        $('.pay_way').show();
    }
    return cart_amount;
}

// 初始化支付页面
function init_payment()
{
    //  如果用户没有余额，使用余额的选项不能选
    if(parseFloat($('#chk_use_balance').val())<=0) $('#chk_use_balance').attr('checked', false).attr('disabled', true);
    var pay_method = $(':radio:checked[name=buy_method_chk]').val();
    if(pay_method!='unionpay'){
        $('#bank_list').hide();
    }else{
        $('#bank_list').show();
        select_bank()
    }
}

function select_bank()
{
    var bank_code = $(':radio:checked[name=bank_code]').val();
    if (bank_code == undefined) {
        $('#current_bank_code').hide();
        return;
    }
    $('#current_bank_code').html($('label[for=bank_code_' + bank_code + ']').html()).show();

}

/**
 * 提交购物车
 * @returns {Boolean}
 */
var last_cart_submit_time = 0
function submit_cart() {
    var cart_amount = init_price();
    if(new Date().getTime() - last_cart_submit_time < 10000){
        alert('请不要重复提交');
        return false;
    }
    // 检查支付方式
    if(cart_amount>0){
        var pay_method = $(':radio:checked[name=buy_method_chk]').val();
        if(!pay_method){
            alert('请选择支付方式');
            return false;
        }
        if(pay_method=='unionpay'){
            var bank_code = $(':radio:checked[name=bank_code]').val();
            if(!bank_code){
                alert('请选择支付银行');
                return false;
            }
        }
    }
    
    // 收货地址
    if ($('#address_block').css('display') != 'none') {
        alert('请先保存收货地址');
        return false;
    }
    var address_id = parseInt($(':radio[name=address_id]:checked').val());
    if(!address_id){
        alert('请选择收货地址');
        return false;
    }
    
    
    // 收集数据，提交
    var data = {rnd:new Date().getTime(),address_id:address_id};
    data['use_balance'] = $('#chk_use_balance').attr('checked')?1:0;
    data['pay_method'] = $(':radio:checked[name=buy_method_chk]').val();
    data['bank_code'] = $(':radio:checked[name=bank_code]').val();
    if(!data['pay_name']) data['payname'] = '';
    if(!data['bank_code']) data['bank_code'] = '';
    last_cart_submit_time = new Date().getTime();
    $.ajax({
        url:'/cart/proc_checkout',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) alert(result.msg);
            if (result.url) {location.href=base_url+result.url;};
            if (result.err) return false;
            if(result.order_id) location.href=base_url+'cart/success/'+result.order_id ; 
        },
        error:function()
        {
            last_cart_submit_time = 0;
        }
    });
}

/*
 * 虚拟购物流程
 */

/**
 * 实始化价格
 * @returns {cart_amount|Number}
 */
function init_price_virtual()
{
    var cart_total = parseFloat(product_amount);
    var balance_payment_amount = 0;
    if ($('#chk_use_balance').attr('checked')) {
        balance_payment_amount = parseFloat($('#chk_use_balance').val());
    }
    var cart_amount = Math.max(cart_total - balance_payment_amount, 0);
    //console.log(cart_total);
    $('#cart_amount').html(cart_amount);
    if(cart_amount<=0){
        $('#tr_alipay').hide();
        $('.pay_way').hide();
    }else{
        $('#tr_alipay').show();
        $('.pay_way').show();
    }
    return cart_amount;
}

function use_balance_virtual()
{
    init_price_virtual();
}

function use_unionpay_virtual()
{
    init_price_virtual();
    init_payment();
}

function use_alipay_virtual()
{
    init_price_virtual();
    init_payment()
}

function submit_cart_virtual()
{
    var cart_amount = init_price_virtual();
    if(new Date().getTime() - last_cart_submit_time < 10000){
        alert('请不要重复提交');
        return false;
    }
    
    // 收货人信息
    var consignee = $.trim($(':input[name=consignee]').val());
    var mobile = $.trim($(':input[name=mobile]').val());
    if(!consignee){
        $(':input[name=consignee]').addClass('err_input');
        alert('请填写购买人姓名');
        return false;
    }
    var movePhone = /^1[3,5,8][0-9]{9}/;
    if(!movePhone.exec(mobile)){
        $(':input[name=mobile]').addClass('err_input');
        alert('请填写购买人的手机号码');
        return false;
    }
    
    // 检查支付方式
    if(cart_amount>0){
        var pay_method = $(':radio:checked[name=buy_method_chk]').val();
        if(!pay_method){
            alert('请选择支付方式');
            return false;
        }
        if(pay_method=='unionpay'){
            var bank_code = $(':radio:checked[name=bank_code]').val();
            if(!bank_code){
                alert('请选择支付银行');
                return false;
            }
        }
    }
    
  
    
    // 收集数据，提交
    var data = {rnd:new Date().getTime()};
    data['consignee'] = consignee;
    data['mobile'] = mobile;
    data['sub_id'] = sub.sub_id;
    data['num'] = parseInt($('div#num').html());
    data['use_balance'] = $('#chk_use_balance').attr('checked')?1:0;
    data['pay_method'] = $(':radio:checked[name=buy_method_chk]').val();
    data['bank_code'] = $(':radio:checked[name=bank_code]').val();
    if(!data['pay_name']) data['payname'] = '';
    if(!data['bank_code']) data['bank_code'] = '';
    last_cart_submit_time = new Date().getTime();
    $.ajax({
        url:'/virtual/proc_checkout',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) alert(result.msg);
            if (result.url) {location.href=base_url+result.url;};
            if (result.err) return false;
            if(result.order_id) location.href=base_url+'cart/success/'+result.order_id ; 
        },
        error:function()
        {
            last_cart_submit_time = 0;
        }
    });
}
