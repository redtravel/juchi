<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title" style="width:200px; text-align:center;">课程名称</th>
        <th class="item_title" style="width:100px; text-align:center;">课程编号</th>
        <th class="item_title" style="width:80px; text-align:center;">价格</th>
        <th class="item_title" style="width:80px; text-align:center;">原报名人数</th>
        <th class="item_title" style="width:80px; text-align:center;">现有人数</th>
        <th class="item_title" style="width:80px; text-align:center;">库存</th>
        <th class="item_title" style="width:100px; text-align:center;">原订单小计</th>
    </tr>
    <?php if(!$order_product):?>
    <tr class="row">
        <td colspan="7" >尚未添加商品</td>
    </tr>
    <?php endif; ?>
    <?php foreach($order_product as $p): ?>
    <tr class="row op_<?php print $p->op_id?>">
        <td style="text-align:left;">
            <?php print "<a href='{$front_url}/product-{$p->product_id}.html?is_preview=1' target='_blank'>{$p->product_name}</a><br/>".($p->track_sn ? $p->track_sn : $order->order_sn); ?>
        </td>
        <td><?php print $p->product_sn; ?></td>
        <td>
            <?php print "{$p->product_price}"; ?>
            <?php if ($perms['edit_price'] && $p->product_num && !$p->package_id && $p->discount_type!=4):?>
            <a class="edit" href="javascript:void(0)" onclick="javascript:edit_price(<?php print $p->op_id ?>);"></a>
            <?php endif;?>
            <?php print "<br/><strike>{$p->shop_price}</strike>"; ?>
        </td>
        <td><?php print "{$p->product_num} {$p->unit_name}<br/>".($p->consign_num?'虚 '.$p->consign_num:''); ?></td>
        <td><?php print "{$p->real_product_num} {$p->unit_name}<br/>".($p->real_consign_num?'虚 '.$p->real_consign_num:''); ?></td>
        <td><?php print "[ $p->gl_num ] [ $p->gl_consign_num ]" ?></td>
        <td><?php print $p->total_price; ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td align="right" colspan="7" style="text-align:right; padding-right:10px;">
            <strong>合计</strong> -- 课程总额：<strong><?php print $order->order_price; ?></strong>
            - 已付金额： <strong><?php print $order->paid_price ?></strong> =
            <?php if ($order->order_amount < 0): ?>
                待返金额：<strong><font color="red"><?php print $order->order_amount; ?></font></strong>
            <?php else: ?>
                待付金额： <strong><?php print $order->order_amount; ?></strong>
            <?php endif ?>
        </td>
    </tr>
</table>
