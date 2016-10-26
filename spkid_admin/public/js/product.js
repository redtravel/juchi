function cs_group_id_change () {
	var group_id = $('select[name=cs_color_group_id]').val();
	var color_select = $('select[name=cs_color_id]');
	color_select[0].options.length=0;
	$.ajax({
		url:'product_api/color_list',
		data:{group_id:group_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err!=0) {return;};
			for(i in result.data){
				color_select[0].options.add(new Option(result.data[i],i));
			}
		}
	});
}

function add_color () {
	var product_id = $(':hidden[name=product_id]').val();
	var color_id = parseInt($('select[name=cs_color_id]').val());
	var color_name = $("select[name=cs_color_id]").find("option:selected").text();
	if(isNaN(color_id) || color_id<1){
		alert('请选择颜色');
		return false;
	}
	if ($('table#cs_color_'+color_id).length>0) {
		alert('该颜色已添加，不能重复操作。');
		return false;
	};
	// add the table
	var html_str = '<table class="access_list" id="cs_color_'+color_id+'" cellspacing=0 cellpadding=0>';
	html_str += '<tr>';
	html_str += '<td colspan="2" class="access_left" style="text-align:center" >';
	html_str += '<strong>'+color_name+'</strong><a href="javascript:remove_color('+color_id+'); ">[删]</a>&nbsp;';
	html_str += '排序：<span onclick="javascript:listTable.edit(this, \'product_api/sort_sub\', '+product_id+', '+color_id+')" title="点击修改内容">0</span>';
	html_str += '</td>';
	html_str += '</tr>';
	html_str += '<tr>';
	html_str += '<td class="access_left" style="text-align:left">';
	html_str += '尺码：<select name="cs_size_src"></select><input type="button" value="添加" onclick="add_size('+color_id+');">';
	html_str += '<br>供应商条码：<input type="text" id="provider_barcode" />';
	html_str += '</td>';
	html_str += '<td class="cs_table_2"></td>';
	html_str += '</tr>';
	html_str += '<tr><td class="access_left" style="text-align:left" class="cs_table_3">';
	html_str += '<form id="cs_upload_'+color_id+'" action="product_api/add_gallery" method="POST" enctype="multipart/form_data">';
	//html_str += '<label><input type="radio" name="cs_upload_image_type" value="default">默认</label><br><label><input type="radio" name="cs_upload_image_type" value="part">局部</label><br><label><input type="radio" name="cs_upload_image_type" value="tonal">色片</label><br>';
	html_str += '<label><input type="radio" name="cs_upload_image_type" value="default">默认</label><br><label><input type="radio" name="cs_upload_image_type" value="part">局部</label><br>';
	html_str += '<input type="file" name="cs_upload_image"><br>';
	html_str += '<input type="hidden" name="cs_upload_color_id" value="'+color_id+'">';
	html_str += '<input type="hidden" name="cs_upload_product_id" value="'+product_id+'">';
	html_str += '<br/><input type="submit" name="mysubmit" value="上传">';
	html_str += '</form>';
	html_str += '</td></td><td class="cs_table_4"></td></tr>';
	html_str += '</table>';
	html_str += '<div class="span5"></div>';
	$('td#cs_list').append($(html_str));
	var container = $('table#cs_color_'+color_id);
	
	ajax_form(color_id);
	
	$.ajax({
		url:'product_api/size_list',
		data:{rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg);};
			if (result.err!=0) {return;};
			var size_select = $('select[name=cs_size_src]', container);
			for(i in result.data){
				size_select[0].options.add(new Option(result.data[i],i));
			}
			$('select[name=cs_size_src]').selected({searchBox:1,maxHeight:300});//for amazeui style
		}
	});
}

function ajax_form (color_id) {
	$('form#cs_upload_'+color_id).ajaxForm({
		clearForm:true,
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options){
			var container = $('table#cs_color_'+color_id);	
			var image_type=$(':radio:checked[name=cs_upload_image_type]',jqForm).val();
			
			if (image_type!='default' && image_type!='part' && image_type!='tonal') {
				alert('请选择图片类型');
				return false;
			};
			if (image_type=='default' && $('div.cs_image_default', container).length>0) {
				if(!confirm('您的操作将覆盖默认图，确认操作？')) return false;
			};
			if (image_type=='tonal' && $('div.cs_image_tonal', container).length>0) {
				if(!confirm('您的操作将覆盖色片图，确认操作？')) return false;
			};
			var file = $(':file[name=cs_upload_image]', container).val();
			if (!file) {
				alert('请选择图片');
				return false;
			};
			return true;
		},
		success:function(result){
			var container = $('table#cs_color_'+color_id);	
			if (result.msg) {alert(result.msg)};
			if (result.err!=0) {return false;};
			var image_id = result.image_id;
			var image_path=result.image_path;
			var image_type=result.image_type;
			var img_desc=result.img_desc;
			var sort_order=result.sort_order;

			var image_type_exp = '';
			switch(image_type){
				case 'default':
					image_type_exp = '默认图';
					$('div.cs_image_default', container).remove();
					break;
				case 'tonal':
					image_type_exp = '色片图';
					$('div.cs_image_tonal', container).remove();
					break;
				default:
					image_type_exp = '局部图';
			}			

			var html_str = '<div class="cs_image_'+image_type+' cs_image_'+image_id+'" style="display:inline-block;float:left;margin:5px;width:100px;">';
			html_str += '<span>'+image_type_exp+'</span>&nbsp;<a href="javascript:remove_gallery('+image_id+')" style="clear:both;">[删]</a><br/>';
			html_str += '<img src="'+image_path+'" /><br>';
			html_str += '<span onclick="javascript:listTable.edit(this, \'product_api/edit_gallery\', \'img_desc\', '+image_id+')" title="点击修改内容">'+(img_desc?img_desc:'无描述')+'</span><br/>';
			html_str += '排序：<span onclick="javascript:listTable.edit(this, \'product_api/edit_gallery\', \'sort_order\', '+image_id+')" title="点击修改内容">'+sort_order+'</span>';
			html_str += '</div>';
			$('td.cs_table_4', container).append($(html_str));
			$(':file[name=cs_upload_image]', container).val('');
		}
	});
}

function remove_color (color_id) {
	if (!confirm('确定移除该颜色下的所有尺码和图片？')) {return false;};
	var container = $('table#cs_color_'+color_id);
	if (container.length<1) {alert('参数错误'); return false;};
	var product_id = $(':hidden[name=product_id]').val();
	$.ajax({
		url:'product_api/delete_color',
		data:{product_id:product_id,color_id:color_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err!=0) {return false;};
			container.next('.span5').remove();
			container.remove();
		}
	});
}

function add_size (color_id) {
	var product_id = $(':hidden[name=product_id]').val();
	var container = $('table#cs_color_'+color_id);
	if (container.length<1) {alert('参数错误'); return false;};
	var size_id = parseInt($('select[name=cs_size_src]', container).val());
	var size_name = $('select[name=cs_size_src]', container).find('option:selected').text();
	var provider_barcode = $.trim($('#provider_barcode', container).val());
	if(isNaN(size_id) || size_id<1){
		alert('请选择尺码');
		return false;
	}
	if ($('div#cs_size_'+size_id, container).length>0) {
		alert('尺码重复');
		return false;
	};
	if(isEmpty(provider_barcode)){
		alert('商品条形码不能为空');
		return false;
	}
        
        if (provider_barcode.length > 16){
            alert('商品条形码长度不能超过16位');
            return false;
        }
        
	$.ajax({
		url:'product_api/add_sub',
		data:{product_id:product_id,color_id:color_id,size_id:size_id,provider_barcode:provider_barcode,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err!=0) {return false;};
			html_str='<div id="cs_size_'+size_id+'" style="border: 1px solid #C8DFA7;display: inline-block;margin: 3px;padding: 2px;text-align: center;width: 210px;"> 尺码：'+size_name+'&nbsp;&nbsp;[<a href="javascript:remove_size('+color_id+','+size_id+');">删</a>]<br>供应商条码：'+provider_barcode+'</div>';
			$('td.cs_table_2', container).append($(html_str));	
		}
	});
}

function remove_size (color_id,size_id) {
	var product_id = $(':hidden[name=product_id]').val();
	var container = $('table#cs_color_'+color_id);
	if (container.length<1) {alert('参数错误'); return false;};
	if ($('div#cs_size_'+size_id, container).length<1) {
		alert('参数错误');
		return false;
	};
	$.ajax({
		url:'product_api/delete_sub',
		data:{product_id:product_id,color_id:color_id,size_id:size_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err!=0) {return false;};
			$('div#cs_size_'+size_id, container).remove();
		}
	});
}

function remove_gallery (image_id) {
	$.ajax({
		url:'product_api/delete_gallery',
		data:{image_id:image_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err!=0) {return false;};
			var color_id = result.color_id;
			var container = $('table#cs_color_'+result.color_id);
			$('div.cs_image_'+image_id).remove();
		}
	});
}

function check_all() {
	if($(':checkbox[name=ck_check_all]').attr('checked'))
		$(':checkbox[name=link_product_id]').attr('checked',true);
	else
		$(':checkbox[name=link_product_id]').attr('checked',false);
}

function link() {
	var product_id = $(':hidden[name=product_id]').val();
	var is_bothway = $(':radio:checked[name=is_bothway]').val();
	if (is_bothway == undefined) is_bothway=0;
	var link_product_ids = '';
	$(':checkbox:checked[name=link_product_id]').each(function(){
		link_product_ids += '|'+$(this).val();
	});
	if (link_product_ids.length>0) 
		link_product_ids = link_product_ids.substr(1,link_product_ids.length-1);
	else {
		alert('请选择关联商品');
		return false;
	}
		
	$.ajax({
		url:'product_api/add_link',
		data:{product_id:product_id,is_bothway:is_bothway,link_product_ids:link_product_ids,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err!=0) return false;
			$('div#link_list').html(result.data);
			listTable.loadList();
		}
	});
}

function remove_link(link_id) {
	if (!confirm('确定执行该操作？')) return false;
	$.ajax({
		url:'product_api/delete_link',
		data:{link_id:link_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err!=0) return false;
			$('tr.link_'+link_id).remove();
		}
	});
}

isEmpty = function (str) {
	return (typeof (str) === "undefined" || str === null || (str.length === 0));
};
