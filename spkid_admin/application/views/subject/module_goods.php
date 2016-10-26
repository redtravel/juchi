<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
    //<![CDATA[
    function selectAllGoods() {
        if($('input:checked[name=selectAllGoods]').length) {
            $('input[name=search_goods_id][type=checkbox]').attr('checked', true);
        } else {
            $('input[name=search_goods_id][type=checkbox]').attr('checked', false);
        }
    }
    
    function doAddSelectedGoods() {
        var searchGoodsAry = document.getElementsByName('search_goods_id');
        for (var i = 0; i < searchGoodsAry.length; i++) {
            if (searchGoodsAry[i].checked) {
                var goods_id = searchGoodsAry[i].value;
                // 避免重复添加
                if ($('table[id="goodsDataTable"] tr[name="row_'+goods_id+'"]').length === 0) {
                    var row = '<tr class="row" name="row_'+goods_id+'">';
                    row += '<td id="' + goods_id + '">' + goods_id + '</td>';
                    row += '<input type="hidden" name="added_goods_id[]" value="' + goods_id + '" />';
                    row += '<td>' + $('td[name=product_name_'+goods_id+']').text() + '</td>';
                    row += '<td>' + $('td[name=product_sn_'+goods_id+']').text() + '</td>';
                    row += '<td>' + $('td[name=provider_productcode_'+goods_id+']').text() + '</td>';
                    row += '<td>' + $('td[name=shop_price_'+goods_id+']').text() + '</td>';
                    row += '<td><input type="text" class="textbox" style="width:60px;text-align:center;" name="added_sort_order[]" value="0" /></td>';
                    row += '<td><input type="button" class="am-btn am-btn-secondary" value="删除" onclick="javascript:doDeleteSelectedGoods('+goods_id+');" /></td>';
                    row += '</tr>';
                    $('#goodsDataTable').append(row);
                }
            }
        }
    }
    
    function doDeleteSelectedGoods(goodsId) {
        $("#"+goodsId).parent().remove();
    }
    
    listTable.url = 'subject/search_goods';
    function doSearchGoods(){
        var container = $('form[name=searchForm]');
        listTable.filter['brand'] = $('select[name=brand]', container).val();
        listTable.filter['category_id'] = $('select[name=category]', container).val();
        listTable.filter['style_id'] = $('select[name=style]', container).val();
        listTable.filter['season_id'] = $('select[name=season]', container).val();
        listTable.filter['product_sex'] = $('select[name=product_sex]', container).val();
        
        listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
        listTable.filter['batch_code'] = $.trim($('input[type=text][name=batch_code]', container).val());
        
        listTable.filter['min_price'] = $.trim($('input[type=text][name=min_price]', container).val());
        listTable.filter['max_price'] = $.trim($('input[type=text][name=max_price]', container).val());
        listTable.filter['min_gl_num'] = $.trim($('input[type=text][name=min_gl_num]', container).val());
        listTable.filter['max_gl_num'] = $.trim($('input[type=text][name=max_gl_num]', container).val());

        listTable.loadList();
    }
    //]]>
</script>

<div class="main">
    <div class="main_title"><span class="l">活动专题管理 >> 模块编辑 >> 单品 </span><a href="subject/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    
    <div class="search_row">
        <?php print form_open_multipart('subject/proc_edit_module',array('name'=>'mainForm'));?>
            <input type="hidden" name="module_id" value="<?php print($row->module_id);?>" />
            <input type="hidden" name="module_type" value="<?php print($row->module_type);?>" />
            
            标题：<input type="text" class="ts" name="module_title" value="<?=$row->module_title?>" style="width:150px;" />
            位置：<select name="module_location">
                <option value="t" <?php if($row->module_location == 't'): ?> selected="true" <?php endif; ?>>头</option>
                <option value='l' <?php if($row->module_location == 'l'): ?> selected="true" <?php endif; ?>>左</option>
                <option value='r' <?php if($row->module_location == 'r'): ?> selected="true" <?php endif; ?>>右</option>
                <option value='b' <?php if($row->module_location == 'b'): ?> selected="true" <?php endif; ?>>底</option>
            </select>
            排序值：<input type="text" class="ts" name="sort_order" value="<?=$row->sort_order?>" style="width:70px;" />
            显示数量：<input type="text" class="ts" name="product_num" value="<?=$row->product_num?>" style="width:70px;" />（0为全部显示）
            <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'保存'));?>
            <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'重置'));?>
            
            <br/>已添加的商品：
            <table id="goodsDataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr class="row">
                    <th width="80px">ID</th>
                    <th width="300px">商品名称</th>
                    <th width="130px">商品款号</th>
                    <th width="130px">供应商货号</th>
                    <th width="130px">商品价格</th>
                    <th width="130px">排序值</th>
                    <th width="100px;">操作</th>
                </tr>
                
                <?php if(!empty($added_products)): ?>
                    <?php foreach($added_products as $p): ?>
                    <tr class="row" name="row_<?=$p->product_id; ?>">
                        <td id="<?=$p->product_id; ?>"><?=$p->product_id; ?></td>
                        <input type="hidden" name="added_goods_id[]" value="<?=$p->product_id; ?>" />
                        <td><?=$p->product_name; ?></td>
                        <td><?=$p->product_sn; ?></td>
                        <td><?=$p->provider_productcode; ?></td>
                        <td><?=$p->shop_price; ?></td>
                        <td><input type="text" class="textbox" style="width:60px;text-align:center;" name="added_sort_order[]" value="<?=$p->sort_order; ?>" /></td>
                        <td><input type="button" class="am-btn am-btn-secondary" value="删除" onclick="javascript:doDeleteSelectedGoods(<?=$p->product_id; ?>);" /></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </table>
        <?php print form_close();?>
            
        <form id="searchForm" name="searchForm" action="javascript:doSearchGoods();">
            <select name="brand">
                <option value="">品牌</option>
                <?php foreach($all_brand as $brand):?>
                <option value="<?=$brand->brand_id?>"><?=$brand->brand_name;?></option>
                <?php endforeach;?>
            </select>
            <select name="category">
                <option value="">分类</option>
                <?php foreach($all_category as $category):?>
                <option value="<?=$category->category_id?>"><?=$category->category_name;?></option>
                <?php endforeach;?>
            </select>
            <select name="style">
                <option value="">风格</option>
                <?php foreach($all_style as $style):?>
                <option value="<?=$style->style_id?>"><?=$style->style_name;?></option>
                <?php endforeach;?>
            </select>
            <select name="season">
                <option value="">季节</option>
                <?php foreach($all_season as $season):?>
                <option value="<?=$season->season_id?>"><?=$season->season_name;?></option>
                <?php endforeach;?>
            </select>
            <select name="product_sex">
                <option value="">性别</option>
                <option value="1">男款</option>
                <option value="2">女款</option>
                <option value="3">男女款</option>
            </select>
            商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
            批次号：<input type="text" class="ts" name="batch_code" value="" style="width:100px;" />
            <br/>
            价格范围:
            <input type="text" class="ts" name="min_price" value="" style="width:100px;" />
            - <input type="text" class="ts" name="max_price" value="" style="width:100px;" />
            实库库存范围:
            <input type="text" class="ts" name="min_gl_num" value="" style="width:100px;" />
            - <input type="text" class="ts" name="max_gl_num" value="" style="width:100px;" />
            
            <input type="submit" class="am-btn am-btn-secondary" value="搜索" />
        </form>
    </div>
    
    <div class="blank5"></div>
    <div id="listDiv"></div><!-- search result -->
    <div class="blank5"></div>
    
</div>
<?php include(APPPATH.'views/common/footer.php');?>