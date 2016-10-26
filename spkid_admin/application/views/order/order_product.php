<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="200px">名称</th>
        <th>款号</th>
        <th>供应商货号</th>
        <th>价格</th>
        <th>数量</th>
        <th>小计</th>
        <th>颜色-尺码</th>
        <th width="120px;">操作</th>
    </tr>
    <?php foreach($order_product as $product): ?>
    <tr class="row op_<?php print $product->op_id?>">
        <td style="text-align:left;">
        <?php if($product->discount_type==4) print '<span style="color:red;">赠品 </span>' ?>
        <?php print $product->product_name; ?>
        </td>
        <td><?php print $product->product_sn; ?></td>
        <td><?php print $product->provider_productcode; ?></td>
        <td><?php print $product->shop_price; ?></td>
        <td><?php print $product->product_num . ($product->consign_num?" 虚库:{$product->consign_num}":""); ?></td>
        <td><?php print $product->total_price; ?></td>
        <td><?php print "{$product->color_name} - {$product->size_name}" ?></td>
        <td>
            <?php if (!empty($edit_product) && $product->discount_type!=4): ?>
                <a href="javascript:remove_product(<?php print $product->op_id;?>);" title="移除">移除</a>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php foreach ($order_package as $package): ?>
    <tr class="row">
        <td align="left" colspan="7" style="text-align:left;">
            【<?php print $package->package_name; ?>】
            实际购买价： <?php print $package->package_real_amount; ?>
        </td>
        <td>
            <?php if (!empty($edit_product)): ?>
                <a href="javascript:void(0);" onclick="remove_package(<?php print $package->extension_id; ?>)">移除</a>
            <?php endif ?>
        </td>
    </tr>
    <?php foreach ($package->product_list as $product): ?>
    <tr class="row">
        <td style="text-align:left">[礼包] <?php print $product->product_name; ?></td>
        <td> <?php print $product->product_sn; ?></td>
        <td><?php print $product->provider_productcode; ?></td>
        <td><?php print $product->shop_price; ?></td>
        <td><?php print $product->product_num . ($product->consign_num?"虚库:{$product->consign_num}":""); ?></td>
        <td><?php print $product->total_price; ?></td>
        <td><?php print "{$product->color_name} - {$product->size_name}" ?></td>
        <td>
            
        </td>
    </tr>
    <?php endforeach ?>
    <?php endforeach ?>
    <tr>
        <td align="right" colspan="9" style="text-align:right; padding-right:20px;">
            <strong>合计</strong>&nbsp;&nbsp;商品总额：<strong><?php print $order->order_price; ?></strong>
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