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
<style type="text/css" rel="stylesheet">
    .up_edit{width:30px}
</style>
<div class="main">
    <div class="main_title"><span class="l"><?= $describe ?>复核详情记录</span></div>
    <div class="blank5"></div>
    <div class="search_row">
        <table style="width:60%">
            <tr>
                <td align="right"><?= $describe ?>单编号:</td>
                <td>&nbsp;&nbsp;<?= $doc_code ?></td>
                <td align="right">预计<?= $describe ?>数量:</td>
                <td>&nbsp;&nbsp;<?= $expected_number ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td align="right">箱子数量:</td>
                <td>
                    &nbsp;&nbsp;<span id="scan_box"><?= $box_count ?></span>
                </td>
                <td align="right">下架数量：</td>
                <td>&nbsp;&nbsp;<?= $all_scan_number ?></td>
                <td align="right">已复核数量</td>
                <td>&nbsp;&nbsp;<?= $all_check_number ?></td>
            </tr>
        </table>
    </div>
    <div class="blank5"></div>
    <div id="listDiv">
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan="10" class="topTd"> </td>
            </tr>
            <tr class="row">
                <th>条码</th>
                <th>货号</th>
                <th>商品名称</th>
                <th>品牌</th>
                <th>商品款号</th>
                <th>颜色【编码】</th>
                <th>尺码【编码】</th>
                <th>下架数量</th>
                <th>复核数量</th>
                <th width="192px">操作</th>
            </tr>
            <?php foreach ($box_details as $rows): ?>
                <tr>
                    <td colspan="9">
                        箱号：<?php print $rows->box_code; ?>
                        &nbsp;&nbsp;拣货件数：<?php print $rows->scan_number; ?>
                        &nbsp;&nbsp;拣货人：<?php print $rows->scan_name; ?>
                        &nbsp;&nbsp;拣货时间：<?php print $rows->scan_starttime; ?>
                        &nbsp;&nbsp;复核件数：<?php print $rows->shelve_number; ?>
                        &nbsp;&nbsp;复核人：<?php print $rows->shelve_name; ?>
                        &nbsp;&nbsp;复核时间：<?php print $rows->shelve_starttime; ?>
                    </td>
                    <td rowspan="<?= count($rows->details) + 1 ?>">
                        <?php if ($rows->shelve_number > 0): if ($doc_type == 1 && check_perm('cancel_box_check_depot_out')): ?>
                                <input type="button" class="am-btn am-btn-primary" onclick="cancel_box_check(<?php print $rows->box_id; ?>);" value="取消此箱复核" style="margin: 2px;"/>
                                <?
                            endif;
                        endif;
                        ?>
                    </td>
                </tr>

                <?php if (!empty($rows->details)):foreach ($rows->details as $row): $identical = $row->product_number != $row->box_finished_check_number; ?>
                        <tr class="row" <?php if ($identical) echo "style='background:yellow'"; ?>>
                            <td><?php print $row->provider_barcode; ?></td>
                            <td><?php print $row->provider_productcode; ?></td>
                            <td><?php print $row->product_name; ?></td>
                            <td><?php print $row->brand_name; ?></td>
                            <td><?php print $row->product_sn; ?></td>
                            <td><?php print $row->color_name; ?>【<?php print $row->color_id; ?>】</td>
                            <td><?php print $row->size_name; ?>【<?php print $row->size_id; ?>】</td>
                            <td><?php
                                if ($identical && check_perm('eidt_pick_val')) {
                                    print "<span class='up' box_sub_id='" . $row->box_sub_id . "'>" . $row->product_number . "</span>";
                                } else {
                                    print $row->product_number;
                                }
                                ?></td>
                            <td><?php print $row->box_finished_check_number; ?></td>
                        </tr>
                        <?php
                    endforeach;
                endif;
                ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="10" class="bottomTd"> </td>
            </tr>
        </table>
        <div class="blank5"></div>
    </div>
</div>
<script type="text/javascript">
                                    $(function() {
                                        $("span.up").click(function() {
                                            var box_sub_id = $(this).attr("box_sub_id");
                                            var val = $(this).html();
                                            if ($("#box_edit_" + box_sub_id)[0]) {
                                                $("#box_edit_" + box_sub_id).show();
                                                $(this).hide();
                                            }
                                            var msg = "<input class='up_edit' type='text' id='box_edit_" + box_sub_id + "' value='" + val + "' old_val='" + val + "' onblur='edit_val_blur(this)' onKeyDown='edit_val_key_down()'/>";
                                            $(this).after(msg);
                                            $(this).hide();
                                            $("#box_edit_" + box_sub_id).focus();
                                        });
                                    });
                                    function edit_val_blur(obj) {
                                        var val = $(obj).val();
                                        if (isNaN(val)) {
                                            $(obj).val($(obj).attr("old_val")).focus();
                                            return;
                                        }
                                        if (val == 0) {
                                            if (!confirm("设置为0将删除此次拣货记录，请确认？")) {
                                                $(obj).val($(obj).attr("old_val")).focus();
                                                return;
                                            }
                                        }
                                        var id = $(obj).attr("id");
                                        var sub_id = id.substr(9);
                                        if (val == $(obj).attr("old_val")) {
                                            $("span[box_sub_id='" + sub_id + "']").show();
                                            $(obj).remove();
                                            return;
                                        }
                                        $.post('/box_check/eidt_pick_val/', {sub_id: sub_id, val: val, is_ajax: 1, rnd: new Date().getTime()}, function(data) {
                                            data = jQuery.parseJSON(data);
                                            if (data.err == 1) {
                                                alert(data.msg);
                                                $("span[box_sub_id='" + sub_id + "']").show();
                                                $(obj).remove();
                                            } else {
                                                $("span[box_sub_id='" + sub_id + "']").html(val).show();
                                                $(obj).remove();
                                                window.location.href = window.location.href;
                                            }
                                        });
                                    }

                                    function edit_val_key_down() {
                                        if (window.event.which == 13) {
                                            edit_val_blur(window.event.target);
                                        }
                                    }
                                    function cancel_box_check(box_id) {
                                        if (!confirm('确定取消此箱子的<?= $describe ?>复核？'))
                                            return;
                                        $.ajax({
                                            url: '/box_check/cancel_box_check/' + box_id,
                                            data: {is_ajax: 1, rnd: new Date().getTime()},
                                            dataType: 'json',
                                            type: 'POST',
                                            success: function(result) {
                                                if (result.err == 0)
                                                {
                                                    location.href = location.href;
                                                } else {
                                                    alert(result.msg);
                                                }
                                            }
                                        });
                                    }
</script>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
