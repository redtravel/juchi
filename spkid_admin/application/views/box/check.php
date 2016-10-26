<?php include(APPPATH . 'views/common/header.php'); ?>
<?php
$describe = "业务类型";
$expected_number = 0;
if ($doc_type == 1) {
    $describe = "出库";
    $expected_number = $biz_content->depot_out_number;
}
?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/alert_msg.js"></script>
<div class="main">
    <div class="main_title"><span class="l"><?= $describe ?>复核</span></div>
    <div class="blank5"></div>
    <div class="search_row">
        <table style="width:60%">
            <tr>
                <td align="right"><?= $describe ?>单编号:</td>
                <td>&nbsp;&nbsp;<?= $doc_code ?></td>
                <td align="right">预计<?= $describe ?>数量:</td>
                <td>&nbsp;&nbsp;<?= $expected_number ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td align="right">已扫描数量:</td>
                <td>&nbsp;&nbsp;<span id="scan_num"><?= $all_scan_number ?></span></td>
                <td align="right">箱子数量:</td>
                <td>&nbsp;&nbsp;<span id="scan_box"><?= $box_count ?></span></td>
                <td align="right">已复核数量:</td>
                <td>&nbsp;&nbsp;<span id="scan_num"><?= $all_check_number ?></span>
                    <input class="am-btn am-btn-primary" type="button" onclick="redirect('box_check/check_details/<?= $doc_type . '/' . $doc_code ?>');" value ="复核记录" />
                </td>
            </tr>
        </table>
    </div>
    <div class="blank5"></div>
    <div id="listDiv">
        <table class="dataTable" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan="10" class="topTd"> </td>
            </tr>
            <tr class="row">
                <th>名称</th>
                <th>商品款号</th>
                <th>货号</th>
                <th>品牌</th>
                <th>条码</th>
                <th>颜色</th>
                <th>尺码</th>
                <th>出库数量</th>
                <th>已复核数量</th>
                <th>扫描数量</th>
            </tr>
            <tbody id="dataTable"></tbody>
            <tr>
                <td colspan="9"><span id="box_sammly" style='font-size:20px;font-weight:bold;color:green'></span> </td>
                <td>本次复核合计：<span id="xiaoji" style="font-size:20px;color:red">0</span></td>
            </tr>
            <tr>
                <td colspan="10" class="bottomTd"> </td>
            </tr>
        </table>
        <?php if (($doc_type == 1 && check_perm('box_check'))): ?>
            <div>
                <table class="form" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="item_title" width="15%">箱号:</td>
                        <td class="item_input" width="35%">
                            <?php print form_input(array('name' => 'box_code', 'id' => 'box_code', 'class' => 'textbox', "size" => "30")); ?>
                            <span id="box_msg" style="color:red"></span>
                        </td>
                        <td class="item_title" width="15%">商品条码:</td>
                        <td class="item_input" width="35%">
                            <?php print form_input(array('name' => 'product_code', 'class' => 'textbox', "size" => "30")); ?>
                            <span id="product_msg" style="color:red"></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"><span id="p_name" style="font-size: 24px;font-family: '微软雅黑';"></span></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:center" >
                            <input class="am-btn am-btn-primary" type="button" name="mysubmit" value="完成复核" />
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input class="am-btn am-btn-primary" type="button" onclick="javascript:location.href = location.href;" value="取消本次复核" />
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
        <div class="blank5"></div>
    </div>
</div>
<script type="text/javascript">
                        $(function() {
                            //箱号扫描完后将焦点给到商品条码
                            $("input[type=text][name=box_code]").keydown(function(event) {
                                if (event.which == 13) {
                                    //检查箱子是否已存在
                                    var box_code = $(this).val();
                                    $("#box_msg").html("");
                                    $.post('/box_check/get_box_detail/<?= $doc_type ?>/<?= $doc_code ?>/' + box_code,
                                            function(data) {
                                                data = jQuery.parseJSON(data);
                                                if (data.err == 1) {
                                                    $("#box_msg").attr("style", "color:red").html(data.msg);
                                                    alt_msg(data.msg, 1);
                                                    return;
                                                } else {
                                                    var all_num = 0, check_num = 0;
                                                    for (var i = 0; i < data.list.length; i++) {
                                                        var item = data.list[i];
                                                        var style = " style='";
                                                        if (parseInt(item.product_number) === parseInt(item.box_finished_check_number)) {
                                                            style += "background:green";
                                                        } else {
                                                            style += "background:yellow";
                                                        }
                                                        style += "' ";
                                                        var tr = "<tr name='item_" + i + "' class='row' " + style + ">";
                                                        tr += "<td>" + item.product_name + "</td>";
                                                        tr += "<td>" + item.product_sn + "</td>";
                                                        tr += "<td>" + item.provider_productcode + "</td>";
                                                        tr += "<td>" + item.brand_name + "</td>";
                                                        tr += "<td>" + item.provider_barcode + "</td>";
                                                        tr += "<td>" + item.color_name + "</td>";
                                                        tr += "<td>" + item.size_name + "</td>";
                                                        tr += "<td>" + item.product_number + "</td>";
                                                        tr += "<td>" + item.box_finished_check_number + "</td>";
                                                        tr += "<td><input s_num='" + (parseInt(item.product_number) - parseInt(item.box_finished_check_number)) + "'";
                                                        tr += " provider_barcode='" + item.provider_barcode + "'";
                                                        tr += " box_sub_id='" + item.box_sub_id + "'";
                                                        tr += " type='text' style='width:30px;text-align: center;' flg='val' value='0' onblur='summly();' readonly='readonly'></td>";
                                                        tr += "</tr>";
                                                        $("#dataTable").append(tr);
                                                        all_num += parseInt(item.product_number);
                                                        check_num += parseInt(item.box_finished_check_number);
                                                    }
                                                    //
                                                    var tal = "本箱已下架数量：<span style='font-size:20px;color:red'>" + all_num + "</span>，已复核数量：<span style='font-size:20px;color:red'>" + check_num + "</span>";
                                                    $("#box_sammly").html(tal);
                                                    $("input[type=text][name=box_code]").attr("readonly", "readonly").css({'backgroundColor': '#bbb', 'backgroundImage': 'none'});
                                                    $("input[type=text][name=product_code]").focus();
                                                }
                                            });
                                }
                            });
                            //商品条码扫描后处理
                            $("input[type=text][name=product_code]").keydown(function(event) {
                                if (event.which == 13) {
                                    var product_code = $(this).val();
                                    $(this).val("").focus();
                                    if (product_code == "") {
                                        return;
                                    }
                                    product_code = product_code.trim();
                                    $("#product_msg").html("");
                                    if ($("input[provider_barcode='" + product_code + "']")[0]) {
                                        var scan_quantity = $("input[provider_barcode='" + product_code + "']");
                                        var scan_flag = false;
                                        for (var n = 0; n < scan_quantity.length; n++) {
                                            var c = $(scan_quantity[n]);
                                            if (isNaN(c.val())) {
                                                c.val("0");
                                            }
                                            var scan_num = c.val();
                                            var s_num = c.attr("s_num");
                                            if (parseInt(scan_num) === parseInt(s_num)) {
                                                continue;
                                            }
                                            //提示
                                            p_msg(c);
                                            var c_num = parseInt(scan_num) + 1;
                                            c.val(c_num);
                                            if (c_num == s_num) {
                                                c.parent().parent().attr("style", "background:green");
                                            }
                                            scan_flag = true;
                                            break;
                                        }
                                        if (!scan_flag) {
                                            var msg = '【' + product_code + '】此商品已经全部复核完成，不允许复核超过，请确认具体数量。';
                                            $("#product_msg").html(msg);
                                            alt_msg(msg, 2);
                                            return;
                                        }
                                        var xj = $("#xiaoji").html();
                                        $("#xiaoji").html(parseInt(xj) + 1);
                                    } else {
                                        var msg = '【' + product_code + '】未找到对应商品或该商品不属于此出库单';
                                        $("#product_msg").html(msg);
                                        alt_msg(msg, 2);
                                    }
                                }
                            });
                            //提交按钮事件
                            $("input[type=button][name=mysubmit]").click(function() {
                                $(this).attr("disabled", "disabled").attr("class", "button_gray");
                                var box_code = $('#box_code').val();
                                if (box_code == "") {
                                    alt_msg('请先扫描箱号', 1);
                                    return;
                                }
                                var provider_barcode_array = [];
                                var box_sub_id_array = [];
                                var number_array = [];
                                $('#dataTable input').each(function() {
                                    var provider_barcode = $(this).attr("provider_barcode");
                                    var box_sub_id = $(this).attr("box_sub_id");
                                    var number = $(this).val();

                                    provider_barcode_array.push(provider_barcode);
                                    box_sub_id_array.push(box_sub_id);
                                    number_array.push(number);
                                });
                                if (box_sub_id_array.length < 1) {
                                    alt_msg("请扫描商品", 2);
                                    return;
                                }
                                $.post("/box_check/do_check",
                                        {doc_code: '<?= $doc_code ?>',
                                            doc_type: '<?= $doc_type ?>',
                                            box_code: box_code,
                                            provider_barcode_array: provider_barcode_array,
                                            box_sub_id_array: box_sub_id_array,
                                            number_array: number_array
                                        },
                                function(data) {
                                    data = jQuery.parseJSON(data);
                                    if (data.err == 0) {
                                        alt_msg('<?= $describe ?>复核成功,点击确认后刷新此页面。', 0);
                                        location.href = location.href;
                                    } else {
                                        alt_msg('<?= $describe ?>复核失败，' + data.msg);
                                    }
                                });
                            });
                            $("input[type=text][name=box_code]").focus();
                        });
                        function alt_msg(msg, focus) {
                            var audio = '<audio src="public/style/audio/alert_msg.ogg"></audio>';
                            if ($('audio').length < 1)
                                $('body').append(audio);
                            $('audio:last').attr('autoplay', '');
                            setTimeout(function() {
                                $('audio').remove()
                            }, 600);
                            var t = setInterval(function() {
                                if ($('audio').length > 0) {
                                    alert_msg(msg, function() {
                                        if (focus == 1) {
                                            $("input[type=text][name=box_code]").focus();
                                        } else {
                                            $("input[type=text][name=product_code]").focus();
                                        }
                                    });
                                    clearInterval(t);
                                }
                            }, 16);
                        }
                        function summly() {
                            var sum = 0;
                            $("input[flg=val]").each(function() {
                                sum += parseInt($(this).val());
                            });
                            $("#xiaoji").html(sum);
                        }
                        function p_msg(c) {
                            var tr = c.parent().parent();
                            var p_n = tr.find("td:first").html();
                            var p_b = tr.find("td:eq(4)").html();
                            var p_c = tr.find("td:eq(5)").html();
                            var p_s = tr.find("td:eq(6)").html();
                            var t_msg = "商品[<span style='color:red'>" + p_n + "</span>]";
                            t_msg += "颜色[<span style='color:red'>" + p_c + "</span>]";
                            t_msg += "规格[<span style='color:red'>" + p_s + "</span>]";
                            t_msg += "条码[<span style='color:red'>" + p_b + "</span>]";
                            t_msg += "复核<span style='color:red'>1</span>件";
                            $("#p_name").html(t_msg);
                        }
</script>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
