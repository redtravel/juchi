<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/clipboard.min.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'file_upload/index';
        function search(){
            listTable.filter['id'] = $.trim($('input[name=id]').val());
listTable.filter['name'] = $.trim($('input[name=name]').val());
listTable.filter['path'] = $.trim($('input[name=path]').val());
listTable.filter['type'] = $.trim($('input[name=type]').val());
listTable.filter['created'] = $.trim($('input[name=created]').val());

            listTable.loadList();
        }
    </script>
    <div class="main">
        <div class="main_title">
            <span class="l">文件上传列表</span><span class="r"><a href="file_upload/scan">扫描</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            ID&nbsp;<input name="id" class="textbox require" id="id" value="" type="text"/>
名称&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>
路径&nbsp;<input name="path" class="textbox require" id="path" value="" type="text"/>
类型&nbsp;<input name="type" class="textbox require" id="type" value="" type="text"/>
创建时间&nbsp;<input name="created" class="textbox require" id="created" value="" type="text"/>

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
<th width="100">名称</th>
<th width="100">路径</th>
<th width="100">类型</th>
<th width="100">创建时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="name" class="editable" data-title="名称" data-value="<?php print $row->name; ?>"><?php if(!empty($fields_source)&&isset($fields_source["name"])&&isset($fields_source["name"]["$row->name"]))
$name = $fields_source["name"][$row->name];
else $name = $row->name; echo $name?></span></td>
<td><span><?php
        if(!empty($fields_source) && isset($fields_source["path"]) && isset($fields_source["path"][$row->path]))
        $path = $fields_source["path"][$row->path];
        else
        $path = $row->path;
        echo $path ?>
    </span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["type"])&&isset($fields_source["type"]["$row->type"]))echo $fields_source["type"]["$row->type"] ;else echo $row->type; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["created"])&&isset($fields_source["created"]["$row->created"]))echo $fields_source["created"]["$row->created"] ;else echo $row->created; ?></span></td>

                    <td>        
                        <button class="copy"  data-am-popover="{content: '复制成功!'}" data-clipboard-text='<?php echo static_url($path)?>'>复制链接</button>
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
<script type="text/javascript">
$(function(){
    $('input[type=text][name=created]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
function _editable(){


$('.editable').editable({ url: '/file_upload/editable', emptytext:'',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
listTable.func = _editable; // 分页加载后调用的函数名
_editable();
var clipboard = new Clipboard('.copy');
clipboard.on('success', function(e) {
    /*
    $(e.trigger).popover({
    content: '复制成功!', 
        //trigger: 'hover'
});*/
setTimeout(function(){$(e.trigger).popover('close')},2000); 
})
//console.log(e.trigger);
//alert('复制成功!');


    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
    });
    });
</script>

        <?php if ($full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>
