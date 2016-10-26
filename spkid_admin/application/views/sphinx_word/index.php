<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'sphinx_word/index';
        function search(){
            listTable.filter['id'] = $.trim($('input[name=id]').val());
listTable.filter['name'] = $.trim($('input[name=name]').val());
listTable.filter['level'] = $.trim($('input[name=level]').val());
listTable.filter['created'] = $.trim($('input[name=created]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">产品搜索分词列表</span><span class="r"><a href="sphinx_word/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            ID&nbsp;<input name="id" class="textbox require" id="id" value="" type="text"/>
分词&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>
优先级&nbsp;<input name="level" class="textbox require" id="level" value="" type="text"/>(大于0，数值小的高)
修改时间&nbsp;<input name="created" class="textbox require" id="created" value="" type="text"/>

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
<th width="100">分词</th>
<th width="100">优先级</th>
<th width="100">修改时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="name" class="editable" data-title="分词" data-value="<?php print $row->name; ?>"><?php if(!empty($fields_source)&&isset($fields_source["name"])&&isset($fields_source["name"]["$row->name"]))echo $fields_source["name"]["$row->name"] ;else echo $row->name; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="level" class="editable" data-title="优先级" data-value="<?php print $row->level; ?>"><?php if(!empty($fields_source)&&isset($fields_source["level"])&&isset($fields_source["level"]["$row->level"]))echo $fields_source["level"]["$row->level"] ;else echo $row->level; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["created"])&&isset($fields_source["created"]["$row->created"]))echo $fields_source["created"]["$row->created"] ;else echo $row->created; ?></span></td>

                    <td>
                        <a href="sphinx_word/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="sphinx_word/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
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


$('.editable').editable({ url: '/sphinx_word/editable', emptytext:'',
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