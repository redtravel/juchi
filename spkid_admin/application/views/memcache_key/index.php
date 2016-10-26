<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'memcache_key/index';
        function search(){
            listTable.filter['key'] = $.trim($('input[name=key]').val());
listTable.filter['name'] = $.trim($('input[name=name]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">memcache_key列表</span><span class="r"><a href="memcache_key/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            Key&nbsp;<input name="key" class="textbox require" id="key" value="" type="text"/>
键名&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>

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
<th width="100">Key</th>
<th width="100">键名</th>
<th width="100">更新人</th>
<th width="100">更新时间</th>
<th width="100">最近更新人</th>
<th width="100">类</th>
<th width="100">函数</th>
<th width="100">文件位置</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php print $row->id; ?></span></td>
<td><span><?php print $row->key; ?></span></td>
<td><span><?php print $row->name; ?></span></td>
<td><span><?php print $row->admin_name; ?></span></td>
<td><span><?php print $row->update_atime; ?></span></td>
<td><span>  <?php 
                $content = unserialize($row->content);
                $admin_name = '';
                foreach ($all_admin as  $admin) {
                    if(in_array($admin->admin_id,$content)) $admin_name .= $admin->admin_name.',';
                }
                echo trim($admin_name,',');
            ?>
</span></td>
<td><span><?php print $row->class; ?></span></td>
<td><span><?php print $row->function; ?></span></td>
<td><span><?php print $row->file_path; ?></span></td>

                    <td>
                        <a href="memcache_key/key_update/<?php print $row->id; ?>" title="手动更新" class="priv"></a>
        
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
    $('.editable').editable({ url: '/memcache_key/editable', emptytext:'',
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