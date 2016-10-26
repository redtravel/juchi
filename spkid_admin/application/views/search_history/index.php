<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'search_history/index';
        function search(){
            listTable.filter['id'] = $.trim($('input[name=id]').val());
listTable.filter['keyword'] = $.trim($('input[name=keyword]').val());
listTable.filter['count'] = $.trim($('input[name=count]').val());
listTable.filter['created'] = $.trim($('input[name=created]').val());

            listTable.loadList();
        }
        //]]>
    </script>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=created]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
    $('.add-dict').click(function(e){
        e.preventDefault();
        $.getJSON('/search_history/addict', {keyword:$(this).attr('href')}, function(data){
            $('#yyw-alert').find('.am-modal-bd').html(data.msg);
            $('#yyw-alert').modal();
        })
    })

});
//]]>
</script>
    <div class="main">
        <div class="main_title">
            <span class="l">搜索记录列表</span><span class="r"><a href="search_history/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            ID&nbsp;<input name="id" class="textbox require" id="id" value="" type="text"/>
关键字&nbsp;<input name="keyword" class="textbox require" id="keyword" value="" type="text"/>
数量&nbsp;<input name="count" class="textbox require" id="count" value="" type="text"/>
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
<th width="100">关键字</th>
<th width="100">数量</th>
<th width="100">创建时间</th>

                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">

        <td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
<td><span data-pk="<?php print $row->id; ?>" data-name="keyword" class="editable" data-title="关键字" data-value="<?php print $row->keyword; ?>"><?php if(!empty($fields_source)&&isset($fields_source["keyword"])&&isset($fields_source["keyword"][$row->keyword]))
$keyword = $fields_source["keyword"][$row->keyword];
else $keyword = $row->keyword;
echo $keyword?></span>

                        <a href="<?php echo $keyword?>" title="加入词库" class="add-dict" style="margin-left:10px;"><span class="am-icon-plus am-icon-sm"></span></a>
</td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["count"])&&isset($fields_source["count"]["$row->count"]))echo $fields_source["count"]["$row->count"] ;else echo $row->count; ?></span></td>
<td><span><?php if(!empty($fields_source)&&isset($fields_source["created"])&&isset($fields_source["created"]["$row->created"]))echo $fields_source["created"]["$row->created"] ;else echo $row->created; ?></span></td>

                    <td>
                        <a class="del" href="javascript:void(0);" rel="search_history/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
                                
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
<div class="am-modal am-modal-alert" tabindex="-1" id="yyw-alert">
  <div class="am-modal-dialog">
    <div class="am-modal-bd">
    </div>
    <div class="am-modal-footer">
      <span class="am-modal-btn">确定</span>
    </div>
  </div>
</div>
<script>
// jquery editable 
function _editable(){


$('.editable').editable({ url: '/search_history/editable', emptytext:'',
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
