<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/listtable.js"></script>
<div class="main">
    <div class="main_title">
        <span class="l">代销采购订单关联详情记录</span>
        <span class="r"><a href="purchase_consign/history" class="add">代销采购操作记录</a></span>
    </div>
    <div class="blank5"></div>
    <div id="listDiv">
        <table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan="5" class="topTd"> </td>
            </tr>
            <tr class="row">
                <th>序列</th>
                <th>批次号</th>
                <th>采购单编码</th>
                <th>供应商名称</th>
                <th>品牌名称</th>
                <th>订单号</th>
                <th>订单客审时间</th>
                <th>原始虚库</th>
                <th>商品名称</th>
                <th>颜色</th>
                <th>尺码</th>
                <th>采购数量</th>
                <!--<th>状态</th>-->               
                <th>创建人</th>
                <th>创建时间</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">
                    <td align="center"><?php print $row->id; ?></td>
                    <td align="center"><?php print $row->batch_code; ?></td>
                    <td align="center">
                        <?php if (!empty($row->purchase_id) && $row->purchase_id > 0): ?>
                            <a href="/purchase/edit_product/<?php print $row->purchase_id ?>" target="_blank"><?php print $row->purchase_code; ?></a>
                        <?php else: ?>
                            <?php print $row->purchase_code; ?>
                        <?php endif; ?>
                    </td>
                    <td align="center"><?php print $row->provider_name; ?></td>
                    <td align="center"><?php print $row->brand_name; ?></td>
                    <td align="center"><a href="/order/info/<?php print $row->order_id ?>" target="_blank"><?php print $row->order_sn; ?></a></td>
                    <td align="center"><?php print $row->confirm_date; ?></td>
                    <td align="center"><?php print $row->consign_mark; ?></td>
                    <td align="center"><?php print $row->product_name; ?></td>
                    <td align="center"><?php print $row->color_name; ?></td>
                    <td align="center"><?php print $row->size_name; ?></td>
                    <td align="center"><?php print $row->consign_num; ?></td>
                    <td align="center"><?php print $row->create_name; ?></td>
                    <td align="center"><?php print $row->create_date; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" class="bottomTd"> </td>
            </tr>
        </table>

        <div class="blank5"></div>
    </div>
</div>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>