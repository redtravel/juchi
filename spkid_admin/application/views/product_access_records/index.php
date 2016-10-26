<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'product_access_records/index';
        function search(){
            listTable.filter['product_id'] = $.trim($('input[name=product_id]').val());
listTable.filter['type'] = $.trim($('input[name=type]').val());
listTable.filter['name'] = $.trim($('input[name=name]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">商品访问记录列表</span><span class="r"><!-- <a href="product_access_records/add" class="add">新增</a> --></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            商品ID&nbsp;<input name="product_id" class="textbox require" id="product_id" value="" type="text"/>
商品类型&nbsp;<input name="type" class="textbox require" id="type" value="" type="text"/>
商品编号/标题&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>

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
<th width="100">商品ID</th>
<th width="100">商品类型</th>
<th width="100">商品编号/标题</th>
<th width="100">年</th>
<th width="100">月</th>
<th width="100">日</th>
<th width="100">小时</th>
<th width="100">访问量</th>
<th width="100">添加时间</th>

                <!-- <th width="77">操作</th> -->
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["product_id"])&&isset($fields_source["product_id"]["$row->product_id"]))echo $fields_source["product_id"]["$row->product_id"] ;else echo $row->product_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["type"])&&isset($fields_source["type"]["$row->type"]))echo $fields_source["type"]["$row->type"] ;else echo $row->type; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["name"])&&isset($fields_source["name"]["$row->name"]))echo $fields_source["name"]["$row->name"] ;else echo $row->name; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["year"])&&isset($fields_source["year"]["$row->year"]))echo $fields_source["year"]["$row->year"] ;else echo $row->year; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["month"])&&isset($fields_source["month"]["$row->month"]))echo $fields_source["month"]["$row->month"] ;else echo $row->month; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["day"])&&isset($fields_source["day"]["$row->day"]))echo $fields_source["day"]["$row->day"] ;else echo $row->day; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["hours"])&&isset($fields_source["hours"]["$row->hours"]))echo $fields_source["hours"]["$row->hours"] ;else echo $row->hours; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["pv"])&&isset($fields_source["pv"]["$row->pv"]))echo $fields_source["pv"]["$row->pv"] ;else echo $row->pv; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["add_time"])&&isset($fields_source["add_time"]["$row->add_time"]))echo $fields_source["add_time"]["$row->add_time"] ;else echo $row->add_time; ?></span></td>

                    <!-- <td>
                        <a href="product_access_records/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
        
                    </td> -->
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


$('.editable').editable({ url: '/product_access_records/editable', emptytext:'',
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