<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <!-- 样式脚本开始 -->
    <style type="text/css">
        .search_array{background-color:white;padding:10px;border:1px solid #ccc;line-height:30px;clear:both;overflow:hidden;}
        .provider_brand{border:2px solid #E2BE00;height:36px;line-height:36px;margin:3px;padding:0 25px 0 10px;text-align:center;background-color:#FFE;cursor:pointer;float:left;white-space:nowrap;}
        .icon_zhengque{background:url(public/style/img/icon_zhenque.png) no-repeat right 7px;}
    </style>
    <script type="text/javascript">
        $(function() {
            $('.provider_brand').hover(function() {
                $(this).css({'borderColor': '#F90'});
            }, function() {
                $(this).removeAttr('style');
            });
            $('.provider_brand').click(function() {
                $('.provider_brand').removeClass('icon_zhengque');
                var provider_id = $(this).attr("provider_id");
                var batch_id = $(this).attr("batch_id");
                var start_time = $(this).attr("start_time");
                var end_time = $(this).attr("end_time");
                query_consign(provider_id, batch_id,start_time,end_time);
                $(this).addClass('icon_zhengque');
            });
        });

        function query_consign(provider_id, batch_id,start_time,end_time) {
            $("#listDiv").html("加载中...");
            $.post('purchase_consign/query_consign_list', 
            {provider_id: provider_id,
                batch_id: batch_id,
                start_time:start_time,
                end_time:end_time,
                rnd: new Date().getTime()},
            function(data) {
                data = jQuery.parseJSON(data);
                if (data.err) {
                    alert(data.msg);
                    return;
                }
                $("#listDiv").html(data.content);
            });
        }
    </script>
    <!-- 样式脚本结束 -->
    <!-- 样式结束 -->

    <div class="main">
        <div class="main_title">
            <span class="l">代销采购</span>
            <?php if (check_perm('purchase_consign_history')): ?>
                <span class="r">
                    <a href="purchase_consign/history" class="add">代销采购操作记录</a>
                </span>
            <?php endif; ?>
        </div>
        <!-- 筛选框开始 -->
        <div class="search_array">
            <span>
                <?php if (count($list) < 1): ?>
                    <span style="font:bolder;color: red;">没有需要采购的商品！</span>
                <?php else: ?>
                    <?php foreach ($list as $item): ?>
                        <div class="provider_brand" provider_id="<?= $item->provider_id ?>" batch_id="<?= $item->batch_id ?>" 
                             start_time="<?= $item->end_time ?>" end_time="<?= $end_time ?>">
                            <?= $item->provider_name ?>-<?= $item->batch_code ?>-<?= $item->brand_name ?>
                            【<span style="font:bolder;color: red;"><?= $item->num ?></span>】
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </span>
        </div>
        <!-- 筛选框结束 -->
        <!-- 5px高 -->
        <div style="height:5px;"></div>
        <!-- 提示开始 -->
        <div class="search_row" style="text-align:left">
            <ul >
                <li>提示：</li>
                <li>1、开始进来展示自“<font color="red"> 2013-04-20 00:00:00</font>”以来产生的虚库销售商品数量，展示方式根据供应商及对应的批次展示。</li>
                <li>2、点击对应选项，拉取详情记录。</li>
                <li>3、点击“<font color="red">生成采购单</font>”按钮创建采购单。</li>
            </ul>
        </div>
        <div class="blank5"></div>
        <form id="export_form" name="export_form" action="/purchase_consign/autio_create" method="post">
            <div id="listDiv">
            <?php else: ?>
                <?php if (!empty($order_list)): ?>
                    <table id="detail_order" class="dataTable" width="100%" cellpadding=0 cellspacing=0 >
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
                        <?php $total_num = 0; foreach ($order_list as $order_id => $rows):?>
                            <tr class="row">
                                <td align="center" colspan="10" >
                                    订单<span style="color: red"><?php print $rows[0]->order_sn; ?></span>,客审时间<span style="color: red"><?php print $rows[0]->confirm_date; ?></span>
                                </td>
                            </tr
                            <?php foreach ($rows as $row): $total_num +=$row->num?>
                                <tr class="row">
                                    <td align="center"><?php print $row->product_name; ?></td>
                                    <td align="center"><?php print $row->product_sn; ?></td>
                                    <td align="center"><?php print $row->brand_name; ?></td>
                                    <td align="center"><?php print $row->provider_productcode; ?></td>
                                    <td align="center"><?php print $row->color_name; ?></td>
                                    <td align="center"><?php print $row->size_name; ?></td>
                                    <td align="center"><?php
                                        if ($row->is_cost == 0) {
                                            echo '<span style="color:red">成本价没有录入</span>';
                                            $cost = FALSE;
                                        }
                                        ?></td>
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
                                <?php
                            endforeach;
                        endforeach;
                    endif;
                    ?>
                    <tr class="row">
                        <td colspan="7"></td>
                        <td>合计：<?=$total_num?></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="bottomTd"> </td>
                    </tr>
                </table>
                <input type="hidden" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>" />
                <input type="hidden" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>" />
                <input type="hidden" name="provider_id" value="<?php if (!empty($provider_id)) echo $provider_id; ?>"/>
                <input type="hidden" name="batch_id" value="<?php if (!empty($batch_id)) echo $batch_id; ?>"/>
                <input value="生成采购单" type="submit" class="am-btn am-btn-primary"/>
            <?php endif; ?>
            <?php if ($full_page): ?>
            </div>
        </form>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>