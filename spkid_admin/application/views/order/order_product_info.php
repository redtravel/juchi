<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title" style="width:200px; text-align:center;">商品名称 [ 品牌 ]</th>
        <th class="item_title" style="width:100px; text-align:center;">供应商编码</th>
        <th class="item_title" style="width:100px; text-align:center;">商品款号/货号/条码</th>
        <th class="item_title" style="width:100px; text-align:center;">颜色-尺码</th>
        <th class="item_title" style="width:80px; text-align:center;">价格</th>
        <th class="item_title" style="width:80px; text-align:center;">原订单数量</th>
        <th class="item_title" style="width:80px; text-align:center;">现有数量</th>
        <th class="item_title" style="width:80px; text-align:center;">库存</th>
        <th class="item_title" style="width:80px; text-align:center;">原订单出库</th>
        <th class="item_title" style="width:80px; text-align:center;">拒收入库</th>
        <th class="item_title" style="width:100px; text-align:center;">原订单小计</th>
    </tr>
    <?php if(!$order_product):?>
    <tr class="row">
        <td colspan=11>尚未添加商品</td>
    </tr>
    <?php endif; ?>
    <?php foreach($order_product as $p): ?>
    <tr class="row op_<?php print $p->op_id?>">
        <td style="text-align:left;">
        <?php if($p->discount_type==4) print '<span style="color:red;">赠品 </span>' ?>
        <?php print ($p->package_id?'[礼包] ':'')."<a href='{$front_url}/product-{$p->product_id}.html?is_preview=1' target='_blank'>{$p->product_name}</a>[ {$p->brand_name} ]<br/>".($p->track_sn?$p->track_sn:$order->order_sn); ?>
        </td>
        <td><?=$p->provider_code?></td>
        <td><?php print "{$p->product_sn}/{$p->provider_productcode}<br/>/{$p->provider_barcode}"; ?></td>
        <td><?php print "{$p->color_name} - {$p->size_name}<br/>{$p->color_sn} - {$p->size_sn}" ?></td>
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
        <td><?php print implode('<br>',$p->op_depot) ?></td>
        <td><?php print implode('<br>',$p->deny_depot) ?></td>
        <td><?php print $p->total_price; ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td align="right" colspan="11" style="text-align:right; padding-right:10px;">
            <strong>合计</strong> -- 商品总额：<strong><?php print $order->order_price; ?></strong>
            + 运费：<strong><?php print $order->shipping_fee; ?></strong>
            - 已付金额： <strong><?php print $order->paid_price ?></strong> =
            <?php if ($order->order_amount < 0): ?>
                待返金额：<strong><font color="red"><?php print $order->order_amount; ?></font></strong>
            <?php else: ?>
                待付金额： <strong><?php print $order->order_amount; ?></strong>
            <?php endif ?>
        </td>
    </tr>
</table>
