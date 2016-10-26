<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    
    <div class="main">
        <div class="main_title">
            <span class="l">代转买管理</span>
        </div>
        <div id="listDiv">
            <div>
                <table class="form" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="item_title" style="width: 40%;">商品形条码:</td>
                        <td class="item_input">
                            <input name="provider_barcode" class="textbox" id="provider_barcode" style="width: 280px;" onkeypress="if(event.keyCode===13) {return false;}"/>
                            <span class="r">
                                <input type="button" class="am-btn am-btn-primary" value="提交" name="mysubmit" id="mysubmit" />
                                <input type="button" class="am-btn am-btn-primary" value="重置" name="myreset" id="myreset" onclick="location.reload();"/>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                  <th>商品名称</th>
                  <th>商品条码</th>
                  <th>商品款号</th>
                  <th>供应商货号</th>
                  <th>批次</th>
                  <th>品牌</th>
                  <th>颜色</th>
                  <th>尺码</th>
                  <th>数量</th>
                </tr>
<!--                <tr class="row">
                    <td>假两件典雅连衣裙</td>
                    <td>MB11225001 0601 0002</td>
                    <td>MB11225001</td>
                    <td>ME2C1093</td>
                    <td>BT20130220002</td>
                    <td>麦肯邦尼</td>
                    <td>绿色</td>
                    <td>90</td>
                    <td>1</td>
                </tr>-->
            </table>
        </div>
    </div>
    
    <script type="text/javascript">
        $(document).ready(function(){
            var product_ary = [];
            $("#provider_barcode").val('').focus();

            function appendTable(product) {
                var sub_id = product.sub_id;
                if ($('#'+sub_id)[0]) {
                    $('#num_'+sub_id).html(parseInt($('#num_'+sub_id).html()) + 1);
                } else {
                    var row = '<tr id="'+sub_id+'" class="row">';
                        row += '<td>'+product.product_name+'</td>';
                        row += '<td>'+product.provider_barcode+'</td>';
                        row += '<td>'+product.product_sn+'</td>';
                        row += '<td>'+product.provider_productcode+'</td>';
                        row += '<td>'+product.batch_code+'</td>';
                        row += '<td>'+product.brand_name+'</td>';
                        row += '<td>'+product.color_name+'</td>';
                        row += '<td>'+product.size_name+'</td>';
                        row += '<td id=num_'+sub_id+'>1</td></tr>';
                    $('#dataTable').append(row);
                }
            }

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

            //商品条形码扫描
            $("#provider_barcode").keydown(function(event) {
                if(event.which === 13) {
                    var provider_barcode = $("#provider_barcode").val();
                    if(provider_barcode === null || provider_barcode === "") {
                        $("#provider_barcode").val('').focus();
                        alt_msg("商品条码为空！");
                        return;
                    } else {
                        $.post("/ctb/get_product", {provider_barcode:provider_barcode}, function(result) {
                            result = jQuery.parseJSON(result);
                            if (result.err === 1) {
                                $("#provider_barcode").val('').focus();
                                alert(result.msg);
                            } else {
                                $("#provider_barcode").val('').focus();
                                appendTable(result.product);
                                product_ary.push(result.product);
                            }
                        });
                    }
                }
            });

            //提交按钮事件
            $("#mysubmit").click(function() {
                if (product_ary.length === 0) {
                    alert("请添加代转买商品！");
                    location.reload();
                    return;
                }
            
                $.post("/ctb/ctb_products", {product_ary:product_ary}, function(result){
                    if (result.err === 1) {
                        alert(result.msg);
                    } else {
                        alert("代转买成功！");
                    }
                    location.reload();
                });
            });

         });
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
