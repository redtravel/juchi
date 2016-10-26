<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'dict/index';
        function search(){
            listTable.filter['id'] = $.trim($('input[name=id]').val());
listTable.filter['dict_id'] = $.trim($('select[name=dict_id]').val());
listTable.filter['field_id'] = $.trim($('input[name=field_id]').val());
listTable.filter['field_value1'] = $.trim($('input[name=field_value1]').val());
listTable.filter['field_value2'] = $.trim($('input[name=field_value2]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">数据字典列表</span><span class="r"><a href="dict/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            自增ID&nbsp;<input name="id" class="textbox require" id="id" value="" type="text"/>
类型&nbsp;<?php print form_dropdown("dict_id",array('0'=>'请选择')+$fields_source["dict_id"],array(""),"data-am-selected");?>
自定义ID&nbsp;<input name="field_id" class="textbox require" id="field_id" value="" type="text"/>
数值1&nbsp;<input name="field_value1" class="textbox require" id="field_value1" value="" type="text"/>
数值2&nbsp;<input name="field_value2" class="textbox require" id="field_value2" value="" type="text"/>

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
        <th width="100">自增ID</th>
<th width="100">类型</th>
<th width="100">自定义ID</th>
<th width="100">数值1</th>
<th width="100">数值2</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span data-type="select" data-pk="<?php print $row->id; ?>" data-name="dict_id" class="editable_select_dict_id" data-title="类型" data-value="<?php print $row->dict_id; ?>"><?php if(!empty($fields_source)&&isset($fields_source["dict_id"])&&isset($fields_source["dict_id"]["$row->dict_id"]))echo $fields_source["dict_id"]["$row->dict_id"] ;else echo $row->dict_id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="field_id" class="editable" data-title="自定义ID" data-value="<?php print $row->field_id; ?>"><?php if(!empty($fields_source)&&isset($fields_source["field_id"])&&isset($fields_source["field_id"]["$row->field_id"]))echo $fields_source["field_id"]["$row->field_id"] ;else echo $row->field_id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="field_value1" class="editable" data-title="数值1" data-value="<?php print $row->field_value1; ?>"><?php if(!empty($fields_source)&&isset($fields_source["field_value1"])&&isset($fields_source["field_value1"]["$row->field_value1"]))echo $fields_source["field_value1"]["$row->field_value1"] ;else echo $row->field_value1; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="field_value2" class="editable" data-title="数值2" data-value="<?php print $row->field_value2; ?>"><?php if(!empty($fields_source)&&isset($fields_source["field_value2"])&&isset($fields_source["field_value2"]["$row->field_value2"]))echo $fields_source["field_value2"]["$row->field_value2"] ;else echo $row->field_value2; ?></span></td>

                    <td>
                        <a href="dict/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="dict/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
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
var dict_id_ds = <?php echo $fields_source_data["dict_id"];?>
$('.editable_select_dict_id').editable({ 
    url: '/dict/editable',
    source: dict_id_ds,
    success: function(response, newValue) {
        if(!response.success) return response.msg;
        if( response.value != newValue  ) return '操作失败';
    }
}); 

$('.editable').editable({ url: '/dict/editable', emptytext:'',
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