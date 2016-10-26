<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'sms_log/index';
        function search(){
            listTable.filter['rec_id'] = $.trim($('input[name=rec_id]').val());
listTable.filter['sms_to'] = $.trim($('input[name=sms_to]').val());
listTable.filter['template_content'] = $.trim($('input[name=template_content]').val());
listTable.filter['create_date'] = $.trim($('input[name=create_date]').val());
listTable.filter['send_date'] = $.trim($('input[name=send_date]').val());

            listTable.loadList();
        }
        //]]>
    </script>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=create_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
    $('input[type=text][name=send_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
    <div class="main">
        <div class="main_title">
            <span class="l">短信日志列表</span><span class="r"><a href="sms_log/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            自增ID&nbsp;<input name="rec_id" class="textbox require" id="rec_id" value="" type="text"/>
收件人&nbsp;<input name="sms_to" class="textbox require" id="sms_to" value="" type="text"/>
邮件内容&nbsp;<input name="template_content" class="textbox require" id="template_content" value="" type="text"/>
创建日期&nbsp;<input name="create_date" class="textbox require" id="create_date" value="" type="text"/>
发送日期&nbsp;<input name="send_date" class="textbox require" id="send_date" value="" type="text"/>

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
<th width="100">收件人</th>
<th width="100">mail模板</th>
<th width="100">邮件内容</th>
<th width="100">创建日期</th>
<th width="100">发送日期</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["rec_id"])&&isset($fields_source["rec_id"]["$row->rec_id"]))echo $fields_source["rec_id"]["$row->rec_id"] ;else echo $row->rec_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["sms_to"])&&isset($fields_source["sms_to"]["$row->sms_to"]))echo $fields_source["sms_to"]["$row->sms_to"] ;else echo $row->sms_to; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["template_id"])&&isset($fields_source["template_id"]["$row->template_id"]))echo $fields_source["template_id"]["$row->template_id"] ;else echo $row->template_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["template_content"])&&isset($fields_source["template_content"]["$row->template_content"]))echo $fields_source["template_content"]["$row->template_content"] ;else echo $row->template_content; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["create_date"])&&isset($fields_source["create_date"]["$row->create_date"]))echo $fields_source["create_date"]["$row->create_date"] ;else echo $row->create_date; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["send_date"])&&isset($fields_source["send_date"]["$row->send_date"]))echo $fields_source["send_date"]["$row->send_date"] ;else echo $row->send_date; ?></span></td>

                    <td>
                        <a href="sms_log/edit/<?php print $row->rec_id; ?>" title="编辑" class="edit"></a>
                                <a class="del" href="javascript:void(0);" rel="sms_log/delete/<?php print $row->rec_id; ?>" title="删除" onclick="do_delete(this)"></a>
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


$('.editable').editable({ url: '/sms_log/editable', emptytext:'',
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