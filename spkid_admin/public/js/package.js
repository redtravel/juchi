function change_area_type () {
	var area_type = $(':input[name=area_type]').val();
	if (area_type==1) {
		$('tr.area_type_tr_2').hide();
		$('tr.area_type_tr_1').show();
	}else{
		$('tr.area_type_tr_1').hide();
		$('tr.area_type_tr_2').show();
	};
}

function check_package () {
	var package_id = $(':input[name=package_id]').val();
	$.ajax({
		url:'package/operate',
		data:{package_id:package_id, op:'check', rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=location.href;
		}
	});
}

function over_package () {
	var package_id = $(':input[name=package_id]').val();
	var over_note = $.trim($(':input[name=over_note]').val());
	if(!over_note) {
		alert('请填写停用说明');
		return false;
	}
	$.ajax({
		url:'package/operate',
		data:{package_id:package_id, op:'over', over_note:over_note, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=location.href;
		}
	});
}

function add_config () {
	var content = $('div.package_config_item_0').clone();
	$(':input[name="goods_number[]"]', content).val('');
	$(':input[name="goods_price[]"]', content).val('');
	$(':input[name="shop_price[]"]', content).val('');
	$(':input[name="market_price[]"]', content).val('');
	$('span.op', content).html('[-]').attr('onclick','remove_config(this)');
	$('div.package_config_item_0').parent().append(content);
}

function remove_config (obj) {
	$(obj).parent().remove();
}

function add_area () {
	var package_id = $(':hidden[name=package_id]').val();
	var form = $('form[name=form_add_area]');
	var area_type = $(':input[name=area_type]', form).val();
	var area_name = $.trim($(':input[name=area_name]', form).val());
	var min_number = $(':input[name=min_number]', form).val();
	var sort_order = $.trim($(':input[name=sort_order]', form).val());
	var area_text = CKEDITOR.instances.area_text.getData();
	
	if(min_number==undefined){
		min_number = 1;
	}else if(area_type==1){
		min_number = parseInt(min_number);
		if(isNaN(min_number) || min_number<1){
			alert('请填写最小购买数量');
			return;
		}
	}

	$.ajax({
		url:'package/proc_add_area',
		data:{package_id:package_id,area_type:area_type, area_name:area_name, 
			min_number:min_number, sort_order:sort_order, area_text:area_text, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=$('base').attr('href')+"/package/edit/"+package_id+"?tab=1"
		}
	});
}

function remove_area (area_id) {
	var package_id = $(':hidden[name=package_id]').val();
	$.ajax({
		url:'package/delete_area',
		data:{package_id:package_id,area_id:area_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=$('base').attr('href')+"/package/edit/"+package_id+"?tab=1"
		}
	});
}

function cancel_area_edit () {
	var form = $('form[name=form_add_area]');
	form.attr('action','javascript:add_area()');
	$('tr:eq(1)',form).css('display','');
	$(':input[name=mycancel]',form).css('display','none');
	CKEDITOR.instances.area_text.setData('');
	change_area_type();
}

function edit_area (area_id) {
	var form = $('form[name=form_add_area]');
	form.attr('action','javascript:proc_edit_area('+area_id+')');
	$('tr.area_type_tr_1', form).css('display','none');
	$('tr.area_type_tr_2', form).css('display','none');
	$('tr.area_text_tr',form).css('display','');
	$(':input[name=mycancel]',form).css('display','');
	CKEDITOR.instances.area_text.setData($('td.area_text_'+area_id).html());
}

function proc_edit_area (area_id) {
	var package_id = $(':hidden[name=package_id]').val();
	var area_text = CKEDITOR.instances.area_text.getData();
	$.ajax({
		url:'package/proc_edit_area',
		data:{package_id:package_id,area_id:area_id,area_text:area_text,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			location.href=$('base').attr('href')+"/package/edit/"+package_id+"?tab=1";
		}
	});
}

function check_all() {
	if($(':checkbox[name=check_all_box]').attr('checked'))
		$(':checkbox[name=product_id]').attr('checked',true);
	else
		$(':checkbox[name=product_id]').attr('checked',false);
}

function add_product () {
	var container = $('div#listDiv');
	var package_id = $(':hidden[name=package_id]').val();
	var area_id = $(':input[name=area_id]',container).val();
	if(!area_id) {
		alert('请选择区域');
		return false;
	}
	var product = '';
	$(':checkbox:checked[name=product_id]').each(function(i){
		var product_id = $(this).val();
		var color_id = $(':input[name=cs_'+product_id+']', container).val();
		if(color_id == undefined) color_id = 0;
		product += '|'+product_id+'-'+color_id;	
	});
	if(product==''){
		alert('请选择商品');
		return false;
	}
	$.ajax({
		url:'package/add_product',
		data:{package_id:package_id, area_id:area_id, product:product, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result) {
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			$('div#product_list').html(result.data);
		}
	});

}

function remove_product (rec_id) {
	var package_id = $(':hidden[name=package_id]').val();
	$.ajax({
		url:'package/delete_product',
		data:{package_id:package_id, rec_id:rec_id, rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err) {return false;};
			$('tr.rec_'+rec_id).remove();
		}
	});
}
