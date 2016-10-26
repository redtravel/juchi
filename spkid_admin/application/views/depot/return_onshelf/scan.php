<?php include APPPATH.'views/common/rf_header.php'; ?>
    <style type="text/css">
	table.dataTable {
		border-bottom:1px solid #d0d0d0;
		border-right:1px solid #d0d0d0;
		width:100%;
		margin-top:2px;
	}
	table.dataTable th, .dataTable td {
		border-top:1px solid #d0d0d0;
		border-left:1px solid #d0d0d0;
		padding:0 2px;
		font-size: 12px;
	}
    </style>
    
    <div class="main">
        <table class="form" cellpadding=0 cellspacing=0>
            <tbody>
                <tr>
                    <td colspan="2" id="scaned_msg" style="color:red"></td>
                </tr>
                <tr>
                    <td class="item_title">
                        <input type="button" id="reset" name="reset" value="重 置" />
                    </td>
                    <td class="item_input">
                        <input type="button" id="commit" name="commit" value="确 认" disabled="true" />
                        <?php print $from_depot->depot_name; ?>
                    </td>
                </tr>
                <tr id="from">
                    <td class="item_title">原储位</td>
                    <td class="item_input">
                        <input type="text" id="from_location_name" name="from_location_name" class="textbox" size="30" style="width:130px;"
                               <?php if($type == '1'): ?> value="<?php print $from_location_name; ?>" <?php endif; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="item_title">扫描商品</td>
                    <td class="item_input">
                        <input type="text" id="provider_barcode" name="provider_barcode" class="textbox" size="30" style="width:130px;" />
                    </td>
                </tr>
                <tr>
                    <td class="item_title">目标储位</td>
                    <td class="item_input">
                        <input type="text" id="to_location_name" name="to_location_name" class="textbox" size="30" style="width:130px;" />
                        <span id="box_msg" style="color:red"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="blank5" id="hidden"></div>
    <div id="dataDiv" ></div>
    
    <script language="javascript" type="text/javascript">
        $(function(){
            //判断是否为退货仓
            var depot_id = <?php print $from_depot->depot_id; ?>;
            var return_depot = <?php print $type == 0 ? 0 : 1; ?>;
            
            //重置方法
            function reset() {
                if (return_depot === 1) {
                    $('#from').hide();
                    $('#provider_barcode').attr('disabled', false).val('').focus();
                } else {
                    $('#from_location_name').attr('disabled', false).val('').focus();
                    $('#provider_barcode').val('').attr('disabled', true);
                }
                $('#to_location_name').val('').attr('disabled', true);
                
                $('#dataDiv').html('');
            }
            
            reset(); //载入时先初始化
            
            // 原储位扫描完后将焦点给到商品条码
            $("#from_location_name").keydown(function(event) {
                if(event.which === 13) {
                    var from_location_val = $('#from_location_name').val();
                    if (from_location_val === null || from_location_val === '') {
                        alert('请扫描原储位！');
                        return;
                    }
                    
                    var url = '/return_onshelf/get_location';
                    var data = {depot_id:depot_id, location_name:from_location_val};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $('#from_location_name').attr('disabled', false).val('').focus();
                        } else {
                            $('#from_location_name').attr('disabled', true);
                            $("#provider_barcode").attr('disabled', false).val('').focus();
                        }
                    });
                    
                }
            });
            
            // 商品条码扫描完后显示商品详情
            $("#provider_barcode").keydown(function(event) {
                if(event.which === 13) {
                    var from_location_val = $('#from_location_name').val();
                    if (from_location_val === null || from_location_val === '') {
                        alert('请扫描原储位！');
                        return;
                    }
                    
                    // 显示已扫描的商品
                    var provider_barcode = $('#provider_barcode').val();
                    if (provider_barcode === null || provider_barcode === '') {
                        $("#product_barcode").attr('disabled', false).val('').focus();
                        return;
                    }
                    
                    var url = '/return_onshelf/get_products';
                    var data = {location_name:from_location_val, provider_barcode:provider_barcode};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $("#product_barcode").attr('disabled', false).val('').focus();
                        } else {
                            var added = false;
                            var products = result.list;
                            for(var i = 0; i < products.length; i++) {
                                var quantity_id = products[i].product_id + "_" + products[i].color_id + "_" + products[i].size_id + "_" + products[i].batch_id;
                                
                                var quantity = $('#'+quantity_id);
                                if (quantity[0]) {
                                    var old_num = parseInt(quantity.attr('scannum'));
                                    if (parseInt(products[i].num_shiji) + parseInt(products[i].num_daicu) <= old_num) {
                                        //alert('商品可出库数为0，不能再扫描！');
                                        //return;
                                        continue;
                                    } else {
                                        var new_num = old_num + 1;
                                        quantity.attr('scannum', new_num);
                                        $("#num_"+quantity_id).html(new_num);
                                        added = true;
                                        break;
                                    }
                                } else {
                                    var row = '<table id="'+quantity_id+'" class="dataTable" product_id="'+products[i].product_id+'" color_id="'+products[i].color_id+'" size_id="'+products[i].size_id+'" shop_price="'+products[i].shop_price+'" batch_id="'+products[i].batch_id+'" scannum="1">';

                                    row += '<tr class="row"><td align="left">条码</td><td>';
                                    row += products[i].provider_barcode;
                                    row += '</td></tr>';

                                    row += '<tr class="row"><td align="left">商品</td><td>';
                                    row += products[i].product_name+'|'+products[i].color_name+'|'+products[i].size_name;
                                    row += '</td></tr>';

                                    row += '<tr class="row"><td align="left">数量</td><td>';
                                    row += '待入:'+products[i].num_dairu+'&nbsp;待出:'+products[i].num_daicu+'&nbsp;实际:'+products[i].num_shiji+'&nbsp;已扫:<span style="color:red;" id="num_'+quantity_id+'">1</span>';
                                    row += '</td></tr>';

                                    row += '</table>';
                                    $('#dataDiv').append(row);
                                    
                                    added = true;
                                    break;
                                }
                            }
                            
                            if (added === false) {
                                alert('商品可出库数为0，不能再扫描！');
                                return;
                            }
                            
                            $('#dataDiv').show();
                        }
                    });
                    
                    $('#from_location_name').attr('disabled', true);
                    $("#commit").attr('disabled', false);
                    $("#provider_barcode").val('').focus();
                }
            });
            
            // 目标储位扫描完后将焦点移到提交按钮
            $("#to_location_name").keydown(function(event) {
                if(event.which === 13) {
                    var to_location_val = $('#to_location_name').val();
                    if (to_location_val === null || to_location_val === '') {
                        alert('请扫描目标储位！');
                        return;
                    }
                    
                    var url = '/return_onshelf/get_location';
                    var data = {depot_id:depot_id, location_name:to_location_val};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $('#to_location_name').attr('disabled', false).val('').focus();
                        } else {
                            $('#to_location_name').attr('disabled', true);
                            $("#commit").attr('disabled', false).focus();
                        }
                    });
                    
                }
            });
            
            //提交方法
            function commit() {
                //检查原储位
                var from_location_val = $('#from_location_name').val();
                if (from_location_val === null || from_location_val === '') {
                    alert('请扫描原储位！');
                    return;
                }
                
                //检查商品
                var product_id_ary = [];
                var color_id_ary = [];
                var size_id_ary = [];
                var shop_price_ary = [];
                var batch_id_ary = [];
                var product_number_ary = [];
                $('#dataDiv table[class=dataTable]').each(function(){
                    product_id_ary.push($(this).attr('product_id'));
                    color_id_ary.push($(this).attr('color_id'));
                    size_id_ary.push($(this).attr('size_id'));
                    shop_price_ary.push($(this).attr('shop_price'));
                    batch_id_ary.push($(this).attr('batch_id'));
                    product_number_ary.push($(this).attr('scannum'));
                });
                if (product_id_ary.length === 0 || 
                        color_id_ary.length === 0 || 
                        size_id_ary.length === 0 || 
                        shop_price_ary.length === 0 || 
                        batch_id_ary.length === 0 || 
                        product_number_ary.length === 0) {
                    alert('请扫描商品！');
                    return;
                }
                
                //检查目标储位
                var to_location_val = $('#to_location_name').val();
                if (to_location_val === null || to_location_val === '') {
                    $('#provider_barcode').val('').attr('disabled', true);
                    $('#to_location_name').attr('disabled', false).val('').focus();
                    return;
                } else {
                    //提交移储
                    var data = {
                        from_location_name:from_location_val, 
                        to_location_name:to_location_val, 
                        product_id_ary:product_id_ary, 
                        color_id_ary:color_id_ary, 
                        size_id_ary:size_id_ary, 
                        shop_price_ary:shop_price_ary, 
                        batch_id_ary:batch_id_ary, 
                        product_number_ary:product_number_ary
                    };
                    $.post('/return_onshelf/exchange', data, function(result) {
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                        } else {
                            alert('提交成功！');
                        }
                    });
                    reset();
                }
            }
            
            //提交按钮事件
            $("#commit").click(function() {
                commit();
            });
            
            //重置按钮事件
            $("#reset").click(function() {
                reset();
            });
            
            //快捷绑定事件
            $(document).keydown(function(event) {
                if (event.which === 38) { // up
                    commit();
                } else if (event.which === 40) { // down
                    reset();
                }
            });
            
        });
    </script>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>