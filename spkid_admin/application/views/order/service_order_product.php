<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0;background-color:#fff">
    <tr class="row">
        <th width="100px">商品图片</th>
        <th width="200px">商品名称</th>
        <th width="100px">颜色尺码</th>
        <th width="80px">成交价</th>
        <th width="80px">数量</th>
        <th width="80px">小计</th>
    </tr>
    <?php foreach($order_product as $p): ?>
    <tr class="row ">
        <td>
        <img src="public/data/images/<?php print $p->img?>.85x85.jpg">
        </td>
        <td style="text-align:left;">
            <?php if($p->discount_type==4) print '<span style="color:red;">赠品 </span>' ?>
            <?php print "{$p->product_name} [ {$p->brand_name} ]"; ?><br/>
        款号：<?php print $p->product_sn; ?><br/>
        货号：<?php print $p->provider_productcode; ?>
        </td>
        <td><?php print "{$p->color_name} - {$p->size_name}"; ?></td>
        <td><?php print $p->product_price; ?></td>
        <td><?php print "{$p->product_num} {$p->unit_name}". ($p->consign_num?" 虚库:{$p->consign_num}":""); ?></td>
        <td><?php print $p->total_price; ?></td>
    </tr>
    <?php endforeach; ?>
</table>