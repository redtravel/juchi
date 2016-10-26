<?php if ($filter['goods_type']=='product'): ?>
<input type="hidden" name="depot_id" value="<?=$filter['depot_id']?>"/>
<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="200px">名称</th>
        <th>款号</th>
        <th>供应商货号</th>
        <th>品牌</th>
        <th>风格</th>
        <th>季节</th>
        <th>性别</th>
        <th>年月</th>
        <th>本店售价</th>
        <th>色码</th>
        <th>数量</th>
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
        <td><?php print $product->provider_productcode; ?></td>
        <td><?php print $product->brand_name; ?></td>
        <td><?php print $product->style_name; ?></td>
        <td><?php print $product->season_name; ?></td>
        <td><?php print $product->product_sex==1?'男':($product->product_sex==2?'女':'男女'); ?></td>
        <td><?php print $product->product_year.'-'.$product->product_month; ?></td>
        <td><?php print $product->is_promote && $product->promote_start_date<=$this->time && $product->promote_end_date>=$this->time?$product->promote_price:$product->shop_price; ?> / <?php print $product->shop_price; ?> / <?php print $product->market_price; ?></td>
        <td style="text-align:left;">
            <select name="color_size">
            <?php foreach ($product->sub_list as $sub): ?>
                <option value="<?php print "{$sub->color_id}|{$sub->size_id}" ?>"><?php print "{$sub->color_name} - {$sub->size_name} [{$sub->gl_num}] [{$sub->consign_num}]" ?></option>
            <?php endforeach ?>
            </select>
        </td>
        <td><?php print form_input('num',1,'style="width:40px;"') ?></td>
        <td>
            <a href="javascript:add_product(<?php print $product->product_id;?>);" title="添加">添加</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th width="150px">礼包名称</th>
        <th>礼包类型</th>
        <th>礼包商品数量</th>
        <th>礼包价</th>
        <th>礼包正常售价</th>
        <th>礼包市场价</th>
        <th>礼包状态</th>
        <th>有效期间</th>
        <th width="120px;">操作</th>
    </tr>
    <?php if(!$list):?>
    <tr class="row">
        <td colspan=9>无记录</td>
    </tr>
    <?php endif; ?>
    <?php foreach($list as $package): ?>
    <tr class="row pkg_<?php print $package->package_id?>">
        <td><?php print $package->package_name; ?></td>
        <td><?php print $all_type[$package->package_type]; ?></td>
        <td><?php print $package->package_goods_number; ?></td>
        <td><?php print $package->package_amount; ?></td>
        <td><?php print $package->own_price; ?></td>
        <td><?php print $package->market_price; ?></td>
        <td><?php print $all_status[$package->package_status]; ?></td>
        <td><?php print substr($package->start_time,0,10).'至'.substr($package->end_time,0,10); ?></td>
        <td>
            <a href="javascript:void(0);" onclick="javascript:load_package(<?php print $package->package_id;?>,0);" title="选购">选购</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif ?>
<div class="page">
    <?php include(APPPATH.'views/common/page.php') ?>
</div>
