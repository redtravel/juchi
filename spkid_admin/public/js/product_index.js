function check_all () {
	if($(':checkbox[name=ck_check_all]').prop('checked'))
		//$(':checkbox[name=product_id]').attr('checked',true);
		$(':checkbox[name=product_id]').prop('checked',true);
	else
		//$(':checkbox[name=product_id]').attr('checked',false);
		$(':checkbox[name=product_id]').prop('checked',false);
}
function batch_audit () {
	var product_id = $(':hidden[name=product_id]').val();
	var is_bothway = $(':radio:checked[name=is_bothway]').val();
	if (is_bothway == undefined) is_bothway=0;
	var product_ids = new Array();
	$(':checkbox:checked[name=product_id]').each(function(){
		product_ids.push($(this).val());
	});

	if (product_ids.length>0) 
		product_ids = product_ids.join(',');
	else {
		alert('请选择商品');
		return false;
	}
		
	$.ajax({
		url:'product_api/batch_audit',
		data:{product_ids:product_ids,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err!=0) return false;
			listTable.loadList();
		}
	});
}

function audit_pic(objId){
	var obj = $(objId);
	var product_id =obj.attr("product_id");
	var color_id =obj.attr("color_id");
	var is_pic =obj.attr("is_pic");
	$.ajax({
		url:'product_api/audit_pic',
		data:{product_id:product_id,color_id:color_id,is_pic:is_pic,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.err!=0){ alert(result.msg);return ;}
			if(result.result == 1){
			    obj.attr("is_pic",1);
			    obj.attr("title","已拍摄");
			    obj.html("已拍摄");
			}else{
			    obj.attr("is_pic",0);
			    obj.attr("title","未拍摄");
			    obj.html("未拍摄");
			}
		}
	});
}
