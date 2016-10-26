<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript" src="public/js/region.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
        var v_goods_number = document.getElementsByName("product_num[]");//扫描数量
        var v_max_return_number = document.getElementsByName("rec_num[]");//最大可退数量
        
        var v_ctb_goods_number = document.getElementsByName("ctb_product_num[]");//扫描数量
        var v_ctb_max_return_number = document.getElementsByName("ctb_rec_num[]");//最大可退数量
		var v_flag = true;
		
		for (var i = 0; i < v_max_return_number.length; i++) {
		    if (v_goods_number.item(i).value == v_max_return_number.item(i).value) {
			    continue;
			}
			v_flag = false;                     
		}
		
		for (var i = 0; i < v_ctb_max_return_number.length; i++) {
		    if (v_ctb_goods_number.item(i).value == v_ctb_max_return_number.item(i).value) {
			    continue;
			}
			v_flag = false;
		}

		if (!v_flag) {
		    alert('扫描数量与实际发货数量不一致，请将该订单的商品全部退回！');
			return false;
		}
         
		return true;
	}

	/*function load_location (op_id) {
		var depot_id = parseInt($(':input[name=depot_'+op_id+']').val());
		location_select = $(':input[name=location_'+op_id+']');
		location_select[0].options.length=1;
		if(!depot_id) return;
		$.ajax({
			url:'order_api/load_location',
			data:{depot_id:depot_id,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if(result.msg) alert(result.msg);
				if(result.err) return false;				
				for(i in result.data){
					location_select[0].options.add(new Option(result.data[i],i));
				}
			}
		});
	}*/    

    // start Gao add by 20120628 商品图片隐藏
    function hide_goods_img(rec_id, prefix)
    {
        if (prefix == undefined) prefix = '';
        $("#"+prefix+"img_rec_"+rec_id).hide();
        $("input[type=text][name=scan_input]").focus();
    }
    // 商品图片显示
    function show_goods_img(rec_id, prefix)
    {
        if (prefix == undefined) prefix = '';
        var pos = $("#"+prefix+"recid_"+rec_id).position();
        $("#"+prefix+"img_rec_"+rec_id).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
        }).fadeIn();
    }

    // 扫描条形码
    function enterkeyBarcode(e)
    {
        e = e ? e : (window.event ? window.event : null);
        var v_scan_code = $.trim($("input[type=text][name=scan_input]").val());
        $("#scan_tip").html('');
        if (e.keyCode != 13 || v_scan_code == '')
        {
            return false;
        }
        var v_goods_barcode = document.getElementsByName("goods_barcode[]");//条码
        var v_goods_number = document.getElementsByName("product_num[]");//扫描数量
        var v_max_return_number = document.getElementsByName("rec_num[]");//最大可退数量
        var v_ogid_obj = document.getElementsByName("op_id[]");
		
        var v_ctb_goods_barcode = document.getElementsByName("ctb_goods_barcode[]");//条码
        var v_ctb_goods_number = document.getElementsByName("ctb_product_num[]");//扫描数量
        var v_ctb_max_return_number = document.getElementsByName("ctb_rec_num[]");//最大可退数量
        var v_ctb_ogid_obj = document.getElementsByName("ctb_op_id[]");
		
        var v_scan_code_exists = false;

        for (var i = 0; i < v_goods_barcode.length; i++)
        {
            var v_return_number = parseInt(v_goods_number.item(i).value);
            var v_ogid = v_ogid_obj.item(i).value;
            if (v_goods_barcode.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code)
            {
                $("#img_rec_"+v_ogid).hide();
                continue;
            }
            v_scan_code_exists = true;
            
            if (v_return_number >= parseInt(v_max_return_number.item(i).value))
            {
                $("#img_rec_"+v_ogid).hide();
                $("#scan_tip").html('此条形码扫描次数过多!');
                continue;
            }
            
            // 每扫描一次退货数量增1
            v_goods_number.item(i).value = v_return_number + 1;
            // 每扫描一次显示一次该商品的图片
            var pos = $("#recid_"+v_ogid).position();
            $("#img_rec_"+v_ogid).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
            }).fadeIn();
        }

        for (var i = 0; i < v_ctb_goods_barcode.length; i++)
        {
            var v_return_number = parseInt(v_ctb_goods_number.item(i).value);
            var v_ogid = v_ctb_ogid_obj.item(i).value;
            if (v_ctb_goods_barcode.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code)
            {
                $("#ctb_img_rec_"+v_ogid).hide();
                continue;
            }
            v_scan_code_exists = true;
            
            if (v_return_number >= parseInt(v_ctb_max_return_number.item(i).value))
            {
                $("#ctb_img_rec_"+v_ogid).hide();
                $("#scan_tip").html('此条形码扫描次数过多!');
                continue;
            }
            
            // 每扫描一次退货数量增1
            v_ctb_goods_number.item(i).value = v_return_number + 1;
            // 每扫描一次显示一次该商品的图片
            var pos = $("#ctb_recid_"+v_ogid).position();
            $("#ctb_img_rec_"+v_ogid).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
            }).fadeIn();
        }
        
        if (!v_scan_code_exists) $("#scan_tip").html('此条形码不存在这个退货单的商品中!');
        
        $("input[type=text][name=scan_input]").val('');
        $("input[type=text][name=scan_input]").focus();
        e.returnValue= false; // 取消此事件的默认操作
    }
    // end Gao add by 20120628	
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> 拒收 </span><span class="r"><a href="order/info/<?php print $order->order_id; ?>" class="return">返回订单详情</a></span></div>
    <div class="produce">
    <span id="span_input_lang" style="font-weight:bold;">条型码扫描：</span><input type="text" id="scan_input" name="scan_input" style="ime-mode:disabled; width:186px;" onkeydown="enterkeyBarcode(event);"/>
    <span id="scan_tip" style="color:#ff0000;"></span>
    </div>
	<?php print form_open('order/proc_deny',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('order_id'=>$order->order_id));?>
        <table class="dataTable" cellpadding=0 cellspacing=0>
			<tr>
				<td class="item_title" style="text-align:center;font-weight:bold;">商品名称</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">商品款号</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">供应商货号</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">批次号</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">颜色规格</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">条码</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">待入库数量</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">扫描数量</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">来源</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">入库储位</td>
				<td class="item_title" style="text-align:center;font-weight:bold;">操作</td>
			</tr>
			
			<?php foreach ($order_product as $product): ?>
			<?php if ($product['real_number']>0): ?>
			<tr>
                <td>
				<a id="recid_<?php print $product['op_id']; ?>" href="#" target="_blank" onmouseout="hide_goods_img(<?php print $product['op_id']; ?>);" onmouseover="show_goods_img(<?php print $product['op_id']; ?>);"><?php print $product['product_name']; ?></a> <br>[ <?php print $product['brand_name']; ?> ]
                <div id="img_rec_<?php print $product['op_id']; ?>" style="display: none; background-color: #f0f0f0; border:1px #cccccc solid;width:222px;">
                <span style="float: right; padding:3px; color:#000000; font-weight: bold;font-size:14px; cursor: pointer;" onclick="hide_goods_img(<?php print $product['op_id']; ?>);">关闭</span><br>
                <img src="<?php print ($product['img_215_215']) ? "public/data/images/".$product['img_215_215'] : ''; ?>"/>
                </div>
	            </td>
                <td><?php print $product['product_sn']; ?></td>
                <td><?php print $product['provider_productcode']; ?></td>
		<td><?php print $product['batch_code']; ?></td>
                <td><?php print $product['color_name']." - ".$product['size_name']." <br> ".$product['color_sn']." - ".$product['size_sn']; ?></td>
                <td><?php print $product['provider_barcode']; ?></td>
                <td><?php print $product['real_number']; ?></td>
				<td><input type="text" name="product_num[]" value="0" style="width:40px;" readonly /></td>
				<td><?php print $product['out_depot']; ?></td>			
				<td>
			    <?php print form_hidden('op_id[]',$product['op_id']); ?>
				<input type="hidden" name="goods_barcode[]" value="<?php print $product['provider_barcode']; ?>">
			    <input type="hidden" name="rec_num[]" value="<?php print $product['real_number']; ?>">
                <select name="depot_id[]" >
				    <option value="<?php print $product['order_depot_id']; ?>"><?php print $product['order_depot_name']; ?></option>
				</select>
				<input type="text" id="ps-<?php print $product['op_id']; ?>" value="<?php print $product['order_location_name']; ?>" readonly />
				<input type="hidden" name="location_id[]" id="psh-<?php print $product['op_id']; ?>" value="<?php print $product['order_location_id']; ?>" />
				</td>
				<td><a href="order_return/print_barcode/<?php print urlencode($product['provider_barcode']); ?>/<?php print urlencode($product['product_name']); ?>/<?php print urlencode($product['color_name']); ?>/<?php print urlencode($product['size_name']); ?>/<?php print urlencode($product['provider_productcode']); ?>" target="_blank">打印条码</a></td>
			</tr>
            <?php endif; ?>
			<?php if ($product['ctb_number'] >0): ?>
            <tr>

                <td>
				<a id="ctb_recid_<?php print $product['op_id']; ?>" href="#" target="_blank" onmouseout="hide_goods_img(<?php print $product['op_id']; ?>, 'ctb_');" onmouseover="show_goods_img(<?php print $product['op_id']; ?>, 'ctb_');"><?php print $product['product_name']; ?></a><br>[ <?php print $product['brand_name']; ?> ]
                <div id="ctb_img_rec_<?php print $product['op_id']; ?>" style="display: none; background-color: #f0f0f0; border:1px #cccccc solid;width:222px;">
                <span style="float: right; padding:3px; color:#000000; font-weight: bold;font-size:14px; cursor: pointer;" onclick="hide_goods_img(<?php print $product['op_id']; ?>, 'ctb_');">关闭</span><br>
                <img src="<?php print ($product['img_215_215']) ? "public/data/images/".$product['img_215_215'] : ''; ?>"/>
                </div>
				</td>
                <td><?php print $product['product_sn']; ?></td>
                <td><?php print $product['provider_productcode']; ?></td>
		<td><?php print $product['batch_code']; ?></td>
                <td><?php print $product['color_name']." - ".$product['size_name']." <br> ".$product['color_sn']." - ".$product['size_sn']; ?></td>
                <td><?php print $product['provider_barcode']; ?></td>
                <td><?php print $product['ctb_number']; ?></td>
				<td><input type="text" name="ctb_product_num[]" value="0" style="width:40px;" readonly /></td>
				<td>
				<?php print $product['out_depot']; ?>
				</td>
                <td>
				<input type="hidden" name="ctb_goods_barcode[]" value="<?php print $product['provider_barcode']; ?>">
                <?php print form_hidden('ctb_op_id[]',$product['op_id']); ?>
				<input type="hidden" name="ctb_rec_num[]" value="<?php print $product['ctb_number']; ?>">
                <select name="ctb_depot_id[]" >
				    <option value="<?php print $product['order_depot_id']; ?>"><?php print $product['order_depot_name']; ?></option>
				</select>
				<input type="text" id="ctb_ps-<?php print $product['op_id']; ?>" value="<?php print $product['order_location_name']; ?>" readonly />
				<input type="hidden" name="ctb_location_id[]" id="ctb_psh-<?php print $product['op_id']; ?>" value="<?php print $product['order_location_id']; ?>" />
						
				</td>
				<td><a href="order_return/print_barcode/<?php print urlencode($product['provider_barcode']); ?>/<?php print urlencode($product['product_name']); ?>/<?php print urlencode($product['color_name']); ?>/<?php print urlencode($product['size_name']); ?>/<?php print urlencode($product['provider_productcode']); ?>" target="_blank">打印条码</a></td>			
			</tr>
            <?php endif; ?>			
			<?php endforeach ?>
			
			<?php if ($order->shipping_fee): ?>
			<tr>
				<td class="item_title">返还运费</td>
				<td class="item_input" colspan="9">
					<?php print form_checkbox('return_shipping_fee','1') ?>
				</td>
			</tr>				
			<?php endif ?>
			
			<tr>
				<td colspan="10">
					<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"') ?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
<script type="text/javascript">
	//<![CDATA[
	/*function check_form(){
        var v_goods_number = document.getElementsByName("product_num[]");//扫描数量
        var v_max_return_number = document.getElementsByName("rec_num[]");//最大可退数量
        
        var v_ctb_goods_number = document.getElementsByName("ctb_product_num[]");//扫描数量
        var v_ctb_max_return_number = document.getElementsByName("ctb_rec_num[]");//最大可退数量
		var v_flag = true;
		
		for (var i = 0; i < v_max_return_number.length; i++) {
		    if (v_goods_number.item(i).value == v_max_return_number.item(i).value) {
			    continue;
			}
			v_falg = false;                     
		}
		
		for (var i = 0; i < v_ctb_max_return_number.length; i++) {
		    if (v_ctb_goods_number.item(i).value == v_ctb_max_return_number.item(i).value) {
			    continue;
			}
			v_falg = false;
		}
		
		if (!v_falg) {
		    alert('扫描数量与实际发货数量不一致，请将该订单的商品全部退回');
			return false;
		}
		
		return true;
	}

	function load_location (op_id) {
		var depot_id = parseInt($(':input[name=depot_'+op_id+']').val());
		location_select = $(':input[name=location_'+op_id+']');
		location_select[0].options.length=1;
		if(!depot_id) return;
		$.ajax({
			url:'order_api/load_location',
			data:{depot_id:depot_id,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if(result.msg) alert(result.msg);
				if(result.err) return false;				
				for(i in result.data){
					location_select[0].options.add(new Option(result.data[i],i));
				}
			}
		});
	}
	

    // start Gao add by 20120628 商品图片隐藏
    function hide_goods_img(rec_id, prefix='')
    {
        $("#"+prefix+"img_rec_"+rec_id).hide();
        $("input[type=text][name=scan_input]").focus();
    }
    // 商品图片显示
    function show_goods_img(rec_id, prefix='')
    {
        var pos = $("#"+prefix+"recid_"+rec_id).position();
        $("#"+prefix+"img_rec_"+rec_id).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
        }).fadeIn();
    }

    // 扫描条形码
    function enterkeyBarcode(e)
    {
        e = e ? e : (window.event ? window.event : null);
        var v_scan_code = $.trim($("input[type=text][name=scan_input]").val());
        $("#scan_tip").html('');
        if (e.keyCode != 13 || v_scan_code == '')
        {
            return false;
        }
        var v_goods_barcode = document.getElementsByName("goods_barcode[]");//条码
        var v_goods_number = document.getElementsByName("product_num[]");//扫描数量
        var v_max_return_number = document.getElementsByName("rec_num[]");//最大可退数量
        var v_ogid_obj = document.getElementsByName("op_id[]");
		
        var v_ctb_goods_barcode = document.getElementsByName("ctb_goods_barcode[]");//条码
        var v_ctb_goods_number = document.getElementsByName("ctb_product_num[]");//扫描数量
        var v_ctb_max_return_number = document.getElementsByName("ctb_rec_num[]");//最大可退数量
        var v_ctb_ogid_obj = document.getElementsByName("ctb_op_id[]");
		
        var v_scan_code_exists = false;

        for (var i = 0; i < v_goods_barcode.length; i++)
        {
            var v_return_number = parseInt(v_goods_number.item(i).value);
            var v_ogid = v_ogid_obj.item(i).value;
            if (v_goods_barcode.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code)
            {
                $("#img_rec_"+v_ogid).hide();
                continue;
            }
            v_scan_code_exists = true;
            
            if (v_return_number >= parseInt(v_max_return_number.item(i).value))
            {
                $("#img_rec_"+v_ogid).hide();
                $("#scan_tip").html('此条形码扫描次数过多!');
                continue;
            }
            
            // 每扫描一次退货数量增1
            v_goods_number.item(i).value = v_return_number + 1;
            // 每扫描一次显示一次该商品的图片
            var pos = $("#recid_"+v_ogid).position();
            $("#img_rec_"+v_ogid).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
            }).fadeIn();
        }

        for (var i = 0; i < v_ctb_goods_barcode.length; i++)
        {
            var v_return_number = parseInt(v_ctb_goods_number.item(i).value);
            var v_ogid = v_ctb_ogid_obj.item(i).value;
            if (v_ctb_goods_barcode.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code)
            {
                $("#ctb_img_rec_"+v_ogid).hide();
                continue;
            }
            v_scan_code_exists = true;
            
            if (v_return_number >= parseInt(v_ctb_max_return_number.item(i).value))
            {
                $("#ctb_img_rec_"+v_ogid).hide();
                $("#scan_tip").html('此条形码扫描次数过多!');
                continue;
            }
            
            // 每扫描一次退货数量增1
            v_goods_number.item(i).value = v_return_number + 1;
            // 每扫描一次显示一次该商品的图片
            var pos = $("#ctb_recid_"+v_ogid).position();
            $("#ctb_img_rec_"+v_ogid).css({
            position:'absolute',
            index:'999',
            border:'solid 3px #EFEFEF',
            left:(pos.left+140)+'px',
            top:(pos.top-10)+'px'
            }).fadeIn();
        }
        
        if (!v_scan_code_exists) $("#scan_tip").html('此条形码不存在这个退货单的商品中!');
        
        $("input[type=text][name=scan_input]").val('');
        $("input[type=text][name=scan_input]").focus();
        e.returnValue= false; // 取消此事件的默认操作
    }*/
    // end Gao add by 20120628	
	//]]>
</script>
