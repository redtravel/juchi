<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.url = 'order_api/search_product';
	function search () {
                listTable.filter['depot_id'] = $.trim($(':input[name=depot_id]').val());
                listTable.filter['goods_type'] = $.trim($(':input[name=goods_type]').val());
                listTable.filter['category_id'] = $.trim($(':input[name=category_id]').val());
                listTable.filter['brand_id']    = $.trim($(':input[name=brand_id]').val());
                listTable.filter['size_id']     = $.trim($(':input[name=size_id]').val());
                listTable.filter['color_group'] = $.trim($(':input[name=color_group]').val());
                listTable.filter['product_sn']  = $.trim($(':input[name=product_sn]').val());
                listTable.filter['provider_productcode']  = $.trim($(':input[name=provider_productcode]').val());
                listTable.filter['product_name'] = $.trim($(':input[name=product_name]').val());
                listTable.filter['package_name'] = $.trim($(':input[name=package_name]').val());
                listTable.filter['page'] = 1;
                $('div#listDiv').html('加载中，请稍候...');
                listTable.loadList();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">订单管理 >> <?php print $act=='add'?'新增订单':'编辑订单' ?> >> 选择商品 </span><span class="r"><a href="order/info/<?php print $order->order_id ?>" class="return">返回订单</a></span></div>
	<?php print form_hidden('act', $act); ?>
	<div id="product_list">
		<?php include 'order_product.php' ?>
	</div>
        <?php print form_hidden('order_id',$order->order_id); ?>
	<div class="search_row">
            <form name="search" action="javascript:search(); ">
            <?php if($order->source_id == 6): ?>
            <select name="depot_id" id="depot_id">
                <option value="9">展会借货仓库</option>
                <option value="11">展会借货仓库2</option>
            </select>
            <?php endif; ?>

            <select name="goods_type" id="goods_type">
                <option value="product">商品</option>
                <option value="package">礼包</option>
            </select>
        <!-- 分类 -->
		<?php print form_product_category('category_id', $all_category, 0, '', array('分类'));?>
		<!-- 品牌 -->
		<?php print form_dropdown('brand_id',array('品牌')+get_pair($all_brand,'brand_id','brand_name'));?>
                款号： <?php print form_input('product_sn'); ?>
                名称： <?php print form_input('product_name'); ?>
                供应商货号： <?php print form_input('provider_productcode'); ?>
                <!-- 颜色组 -->
                <?php print form_dropdown('color_group', array('颜色组')+get_pair($all_color_group,'group_id','group_name')); ?>
                <!-- 尺寸 -->
                <?php print form_dropdown('size_id', array('规格')+get_pair($all_size,'size_id','size_name')); ?>
                礼包名称：<?php print form_input('package_name'); ?>
        		<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
        	</form>
	</div>
	<div id="listDiv" style="margin-top:5px;">
	</div>
        <div style="text-align:center;">
                <?php print form_button('mysubmit',$act=='add'?'下一步':'提交','class="am-btn am-btn-primary" onclick="submit_add_product();"') ?>
                <?php print form_button('mycancel','取消','class="am-btn am-btn-primary" onclick="location.href=base_url+\'order/info/'.$order->order_id.'\';"'); ?>
        </div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
