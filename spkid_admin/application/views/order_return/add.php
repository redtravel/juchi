<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript">
        //选择返回用户运费
        function changeCheckbox(obj,ename){
            if($(obj).prop('checked')){
                $('input[type=text][name='+ename+']').removeAttr('readonly');
            }else{
                $('input[type=text][name='+ename+']').attr('readonly','readonly');
                $('input[type=text][name='+ename+']').val('');
            }
        }
        function inputNumber(obj){
            var val = $(obj).val();
            if(isNaN(val)){
                alert(val+"不是合法数字!");
                $(obj).val('');
            }
        }
        
        function select_shipping(obj){
            if($(obj).val()==='其他'){
                var shipping_name = window.prompt("请输入一个新的快递公司名称：", "");
                if (shipping_name!=null && shipping_name!==""){
                    $(obj).append('<option value="'+shipping_name+'">'+shipping_name+'</option>');
                    $(obj).val(shipping_name);;
                }
            }
        }
        //add by shangguannan 2013-04-18
		//<![CDATA[

		$(function(){
            load_order_data();
	    });

	    function load_order_data(){
	        var order_sn = $.trim($('input[type=text][name=order_sn]').val());
                var apply_id = $.trim($('input[type=hidden][name=apply_id]').val());
	        if (order_sn == '') return false;
	        $.ajax({
	            url: '/order_return/get_order_data',
	            data: {order_sn:order_sn,apply_id:apply_id,rnd:new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success:function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	document.getElementById('listDiv').innerHTML = result.content;
	                	//$('#listDiv').html(result.content);
						$('input[type=text][name=hope_time]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
                        // start Gao add by 20120628 条码扫描
                        $("#span_input_lang").show();
                        $('input[type=text][name=scan_input]').show().focus();
                        // end Gao
					}
	            }
	        });
	        return false;
	    }

		function check_add(){
	        return check_form_input();
	    }

	    function check_form_input(){
	        var order_id = parseInt($('input[name=order_id][type=hidden]').val());
	        if(isNaN(order_id)||order_id<1){
	            alert('订单ID错误!');
	            return false;
	        }
	        var return_product = new Array();
	        var max_error = false;
	        var param_error = false;
	        $('tr.tr_goods').each(function(i){
	            var tr = $(this);
	            var op_id = parseInt($('input[type=hidden][name^="op_id"]',tr).val());
	            var product_num = parseInt($('input[type=text][name^="product_num"]',tr).val());
	            var max_return_number = parseInt($('input[type=hidden][name^="max_return_number"]',tr).val());
	            if(isNaN(op_id)||isNaN(product_num)||isNaN(max_return_number)||product_num<0){
	                param_error = true;
	            }
	            if(product_num > max_return_number){
	                max_error = true;
	            }
	            if(product_num>0){
	                return_product.push(op_id+'|'+product_num);
	            }
	        });
	        if(max_error){
	            alert('退货数量超过商品可退数量！');
	            return false;
	        }
	        if(param_error){
	            alert('输入数据错误：退货数量请输入整数！');
	            return false;
	        }
	        if(return_product.length<1){
	            alert('请选择退货商品！');
	            return false;
	        }
	        return true;

	    }
	    function pre_calc_voucher(){
	        if(!check_form_input()){
	            return false;
	        }
	        var order_id = parseInt($('input[name=order_id][type=hidden]').val());
	        var return_product = new Array();
	        $('tr.tr_goods').each(function(i){
	            var tr = $(this);
	            var op_id = parseInt($('input[type=hidden][name^="op_id"]',tr).val());
	            var product_num = parseInt($('input[type=text][name^="product_num"]',tr).val());
	            var max_return_number = parseInt($('input[type=hidden][name^="max_return_number"]',tr).val());
	            return_product.push(op_id+'|'+product_num);
	        });

	        $.ajax({
	            url: '/order_return/pre_calc_voucher',
	            data: {is_ajax:1,order_id:order_id,return_product:return_product.join('$'),return_id:0, rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	            }
	        });
	        return false;
	    }

    // start Gao add by 20120628 商品图片隐藏
    function hide_goods_img(rec_id)
    {
        $("#img_rec_"+rec_id).hide();
        $("input[type=text][name=scan_input]").focus();
    }
    // 商品图片显示
    function show_goods_img(rec_id)
    {
        var pos = $("#recid_"+rec_id).position();
        $("#img_rec_"+rec_id).css({
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
        var v_goods_barcode = document.getElementsByName("goods_barcode[]");
        var v_goods_number = document.getElementsByName("product_num[]");
        var v_max_return_number = document.getElementsByName("max_return_number[]");
        var v_ogid_obj = document.getElementsByName("op_id[]");
		var v_sku = document.getElementsByName("sku[]");
        var v_scan_code_exists = false;
        for (var i = 0; i < v_goods_barcode.length; i++)
        {
            var v_return_number = parseInt(v_goods_number.item(i).value);
            var v_ogid = v_ogid_obj.item(i).value;
            if (v_goods_barcode.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code && v_sku.item(i).value.replace(/^\s+|\s+$/g, "") != v_scan_code)
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
        
        if (!v_scan_code_exists) $("#scan_tip").html('此条形码不存在这个退货单的商品中!');
        
        $("input[type=text][name=scan_input]").val('');
        $("input[type=text][name=scan_input]").focus();
        e.returnValue= false; // 取消此事件的默认操作
    }
    // end Gao add by 20120628
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">退货单管理 &gt;&gt; 新增退货单</span><span class="r">[ <a href="/order_return">返回列表 </a>]</span></div>
		<div class="produce">
		<div class="pc base">
		<div class="search_row">
			订单编号：<input type="text" class="tl" name="order_sn" value="<?php print isset($order_info)?$order_info->order_sn:'' ?>" />
            <input type="button" class="am-btn am-btn-primary" value="载入" onclick="load_order_data()" />
            <span id="span_input_lang" style="margin-left:200px;display:none;">条型码扫描：</span><input type="text" id="scan_input" name="scan_input" style="ime-mode:disabled; width:186px;display:none;" onkeydown="enterkeyBarcode(event);"/>
            <span id="scan_tip" style="color:#ff0000;"></span>
            <input type="hidden" name="apply_id" value="<?php print $apply_id; ?>"/>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">

		</div>
		</div></div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
