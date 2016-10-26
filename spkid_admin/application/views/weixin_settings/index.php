<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'weixin_settings/index';
        function search(){
            listTable.filter['key_code'] = $.trim($('input[name=key_code]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">微信转发配置列表</span><span class="r"><a href="weixin_settings/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            memcache key&nbsp;<input name="key_code" class="textbox require" id="key_code" value="" type="text"/>

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
        <th width="100">memcache key</th>
<th width="100">微信转发标题</th>
<th width="100">微信转发描述</th>
<th width="100">微信转发图片</th>
<th width="100">创建日期</th>
<th width="100">创建人</th>
<th width="100">更新日期</th>
<th width="100">更新人</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["key_code"])&&isset($fields_source["key_code"]["$row->key_code"]))echo $fields_source["key_code"]["$row->key_code"] ;else echo $row->key_code; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["title"])&&isset($fields_source["title"]["$row->title"]))echo $fields_source["title"]["$row->title"] ;else echo $row->title; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["describe"])&&isset($fields_source["describe"]["$row->describe"]))echo $fields_source["describe"]["$row->describe"] ;else echo $row->describe; ?></span></td>
<td><span><a href="<?php print "public/data/static/".$row->file_url;?>" target="_blank" title="点击查看原图"><img width="50" height="55" src="<?php print "public/data/static/".$row->file_url; ?>" alt="质量图片"></a></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["create_date"])&&isset($fields_source["create_date"]["$row->create_date"]))echo $fields_source["create_date"]["$row->create_date"] ;else echo $row->create_date; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["create_admin"])&&isset($fields_source["create_admin"]["$row->create_admin"]))echo $fields_source["create_admin"]["$row->create_admin"] ;else echo $row->create_admin; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["update_date"])&&isset($fields_source["update_date"]["$row->update_date"]))echo $fields_source["update_date"]["$row->update_date"] ;else echo $row->update_date; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["update_admin"])&&isset($fields_source["update_admin"]["$row->update_admin"]))echo $fields_source["update_admin"]["$row->update_admin"] ;else echo $row->update_admin; ?></span></td>

                    <td>
                        <a href="weixin_settings/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a>
                        <a class="del" href="javascript:void(0);" rel="weixin_settings/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
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


$('.editable').editable({ url: '/weixin_settings/editable', emptytext:'',
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