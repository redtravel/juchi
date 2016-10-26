<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'proc_log/index';
        function search(){
            listTable.filter['proc_name'] = $.trim($('input[name=proc_name]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">存储过程日志列表</span><span class="r"><a href="proc_log/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            进程名称&nbsp;<input name="proc_name" class="textbox require" id="proc_name" value="" type="text"/>

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
        <th width="100">ID</th>
<th width="100">进程名称</th>
<th width="100">执行结果</th>
<th width="100">LOG时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["proc_name"])&&isset($fields_source["proc_name"]["$row->proc_name"]))echo $fields_source["proc_name"]["$row->proc_name"] ;else echo $row->proc_name; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["content"])&&isset($fields_source["content"]["$row->content"]))echo $fields_source["content"]["$row->content"] ;else echo $row->content; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["t"])&&isset($fields_source["t"]["$row->t"]))echo $fields_source["t"]["$row->t"] ;else echo $row->t; ?></span></td>

                    <td>
                        <a href="proc_log/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
        
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


$('.editable').editable({ url: '/proc_log/editable', emptytext:'',
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