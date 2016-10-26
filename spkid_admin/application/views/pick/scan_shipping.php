<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="expires" content="Wed, 23 Aug 2006 12:40:27 UTC" />
    <base href="<?php print base_url(); ?>" target="_self" />
    <title>演示站电子商务管理系统</title>
    <link rel="stylesheet" href="public/style/style.css" type="text/css" media="all" />
    <script type="text/javascript" src="public/js/jquery.js"></script>
    <script type="text/javascript">var base_url='<?php print base_url(); ?>';</script>
</head>
<body bgcolor="<?php print empty($print_bgcolor)?'#FAFEF0':$print_bgcolor; ?>">
 <script type="text/javascript">
    function scan_invoice(e){
        e = e ? e : (window.event ? window.event : null);    
        var invoice_no=$.trim($('#scan_input').val()).toUpperCase();
        if (e.keyCode != 13) 
        {   
            return false;
        }
        $('#scan_weight').focus();
    }
	//<![CDATA[
	/**
	 * scan
	 */	
	function scan(e) {
            e = e ? e : (window.event ? window.event : null);    
            var invoice_no=$.trim($('#scan_input').val()).toUpperCase();
            var scan_weight=$.trim($('#scan_weight').val());
            if (e.keyCode != 13) 
            {   
                return false;
            }   

            if (invoice_no == '') 
            {   
                alert('请您扫描运单号！'); 
                return false;
            }
            
            if (scan_weight == '' || scan_weight <= 0) 
            {   
                alert('请输入包裹重量！'); 
                return false;
            }
        //var e=$('#scan_input');
		//if(sn==''){//输入的是订单号
		//	if(!/^(DD|HH)\d{13}$/.test(content) || $('tr[sn='+content+']').length<1){
		//		alert('请扫描正确的订单号或换货单号');
		//		e.val('').focus();
		//		return false;
		//	}	
		//	var tr=$('tr[sn='+content+']');
		//	if(tr.attr('status')=='1'){
		//		alert('该单已发货，不能重复扫描');
		//		e.val('').focus();
		//		return false;
		//	}
		//	sn=content;
		//	$('td:eq(0) a',tr).css('color','red');
        //                e.val('')
		//	$('#msg').html('请扫描快递单号');
		//}else{//输入的是运单号
		//	if(/^(DD|HH)\d{13}$/.test(content)){
		//		alert('请扫描快递单号');
		//		e.val('').focus();
		//		return false;
		//	}
		//	var tr=$('tr[sn='+sn+']');
		//	invoice_no=content;
		//	$('td:eq(1)',tr).html('<span style="color:red;">'+content+'</span>');
		//	$('#msg').html('请扫描订单号/换货单号');
		//	
		//	if(confirm('是否取消当前发货\n如果取消发货请点“是”,如果确认发货请点“否”')){
		//		sn='';
		//		invoice_no='';
		//		$('td:eq(0) a',tr).css('color','');
		//		$('td:eq(1)',tr).html('');
        //                        e.val('').focus();
		//		return false;
		//	}
			$.ajax({
				url:'pick/scan_shipping_process',
				data:{invoice_no:invoice_no,scan_weight:scan_weight, rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					//if(result.msg) alert(result.msg);
                                    $('#scan_input').val('').focus();
                                    $('#scan_weight').val('');
                                    if(result.err){
                                        alert(result.msg);
                                        return false;
                                                        //	$('td:eq(0) a',tr).css('color','');
                                                        //	$('td:eq(1)',tr).html('');
                                    //                            e.val('').focus();
                                                        //	return false;
                                    }
                                    $('#order_sn').html(result.msg+"已发运。");
					//$('td:eq(0) a',tr).css('color','');
					//$('td:eq(1)',tr).html(invoice_no);
                    //                    tr.attr('status','1');
                    //                    e.val('').focus();
                    //                    sn='';
                    //                    invoice_no='';
                    //                   
				}
			});
		//}
	}
	
	//function tick(odd_sn){
	//	if($('#float_advice').dialog('isOpen')){
	//		odd_sn = $(':hidden[name=odd_sn]').val();
	//		var odd_advice=$.trim($(':input[name=odd_advice]').val());
	//		if(odd_advice==''){
	//			alert('请填写意见');
	//			return false;
	//		}
	//		$.ajax({
	//			url:'pick/tick',
	//			data:{odd_sn:odd_sn,odd_advice:odd_advice,rnd:new Date().getTime()},
	//			dataType:'json',
	//			type:'POST',
	//			success:function(result){
	//				if(result.msg) alert(result.msg);
	//				if(result.err) return false;
	//				var tr=$('tr[sn='+odd_sn+']');
	//				tr.remove();
	//				if(sn==odd_sn){
	//					sn='';
	//					invoice_no='';
	//				}
	//				$(':input[name=odd_advice]').val('');
	//				$('#float_advice').dialog('close')
	//			}
	//		});

	//	}else{
	//		$(':hidden[name=odd_sn]').val(odd_sn);
	//		$('#float_advice').dialog('open')
	//	}
	//}
	
	$(function(){
		$('#scan_input').focus();
		//$('#float_advice').dialog({autoOpen:false,width:300,modal:true,resizable:false,title:'标记问题单：请填写意见'});
	});
	//]]>
</script>
	<div class="main">
		<div class="main_title"><span class="l">扫描发货</span></div>
		<div class="blank5"></div>
           <table class="form" cellpadding=0 cellspacing=0>
                <tr>
                <td class="item_title">运单号：</td>
                <td class="item_input"><input type="text" name="scan_input" id="scan_input" value="" size="32" style="ime-mode:disabled;" onkeydown="scan_invoice(event);"/>
                <td class="item_title">重量：</td>
                <td class="item_input"><input type="text" name="scan_weight" id="scan_weight" onkeydown="scan(event);" value="" size="32" style="ime-mode:disabled;" />KG
                </tr>
                <tr>
                <td class="item_title">订单号：</td>
                <td class="item_input" colspan="3"><span id="order_sn"></span></td>
                </tr>
           </table> 
           <span style="color:red;font-weight:bold;">注：扫描运单号后，该包裹在系统中将标识为已发货</span>    
	</div>
<div id="float_advice" style="display:none;">
	<input type="hidden" name="odd_sn" value="" />
	<textarea name="odd_advice" class="log"></textarea><br/>
	<input type="button" name="btn_advice" value="提交" onclick="tick('')" />
</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
