<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="200px">名称</th>
        <th>课程编号</th>
        <th>价格</th>
        <th>人数</th>
        <th>小计</th>
        <th width="120px;">操作</th>
    </tr>
    <?php foreach($order_product as $product): ?>
    <tr class="row op_<?php print $product->op_id?>">
        <td style="text-align:left;">
        <?php if($product->discount_type==4) print '<span style="color:red;">赠品 </span>' ?>
        <?php print $product->product_name; ?>
        </td>
        <td><?php print $product->product_sn; ?></td>
        <td><?php print $product->shop_price; ?></td>
        <td><?php print $product->product_num . ($product->consign_num?" 虚库:{$product->consign_num}":""); ?></td>
        <td><?php print $product->total_price; ?></td>
        <td>
            <?php if (!empty($edit_product) && $product->discount_type!=4): ?>
                <a href="javascript:remove_product(<?php print $product->op_id;?>);" title="移除">移除</a>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td align="right" colspan="9" style="text-align:right; padding-right:20px;">
            <strong>合计</strong>&nbsp;&nbsp;课程总额：<strong><?php print $order->order_price; ?></strong>
            - 已付金额： <strong><?php print $order->paid_price ?></strong> =
            <?php if ($order->order_amount < 0): ?>
                待返金额：<strong><font color="red"><?php print $order->order_amount; ?></font></strong>
            <?php else: ?>
                待付金额： <strong><?php print $order->order_amount; ?></strong>
            <?php endif ?>
        </td>
    </tr>
</table>