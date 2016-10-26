function check_product_valid(t){
	$.ajax({
		   type: "POST",
		   url: "campaign/check_product",
		   data: "name="+t+'&content='+$('#product_sns').val(),
		   dataType: "JSON",
		   success: function(msg){
					$('#check_result').text("["+msg.type+"]:"+msg.content);
			}
		});

}
function sel(te,c, cb){
	$('select[name='+cb+']')[0].options.length = 1;
	$.ajax({
	   type: "POST",
	   url: "campaign/sel_"+c,
	   data: "cb="+cb+"&class="+c+"&val="+te,
	   dataType: "JSON",
	   success: function(msg){
		 	if(msg.type == 1){
				if( msg.cb == 'tag_id' )
				for(i in msg.list){
					$('select[name='+msg.cb+']')[0].options.add(new Option(msg.list[i].product_name+' '+msg.list[i].product_sn+' '+msg.list[i].provider_name , msg.list[i].product_id));
				}
				if( msg.cb == 'brand_id' )
				for(i in msg.list){
					$('select[name='+msg.cb+']')[0].options.add(new Option(msg.list[i].brand_name, msg.list[i].brand_id));
				}
			}
		 	if(msg.type == 3){
				alert('没搜索到相关记录');
				return false;
			}
		}
	});
}


