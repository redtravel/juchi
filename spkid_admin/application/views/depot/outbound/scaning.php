<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
	<div class="main">
	    <div class="main_title"><span class="l">扫描出库</span></div>
	    <div class="blank5"></div>
	    <div class="search_row">
			<table style="width:60%">
			    <tr>
				<td align="right">出库单编号:</td>
				<td>&nbsp;&nbsp;<?=$depot_content->depot_out_code?></td>
				<td align="right">预计出库数量:</td>
				<td>&nbsp;&nbsp;<?=$depot_content->depot_out_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align="right">已扫描数量:</td>
				<td>&nbsp;&nbsp;<span id="scan_num"><?=$finished_scan_number?></span></td>
				<td align="right">箱子数量:</td>
				<td>
				    &nbsp;&nbsp;<span id="scan_box"><?=$box_count?></span>
				    <input class="am-btn am-btn-primary" type="button" onclick="redirect('outbound/box_detail/<?=$depot_content->depot_out_code?>');" value ="扫描记录" />
				</td>
			    </tr>
			</table>
			</div>
			    <div class="blank5"></div>
			    <div id="listDiv">
				    <table class="dataTable" cellpadding=0 cellspacing=0>
					    <tr>
						    <td colspan="11" class="topTd"> </td>
					    </tr>
					    <tr class="row">
						<th>名称</th>
						<th>商品款号</th>
						<th>货号</th>
						<th>品牌</th>
						<th>条码</th>
						<th>颜色</th>
						<th>尺码</th>
						<th>生产批号</th>
                    	<th>有效期</th>
						<th>出库数量</th>
						<th>已扫描数量</th>
						<th>本箱扫描数量</th>
						<th>扫描数量</th>
					    </tr>
					    <tbody id="dataTable">
						<?php if(!empty($details_list)):foreach ($details_list as $detail):
//						    $seq_id = str_replace(" ", "-",$detail->provider_barcode);
//						    $seq_id .= "_".$detail->product_id."_".$detail->color_id."_".$detail->size_id;
						    ?>
						<tr class="row">
						    <td><?php echo $detail->product_name?></td>
						    <td><?php echo $detail->product_sn?></td>
						    <td><?php echo $detail->provider_productcode?></td>
						    <td><?php echo $detail->brand_name?></td>
						    <td><?php echo $detail->provider_barcode?></td>
						    <td><?php echo $detail->color_name?></td>
						    <td><?php echo $detail->size_name?></td>
						    <td>                    	
                    	<input type="text" class="v-batch-<?php echo $detail->box_sub_id?>" value="<?php echo $detail->production_batch?>" style="width: 150px;text-align: center;"/>
                    </td>
                    <td>
                    	<input type="text" class="v-expire-<?php echo $detail->box_sub_id?>" value="<?php echo $detail->expire_date?>" style="width: 100px;text-align: center;" onblur="v_check(this);"/>
                    </td>
						    <td><?php echo $detail->depot_num?></td>
						    <td><?php echo $detail->finished_scan_number?></td>
						    <td><?php echo $detail->product_number?></td>
						    <td><input type="text" class="v-add"
						   		box_sub_id="<?php echo $detail->box_sub_id?>"
						        provider_barcode="<?php echo $detail->provider_barcode?>"
							    product_id="<?php echo $detail->product_id?>" 
							    color_id="<?php echo $detail->color_id?>" 
							    size_id="<?php echo $detail->size_id?>"
							    value="0" style="width: 30px;text-align: center;" flg="val" onblur="summly();"/></td>
						</tr>		
						<?php endforeach;endif;?>
					    </tbody>
					     <tr>
						<td colspan="10"> </td>
						<td>本次扫描合计：<span id="xiaoji" style="font-size:20px;color:red">0</span></td>
					    </tr>
					    <tr>
						<td colspan="11" class="bottomTd"> </td>
					    </tr>
				    </table>
				<?php if (check_perm('outbound_scanning')): ?>
				<div>
				    <table class="form" cellspacing="0" cellpadding="0">
					<tr>
					    <td class="item_title" width="15%">箱号:</td>
					    <td class="item_input" width="35%">
						<?php print form_input(array('name'=> 'box_code','id'=>'box_code','class'=> 'textbox',"size"=>"30",'value'=>$box_code));?>
						<span id="box_msg" style="color:red"></span>
					    </td>
					    <td class="item_title" width="15%">商品条码:</td>
					    <td class="item_input" width="35%">
						<?php print form_input(array('name'=> 'product_code','class'=> 'textbox',"size"=>"30"));?>
						<span id="product_msg" style="color:red"></span>
					    </td>
					</tr>
					 <tr>
					    <td colspan="4"><span id="p_name" style="font-size: 24px;font-family: '微软雅黑';color:#00A707"></span></td>
					</tr>
					<tr>
					    <td colspan="4" style="text-align:center" >
						<?php if(!$depot_content->audit_admin >0): ?>
						<input class="am-btn am-btn-primary" type="button" name="mysubmit" value="完成扫描" />
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input class="am-btn am-btn-primary" type="button" onclick="javascript:location.href=location.href;" value="取消本次扫描" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input class="am-btn am-btn-primary" type="button" onclick="javascript:gen_box_code();" value="新箱号" />
						<?php endif;?>
					    </td>
					</tr>
				    </table>
			    </div>
				<?php endif;?>
			    <div class="blank5"></div>
			</div>
	</div>
    <script type="text/javascript">
	function gen_box_code(){
	    var xj = $("#xiaoji").html();
	    if(parseInt(xj)>0){ 
		if(!confirm('当前共有'+xj+'件商品未【完成扫描】，生成新箱号后这些数据将被清除，确认继续？')){
		return;
		}
	    }
	    $("#xiaoji").html("0");
	    $("#p_name").html("");
	    $("input[type=text][name=box_code]").val("");
	    $("input[type=text][name=product_code]").val("");
	    $("#dataTable").html("");
	    $.post('/outbound/gen_box/<?=$depot_content->depot_out_code?>/',
		    function(data){
			data = jQuery.parseJSON(data);
			if(data.err != 1){
			   $("input[type=text][name=box_code]").val(data.box_code);
			   $("input[type=text][name=box_code]").attr('disabled',"disabled").css({'backgroundColor':'#bbb','backgroundImage':'none'});                                
			   $("#box_msg").attr("style","color:green").html("新箱号");
			}else{
			    alt_msg("生成箱号错误");
			}
		    });
	    $("input[type=text][name=product_code]").focus();
	    return;
	}
        $(function(){
	    var box_code = $("input[type=text][name=box_code]").val();
	    if(box_code != null && box_code != ""){
		$("input[type=text][name=box_code]").attr("readonly","readonly").css({'backgroundColor':'#bbb','backgroundImage':'none'});
                $("input[type=text][name=product_code]").focus();
	    }
            //箱号扫描完后将焦点给到商品条码
            $("input[type=text][name=box_code]").keydown(function(event){
                    if(event.which==13){
			//检查箱子是否已存在
			var box_code=$(this).val();
			$("#box_msg").html("");
			$.post('/outbound/check_box/<?=$depot_content->depot_out_code?>/'+box_code,
				function(data){
				    data = jQuery.parseJSON(data);
				    if(data.err == 1){
					$("#box_msg").attr("style","color:red").html(data.msg);
					return;
				    }else{
					$("#box_msg").attr("style","color:green").html(data.msg);
				    }
				});
                        $("input[type=text][name=box_code]").attr("readonly","readonly").css({'backgroundColor':'#bbb','backgroundImage':'none'});                                
                        $("input[type=text][name=product_code]").focus();
                        return;
                    }
             }); 
            //商品条码扫描后处理
            $("input[type=text][name=product_code]").keydown(function(event){
                    if(event.which==13){
                        var product_code=$(this).val();
                        $(this).val("").focus();
                        if(product_code==""){
                            return;
                        }
			product_code = product_code.trim();
			 $("#product_msg").html("");
			 var box_code = $("input[type=text][name=box_code]").val();
                        //根据条码检索商品
                        $.post("/outbound/get_product/<?=$depot_content->depot_out_id?>",{product_code:product_code,box_code:box_code},function(data){
                                if(data['result']==0){
                                    var msg = '未找到对应商品或该商品不属于此出库单';
				    //alert();
				    $("#product_msg").html(msg);
                                }
                                else{
				     var xj = $("#xiaoji").html();
				    $("#xiaoji").html(parseInt(xj) +1);
				    $("#p_name").html(" 商品名称："+data.product_name +" "+ data.color_name +"    "+ data.size_name+" 数量： 1");
                                    //如已存在则不创建tr
                                    if($("input[product_id='"+data.product_id+"'][color_id='"+data.color_id+"'][size_id='"+data.size_id+"']")[0]){
                                        var scan_quantity=$("input[product_id='"+data.product_id+"'][color_id='"+data.color_id+"'][size_id='"+data.size_id+"']");
                                        if(isNaN(scan_quantity.val())){
                                            scan_quantity.val("0");
                                        }
                                        scan_quantity.val(scan_quantity.val()-0+1);
                                        return;
                                    }
                                    var tr="<tr class='row'>";
                                    tr+="<td>"+data.product_name+"</td>";
				    tr+="<td>"+data.product_sn+"</td>";
				    tr+="<td>"+data.provider_barcode+"</td>";
				    tr+="<td>"+data.brand_name+"</td>";
				    tr+="<td>"+data.provider_barcode+"</td>";
                                    tr+="<td>"+data.color_name+"</td>";
                                    tr+="<td>"+data.size_name+"</td>";
                                    tr+="<td><input class='v-batch-"+data.box_sub_id+"' value='"+data.production_batch+"' style='width: 150px;text-align: center;'/></td>";
                    tr+="<td><input class='v-expire-"+data.box_sub_id+"' value='"+data.expire_date+"' style='width: 100px;text-align: center;' onblur='v_check(this);' /></td>";
                                    tr+="<td>"+data.product_number+"</td>";
				    tr+="<td>"+data.finished_scan_number+"</td>";
				    tr+="<td>"+data.box_finished_scan_number+"</td>";
                                    tr+="<td><input type='text' value='1' class='v-add' ";
                                    tr+=" box_sub_id = '"+data.box_sub_id+"'"; 
				    tr+=" provider_barcode='"+data.provider_barcode+"'";
				    tr+=" product_id='"+data.product_id+"'";
				    tr+=" color_id='"+data.color_id+"'";
				    tr+=" size_id='"+data.size_id+"'";
				    tr+=" type='text' style='width:30px;text-align: center;' flg='val' value='1' onblur='summly();'></td>";
                                    tr+="</tr>";
                                    $("#dataTable").append(tr);
                                }
                            },"json");
                        return;
                    }
             }); 
            //提交按钮事件
            $("input[type=button][name=mysubmit]").click(function(){
                    var box_code=$('#box_code').val();
                    if(box_code==""){
                        alt_msg('请先扫描箱号');
                        return;
                    }
		    var provider_barcode_array=[];
		    var product_id_array=[];
		    var color_id_array=[];
		    var size_id_array=[];
		    var number_array=[];
		    var v_batch_array=[];
			    var v_expire_array=[];
                    $('#dataTable .v-add').each(function(){
                    	var box_sub_id = $(this).attr("box_sub_id");
			    var provider_barcode = $(this).attr("provider_barcode");
			    var product_id = $(this).attr("product_id");
			    var color_id = $(this).attr("color_id");
			    var size_id = $(this).attr("size_id");
			    var number = $(this).val();
			    var v_batch = $('.v-batch-'+box_sub_id).val();
				    var v_expire = $('.v-expire-'+box_sub_id).val();
			    
			    provider_barcode_array.push(provider_barcode);
			    product_id_array.push(product_id);
			    color_id_array.push(color_id);
			    size_id_array.push(size_id);
			    number_array.push(number);
			    v_batch_array.push(v_batch);
						v_expire_array.push(v_expire);
                        });
                    if(provider_barcode_array.length <1){
                        alt_msg("请扫描商品");
                        return;
                    }
                    $.post("/outbound/do_scan",
                            {depot_out_code:'<?=$depot_content->depot_out_code?>',
				box_code:box_code,
				provider_barcode_array:provider_barcode_array,
				product_id_array:product_id_array,
				color_id_array:color_id_array,
				size_id_array:size_id_array,
				number_array:number_array
				v_batch_array:v_batch_array,
					v_expire_array:v_expire_array
			    },
                            function(data){
				data = jQuery.parseJSON(data);
                                if(data.err == 0){
                                    alt_msg('扫描出货成功');
                                     if(location.href.indexOf("?")>0){
					 
					location.href=location.href.split("?")[0]+"?box_code="+box_code;
				    }else{
					location.href=location.href+"?box_code="+box_code;
				    }
                                } else {
                                    alt_msg('扫描出货失败，'+data.msg);
                                }
                            });
            });
         });
	 function alt_msg(msg){
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
	function summly(){
	   var sum = 0;
	   $("input[flg=val]").each(function(){
	    sum += parseInt($(this).val());
	  });
	   $("#xiaoji").html(sum);
	}
	function v_check(obj){
		var str = $(obj).val();
		if(str != ''){
			if(!str.match(/^20\d{2}-\d{2}-\d{2}/)) { 
				alert('请输入正确的日期格式:2016-01-01或者为空');
			}
		}
	}
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
