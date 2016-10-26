<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'product_genre/index';
        function search(){
            listTable.filter['name'] = $.trim($('input[name=name]').val());
listTable.filter['code'] = $.trim($('input[name=code]').val());
listTable.filter['virtual'] = $.trim($('select[name=virtual]').val());
listTable.filter['delivery'] = $.trim($('select[name=delivery]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">商品大类列表</span><span class="r"><a href="product_genre/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            名称&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>
代码&nbsp;<input name="code" class="textbox require" id="code" value="" type="text"/>
虚拟产品&nbsp;<?php print form_dropdown("virtual",array('0'=>'请选择')+$fields_source["virtual"],array(""),"data-am-selected");?>
是否快递&nbsp;<?php print form_dropdown("delivery",array('0'=>'请选择')+$fields_source["delivery"],array(""),"data-am-selected");?>

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
        <th width="100">编号</th>
<th width="100">名称</th>
<th width="100">代码</th>
<th width="100">虚拟产品</th>
<th width="100">是否快递</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="name" class="editable" data-title="名称" data-value="<?php print $row->name; ?>"><?php if(!empty($fields_source)&&isset($fields_source["name"])&&isset($fields_source["name"]["$row->name"]))echo $fields_source["name"]["$row->name"] ;else echo $row->name; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="code" class="editable" data-title="代码" data-value="<?php print $row->code; ?>"><?php if(!empty($fields_source)&&isset($fields_source["code"])&&isset($fields_source["code"]["$row->code"]))echo $fields_source["code"]["$row->code"] ;else echo $row->code; ?></span></td>
<td><span data-type="select" data-pk="<?php print $row->id; ?>" data-name="virtual" class="editable_select_virtual" data-title="虚拟产品" data-value="<?php print $row->virtual; ?>"><?php if(!empty($fields_source)&&isset($fields_source["virtual"])&&isset($fields_source["virtual"]["$row->virtual"]))echo $fields_source["virtual"]["$row->virtual"] ;else echo $row->virtual; ?></span></td>
<td><span data-type="select" data-pk="<?php print $row->id; ?>" data-name="delivery" class="editable_select_delivery" data-title="是否快递" data-value="<?php print $row->delivery; ?>"><?php if(!empty($fields_source)&&isset($fields_source["delivery"])&&isset($fields_source["delivery"]["$row->delivery"]))echo $fields_source["delivery"]["$row->delivery"] ;else echo $row->delivery; ?></span></td>

                    <td>
                        <a href="product_genre/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="product_genre/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
                    </td>
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
var virtual_ds = <?php echo $fields_source_data["virtual"];?>
$('.editable_select_virtual').editable({ 
    url: '/product_genre/editable',
    source: virtual_ds,
    success: function(response, newValue) {
        if(!response.success) return response.msg;
        if( response.value != newValue  ) return '操作失败';
    }
}); var delivery_ds = <?php echo $fields_source_data["delivery"];?>
$('.editable_select_delivery').editable({ 
    url: '/product_genre/editable',
    source: delivery_ds,
    success: function(response, newValue) {
        if(!response.success) return response.msg;
        if( response.value != newValue  ) return '操作失败';
    }
}); 

$('.editable').editable({ url: '/product_genre/editable', emptytext:'',
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