<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="300px">商品名称</th>
        <th>款号</th>
        <th>供应商货号</th>
        <th>品牌</th>
        <th>分类</th>
        <th>颜色尺码</th>
        <th width="120px;">操作</th>
    </tr>
    <tr class="row">
        <td colspan="7" style="text-align:left;">
            <?php print "【{$package->package_name} - ".$all_type[$package->package_type]."】"; ?>
            <?php print "{$package->package_goods_number} 件 {$package->package_amount} 元; "; ?>
            <?php foreach ($package->package_other_config as $key => $config): ?>
                <?php print "{$config[0]} 件 {$config[1]} 元; "; ?>
            <?php endforeach ?>
            <?php foreach ($package_area as $key => $area): ?>
                <?php print "【{$area->area_name}】 x {$area->min_number} 件 "; ?>
            <?php endforeach ?>
        </td>
    </tr>
    <tr class="row">
        <td colspan="7" style="text-align:left;">【<b>已选购商品</b>】</td>
    </tr>
    <?php foreach($order_product as $p): ?>
    <tr class="row pp">
        <td style="text-align:left;"><?php print "【{$p->area_name}】{$p->product_name}"; ?></td>
        <td><?php print $p->product_sn; ?></td>
        <td><?php print $p->provider_productcode; ?></td>
        <td><?php print $p->brand_name; ?></td>
        <td><?php print $p->category_name; ?></td>
        <td><?php print $p->color_name.' - '.$p->size_name; ?></td>
        <td>
            <a href="javascript:void(0)" onclick="javascript:remove_package_product(this)">移除</a>
            <?php print form_hidden('pp',"{$p->product_id}|{$p->color_id}|{$p->size_id}"); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr class="row package_op_tr">
        <td colspan="7">
            <?php print form_hidden('package_id',$package->package_id); ?>
            <input type="button" class="am-btn am-btn-secondary" value="提交" onclick="add_package();" />
        </td>
    </tr>
    <?php foreach ($package_area as $area): ?>
    <tr class="row">
        <td colspan="7" style="text-align:left;">【<b><?php print $area->area_name ?></b>】</td></tr></td>
    </tr>
    <?php foreach ($area->area_product as $p): ?>
    <tr class="row pp_<?php print $p->product_id ?>">
       <td style="text-align:left;"><?php print $p->product_name; ?></td>
       <td><?php print $p->product_sn; ?></td>
       <td><?php print $p->provider_productcode; ?></td>
       <td><?php print $p->brand_name; ?></td>
       <td><?php print $p->category_name; ?></td>
       <td style="text-align:left;">
            <select name="color_size">
            <?php foreach ($p->sub_list as $sub): ?>
                <option value="<?php print $sub->sub_id ?>"><?php print "{$sub->color_name} - {$sub->size_name} [{$sub->gl_num}] [{$sub->consign_num}]" ?></option>
            <?php endforeach ?>
            </select>
       </td>
       <td>
            <?php print form_hidden('area_name',$area->area_name); ?>
            <?php print form_hidden('area_id',$area->area_id); ?>
            <a href="javascript:void(0)" onclick ="javascript:add_package_product('<?php print $p->product_id ?>');">添加</a>
       </td>
    </tr>   
    <?php endforeach ?>
    <?php endforeach ?>
</table>