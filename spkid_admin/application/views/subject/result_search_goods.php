<table id="searchDataTable" class="dataTable" cellpadding=0 cellspacing=0>
    <tr class="row">
        <td colspan="5" style="text-align:right;">
            <input type="checkbox" name="selectAllGoods" onclick="javascript:selectAllGoods();" />
            全选
            <input type="button" class="am-btn am-btn-secondary" value="添加所选商品" onclick="javascript:doAddSelectedGoods();" />  
        </td>
    </tr>
    
    <tr class="row">
        <th width="80px">ID</th>
        <th width="350px">商品名称</th>
        <th width="160px">商品款号</th>
        <th width="160px">供应商货号</th>
        <th width="160px">商品价格</th>
    </tr>
    
    <?php foreach($list as $row): ?>
    <tr class="row">
        <td style="text-align: left;">
            <input type="checkbox" name="search_goods_id" value="<?=$row->product_id; ?>" />
            <?=$row->product_id; ?>
        </td>
        <td name="product_name_<?=$row->product_id; ?>"><?=$row->product_name; ?></td>
        <td name="product_sn_<?=$row->product_id; ?>"><?=$row->product_sn; ?></td>
        <td name="provider_productcode_<?=$row->product_id; ?>"><?=$row->provider_productcode; ?></td>
        <td name="shop_price_<?=$row->product_id; ?>"><?=$row->shop_price;?></td>
    </tr>
    <?php endforeach; ?>
    
</table>
<div class="blank5"></div>
<div class="page">
    <?php include(APPPATH.'views/common/page.php') ?>
</div>