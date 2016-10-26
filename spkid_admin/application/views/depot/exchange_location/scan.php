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
                    <td class="item_title">
                        <input type="button" name="reset" value="重 置" />
                    </td>
                    <td class="item_input">
                        <input type="button" name="commit" value="确 认" disabled="true" />
                    </td>
                </tr>
                <tr>
                    <td class="item_title">源储位</td>
                    <td class="item_input">
                        <input type="text" name="from_location_name" class="textbox" size="30" style="width:130px;" />
                        <span id="box_msg" style="color:red"></span>
                    </td>
                </tr>
                <tr>
                    <td class="item_title">目标储位</td>
                    <td class="item_input">
                        <input type="text" name="to_location_name" class="textbox" size="30" style="width:130px;" />
                        <span id="box_msg" style="color:red"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="blank5" id="hidden"></div>
    
    <div id="dataDiv" style="display:none;"></div>
    
    <script language="javascript" type="text/javascript">
        $(function(){
            $('input[type=text][name=from_location_name]').focus();
            $('input[type=text][name=to_location_name]').val('').attr('disabled', true);
            
            function reset() {
                $('input[type=button][name=commit]').attr('disabled', true);
                
                $('#dataDiv').html('');
                $('#dataDiv').hide();
            };
            
            // 源储位条码扫描完后将焦点给目标储位
            $("input[type=text][name=from_location_name]").keydown(function(event) {
                if(event.which === 13) {
                    var location_name_obj = $('input[type=text][name=from_location_name]');
                    var location_name = location_name_obj.val();
                    if (location_name === null || location_name === '') {
                        alert('请扫描源储位编码！');
                        return;
                    }
                    
                    var url = '/exchange_location/get_products';
                    var data = {location_name:location_name,source_location:true};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $('input[type=text][name=from_location_name]').attr('disabled', false).val('').focus();
                        } else {
                            location_name_obj.attr('disabled', true);
                            $('input[type=button][name=commit]').attr('disabled', false);
                            
                            var products = result.products;
                            for(var i = 0; i < products.length; i++) {
                                if (products[i].num_dairu === '0' && products[i].num_daichu === '0' && products[i].num_shiji === '0') {
                                    continue;
                                }
                                
                                var quantity_id = products[i].provider_barcode.replace(' ','-').replace(' ','-');
                                
                                var row = '<table name="'+quantity_id+'" class="dataTable" barcode="'+products[i].provider_barcode+'" scannum="0">';
                                
                                row += '<tr class="row"><td align="left">条码</td><td>';
                                row += products[i].provider_barcode;
                                row += '</td></tr>';
                                
                                row += '<tr class="row"><td align="left">商品</td><td>';
                                row += products[i].product_name+'|'+products[i].color_name+'|'+products[i].size_name;
                                row += '</td></tr>';
                                
                                row += '<tr class="row"><td align="left">数量</td><td>';
                                row += '待入:'+products[i].num_dairu+'&nbsp;待出:'+products[i].num_daichu+'&nbsp;实际:'+products[i].num_shiji;
                                row += '</td></tr>';
                                
                                row += '</table>';
                                $('#dataDiv').append(row);
                            }
                            
                            $('#dataDiv').show();
                        }
                    });
                }
            });

            //确认按钮点击事件
            $("input[type=button][name=commit]").click(function() {
                var from_location_name = $('input[type=text][name=from_location_name]').val();
                var to_location_name = $('input[type=text][name=to_location_name]').val();
                if (from_location_name === null || from_location_name === '') {
                    alert('请扫描源储位编码！');
                    reset();
                    $('input[type=text][name=from_location_name]').attr('disabled', false).val('').focus();
                } else {
                    if (to_location_name === null || to_location_name === '') {
                        reset();
                        $('input[type=text][name=to_location_name]').attr('disabled', false).val('').focus();
                    } else {
                        var url = '/exchange_location/exchange';
                        var data = {from_location_name:from_location_name, to_location_name:to_location_name};
                        $.post(url, data, function(result){
                            result = jQuery.parseJSON(result);
                            if (result.err === 1) {
                                alert(result.msg);
                            } else {
                                alert('仓内移储操作成功！');
                            }
                            reset();
                            $('input[type=text][name=to_location_name]').attr('disabled', true).val('');
                            $('input[type=text][name=from_location_name]').attr('disabled', false).val('').focus();
                        });
                    }
                }
            });
            
            // 目标储位条码扫描
            $("input[type=text][name=to_location_name]").keydown(function(event) {
                if(event.which === 13) {
                    var location_name_obj = $('input[type=text][name=to_location_name]');
                    var location_name = location_name_obj.val();
                    if (location_name === null || location_name === '') {
                        alert('请扫描目标储位编码！');
                        return;
                    }
                    
                    var url = '/exchange_location/get_products';
                    var data = {location_name:location_name};
                    $.post(url, data, function(result){
                        result = jQuery.parseJSON(result);
                        if (result.err === 1) {
                            alert(result.msg);
                            $('input[type=text][name=to_location_name]').attr('disabled', false).val('').focus();
                        } else {
                            location_name_obj.attr('disabled', true);
                            $('input[type=button][name=commit]').attr('disabled', false);
                            $('#dataDiv').html('');
                            
                            var products = result.products;
                            for(var i = 0; i < products.length; i++) {
                                if (products[i].num_dairu === '0' && products[i].num_daichu === '0' && products[i].num_shiji === '0') {
                                    continue;
                                }
                                
                                var quantity_id = products[i].provider_barcode.replace(' ','-').replace(' ','-');
                                
                                var row = '<table name="'+quantity_id+'" class="dataTable" barcode="'+products[i].provider_barcode+'" scannum="0">';
                                
                                row += '<tr class="row"><td align="left">条码</td><td>';
                                row += products[i].provider_barcode;
                                row += '</td></tr>';
                                
                                row += '<tr class="row"><td align="left">商品</td><td>';
                                row += products[i].product_name+'|'+products[i].color_name+'|'+products[i].size_name;
                                row += '</td></tr>';
                                
                                row += '<tr class="row"><td align="left">数量</td><td>';
                                row += '待入:'+products[i].num_dairu+'&nbsp;待出:'+products[i].num_daichu+'&nbsp;实际:'+products[i].num_shiji;
                                row += '</td></tr>';
                                
                                row += '</table>';
                                $('#dataDiv').append(row);
                            }
                            
                            $('#dataDiv').show();
                        }
                    });
                }
            });
            
            //重置按钮点击事件
            $("input[type=button][name=reset]").click(function() {
                reset();
                
                $('input[type=text][name=from_location_name]').attr('disabled', false).val('').focus();
                $('input[type=text][name=to_location_name]').attr('disabled', true).val('');
            });
            
        });
    </script>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>