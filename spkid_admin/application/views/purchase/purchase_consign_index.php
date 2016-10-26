<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        $(function() {
            $('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, nextText: '', prevText: '', yearRange: '-100:+0'});
        });

        jQuery.download = function(url, data, method) {
            if (url && data) {
                var inputs = '';
                jQuery.each(data, function(key, val) {
                    inputs += '<input type="hidden" name="' + key + '" value="' + val + '" />';
                });
                jQuery('<form action="' + url + '" method="' + (method || 'post') + '">' + inputs + '</form>')
                        .appendTo('body').submit().remove();
            }
            ;
        };

        //<![CDATA[
        listTable.url = '/purchase_consign';
        function search() {
            if ($('select[name=provider_id]').val() == '') {
                alert('请选择供应商');
                return;
            }
            if ($('select[name=batch_id]').val() == undefined) {
                alert("该供应商没有对应批次，请先新建批次，再导入成本价后继续操作");
                return;
            }
            if ($('select[name=batch_id]').val() == '') {
                alert('请选择采购批次');
                return;
            }
            listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
            listTable.filter['batch_id'] = $.trim($('select[name=batch_id]').val());
            listTable.filter['start_time'] = $.trim($('input[type=text][name=start_time]').val());
            listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
            listTable.filter['end_time'] = $.trim($('input[type=text][name=end_time]').val());
            listTable.loadList();
        }

        function get_batch() {
            var provider_id = $.trim($('select[name=provider_id]').val());
            if (provider_id == '') {
                $("#batch_panel").html("");
                $("#start_time").val("");
                return;
            }
            var url = '/purchase_consign/get_provider_batch';
            var data = {
                provider_id: provider_id,
                rnd: new Date().getTime()
            };
            $.post(url, data, function(result) {
                result = jQuery.parseJSON(result);
                if (result.result == 0 || result.list.length < 1) {
                    $("#batch_panel").html("该供应商没有对应批次，请先新建批次，再导入成本价后继续操作");
                    $("#start_time").val("");
                } else {
                    var content = "<select name='batch_id' onchange='get_time();'>";
                    var l = result.list;
                    var autio_finish_t = false;
                    if (l.length == 1) {
                        content += "<option value='" + l[0].batch_id + "'>" + l[0].batch_code + "</option>";
                        autio_finish_t = true;
                    } else {
                        content += "<option value = '' selected>请选择批次号</option>";
                        for (i = 0; i < l.length; i++) {
                            content += "<option value='" + l[i].batch_id + "'>" + l[i].batch_code + "</option>";
                        }
                    }
                    content += "</select>";
                    $("#batch_panel").html(content);
                    if (autio_finish_t) {
                        get_time();
                    } else {
                        $("#start_time").val("");
                    }
                }
            });
        }

        function get_time() {
            var provider_id = $.trim($('select[name=provider_id]').val());
            if (provider_id == '') {
                return;
            }
            var batch_id = $.trim($('select[name=batch_id]').val());
            var url = '/purchase_consign/get_start_time';
            var data = {
                provider_id: provider_id,
                batch_id: batch_id,
                rnd: new Date().getTime()
            };
            $.post(url, data, function(result) {
                $("#start_time").val(result);
            });
        }

        function doexport() {
            $("#export").attr("disabled", "disabled");
            var provider_id = $("#export_provider_id").val();
            var batch_id = $("#export_batch_id").val();
            var start_time = $("#export_start_time").val();
            var end_time = $("#export_end_time").val();
            var param_str = {
                provider_id: provider_id,
                batch_id: batch_id,
                start_time: start_time,
                end_time: end_time,
                rnd: new Date().getTime()
            };
            $("#export_form :input[name^='num_']").each(function(i, dom) {
                var tmp_num = $(dom).val();
                if (tmp_num === null || (tmp_num.length === 0)) {
                    return true;
                }
                if (tmp_num == parseInt(tmp_num) && tmp_num > 0) {
                    param_str[dom.name] = parseInt(tmp_num);
                } else {
                    alert('无效的采购数量');
                    return false;
                }
            });

            $.download('/purchase_consign/export', param_str);
        }

        function docreate() {
            $("#create").attr("disabled", "disabled");
            $("#export_form").submit();
        }
        function show_detail_order(obj){
            if($("#detail_order").is(":hidden")){
                $(obj).html("隐藏订单详情");
                $("#detail_order").show();
            }else{
                 $(obj).html("展示订单详情");
                 $("#detail_order").hide();
            }
        }
        //]]>
    </script>
    <div class="main">
        <div class="main_title">
            <span class="l">代销采购</span>
            <?php if (check_perm('purchase_consign_history')): ?>
                <span class="r">
                    <a href="purchase_consign/history" class="add">代销采购操作记录</a>
                </span>
            <?php endif; ?>
        </div>

        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                <?php print form_dropdown('provider_id', get_pair($all_provider, 'provider_id', 'provider_code,provider_name', array('' => '供应商')), '', 'onchange=get_batch() data-am-selected="{searchBox:1, max_height:300}"'); ?>
                <span id="batch_panel" style="color:red"></span>
                时间段：<input type="text" name="start_time" id="start_time" readonly />-
                <input type="text" name="end_date" id="end_date" style="width:100px;" value="<?php print substr($end_time, 0, 10); ?>" />
                <input type="text" name="end_time" id="end_time" style="width:100px;" value="<?php print substr($end_time, 11); ?>" />
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div style="height:5px;"></div>
        <div class="search_row" style="text-align:left">
            <ul >
                <li>提示：</li>
                <li>1、开始进来第一个时间为空，第二个时间为当前时间。当选择供应商时，第一个时间会显示到上一次操作时间，且不可修改。如没有上次操作时间，默认时间是“<font color="red"> 2013-03-11 00:00:00</font>”；</li>
                <li>2、如果一个供应商存在多个品牌的需要采购的商品，则系统会根据各个商品品牌生成多张采购单。</li>
            </ul>
        </div>
        <div class="blank5"></div>

        <div id="listDiv">
        <?php endif; ?>

        <form id="export_form" name="export_form" action="/purchase_consign/create" method="post">
            <?php if (!empty($error)): ?>
                <span style="color:red"><?php echo $error; ?></span>
            <?php else: ?>
                <?php if (!empty($provider_name)): ?>
                    <?php if (!empty($list)): ?>
                        <table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
                            <tr>
                                <td colspan="9" class="topTd"> </td>
                            </tr>
                            <tr class="row">
                                <th>商品名称</th>
                                <th>商品款号</th>
                                <th>品牌</th>
                                <th>供应商货号</th>
                                <th>颜色</th>
                                <th>尺码</th>
                                <th>关于成本价</th>
                                <th>需求数量</th>
                                <th>建议数量</th>
                                <th>采购数量</th>
                            </tr>
                            <?php $cost = TRUE;
                            foreach ($list as $row): ?>
                                <tr class="row">
                                    <td align="center"><?php print $row->product_name; ?></td>
                                    <td align="center"><?php print $row->product_sn; ?></td>
                                    <td align="center"><?php print $row->brand_name; ?></td>
                                    <td align="center"><?php print $row->provider_productcode; ?></td>
                                    <td align="center"><?php print $row->color_name; ?></td>
                                    <td align="center"><?php print $row->size_name; ?></td>
                                    <td align="center"><?php if ($row->is_cost == 0) {
                                    echo '<span style="color:red">成本价没有录入</span>';
                                    $cost = FALSE;
                                } ?></td>
                                    <td align="center"><?php print $row->num; ?></td>
                                    <td align="center"><?php print $row->num; ?></td>
                                    <td align="center">
                                        <input type="text" name="num_<?php print $row->product_id; ?>_<?php print $row->color_id; ?>_<?php print $row->size_id; ?>" value="<?php print $row->num; ?>" />
                                    </td>
                                </tr>
            <?php endforeach; ?>
                            <tr>
                                <td colspan="9" class="bottomTd"> </td>
                            </tr>
                        </table>

                        <div class="blank5"></div>

                        <div class="search_row">
                            <span class="red bold">供应商：<?php print $provider_name; ?> 批次号：<?php print $batch_info->batch_code; ?></span>
                            <input type="hidden" id="export_provider_id" name="provider_id" value="<?php print $provider_id; ?>" />
                            <input type="hidden" id="export_batch_id" name="batch_id" value="<?php print $batch_info->batch_id; ?>" />
                            <input type="hidden" id="export_start_time" name="start_time" value="<?php print $start_time; ?>" />
                            <input type="hidden" id="export_end_time" name="end_time" value="<?php print $end_time; ?>" />
                            <!--<input type="button" id="export" value="导出采购单" class="am-btn am-btn-primary" onclick="doexport();"/>-->
                        <?php if (check_perm('purchase_consign_create') && $cost): ?><input type="button" id="create" value="生成采购单" class="am-btn am-btn-primary" onclick="docreate();"/><?php endif; ?>
                        <a href="javascript:void(0);" onclick="show_detail_order(this);">展示订单详情</a>
                        </div>

        <?php else: ?>
                        <span style="color:red">没有需要采购的商品</span>
        <?php endif; ?>
    <?php endif; if(!empty($order_list)):?>
                <table id="detail_order" class="dataTable" width="100%" cellpadding=0 cellspacing=0 style="display: none;">
                    <tr>
                        <td colspan="9" class="topTd"> </td>
                    </tr>
                    <tr class="row">
                        <th>商品名称</th>
                        <th>商品款号</th>
                        <th>品牌</th>
                        <th>供应商货号</th>
                        <th>颜色</th>
                        <th>尺码</th>
                        <th>关于成本价</th>
                        <th>需求数量</th>
                    </tr>
    <?php foreach ($order_list as $order_id => $rows): ?>
                        <tr class="row">
                            <td align="center" colspan="10" >
                                订单<span style="color: red"><?php print $rows[0]->order_sn; ?></span>,客审时间<span style="color: red"><?php print $rows[0]->confirm_date; ?></span>
                            </td>
                        </tr>
        <?php foreach ($rows as $row): ?>
                            <tr class="row">
                                <td align="center"><?php print $row->product_name; ?></td>
                                <td align="center"><?php print $row->product_sn; ?></td>
                                <td align="center"><?php print $row->brand_name; ?></td>
                                <td align="center"><?php print $row->provider_productcode; ?></td>
                                <td align="center"><?php print $row->color_name; ?></td>
                                <td align="center"><?php print $row->size_name; ?></td>
                                <td align="center"><?php if ($row->is_cost == 0) {
                            echo '<span style="color:red">成本价没有录入</span>';
                            $cost = FALSE;
                        } ?></td>
                                <td align="center">
            <?php print $row->num; ?>
                                    <input type="hidden" name="order_id[]" value="<?php print $order_id; ?>" />
                                    <input type="hidden" name="op_id[]" value="<?php print $row->op_id; ?>" />
                                    <input type="hidden" name="brand_id[]" value="<?php print $row->brand_id; ?>" />
                                    <input type="hidden" name="confirm_date[]" value="<?php print $row->confirm_date; ?>" />
                                    <input type="hidden" name="product_id[]" value="<?php print $row->product_id; ?>" />
                                    <input type="hidden" name="color_id[]" value="<?php print $row->color_id; ?>" />
                                    <input type="hidden" name="size_id[]" value="<?php print $row->size_id; ?>" />
                                    <input type="hidden" name="num[]" value="<?php print $row->num; ?>" />
                                </td>
                            </tr>
        <?php endforeach;
    endforeach; 
endif;?>
                    <tr>
                        <td colspan="9" class="bottomTd"> </td>
                    </tr>
                </table>

<?php endif; ?>
        </form>

<?php if ($full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>