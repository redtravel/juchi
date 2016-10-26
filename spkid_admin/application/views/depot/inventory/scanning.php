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
    
    <div id="listDiv">
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan="2" id="scaned_msg"></td>
            </tr>
            <tr>
                <td class="item_title">
                    <input type="button" id="detail" name="detail" value="详情" />
                </td>
                <td class="item_input">
                    <input type="button" id="reset" name="reset" value="重置" />
                    <input type="button" id="commit" name="commit" value="提 交" disabled="true" />
                    储位无货<input type="checkbox" id="empty" name="empty" />
                </td>
            </tr>
            <tr>
                <td class="item_title" width="20%">编号:</td>
                <td class="item_input"><?=$row->inventory_sn; ?></td>
            </tr>
            <tr>
                <td class="item_title" width="20%">储位:</td>
                <td class="item_input">
                    <input type="text" id="location_name" name="location_name" class="textbox" size="30" style="width:130px;" />
                    <span id="box_msg" style="color:red"></span>
                </td>
            </tr>
            <tr>
                <td class="item_title" width="20%">商品:</td>
                <td class="item_input">
                    <input type="text" id="product_barcode" name="product_barcode" class="textbox" size="30" style="width:130px;" />
                    <span id="product_msg" style="color:red"></span>
                </td>
            </tr>
        </table>
    </div>
    
    <input type="hidden" id="inventory_id" name="inventory_id" value="<?=$row->inventory_id;?>" />
    <div id="scanDiv" style="display:none;"></div>
    <br style="clear:all; height:5px; line-height:5px;" />
    <div id="dataDiv" style="display:none;"></div>

    <script language="javascript" type="text/javascript">
        $(function(){
            var location_product_barcodes = null;
            
            $('#location_name').focus();
            $('#product_barcode').val('').attr('disabled', true);
            
            function reset() {
                $("#location_name").attr('disabled', false).val('').focus();
                $('#commit').attr('disabled', true);
                
                $('#dataDiv').html('');
                $('#dataDiv').hide();
                
                $('#scanDiv').html('');
                $('#scanDiv').hide();
               
                $('#scaned_msg').html('');
                $('#empty').prop('checked', false);
                
                location_product_barcodes = null;
            }
            
            function inLocation(product_barcode) {
                if (location_product_barcodes !== null) {
                    for (var i = 0; i < location_product_barcodes.length; i++) {
                        if (product_barcode === location_product_barcodes[i].provider_barcode) {
                            return true;
                        }
                    }
                }
                return false;
            }
            
            function prependScanTable(product_barcode) {
                var row = '<table class="dataTable" barcode="'+product_barcode+'" scannum="1">';
                row += '<tr class="row"><td align="left">条码:'+product_barcode+'</td><td>';
                row += '已扫:<span style="color:red;">1</span>';
                row += '</td></tr></table>';
                $('#scanDiv').prepend(row);

                $('#scanDiv').show();
            }
            
            function commit() {
                $('#commit').attr('disabled', true);
                
                var location_name = $('#location_name').val();
                if (location_name === "") {
                    alert('请扫描储位');
                    return;
                }
                
                var product_barcode_ary = [];
                var product_number_ary = [];
                var emptyChecked = true;
                if (!$('#empty').is(':checked')) {
                    emptyChecked = false;
                    
                    $('#scanDiv table[class=dataTable]').each(function(){
                        var scannum = $(this).attr('scannum');
                        if (scannum > 0) {
                            product_barcode_ary.push($(this).attr('barcode'));
                            product_number_ary.push(scannum);
                        }
                    });
                    
                    if (product_barcode_ary.length === 0 || product_number_ary.length === 0) {
                        alert('请扫描商品');
                        $('#commit').attr('disabled', false);
                        $("#product_barcode").attr('disabled', false).val('').focus();
                        return;
                    }
                }

                var inventory_id = $('#inventory_id').val();
                var data = {inventory_id:inventory_id, location_name:location_name, product_barcode_ary:product_barcode_ary, product_number_ary:product_number_ary, empty:emptyChecked};
                $.post("/inventory/add_details", data, function(result) {
                    result = jQuery.parseJSON(result);
                    if (result.err === 1) {
                        alert(result.msg);
                    } else {
                        alert('库存数量：' + result.inventory_number + ', 盘点数量：' + result.scaned_number);
                    }

                    reset();
                });
            }

            // 储位条码扫描完后将焦点给到商品条码
            $("#location_name").keydown(function(event) {
                if(event.which === 13) {
                    var location_val = $('#location_name').val();
                    if (location_val === null || location_val === '') {
                        alert('请扫描储位编码！');
                        return;
                    }
                    
                    var url = '/inventory/get_location_products';
                    var inventory_id = $('#inventory_id').val();
                    var data = {inventory_id:inventory_id, location_name:location_val};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $('#location_name').attr('disabled', false).val('').focus();
                        } else {
                            $('#location_name').attr('disabled', true);
                            $('input[type=button][name=commit]').attr('disabled', false);
                            
                            $('#scaned_msg').html('库存量:' + result.inventory_number + ', 已提交:'+result.scaned_number+', 本次扫描：<span style="color:red;" id="num_scaned">0</span>');
                            
                            location_product_barcodes = result.list;
                        }
                    });
                    
                    $("#product_barcode").attr('disabled', false).val('').focus();
                }
            });

            // 商品条码扫描完后添加扫描结果
            $("#product_barcode").keydown(function(event) {
                if(event.which === 13) {
                    var product_barcode = $(this).val();
                    $(this).val("").focus();
                    if(product_barcode === ""){
                        return;
                    } else if(!inLocation(product_barcode)) {
                        $.post('/inventory/checkProviderBarcode', {provider_barcode:product_barcode}, function(result){
                            result = jQuery.parseJSON(result);
                            if (result.err === 1) {
                                alert(result.msg);
                            } else {
                                // 设置扫描数量
                                $("#num_scaned").html($("#num_scaned").html() - 0 + 1);
                                // 渲染扫描的商品信息
                                prependScanTable(product_barcode);
                            }
                        });
                    } else {
                        // 设置扫描数量
                        $("#num_scaned").html($("#num_scaned").html() - 0 + 1);
                        // 渲染扫描的商品信息
                        prependScanTable(product_barcode);
                    }
                }
            });

            //提交按钮事件
            $("#commit").click(function() {
                commit();
            });
            
            //重置按钮事件
            $("#reset").click(function() {
                reset();
            });
            
            //详情按钮事件
            $("#detail").click(function() {
                if ($("#dataDiv").css("display")==="none") {
                    var location_val = $('#location_name').val();
                    if (location_val === null || location_val === '') {
                        alert('请扫描储位编码！');
                        return;
                    }

                    var url = '/inventory/location_product_details';
                    var inventory_id = $('#inventory_id').val();
                    var data = {inventory_id:inventory_id, location_name:location_val};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                        } else {
                            $('#dataDiv').html(result.list);
                            $('#dataDiv').show();
                        }
                    });
                } else {
                    $('#dataDiv').html('');
                    $('#dataDiv').hide();
                }
            });
            
            //快捷键
            $(document).keydown(function(event) {
//                if (event.which === 38) { // up
//                    commit();
//                } else 
                if (event.which === 40) { // down
                    reset();
                } else if (event.which === 39) { // right
                    if ($('#empty').is(':checked')) {
                        $('#empty').prop('checked', false);
                    } else {
                        $('#empty').prop('checked', true);
                    }
                }
            });
            
        });
    </script>
    
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>