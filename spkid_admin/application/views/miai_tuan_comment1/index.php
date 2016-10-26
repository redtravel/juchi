<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'miai_tuan_comment1/index';
        function search(){
            listTable.filter['wechat_nickname'] = $.trim($('input[name=wechat_nickname]').val());
listTable.filter['tuan_id'] = $.trim($('input[name=tuan_id]').val());
listTable.filter['wechat_date'] = $.trim($('input[name=wechat_date]').val());
listTable.filter['register_name'] = $.trim($('input[name=register_name]').val());
listTable.filter['register_mobile'] = $.trim($('input[name=register_mobile]').val());
listTable.filter['register_date'] = $.trim($('input[name=register_date]').val());

            listTable.loadList();
        }
        //]]>
    </script>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=wechat_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
    $('input[type=text][name=register_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
    <div class="main">
        <div class="main_title">
            <span class="l">微信用户信息列表</span><span class="r"><a href="miai_tuan_comment1/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            昵称&nbsp;<input name="wechat_nickname" class="textbox require" id="wechat_nickname" value="" type="text"/>
活动编号&nbsp;<input name="tuan_id" class="textbox require" id="tuan_id" value="" type="text"/>
授权时间&nbsp;<input name="wechat_date" class="textbox require" id="wechat_date" value="" type="text"/>
报名姓名&nbsp;<input name="register_name" class="textbox require" id="register_name" value="" type="text"/>
联系方式&nbsp;<input name="register_mobile" class="textbox require" id="register_mobile" value="" type="text"/>
报名日期&nbsp;<input name="register_date" class="textbox require" id="register_date" value="" type="text"/>

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
        <th width="100">微信用户编号</th>
<th width="100">昵称</th>
<th width="100">性别</th>
<th width="100">活动编号</th>
<th width="100">授权时间</th>
<th width="100">报名姓名</th>
<th width="100">联系方式</th>
<th width="100">购买数量</th>
<th width="100">报名日期</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["wechat_id"])&&isset($fields_source["wechat_id"]["$row->wechat_id"]))echo $fields_source["wechat_id"]["$row->wechat_id"] ;else echo $row->wechat_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["wechat_nickname"])&&isset($fields_source["wechat_nickname"]["$row->wechat_nickname"]))echo $fields_source["wechat_nickname"]["$row->wechat_nickname"] ;else echo $row->wechat_nickname; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["wechat_sex"])&&isset($fields_source["wechat_sex"]["$row->wechat_sex"]))echo $fields_source["wechat_sex"]["$row->wechat_sex"] ;else echo $row->wechat_sex; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["tuan_id"])&&isset($fields_source["tuan_id"]["$row->tuan_id"]))echo $fields_source["tuan_id"]["$row->tuan_id"] ;else echo $row->tuan_id; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["wechat_date"])&&isset($fields_source["wechat_date"]["$row->wechat_date"]))echo $fields_source["wechat_date"]["$row->wechat_date"] ;else echo $row->wechat_date; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["register_name"])&&isset($fields_source["register_name"]["$row->register_name"]))echo $fields_source["register_name"]["$row->register_name"] ;else echo $row->register_name; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["register_mobile"])&&isset($fields_source["register_mobile"]["$row->register_mobile"]))echo $fields_source["register_mobile"]["$row->register_mobile"] ;else echo $row->register_mobile; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["register_num"])&&isset($fields_source["register_num"]["$row->register_num"]))echo $fields_source["register_num"]["$row->register_num"] ;else echo $row->register_num; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["register_date"])&&isset($fields_source["register_date"]["$row->register_date"]))echo $fields_source["register_date"]["$row->register_date"] ;else echo $row->register_date; ?></span></td>

                    <td>
                        <a href="miai_tuan_comment1/edit/<?php print $row->wechat_id; ?>" title="编辑" class="edit"></a>
        
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


$('.editable').editable({ url: '/miai_tuan_comment1/editable', emptytext:'',
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