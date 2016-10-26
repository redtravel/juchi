<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order_course.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.url = 'order_course_api/search_product';
	function search () {
                listTable.filter['category_id'] = $.trim($(':input[name=category_id]').val());
                listTable.filter['product_sn']  = $.trim($(':input[name=product_sn]').val());
                listTable.filter['product_name'] = $.trim($(':input[name=product_name]').val());
                listTable.filter['page'] = 1;
                $('div#listDiv').html('加载中，请稍候...');
                listTable.loadList();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">课程订单管理 >> <?php print $act=='add'?'新增课程订单':'编辑课程订单' ?> >> 选择课程 </span><span class="r"><a href="order_course/info/<?php print $order->order_id ?>" class="return">返回订单</a></span></div>
	<?php print form_hidden('act', $act); ?>
	<div id="product_list">
		<?php include 'order_product.php' ?>
	</div>
        <?php print form_hidden('order_id',$order->order_id); ?>
	<div class="search_row">
		<form name="search" action="javascript:search(); ">
            <!-- 分类 -->
    		<?php print form_product_category('category_id', $all_category, 0, '', array('分类'));?>
            课程编号： <?php print form_input('product_sn'); ?>
            课程名称： <?php print form_input('product_name'); ?>
    		<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
        </form>
	</div>
	<div id="listDiv" style="margin-top:5px;">
	</div>
        <div style="text-align:center;">
            <?php print form_button('mysubmit',$act=='add'?'下一步':'提交','class="am-btn am-btn-primary" onclick="submit_add_product();"') ?>
            <?php print form_button('mycancel','取消','class="am-btn am-btn-primary" onclick="location.href=base_url+\'order_course/info/'.$order->order_id.'\';"'); ?>
        </div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
