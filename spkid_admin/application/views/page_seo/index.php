<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'page_seo/index';
        function search(){
            listTable.filter['code'] = $.trim($('input[name=code]').val());
listTable.filter['name'] = $.trim($('input[name=name]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">页面SEO列表</span><span class="r"><a href="page_seo/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            代码&nbsp;<input name="code" class="textbox require" id="code" value="" type="text"/>
名称&nbsp;<input name="name" class="textbox require" id="name" value="" type="text"/>

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
<th width="100">代码</th>
<th width="100">名称</th>
<th width="100">标题</th>
<th width="100">关键字</th>
<th width="100">描述</th>
<th width="100">添加人</th>
<th width="100">添加时间</th>
<th width="100">更新人</th>
<th width="100">更新时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php print $row->id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="code" class="editable" data-title="代码" data-value="<?php print $row->code; ?>"><?php print $row->code; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="name" class="editable" data-title="名称" data-value="<?php print $row->name; ?>"><?php print $row->name; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="title" data-type="textarea" class="editable" data-title="标题" data-value="<?php print $row->title; ?>"><?php print $row->title; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="keywords" data-type="textarea" class="editable" data-title="关键字" data-value="<?php print $row->keywords; ?>"><?php print $row->keywords; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="description" data-type="textarea" class="editable" data-title="描述" data-value="<?php print $row->description; ?>"><?php print $row->description; ?></span></td>
<td><span><?php print $row->admin_name; ?></span></td>
<td><span><?php print $row->add_time; ?></span></td>
<td><span><?php print $row->update_admin_name; ?></span></td>
<td><span><?php print $row->update_time; ?></span></td>

                    <td>
                        <!-- <a href="page_seo/edit/<?php print $row->id; ?>" title="编辑" class="edit"></a> -->
                        <a href="javascript:void(0);" title="编辑" class="edit"></a>
        
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
    $('.editable').editable({ url: '/page_seo/editable', emptytext:'',
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