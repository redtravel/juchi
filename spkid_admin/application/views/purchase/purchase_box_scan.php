<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/alert_msg.js"></script>
	<div class="main">
		<div class="main_title">
		    <span class="l">检验收货</span>
		</div>
		<div class="blank5"></div>
		<div class="search_row">
		<table>
                <tr>
                    <td align="right">采购单编号:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_code?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		    <td align="right">供应商:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->provider_name?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		    <td align="right">品牌:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->brand_name?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">预采购数量:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">已收货数量:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_finished_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">箱子数量:</td>
                    <td><?=$box_count?>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>
			<?php if(check_perm('pruchase_box_scan_list') ):?><input class="am-btn am-btn-primary" type="button" onclick="redirect('purchase_box/pruchase_box_scan_list/<?=$purchase->purchase_code?>');" value="扫描记录" /><? endif;?>
		    </td>
                </tr>
            </table>
	    </div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
            <table class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="15" class="topTd"> </td>
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
                    <th>外观验收</th>
                    <th>预采购数量</th>
                    <th>检查数量</th>
                    <th>已收货数量</th>
                    <th>本箱已收货数量</th>
                    <th>本次扫描数量</th>
                </tr>
                <tbody id="dataTable">
                <?php if(!empty($details_list)):foreach ($details_list as $detail): ?>
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
                    <td>
                        <!--
                    	<input type="text" class="v-oqc-<?php echo $detail->box_sub_id?>" value="<?php echo $detail->oqc?>" style="width: 100px;text-align: center;"/>
                        -->
                        <select class="v-oqc-<?php echo $detail->box_sub_id?>">
                            <option value="合格"<?=($detail->oqc == '合格') ? ' selected' : '';?>>合格</option>
                            <option value="不合格"<?=($detail->oqc == '不合格') ? ' selected' : '';?>>不合格</option>
                        </select>
                    </td>
                    <td><?php echo $detail->pnum?></td>
                    <td>
                    	<input type="text" class="v-check_num-<?php echo $detail->box_sub_id?>" value="<?php echo $detail->check_num?>" style="width: 150px;text-align: center;"/>
                    </td>
                    <td><?php echo $detail->product_finished_number?></td>
                    <td><?php echo $detail->product_number?></td>
                    <td>
                                <input type="text" class="v-add"
                                   box_sub_id="<?php echo $detail->box_sub_id?>"
                                   provider_barcode="<?php echo $detail->provider_barcode?>" 
                               product_id="<?php echo $detail->product_id?>" 
                               color_id="<?php echo $detail->color_id?>" 
                               size_id="<?php echo $detail->size_id?>" 
                               value="0" style="width: 30px;text-align: center;" flg="val" onblur="summly();"/>
                    </td>
                </tr>		
                <?php endforeach;endif;?>
                </tbody>
                <tr>
                    <td colspan="14"></td>
                    <td>本次扫描合计：<span id="xiaoji" style="font-size:20px;color:red">0</span></td>
                </tr>
                <tr>
                    <td colspan="15" class="bottomTd"> </td>
                </tr>
            </table>
            <div>
                <table class="form" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="item_title">箱号:</td>
                        <td class="item_input">
                            <?php print form_input(array('name'=> 'box_code','id'=>'box_code','class'=> 'textbox','value'=>$box_code));?>
                        </td>
                        <td class="item_title">到货日期:</td>
                        <td class="item_input">
                            <?php print form_input(array('name'=> 'delivery_date','id'=>'delivery_date','class'=> 'textbox','value' => $delivery_date, "style"=>"width:180px;"));?>
                        </td>
                        <td class="item_title">商品条码:</td>
                        <td class="item_input">
                            <?php print form_input(array('name'=> 'product_code','class'=> 'textbox',"style"=>"width:180px;"));?>
                        </td>
                    </tr>
		    <tr>
			<td colspan="6"><span id="p_name" style="font-size: 24px;font-family: '微软雅黑';color:#00A707"></span></td>
		    </tr>
                    <tr>
                        <td colspan="6" style="text-align:center">
			    <input type="button" name="mysubmit" class="am-btn am-btn-primary" value="完成扫描"/>
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			    <input type="button" class="am-btn am-btn-primary" onclick="javascript:location.href=location.href;" value="取消本次扫描" />	        
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			    <input type="button" onclick="gen_new_box();" class="am-btn am-btn-primary" value="新箱号"/>
                        </td>
                    </tr>
                </table>
            </div>
			<div class="blank5"></div>
<?php if($full_page): ?>
		</div>
	</div>
    <script>
	String.prototype.replaceAll  = function(s1,s2){    
	    return this.replace(new RegExp(s1,"gm"),s2);    
	  }
        $(function(){
            $('input[type=text][name=delivery_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

            //箱号扫描完后将焦点给到商品条码
            $("input[type=text][name=box_code]").keydown(function(event){
                if(event.which==13){
                    //检查该箱子是否可用
                    var input_box=$(this);
                    var box_code=input_box.val();
                    $.post('/purchase_box/check_box/<?=$purchase->purchase_code?>/'+box_code,
                        function(data){
                            //如果采购单是终止状态 则不能新增箱子
                            if(<?=$purchase->purchase_break?>==1){
                                if(data.is_exists==false){
                                    alt_msg("该采购单已终止,不能添加箱子",1);
                                    input_box.val('');
                                    return;
                                }
                            }
                            if(data['is_match']==false){
                                alt_msg('该箱子已被其他采购单占用',1);
                                input_box.val('');
                                return;
                            }
                            if(data.is_close==true){
                                alt_msg('该箱子已关闭,不能添加商品',1);
                                input_box.val('');
                                return;
                            }
                            $("input[type=text][name=box_code]").attr("readonly","readonly").css({'backgroundColor':'#bbb','backgroundImage':'none'});
                            //$("input[type=text][name=box_code]").removeAttr('class');
                            $("input[type=text][name=delivery_date]").focus();
                        },'json');
                }
            });
            
            $("input[type=text][name=delivery_date]").keydown(function(event){
                if(event.which==13){
                    $("input[type=text][name=product_code]").focus();
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
			var box_code = $("input[type=text][name=box_code]").val();
                        //根据条码检索商品
                        $.post("/purchase_box/get_product/<?=$purchase->purchase_id?>",{product_code:product_code,box_code:box_code},function(data){
                                if(data['result']==0){
                                    alt_msg('['+product_code+']未找到对应商品或该商品不属于此采购单',2);
                                }else if(data.provider_barcode != product_code){
                                    alt_msg('条码不匹配，输入条码：['+product_code+']；系统条码：['+data.provider_barcode+']',2)
                                }
                                /*else if (data.is_consign==1 && checkScanCount(data))
                                {
                                    alt_msg("虚库销售采购单不允许超收！");
                                }*/
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
                                    tr+="<td>"+data.provider_productcode+"</td>";
				    tr+="<td>"+data.brand_name+"</td>";
                                    tr+="<td>"+data.provider_barcode+"</td>";
                                    tr+="<td>"+data.color_name+"</td>";
                                    tr+="<td>"+data.size_name+"</td>";
                                    tr+="<td><input class='v-batch-"+data.box_sub_id+"' value='"+data.production_batch+"' style='width: 150px;text-align: center;'/></td>";
                                    tr+="<td><input class='v-expire-"+data.box_sub_id+"' value='"+data.expire_date+"' style='width: 100px;text-align: center;' onblur='v_check(this);' /></td>";
                                   // tr+="<td><input type='text' class='v-oqc-"+data.box_sub_id+"' value='"+data.oqc+"' style='width: 100px;text-align: center;'/></td>";
                                    tr+="<td><select class='v-oqc-"+data.box_sub_id+"'><option value='合格'>合格</option><option value='不合格'>不合格</option></select></td>";
                                    tr+="<td>"+data.p_number+"</td>";
                                    tr+="<td><input type='text' class='v-check_num-"+data.box_sub_id+"' value='"+data.check_num+"' style='width: 150px;text-align: center;'/></td>";
				    tr+="<td>"+data.product_finished_number+"</td>";
                                    tr+="<td>"+data.box_number+"</td>";
                                    tr+="<td><input type='text' value='1' class='v-add' ";
				    tr+=" box_sub_id = '"+data.box_sub_id+"'";
				    tr+=" provider_barcode = '"+data.provider_barcode+"'";
				    tr+=" product_id = '"+data.product_id+"'";
				    tr+=" color_id = '"+data.color_id+"'";
				    tr+=" size_id = '"+data.size_id+"'";
				    tr+=" is_consign = '"+data.is_consign+"'";
				    tr+=" style='width: 30px;text-align: center;' flg='val' onblur='summly(" + data.is_consign + "," + data.p_number + "," + data.product_finished_number + ");'></td>";
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
                var delivery_date=$('#delivery_date').val();
                if(box_code==""){
                    alt_msg('请先扫描箱号',1);
                    $("input[type=text][name=box_code]").focus();
                    return;
                }
                var provider_barcode_array=[];
                var product_id_array=[];
                var color_id_array=[];
                var size_id_array=[];
                var is_consign_array=[];
                var number_array=[];
                var v_batch_array=[];
                var v_expire_array=[];

                var v_oqc_array=[];
                var v_check_num_array=[];

                var reg = /^[0-9]*[1-9][0-9]*$/;
                var error = 0;
                $('#dataTable .v-add').each(function(){
                    var box_sub_id = $(this).attr("box_sub_id");
                    var provider_barcode = $(this).attr("provider_barcode");
                    var product_id = $(this).attr("product_id");
                    var color_id = $(this).attr("color_id");
                    var size_id = $(this).attr("size_id");
                    var is_consign = $(this).attr("is_consign");
                    var number = $(this).val();
                    var v_batch = $('.v-batch-'+box_sub_id).val();
                    var v_expire = $('.v-expire-'+box_sub_id).val();

                    var v_oqc = $('.v-oqc-'+box_sub_id).val();
                    var v_check_num = $('.v-check_num-'+box_sub_id).val();

                    if(!reg.test(number)){
                                alt_msg('数值不合法',2);
                                error = 1;
                                return;
                    }

                    if(parseInt(number) >0){
                                provider_barcode_array.push(provider_barcode);
                                product_id_array.push(product_id);
                                color_id_array.push(color_id);
                                size_id_array.push(size_id);
                                is_consign_array.push(is_consign);
                                number_array.push(number);
                                v_batch_array.push(v_batch);
                                v_expire_array.push(v_expire);

                                v_oqc_array.push(v_oqc);
                                v_check_num_array.push(v_check_num);
                    }
                });
                if(error == 1){
                    return;
                }
                if(provider_barcode_array.length <1){
                    alt_msg("请扫描商品",2);
                    $("input[type=text][name=product_code]").focus();
                    return;
                }
           	$("input[type=button][name=mysubmit]").attr("disabled","disabled").attr("class","button_gray");
                $.post("/purchase_box/do_scan/<?=$purchase->purchase_id?>",{
                                    purchase_code:'<?=$purchase->purchase_code?>',
                                    box_code:box_code,
                                    delivery_date:delivery_date,
                                    provider_barcode_array:provider_barcode_array,
                                    product_id_array:product_id_array,
                                    color_id_array:color_id_array,
                                    size_id_array:size_id_array,
                                    is_consign_array:is_consign_array,
                                    number_array:number_array,
                                    v_batch_array:v_batch_array,
                                    v_expire_array:v_expire_array, 
                                    v_oqc_array:v_oqc_array,
                                    v_check_num_array:v_check_num_array
                                },
                    function(data){
                        if (data === '1') {
                            alert('扫描收货成功');
                            if(location.href.indexOf("?")>0){
                                location.href=location.href.split("?")[0]+"?box_code="+box_code;
                            }else{
                                location.href=location.href+"?box_code="+box_code;
                            }
                        } else if (data === '2') {
                            alt_msg('该箱子已被其他采购单占用，请重新选择！', 1);
                        } else {
                            alt_msg('扫描收货失败', 2);
                        }
                });
            });
        });
	
	function gen_new_box(){
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
	    $("input[type=text][name=box_code]").removeAttr("readonly").removeAttr("style").focus();
	}
	
	function alt_msg(msg,focus){
	    var audio = '<audio src="public/style/audio/alert_msg.ogg"></audio>';
	    if ($('audio').length<1) $('body').append(audio);
	    $('audio:last').attr('autoplay','');
	    setTimeout(function () {$('audio').remove()},600);
	    var t = setInterval(function(){
		if ($('audio').length>0){
		    alert_msg(msg, function() {
                                        if (focus == 1) {
                                            $("input[type=text][name=box_code]").focus();
                                        } else {
                                            $("input[type=text][name=product_code]").focus();
                                        }
                                    });
		    clearInterval(t);
		}
	    },16);
	}
	$(function(){
	    var box_code = $("input[type=text][name=box_code]").val();
	    if(box_code != null && box_code != ""){
		$("input[type=text][name=box_code]").attr("readonly","readonly").css({'backgroundColor':'#bbb','backgroundImage':'none'});
                $("input[type=text][name=product_code]").focus();
	    }
	});
	function redirect(url){
	    if (!/*@cc_on!@*/0) {            
		window.open(url,'_blank');        
	    } else {            
		var a = document.createElement('a');            
		a.href = url;            
		a.target = '_blank';            
		document.body.appendChild(a);            
		a.click();        
	    }
	}
	
	function summly(is_consign, p_number, product_finished_number){
	    if($.trim(event.target.value) == "0"){
		if(!confirm('修改为0将删除此条收货记录，确认继续？')){
		    $(event.target).focus();
		    return;
		}
		$(event.target).parent().parent().remove();
	    }
             
            if ($.trim(event.target.value)!=0 && is_consign==1 && p_number<product_finished_number+parseInt($.trim(event.target.value)))
            {
                alert("虚库销售采购单不允许超收！");
                $(event.target).focus();
                return;
            }
            
	   var sum = 0;
	   $("input[flg=val]").each(function(){
	    sum += parseInt($(this).val());
	  });
	   $("#xiaoji").html(sum);
	}

	function v_check(obj){
            $(obj).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		/*var str = $(obj).val();
		if(str != ''){
			if(!str.match(/^20\d{2}-\d{2}-\d{2}/)) { 
				alert('请输入正确的日期格式:2016-01-01或者为空');
			}
		}*/
	}
        
        function checkScanCount(data)
        {
            var scan_count = 1;
            if($("input[product_id='"+data.product_id+"'][color_id='"+data.color_id+"'][size_id='"+data.size_id+"']")[0]){
                var scan_quantity=$("input[product_id='"+data.product_id+"'][color_id='"+data.color_id+"'][size_id='"+data.size_id+"']");
                if(!isNaN(scan_quantity.val())){
                    scan_count += parseInt(scan_quantity.val());
                }
            }
            
            if (data.p_number<data.product_finished_number-0+scan_count)
            {
                return true;
            }
            
            return false;
        }
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
