<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'team_work/index';
        function search(){
            // listTable.filter['team_type'] = $.trim($('input[name=team_type]').val());
listTable.filter['team_company'] = $.trim($('input[name=team_company]').val());
listTable.filter['team_name'] = $.trim($('input[name=team_name]').val());

            listTable.loadList();
        }
        //]]>
    </script>

    <div class="main">
        <div class="main_title">
            <span class="l">合作咨询列表</span><span class="r"><a href="team_work/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
<!--             合作属性&nbsp;<input name="team_type" class="textbox require" id="team_type" value="" type="text"/> -->
企业名称&nbsp;<input name="team_company" class="textbox require" id="team_company" value="" type="text"/>
联系人名字&nbsp;<input name="team_name" class="textbox require" id="team_name" value="" type="text"/>

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
<th width="100">合作属性</th>
<th width="100">企业名称</th>
<th width="100">联系人名字</th>
<th width="100">联系电话</th>
<th width="100">联系邮箱</th>
<th width="100">添加时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["team_id"])&&isset($fields_source["team_id"]["$row->team_id"]))echo $fields_source["team_id"]["$row->team_id"] ;else echo $row->team_id; ?></span></td> 
    <td><span><?php if(!empty($fields_source)&&isset($fields_source["team_type"])&&isset($fields_source["team_type"]["$row->team_type"]))echo $fields_source["team_type"]["$row->team_type"] ;else echo $team_type[$row->team_type]; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["team_company"])&&isset($fields_source["team_company"]["$row->team_company"]))echo $fields_source["team_company"]["$row->team_company"] ;else echo $row->team_company; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["team_name"])&&isset($fields_source["team_name"]["$row->team_name"]))echo $fields_source["team_name"]["$row->team_name"] ;else echo $row->team_name; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["team_tel"])&&isset($fields_source["team_tel"]["$row->team_tel"]))echo $fields_source["team_tel"]["$row->team_tel"] ;else echo $row->team_tel; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["team_email"])&&isset($fields_source["team_email"]["$row->team_email"]))echo $fields_source["team_email"]["$row->team_email"] ;else echo $row->team_email; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["team_date"])&&isset($fields_source["team_date"]["$row->team_date"]))echo $fields_source["team_date"]["$row->team_date"] ;else echo $row->team_date; ?></span></td>

                    <td>
                        <a href="team_work/edit/<?php print $row->team_id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="team_work/delete/<?php print $row->team_id; ?>" title="删除" onclick="do_delete(this)"></a>
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


$('.editable').editable({ url: '/team_work/editable', emptytext:'',
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