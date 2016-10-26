<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="200px">名称</th>
        <th>课程编号</th>
        <th>本店售价</th>
        <th>人数</th>
        <th width="120px;">操作</th>
    </tr>
    <?php if(!$list):?>
    <tr class="row">
        <td colspan=12>无记录</td>
    </tr>
    <?php endif; ?>
    <?php foreach($list as $product): ?>
    <tr class="row p_<?php print $product->product_id?>">
        <td><?php print $product->product_name; ?></td>
        <td><?php print $product->product_sn; ?></td>
        <td><?php print $product->is_promote && $product->promote_start_date <= $this->time && $product->promote_end_date >= $this->time ? $product->promote_price : $product->shop_price; ?> / <?php print $product->shop_price; ?> / <?php print $product->market_price; ?></td>
        <td><?php print form_input('num',1,'style="width:40px;"') ?></td>
        <td>
            <a href="javascript:add_product(<?php print $product->product_id;?>);" title="添加">添加</a>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<div class="page">
    <?php include(APPPATH.'views/common/page.php') ?>
</div>
