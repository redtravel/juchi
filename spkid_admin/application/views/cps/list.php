<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'cps/index';
        function search(){
            listTable.filter['cps_sn'] = $.trim($('input[name=cps_sn]').val());
            listTable.filter['cps_name'] = $.trim($('input[name=cps_name]').val());
            listTable.loadList();
        }
        //]]>
    </script>
    <div class="main">
        <div class="main_title"><span class="l">CPS列表</span><span class="r"><a href="cps/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                名称：
                <input type="text" name="cps_name" id="cps_name" />
                SN：
                <input type="text" name="cps_sn" id="cps_sn" />
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
                <th width="42">ID</th>
                <th width="180">SN</th>
                <th width="180">名称</th>
                <th width="90">COOKIE有效期</th>
                <th width="100">状态</th>
                <th width="99">开始时间</th>
                <th width="99">结束时间</th>
                <th width="140">创建人</th>
                <th width="163">创建日期</th>
                <th width="77">操作</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <tr class="row">
                    <td align="center"><?php print $row->cps_id; ?></td>
                    <td><?php print $row->cps_sn; ?></td>
                    <td><?php print $row->cps_name; ?></td>
                    <td><?php print $row->cps_cookie_time; ?></td>
                    <td><?php print $row->cps_status == 0 ? '无效' : '有效'; ?></td>
                    <td><?php print $row->cps_start_time; ?></td>
                    <td><?php print $row->cps_shut_time; ?></td>
                    <td><?php echo empty($row->create_admin) ? '' : $all_admin[$row->create_admin]->admin_name; ?></td>
                    <td><?php print $row->create_date; ?></td>
                    <td>
                        <a href="cps/edit/<?php print $row->cps_id; ?>" title="编辑" class="edit"></a>
                        <a class="del" href="javascript:void(0);" rel="cps/delete/<?php print $row->cps_id; ?>" title="删除" onclick="do_delete(this)"></a>
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
        <?php if ($full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>