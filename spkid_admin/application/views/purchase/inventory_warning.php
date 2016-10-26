<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'inventory_warning/index';
	function search () {
                listTable.filter['provider_id'] = $.trim($(':input[name=provider_id]').val());
                listTable.filter['purchase_batch'] = $.trim($(':input[name=purchase_batch]').val());
                listTable.filter['product_sn'] = $.trim($(':input[name=product_sn]').val());
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
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">仓库管理 >> 库存预警 </span><span class="r">[ <a href="inventory_warning/view_warning_list">查看预警条目</a>]</span></div>
	<div class="search_row">
		<form name="search" action="javascript:search(); ">
		供应商：
                <select id="provider_id" name="provider_id" onchange="get_provider_batch(this);" >
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
                具体款号：<?php print form_input('product_sn'); ?>
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
        	</form>
	</div>
	<div id="listDiv" style="margin-top:5px;">
<?php endif; ?>
            <table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
                <tr class="row">
                    <th width="250px">名称</th>
                    <th width="150px">款号</th>
                    <th width="150px">色码</th>
                    <th width="150px">供应商货号</th>
                    <th>品牌</th>
                    <th>库存余量 / 预警值</th>
                    <th>预警ID</th>
                </tr>
                <?php if(!$list):?>
                <tr class="row">
                    <td colspan=7>无记录</td>
                </tr>
                <?php endif; ?>
                <?php foreach($list as $product): ?>
                <tr class="row p_<?php print $product->product_id?>">
                    <td><?php print $product->product_name; ?></td>
                    <td><?php print $product->product_sn; ?></td>
                    <td><?php print $product->color_name.'-'.$product->size_name; ?></td>
                    <td><?php print $product->provider_productcode; ?></td>
                    <td><?php print $product->brand_name; ?></td>
                    <td><?php print $product->consign_num.' / <font color="red">'.$product->min_number.'</font>'; ?></td>
                    <td><a href="inventory_warning/view_warning_list/<?php print $product->id; ?>" title="点击查看预警条目 <?php print $product->id; ?>"><?php print $product->id; ?></a></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7" class="bottomTd"> </td>
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
