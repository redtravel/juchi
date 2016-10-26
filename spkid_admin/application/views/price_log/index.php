<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'price_log/index';
        function search(){
            listTable.filter['product_id'] = $.trim($('input[name=product_id]').val());
listTable.filter['create_date'] = $.trim($('input[name=create_date]').val());

            listTable.loadList();
        }
        //]]>
    </script>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=create_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
    <div class="main">
        <div class="main_title">
            <span class="l">商品价格调整日志列表</span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            商品ID&nbsp;<input name="product_id" class="textbox require" id="product_id" value="" type="text"/>
调整时间&nbsp;<input name="create_date" class="textbox require" id="create_date" value="" type="text"/>

                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
        <div id="listDiv">
        <?php endif; ?>
        <table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
            <tr>
                <td colspan="9" class="topTd"> </td>
            </tr>
            <tr class="row">
        <th width="100">调价编号</th>
<th width="100">商品ID</th>
<th width="100">市场价</th>
<th width="100">本店价</th>
<th width="100">调整员</th>
<th width="100">调整时间</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["price_id"])&&isset($fields_source["price_id"]["$row->price_id"]))echo $fields_source["price_id"]["$row->price_id"] ;else echo $row->price_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["product_id"])&&isset($fields_source["product_id"]["$row->product_id"]))echo $fields_source["product_id"]["$row->product_id"] ;else echo $row->product_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["market_price"])&&isset($fields_source["market_price"]["$row->market_price"]))echo $fields_source["market_price"]["$row->market_price"] ;else echo $row->market_price; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["shop_price"])&&isset($fields_source["shop_price"]["$row->shop_price"]))echo $fields_source["shop_price"]["$row->shop_price"] ;else echo $row->shop_price; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["admin_name"])&&isset($fields_source["admin_name"]["$row->admin_name"]))echo $fields_source["admin_name"]["$row->admin_name"] ;else echo $row->admin_name; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["create_date"])&&isset($fields_source["create_date"]["$row->create_date"]))echo $fields_source["create_date"]["$row->create_date"] ;else echo $row->create_date; ?></span></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="9" class="bottomTd"> </td>
            </tr>
        </table>
        <div class="blank5"></div>
        <div class="page">
            <?php include(APPPATH . 'views/common/page.php') ?>
        </div>
<script>
// jquery editable 
function _editable(){


$('.editable').editable({ url: '/price_log/editable', emptytext:'',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
listTable.func = _editable; // 分页加载后调用的函数名
_editable();
</script>

        <?php if ($full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>