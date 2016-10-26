function search_user () {
	var user_name = $.trim($(':input[name=user_name]').val());
	if(!user_name){
		alert('请填写搜索关键字');
		return false;
	}
	var sel = $(':input[name=user_id]');
	$.ajax({
		url:'order_api/search_user',
		data:{user_name:user_name,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err) {return false};
			sel[0].options.length=0;
			for(i in result.user_list){
				var user = result.user_list[i];
				var val = user.user_name;
				if(user.real_name) val += ' [ 真实姓名:'+user.real_name+' ]';
				if(user.email) val += ' [ 邮箱:'+user.email+' ]';
				if(user.mobile) val += ' [ 手机:'+user.mobile+' ]';
				sel[0].options.add(new Option(val,user.user_id));
			}
		}
	});
}

function submit_add_product () {
	var order_id = $(':hidden[name=order_id]').val();
	var act = $(':hidden[name=act]').val();	
	if (act=='add') {
		location.href=base_url+"order/consignee/"+order_id+'?act=add';
	}else{
		location.href=base_url+"order/info/"+order_id;
	}
}

function switch_lock (op) {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/switch_lock',
		data:{order_id:order_id,op:op,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function odd()
{
    var order_id = $(':hidden[name=order_id]').val();
    if(!confirm('确定要将该订单标记为问题单？')) return false;
    $.ajax({
            url:'order_api/odd',
            data:{order_id:order_id,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            success:function (result) {
                    if(result.msg) alert(result.msg);
                    if(result.err) return false;
                    location.href=location.href;
            }
    });
}

function odd_cancel()
{
    var order_id = $(':hidden[name=order_id]').val();
    if(!confirm('确定要取消问题单标记？')) return false;
    $.ajax({
            url:'order_api/odd_cancel',
            data:{order_id:order_id,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            success:function (result) {
                    if(result.msg) alert(result.msg);
                    if(result.err) return false;
                    location.href=location.href;
            }
    });
}

function post_advice () {
	var order_id = $(':hidden[name=order_id]').val();
	var type_id = $(':input[name=advice_type_id]').val();
	var advice_content = $.trim($(':input[name=advice_content]').val());
	if(type_id<1){
		alert('请选择意见类型');
		return false;
	}
	if(advice_content==''){
		alert('请填写意见内容');
		return false;
	}
	$.ajax({
		url:'order_api/post_advice',
		data:{order_id:order_id,type_id:type_id,advice_content:advice_content,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function add_product (product_id) {
	var order_id = $(':hidden[name=order_id]').val();
        var depot_id = $(':hidden[name=depot_id]').val();
	var tr = $('tr.p_'+product_id);
	if(tr.length<1){
		alert('商品不存在');
		return false;
	}
	var cs = $(':input[name=color_size]',tr).val();
	var num = parseInt($(':input[name=num]',tr).val());
	if(isNaN(num) || num<1){
		alert('请填写商品数量');
		return false;
	}
	cs = cs.split('|');
	var color_id = parseInt(cs[0]);
	var size_id = parseInt(cs[1]);
	
    $.ajax({
    	url:'order_api/add_product',
    	data:{order_id:order_id,product_id:product_id,color_id:color_id,size_id:size_id,num:num,depot_id:depot_id,rnd:new Date().getTime()},
    	dataType:'json',
    	type:'POST',
    	success:function(result){
    		if(result.msg) alert(result.msg);
			if(result.err) return false;
			$('#product_list').html(result.data);
    	}
    });
}

function remove_product (op_id) {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/remove_product',
    	data:{order_id:order_id,op_id:op_id,rnd:new Date().getTime()},
    	dataType:'json',
    	type:'POST',
    	success:function(result){
    		if(result.msg) alert(result.msg);
			if(result.err) return false;
			$('#product_list').html(result.data);
    	}
	});
}

function pay_balance (argument) {
	var order_id = $(':hidden[name=order_id]').val();
	var balance_amount = parseFloat($(':input[name=balance_amount]').val());
	if(isNaN(balance_amount) || balance_amount<=0){
		alert('请输入支付金额');
		return false;
	}
	$.ajax({
		url:'order_api/pay_balance',
		data:{order_id:order_id,balance_amount:balance_amount,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			alert('支付成功');
			location.href=location.href;
		}
	});
}

function choice_voucher () {
	$(':input[name=voucher_sn]').val($(':input[name=available_voucher]').val());
}

function pay_voucher () {
	var order_id = $(':hidden[name=order_id]').val();
	var voucher_sn = $.trim($(':input[name=voucher_sn]').val());
	if(!voucher_sn){
		alert('请输入现金券号');
		return false;
	}
	$.ajax({
		url:'order_api/pay_voucher',
		data:{order_id:order_id,voucher_sn:voucher_sn,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function remove_voucher () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/remove_voucher',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function invalid () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/invalid',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			if(result.redirect)
				location.href = $('base').attr('href')+'order/';
			else
				location.href=location.href;
		}
	});
}

function free_shipping_fee () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/free_shipping_fee',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}
function reset_shipping_fee () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/reset_shipping_fee',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}


function reset_saler () {
    var order_id = $(':hidden[name=order_id]').val();
    var saler = $.trim($(':input[name=saler]').val());
    if(saler=='') {
            alert('请输入销售员');
            return false;
    }	
    $.ajax({
            url:'order_api/reset_saler',
            data:{order_id:order_id,saler:saler,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            success:function(result){
                    if(result.msg) alert(result.msg);
                    if(result.err) return false;
                    location.href=location.href;
            }
    });
}

function update_shipping_fee () {
	var order_id = $(':hidden[name=order_id]').val();
	var new_shipping_fee = $.trim($(':input[name=new_shipping_fee]').val());
	if(new_shipping_fee==''||isNaN(parseFloat(new_shipping_fee))) {
		alert('请输入新运费');
		return false;
	}	
	$.ajax({
		url:'order_api/update_shipping_fee',
		data:{order_id:order_id,new_shipping_fee:new_shipping_fee,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function order_confirm () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/confirm',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function order_unconfirm () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/unconfirm',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function order_shipping (shipping_true,invoice_no) {
   // if ($('#h_shipping').dialog('isOpen')) {
        var order_id = $(':hidden[name=order_id]').val();
        if (invoice_no == undefined) var invoice_no = $('[type=text][name=invoice_no]').val();
        if (shipping_true == undefined) var shipping_true = $('[type=radio][name=shipping_true]:checked').val();
        if(invoice_no=='') {
            alert('请填写运单号');            
            return false;
        }
        $.ajax({
            url:'order_api/shipping',
            data:{order_id:order_id,shipping_true:shipping_true,invoice_no:invoice_no,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            success:function(result){
                if(result.confirm){
                    if(confirm(result.msg)){
                        order_shipping(0,invoice_no);
                    }else{
                        return false;
                    }
                }else{
                    if(result.msg) alert(result.msg);
                    if(result.err) return false;
                    location.href=location.href;
                }			
            }
        });
    //} 
    //else {
        //$('#h_shipping').dialog('open');
    //}
}

function add_payment () {
	var order_id = $(':hidden[name=order_id]').val();
	var pay_id = $(':input[name=payment_pay_id]').val();
	var payment_money = $(':input[name=payment_payment_money]').val();
	var payment_account = $(':input[name=payment_payment_account]').val();
	var trade_no = $(':input[name=payment_trade_no]').val();
	var payment_remark = $(':input[name=payment_payment_remark]').val();
	$.ajax({
		url:'order_api/payment',
		data:{order_id:order_id,pay_id:pay_id,
			payment_money:payment_money,payment_account:payment_account,
			trade_no:trade_no,payment_remark:payment_remark,
			rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function remove_payment (payment_id) {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/delete_payment',
		data:{order_id:order_id,payment_id:payment_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function order_pay () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/pay',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}
function order_unpay () {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/unpay',
		data:{order_id:order_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}
function order_deny () {
	var order_id = $(':hidden[name=order_id]').val();
	location.href=$('base').attr('href')+'order/deny/'+order_id;
}

function edit_price (op_id) {
	var order_id = $(':hidden[name=order_id]').val();
	var reason = prompt('请输入原因','')
	if(reason === null){return false}
	var new_price = prompt('请输入新价格','');
	if (new_price===null) {return false};
	reason = $.trim(reason)
	if (reason=='') {
		alert('请填写原因')
		return false
	};
	new_price = parseFloat(new_price);
	if (isNaN(new_price)||new_price<0) {
		alert('价格错误');
		return false;
	};
	$.ajax({
		url:'order_api/edit_price',
		data:{order_id:order_id,op_id:op_id,new_price:new_price,reason:reason,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function load_package (package_id,extension_id) {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/load_package',
		data:{order_id:order_id,package_id:package_id,extension_id:extension_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			$('#listDiv').html(result.data);
		}
	});
}

function add_package_product (product_id) {
	var tr = $('tr.pp_'+product_id);
	var sub_id = $(':input[name=color_size]',tr).val();
	var cs_name = $(':input[name=color_size] option:selected',tr).text();
	cs_name = cs_name.replace(/\[.*\]/g,'');
	var area_name = $(':hidden[name=area_name]',tr).val();
	var area_id = $(':hidden[name=area_id]',tr).val();
	var tr_clone = tr.clone();
	$('td:first',tr_clone).html('【'+area_name+'】'+$('td:eq(0)',tr_clone).html());
	tr_clone.removeClass('pp_'+product_id).addClass('pp');
	$('td:eq(5)',tr_clone).html(cs_name);
	var html_str = '<a href="javascript:void(0)" onclick="javascript:remove_package_product(this)">移除</a>';
	html_str += '<input type="hidden" name="sub_id" value="'+sub_id+'">';
	$('td:last',tr_clone).html(html_str);
	$('tr.package_op_tr').before(tr_clone);
}

function remove_package_product (obj) {
	var obj = $(obj);
    var tr = obj.parent().parent();
    tr.remove();
}

function add_package () {
	var order_id = $(':hidden[name=order_id]').val();
	var package_id = $(':hidden[name=package_id]').val();
	var sub_ids = new Array();
	$('tr.pp').each(function(i){
		tr = $(this);
		var sub_id = $(':hidden[name=sub_id]',tr).val();
		sub_ids.push(sub_id);
	});
	$.ajax({
		url:'order_api/add_package',
		data:{order_id:order_id,package_id:package_id,sub_ids:sub_ids.join('|'),rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function remove_package (extension_id) {
	var order_id = $(':hidden[name=order_id]').val();
	$.ajax({
		url:'order_api/remove_package',
		data:{order_id:order_id,extension_id:extension_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function change_source () {
	var source_id = $(':input[name=source_id]').val();
	var pay_sel = $(':input[name=pay_id]');
	var shipping_sel = $(':input[name=shipping_id]');
	pay_sel[0].options.length=1;
	shipping_sel[0].options.length=1;
	if(!source_id) return;
	$.ajax({
		url:'order/load_pay_list',
		data:{source_id:source_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			for(i in result.data) pay_sel[0].options.add(new Option(result.data[i],i));
		}
	});
}

function change_pay () {
	var order_id = $(':hidden[name=order_id]').val();
	var source_id = $(':input[name=source_id]').val();
	var pay_id = $(':input[name=pay_id]').val();
	var shipping_sel = $(':input[name=shipping_id]');
	shipping_sel[0].options.length=1;
	if(!pay_id) return;
	$.ajax({
		url:'order/load_shipping_list',
		data:{order_id:order_id,source_id:source_id,pay_id:pay_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			for(i in result.data) shipping_sel[0].options.add(new Option(result.data[i],i));
		}
	});
}

function change_routing () {
	var order_id = $(':hidden[name=order_id]').val();
	var source_id = $(':input[name=source_id]').val();
	var pay_id = $(':input[name=pay_id]').val();
	var shipping_id = $(':input[name=shipping_id]').val();
	if(!shipping_id) {
		alert('请选择配送方式');
		return false;
	}
	
	$.ajax({
		url:'order/change_routing',
		data:{order_id:order_id,source_id:source_id,pay_id:pay_id,shipping_id:shipping_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function edit_invoice_no(){
	var order_id = $(':hidden[name=order_id]').val();
	var invoice_no = prompt('请输入运单号','');
	if(invoice_no==null) return false;
	$.ajax({
		url:'order_api/edit_invoice_no',
		data:{order_id:order_id,invoice_no:invoice_no,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function edit_real_shipping_fee(){
	var order_id = $(':hidden[name=order_id]').val();
	var real_shipping_fee = prompt('请输入实际运费','');
	if(real_shipping_fee==null) return false;
	$.ajax({
		url:'order_api/edit_real_shipping_fee',
		data:{order_id:order_id,real_shipping_fee:real_shipping_fee,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=location.href;
		}
	});
}

function load_address () {
	var order_id = $(':hidden[name=order_id]').val();
	var address_id = $(':input[name=address_id]').val();
	if(address_id=='') return false;
	var act = $(':hidden[name=act]').val();	
	if (act=='add') {
		location.href=base_url+"order/consignee/"+order_id+'?act=add&address_id='+address_id;
	}else{
		location.href=base_url+"order/consignee/"+order_id+'?address_id='+address_id;
	}
	
}
