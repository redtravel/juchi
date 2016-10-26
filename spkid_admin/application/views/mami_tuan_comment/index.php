<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'mami_tuan_comment/index';
        function search(){
            listTable.filter['wechat_id'] = $.trim($('input[name=wechat_id]').val());
listTable.filter['tuan_id'] = $.trim($('input[name=tuan_id]').val());
listTable.filter['comment_date'] = $.trim($('input[name=comment_date]').val());

            listTable.loadList();
        }
        //]]>
    </script>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=comment_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
    <div class="main">
        <div class="main_title">
            <span class="l">团购活动评论列表</span><span class="r"><a href="mami_tuan_comment/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            微信用户编号&nbsp;<input name="wechat_id" class="textbox require" id="wechat_id" value="" type="text"/>
活动编号&nbsp;<input name="tuan_id" class="textbox require" id="tuan_id" value="" type="text"/>
评论时间&nbsp;<input name="comment_date" class="textbox require" id="comment_date" value="" type="text"/>

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
        <th width="100">活动评论编号</th>
<th width="100">微信用户编号</th>
<th width="100">活动编号</th>
<th width="100">评论内容</th>
<th width="100">评论时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["comment_id"])&&isset($fields_source["comment_id"]["$row->comment_id"]))echo $fields_source["comment_id"]["$row->comment_id"] ;else echo $row->comment_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["wechat_id"])&&isset($fields_source["wechat_id"]["$row->wechat_id"]))echo $fields_source["wechat_id"]["$row->wechat_id"] ;else echo $row->wechat_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["tuan_id"])&&isset($fields_source["tuan_id"]["$row->tuan_id"]))echo $fields_source["tuan_id"]["$row->tuan_id"] ;else echo $row->tuan_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["comment_content"])&&isset($fields_source["comment_content"]["$row->comment_content"]))echo $fields_source["comment_content"]["$row->comment_content"] ;else echo $row->comment_content; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["comment_date"])&&isset($fields_source["comment_date"]["$row->comment_date"]))echo $fields_source["comment_date"]["$row->comment_date"] ;else echo $row->comment_date; ?></span></td>

                    <td>
                        <a href="mami_tuan_comment/edit/<?php print $row->comment_id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="mami_tuan_comment/delete/<?php print $row->comment_id; ?>" title="删除" onclick="do_delete(this)"></a>
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


$('.editable').editable({ url: '/mami_tuan_comment/editable', emptytext:'',
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