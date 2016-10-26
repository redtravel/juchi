<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'inventory_query/index';
	function search () {
                listTable.filter['provider_id'] = $.trim($(':input[name=provider_id]').val());
                listTable.filter['brand_id'] = $.trim($(':input[name=brand_id]').val());
                listTable.filter['purchase_batch'] = $.trim($(':input[name=purchase_batch]').val());
                listTable.filter['sell_mode'] = $.trim($(':input[name=sell_mode]').val());
                listTable.filter['depot_id'] = $.trim($(':input[name=depot_id]').val());
                listTable.filter['product_sn'] = $.trim($(':input[name=product_sn]').val());
                listTable.filter['provider_barcode'] = $.trim($(':input[name=provider_barcode]').val());
                listTable.loadList();
	}
        
        function get_provider_batch(dom) {
		$("#purchase_batch option").remove();
                var url = '/inventory_query/get_inventory_batch/'+dom.value;
		$.get(url,function(result){
			$.each($.parseJSON(result), function(key, value) {
		        var htmlStr = '<option value="'+key+'">'+value+'</option>';
		        $("#purchase_batch").append(htmlStr);
		    });
		});
	}
        
        function get_provider_brand(dom) {
		$("#brand_id option").remove();
		var emptyStr = '<option value="">请选择</option>';
		$("#brand_id").append(emptyStr);
                var url = '/inventory_query/get_inventory_brand/'+dom.value;
		$.get(url,function(result){
			$.each($.parseJSON(result), function() {
		        var htmlStr = '<option value="'+this.brand_id+'">'+this.brand_name+'</option>';
		        $("#brand_id").append(htmlStr);
		    });
		});
	}
        
        function load_product_location(product_id, color_id, size_id)
        {
            $.ajax({
                    url: '/inventory_query/get_product_location',
                    data: {product_id:product_id, color_id:color_id, size_id:size_id, rnd : new Date().getTime()},
                    dataType: 'json',
                    type: 'POST',
                    success: function(result){
                        if(result.error == 0)
                        {
                            var content = result.content;
                            new $.dialog({ id:'thepanel',height:300,width:600,maxBtn:false, title:'商品所在仓储信息',iconTitle:false,cover:true,html: content}).ShowDialog();
                        }
                    }
                 });
        }
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">仓库管理 >> 库存余量查询 </span>
<?php if ( $inventory_export ) : ?>
<span class="r"><a href="inventory_query/export">导出库存</a></span>
<?php endif; ?>
</div>
	<div class="search_row">
		<form name="search" action="javascript:search(); ">
		供应商：
                <select id="provider_id" name="provider_id" onchange="get_provider_batch(this);get_provider_brand(this);" data-am-selected="{searchBox:1,maxHeight:300}">
                        <?php foreach ($provider_list as $key => $val): ?>
                            <option value="<?php print $key; ?>"><?php print $val; ?></option>
                        <?php endforeach; ?>
                </select>
                批次号：
                <select id="purchase_batch" name="purchase_batch">
                    <?php foreach ($batch_list as $key => $val): ?>
                        <option value="<?php print $key; ?>"><?php print $val; ?></option>
                    <?php endforeach; ?>
                </select>
                品牌：
                <select id="brand_id" name="brand_id">
                    <option value="">请选择</option>
                    <?php foreach ($brand_list as $val): ?>
                        <option value="<?php print $val->brand_id; ?>"><?php print $val->brand_name; ?></option>
                    <?php endforeach; ?>
                </select>
                销售方式：
                <select name="sell_mode" id="sell_mode">
                    <option value="">请选择</option>
                    <option value="0">实库销售</option>
                    <option value="1">虚库销售</option>
                </select>
                仓：
                <select id="depot_id" name="depot_id" >
                    <option value="">请选择</option>    
                    <?php foreach ($depot_list as $key => $val): ?>
                        <option value="<?php print $key; ?>"><?php print $val; ?></option>
                    <?php endforeach; ?>
                </select>
                具体款号：<?php print form_input('product_sn'); ?>
                条码：<?php print form_input('provider_barcode'); ?>
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
        	</form>
	</div>
	<div id="listDiv" style="margin-top:5px;">
<?php endif; ?>
            <table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
                <tr class="row">
                    <th width="150px">名称</th>
                    <th>款号</th>
                    <th>条形码</th>
                    <!--
                    <th>供应商货号</th>
                    -->
                    <th>品牌</th>
                   <!-- <th>风格</th>
                    <th>季节</th>
                    <th>性别</th>
                   -->
                    <th>后台分类</th>
                    <!--
                    <th>年月</th>
                    -->
                    <th width="130px">促销价 / 本店价 / 市场价</th>
                    <!--<th>[色-码]  [<font color='red'>可售</font>]  [<font color='green'>实库</font>]  [<font color='blue'>虚库</font>]</th>-->
                    <th>色码</th>
                    <th>批次</th>
                    <th><font color='red'>可售</font></th>
                    <th><font color='green'>实库</font></th>
                    <th><font color='blue'>虚库</font></th>
                </tr>
                <?php if(!$list):?>
                <tr class="row">
                    <td colspan=10>无记录</td>
                </tr>
                <?php endif; ?>
                <?php foreach($list as $product): ?>
                <tr class="row p_<?php print $product->product_id?>">
                    <td><?php print $product->product_name; ?></td>
                    <td><?php print $product->product_sn; ?></td>
                    <td><?php print $product->provider_barcode; ?></td>
                    <!--
                    <td><?php print $product->provider_productcode; ?></td>
                    -->
                    <td><?php print $product->brand_name; ?></td>
                    <!--<td><?php print $product->style_name; ?></td>
                    <td><?php print $product->season_name; ?></td>
                    <td><?php print $product->product_sex==1?'男':($product->product_sex==2?'女':'男女'); ?></td>
                    -->
                    <td><?php print $product->category_name; ?></td>
                    <!--
                    <td><?php print $product->product_year.'-'.$product->product_month; ?></td>
                    -->
                    <td><?php print $product->is_promote && $product->promote_start_date<=$this->time && $product->promote_end_date>=$this->time?$product->promote_price:$product->shop_price; ?> / <?php print $product->shop_price; ?> / <?php print $product->market_price; ?></td>
                    <td> <?php print $product->color_name.'-'.$product->size_name; ?> </td>
                    <td> <?php print $product->batch_codes; ?> </td>
                    <td><font color='red'><?php print $product->gl_num; ?></font></td>
                    <td><font color='green'><?php print $product->product_number; ?></font>
                    <br>
                    <a href="javascript:load_product_location(<?=$product->product_id?>, <?=$product->color_id?>, <?=$product->size_id?>);">查看仓储</a>
                    </td>
                    <td><font color='blue'>
		    <?php
		    if ($product->consign_num==-2) {
		        echo '不限量代销';
		    } elseif ($product->consign_num==-2){
		        echo '不代销';
		    } else {
		        echo $product->consign_num;
		    }
		    ?>
		    </font></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="10" class="bottomTd"> </td>
                </tr>
            </table>
            <div class="blank5"></div>
             <div class="page">
                <?php include(APPPATH.'views/common/page.php') ?>
            </div>
<?php if($full_page): ?>
        </div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
<?php endif; ?>
