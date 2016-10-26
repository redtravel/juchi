<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
	<div class="main">
		<div class="main_title">
			<span class="l">订单复核</span>
		</div>
		<div id="listDiv">
<?php endif; ?>
			<?php print form_open_multipart('order_recheck/recheck',array('name'=>'mainForm','id'=>'mainForm'));?>
			<div>
                <table class="form" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="item_title">订单号:</td>
                        <td class="item_input">
                        	<input name="order_sn" class="textbox" id="order_sn" onkeypress="if(event.keyCode==13) {return false;}"/>
                            <span style="color: red" id="order_sn_msg"></span>
                        </td>
                        <td class="item_title">运单号:</td>
                        <td class="item_input">
                        	<input name="invoice_no" class="textbox" id="invoice_no" onkeypress="if(event.keyCode==13) {return false;}"/>
                        </td>
                        <td class="item_title">商品形条码:</td>
                        <td class="item_input">
                        	<input name="provider_barcode" class="textbox" id="provider_barcode" onkeypress="if(event.keyCode==13) {return false;}"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:center">
					        <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交扫描'));?>
                        	<input type="button" class="am-btn am-btn-primary" value="重置" name="myreset" id="myreset" onclick="location.reload();"/>
                                <input type="button" class="am-btn am-btn-primary" value="标记为异常订单" name="unusual_order" id="unusual_order"/>
                        <td>
                    </tr>
                </table>
                <table class="form" cellpadding=0 cellspacing=0>
					<tr>
						<td class="item_input">
							扫描提示：<span style="color: red" id="error_msg"></span><span style="color: green" id="success_msg"></span>
						</td>
					</tr>
				</table>
                
            </div>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>名称</th>
				  <th>商品款号</th>
				  <th>货号</th>
				  <th>品牌</th>
				  <th>条码</th>
				  <th>颜色</th>
				  <th>尺码</th>
				  <th>分配数量</th>
				  <th>复核数量</th>
				  <th>未复核数量</th>
				  <th>状态</th>
				</tr>
				<tbody id="dataTable_tbody">
				    
				</tbody>
				<tr>
					<td colspan="6" class="bottomTd"> </td>
				</tr>
			</table>
			<span id="input_data"></span>
			<div class="blank5"></div>
			<?php print form_close();?>
<?php if($full_page): ?>
		</div>
	</div>
    <script>
    $(document).ready(function(){
        $("#order_sn").val('');
        $("#invoice_no").val('');
        $("#provider_barcode").val('');
        $("#order_sn").focus();
        
        function alt_msg(msg) {
	    var audio = '<audio src="public/style/audio/alert_msg.ogg"></audio>';
	    if ($('audio').length<1) $('body').append(audio);
	    $('audio:last').attr('autoplay','');
	    setTimeout(function () {$('audio').remove()},600);
	    var t = setInterval(function(){
		if ($('audio').length>0){
		    alert(msg);
		    clearInterval(t);
		}
	    },16);
	}
        
        //订单号扫描
        $("#order_sn").keydown(function(event){
        	$("#error_msg").html('');
        	$("#success_msg").html('');
			if(event.which == 13){
				var order_sn = $("#order_sn").val();
				if(order_sn == "" || order_sn == null){
					$("#order_sn").val('');
					$("#error_msg").html("订单号为空，1秒后重试");
					setTimeout("location.reload()",1000);
					return;
				}
				//根据订单号检索商品
                $.post("/order_recheck/get_order_product/"+order_sn,function(data){
                        if(data['result']==0){
                        	$("#order_sn").val('');
                        	$("#error_msg").html(data['msg']+'，2秒后重试');
                        	setTimeout("location.reload()",2000);
                        }else{
                        	$("#dataTable_tbody").html('');
                                $("#input_data").html('');
                                var invoice_no = '';
                                for(var i=0; i<data.length; i++)  
                        	{
                                    var tr="<tr class='row'>";
                                    var input="<input type='hidden' id='sub_id_"+data[i].sub_id+"' name='sub_ids[]' value='"+data[i].sub_id+"'>";
                                    input+="<input type='hidden' id='pick_sn' name='pick_sn' value='"+data[i].pick_sn+"'>";
                                    input+="<input type='hidden' name='provider_barcode[]' value='"+data[i].provider_barcode+"'>";
                                    
                                    tr+="<td style='padding: 8px;'>"+data[i].product_name+"</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].product_sn+"</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].provider_productcode+"</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].brand_name+"</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].provider_barcode+"</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].color_name+"["+data[i].color_sn+"]</td>";
				    tr+="<td style='padding: 8px;'>"+data[i].size_name+"["+data[i].size_sn+"]</td>";
				    
                                    tr+="<td style='padding: 8px;'>"+data[i].product_number+"</td>";
                                    tr+="<td style='padding: 8px;'><input type='text' size='8' readonly='readonly' value="+data[i].qc_num+" name='qc_num_"+data[i].sub_id+"' id='qc_num_"+data[i].sub_id+"'></td>";
                                    tr+="<td style='padding: 8px;'><input type='text' size='8' readonly='readonly' value="+data[i].unqc_num+" name='unqc_num_"+data[i].sub_id+"' id='unqc_num_"+data[i].sub_id+"'></td>";
                                    tr+="<td style='padding: 8px;'>已拣货</td>";
                                    tr+="</tr>";
                                    $("#dataTable_tbody").append(tr);
                                    $("#input_data").append(input);
                                    invoice_no = data[i].invoice_no;
                        	}
                                $("#invoice_no").removeAttr('readonly');
                                if (invoice_no == ''){
                                    $("#invoice_no").val('');
                                    $("#invoice_no").focus();
                                } else {
                                    $("#invoice_no").val(invoice_no);
                                    $("#invoice_no").attr('readonly', 'readonly');
                                    $("#provider_barcode").val('');
                                    $("#provider_barcode").focus();
                                }                             
                        }
                    },"json");
                return;
			}	
		});

      	//运单号扫描
        $("#invoice_no").keydown(function(event){
        	$("#error_msg").html('');
        	$("#success_msg").html('');
			if(event.which == 13){
				var order_sn = $("#order_sn").val();
				var invoice_no = $("#invoice_no").val();
				if(order_sn == "" || order_sn == null){
					$("#order_sn").val('');
                                        $("#invoice_no").val('');
					$("#error_msg").html("订单号为空，1秒后重试");
					setTimeout("location.reload()",1000);
					return;
				}
				if(invoice_no == "" || invoice_no == null){
					$("#invoice_no").val('');
					$("#invoice_no").focus();
					$("#error_msg").html("运单号为空");
					return;
				}
				//光标下移
				$("#provider_barcode").val('');
        		$("#provider_barcode").focus();
                return;
			}	
		});

      	//商品条形码扫描
        $("#provider_barcode").keydown(function(event){
        	$("#error_msg").html('');
        	$("#success_msg").html('');
			if(event.which == 13){
				var order_sn = $("#order_sn").val();
				var invoice_no = $("#invoice_no").val();
				var provider_barcode = $("#provider_barcode").val();
				if(order_sn == "" || order_sn == null){
					$("#order_sn").val('');
                                        $("#invoice_no").val('');
                                        $("#provider_barcode").val('');
					$("#error_msg").html("订单号为空，1秒后重试");
					setTimeout("location.reload()",1000);
					return;
				}
				if(invoice_no == "" || invoice_no == null){
					$("#invoice_no").val('');
					$("#invoice_no").focus();
//					$("#error_msg").html("运单号为空");
                                        alt_msg("运单号为空");
					return;
				}
				if(provider_barcode == "" || provider_barcode == null){
					$("#provider_barcode").val('');
					$("#provider_barcode").focus();
//					$("#error_msg").html("商品条形码为空");
                                        alt_msg("商品条形码为空");
					return;
				}
				//订单商品复核检查
                                var v_goods_barcode = document.getElementsByName("provider_barcode[]");
                                var sub_ids = document.getElementsByName("sub_ids[]");
                                var v_flag = 0;
                                for (var i = 0; i < sub_ids.length; i++)
                                {
                                    var qc_num_quantity = $("#qc_num_"+sub_ids.item(i).value);
                                    var unqc_num_quantity = $("#unqc_num_"+sub_ids.item(i).value);
                                    if (v_goods_barcode.item(i).value != provider_barcode)
                                    {
                                        continue;
                                    }
                                    if(unqc_num_quantity.val() == 0){
                                        v_flag = -1;
                                        continue;
                                    }else{
                                        v_flag = 1;
                                        qc_num_quantity.val(qc_num_quantity.val()-0+1);
                                        unqc_num_quantity.val(unqc_num_quantity.val()-0-1);
                                        $("#provider_barcode").val('');
                                        $("#provider_barcode").focus();
                                        if(unqc_num_quantity.val() == 0){
                                            $("#success_msg").html("该商品已扫描完成");
//                                            alt_msg("该商品已扫描完成");
                                        }else{
//                                            $("#success_msg").html("该商品还有 "+unqc_num_quantity.val()+" 个未复核");
                                            alt_msg("该商品还有 "+unqc_num_quantity.val()+" 个未复核");
                                        }
                                    }
				    break;
                                }
				var v_finished = true;
				for (var i = 0; i < sub_ids.length; i++){
				    var unqc_num_quantity = $("#unqc_num_"+sub_ids.item(i).value);
				    if(unqc_num_quantity.val() != 0){
					 v_finished = false;
					 break;
				    }
				}
				if(v_finished){
				    if(confirm("订单中商品全部复核完成，是否自动提交扫描记录？")){
					$("#mainForm").submit();
				    }
				}
				
                                if (v_flag == 0)
                                {
                                    $("#provider_barcode").val('');
                                    $("#provider_barcode").focus();
//                                    $("#error_msg").html("该订单中没有此商品！");
                                    alt_msg("该订单中没有此商品！");
                                    return false;
                                }else if(v_flag == -1){
                                    $("#provider_barcode").val('');
                                    $("#provider_barcode").focus();
//                                    $("#error_msg").html("该商品已经扫描完成，无需重复扫描");
                                    alt_msg("该商品已经扫描完成，无需重复扫描");
                                    return false;
                                }
                return;
			}
		});
                
        //异常订单标记
        $("#unusual_order").click(function(){
        	$("#error_msg").html('');
        	$("#success_msg").html('');
                var order_sn = $("#order_sn").val();
                var pick_sn = $("#pick_sn").val();
                if(order_sn == "" || order_sn == null || pick_sn == "" || pick_sn == null){
                        $("#order_sn").val('');
                        $("#invoice_no").val('');
                        $("#provider_barcode").val('');
                        $("#error_msg").html("没有需要设置的异常订单，1秒后重试");
                        setTimeout("location.reload()",2000);
                        return false;
                }
		var len=$('.row').length,arr=[];
		for(var i=1;i<len;i++){
		    var num=i;
		    var val=$('.row:eq('+num+') input:last').val();
		    if(val>0){arr.push($('.row:eq('+num+') td:eq(1)').html())}
		}
		if(arr.length == 0){
		     $("#error_msg").html("没有需要设置的异常订单，1秒后重试");
                     setTimeout("location.reload()",2000);
		     return false;
		}
		var g_sn ="款号 ";
		$.each(arr,function(i,v){
		    if(i==arr.length-1){ g_sn += v ; }else{g_sn += v +"、";}
		});
		g_sn += " 没有通过复核。";
                if(!confirm(g_sn+"是否要标记为异常订单？")){
                    return false;
                }
                //设置异常订单
		$.post("/order_recheck/set_unusual_order/"+pick_sn+"/"+order_sn,{content:g_sn,rnd : new Date().getTime()},function(data){
	                if(data['result'] == 0){
	                	$("#order_sn").val('');
                                $("#invoice_no").val('');
                                $("#provider_barcode").val('');
                                $("#error_msg").html(data['msg']);
                                setTimeout("location.reload()",2000);
	                }else{
                            alert("异常订单标记成功！");
                            setTimeout("location.reload()",2000);
                            return;
	               	}
                },"json");
                return;
	});  
            
     });
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
