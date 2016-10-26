function change_campaign_type () {
	var campaign_type = $(':input[name=campaign_type]').val();
	var config = voucher_config[campaign_type];

	if(config.sys){
		$('tr#tr_voucher_name').css('display','');
		$('tr#tr_voucher_amount').css('display','');
		$('tr#tr_min_order').css('display','');
		$('tr#tr_exp_days').css('display','');
	} else {
		$('tr#tr_voucher_name').css('display','none');
		$('tr#tr_voucher_amount').css('display','none');
		$('tr#tr_min_order').css('display','none');
		$('tr#tr_exp_days').css('display','none');
	}

	if (config.worth) {
		$('tr#tr_worth').css('display','');
	} else {
		$('tr#tr_worth').css('display','none');
	}

	if (config.logo) {
		$('tr#tr_logo').css('display','');
	} else {
		$('tr#tr_logo').css('display','none');
	}
}

function search_product () {
	listTable.filter['campaign_id'] = $(':hidden[name=campaign_id]').length>0 ? $(':hidden[name=campaign_id]').val():0;
	var product_ids = new Array();
	$(':hidden[name="product_ids[]"]').each(function(){
		product_ids.push($(this).val());
	});
	listTable.filter['product_ids'] = product_ids.join('|');
	listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
	listTable.filter['category_id'] = $.trim($(':input[name=category_id]').val());
	listTable.filter['brand_id'] = $.trim($(':input[name=brand_id]').val());
	listTable.filter['min_price'] = $.trim($('input[type=text][name=min_price]').val());
	listTable.filter['max_price'] = $.trim($('input[type=text][name=max_price]').val());
	listTable.loadList();
}

function check_all() {
	if($(':checkbox[name=check_all_box]').attr('checked'))
		$(':checkbox[name=product_id]').attr('checked',true);
	else
		$(':checkbox[name=product_id]').attr('checked',false);
}

function add_product () {
	var campaign_id = $(':hidden[name=campaign_id]').length>0 ? $(':hidden[name=campaign_id]').val():0;
	
	var product_ids = new Array();
	$(':checkbox:checked[name=product_id]').each(function(){
		product_ids.push($(this).val());
	});
	product_ids = product_ids.join('|');

	var old_product_ids = new Array();
	$(':hidden[name="product_ids[]"]').each(function(){
		old_product_ids.push($(this).val());
	});
	old_product_ids = old_product_ids.join('|');

	$.ajax({
		url:'voucher/add_product',
		data:{campaign_id:campaign_id, product_ids:product_ids, old_product_ids:old_product_ids, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			$('div#product_list').html(result.product_list);
		}
	});
}

function remove_product (product_id) {
	var campaign_id = $(':hidden[name=campaign_id]').length>0 ? $(':hidden[name=campaign_id]').val():0;
	if (!campaign_id) {$('tr.pro_'+product_id).remove(); return;};
	$.ajax({
		url:'voucher/remove_product',
		data:{campaign_id:campaign_id,product_id:product_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			$('tr.pro_'+product_id).remove();
		}
	});
}

function operate_campaign (operation) {
	if (!confirm('确定要执行该操作？')) {return false;};
	var campaign_id = $(':hidden[name=campaign_id]').val();
	var stop_reason = $(':input[name=stop_reason]').val();
	$.ajax({
		url:'voucher/operate',
		data:{campaign_id:campaign_id, operation:operation, stop_reason:stop_reason, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=location.href;
		}
	});
}

function add_release () {
	var campaign_id = $(':hidden[name=campaign_id]').val();
	location.href=$('base').attr('href')+"voucher/add_release/"+campaign_id;
}

function change_release_rule () {
	var rule = $(':input[name=rule]').val();
	$('tr[class^=rule_]').css('display','none');
	$('tr.rule_'+rule).css('display','');
}

function operate_release (operation) {
	if (!confirm('确定要执行该操作？')) {return false;};
	var campaign_id = $(':hidden[name=campaign_id]').val();
	var release_id = $(':hidden[name=release_id]').val();
	var back_note = $(':input[name=back_note]').val();
	$.ajax({
		url:'voucher/operate_release',
		data:{campaign_id:campaign_id, release_id:release_id, operation:operation, back_note:back_note, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function (result) {
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=location.href;
		}
	});
}