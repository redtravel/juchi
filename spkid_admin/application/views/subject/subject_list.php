<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.filter.sort_by = 's.subject_id';
        listTable.filter.sort_order = 'DESC';
        listTable.url = 'subject/index';
        function search(){
            listTable.filter['subject_title'] = $('#subject_title').val();
            listTable.filter['start_date'] = $('input[name=start_date]').val();
            listTable.filter['end_date'] = $('input[name=end_date]').val();
            listTable.loadList();
        }
        
        $(function(){
            $(':input[name=start_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
            $(':input[name=end_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
        });
        //]]>
    </script>
    <div class="main">
        <div class="main_title"><span class="l">活动专题列表</span><span class="r"><a href="subject/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                活动标题：<input type="text" class="ts" id="subject_title" name="subject_title" value="" style="width:140px;height:23px;" />
                添加时间范围：<?php print form_input('start_date', '', 'class="textbox"');?> 
                至
                <?php print form_input('end_date', '', 'class="textbox"');?> 
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
        <div id="listDiv">
<?php endif; ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="5" class="topTd"> </td>
                </tr>
                <tr class="row">
                    <th width="50px">
                        <a href="javascript:listTable.sort('s.subject_id', 'DESC'); ">
                            ID<?php echo ($filter['sort_by'] == 's.subject_id') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="220px">活动标题</th>
                    <th width="100px">生成频道页</th>
                    <th width="130px">
                        <a href="javascript:listTable.sort('s.create_date', 'ASC'); ">
                            添加时间<?php echo ($filter['sort_by'] == 's.create_date') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="60px">添加人</th>
                    <th width="130px">
                        <a href="javascript:listTable.sort('s.gen_date', 'ASC'); ">
                            生成时间<?php echo ($filter['sort_by'] == 's.gen_date') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="60px">生成人</th>
                    <th width="250px;">操作</th>
                </tr>
                <?php foreach($list as $row): ?>
                <tr class="row">
                    <td><?php print $row->subject_id; ?></td>
                    <td><?php print $row->subject_title?></td>
                    <td><a target="_blank" href="<?=FRONT_HOST;?>/zhuanti/<?php print $row->page_file; ?>.html"><?=$row->page_file?>.html</a></td>
                    <td><?=$row->create_date?></td>
                    <td><?php echo empty($row->create_admin) ? '' : $admin_arr[$row->create_admin]->admin_name; ?></td>
                    <td><?=$row->gen_date?></td>
                    <td><?php echo empty($row->gen_admin) ? '' : $admin_arr[$row->gen_admin]->admin_name; ?></td>
                    <td>
                        <a href="subject/edit/<?php print $row->subject_id; ?>">编辑</a>
                        | <a href="subject/manage/<?php print $row->subject_id; ?>">
                            管理
                        </a>
                        | <a target="_blank" href="subject/preview/<?php print $row->subject_id; ?>">
                            预览
                        </a>
                        <?php if ($perm_delete): ?>
                           | <a href="javascript:void(0)" rel="subject/delete/<?php print $row->subject_id; ?>" onclick="do_delete(this)">删除</a>
                        <?php endif ?>
                        | <a href="subject/generate_file/<?php print $row->subject_id; ?>">
                            生成文件
                        </a>
                        | <a href="subject/remove_file/<?php print $row->subject_id; ?>">
                            移除文件
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="bottomTd"> </td>
                </tr>
            </table>
            <div class="blank5"></div>
            <div class="page">
                <?php include(APPPATH.'views/common/page.php') ?>
            </div>
<?php if($full_page): ?>
        </div>
    </div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>