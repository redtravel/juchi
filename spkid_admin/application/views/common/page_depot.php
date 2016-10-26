    <span id="depot_totalRecords"><?php echo $depot_filter['record_count'] ?></span> 条记录
    <span id="depot_pageCurrent"><?php echo $depot_filter['page'] ?></span> / <span id="depot_totalPages"><?php echo $depot_filter['page_count'] ?></span> 页
    每页 <input type='text' size='3' id='depot_pageSize' value="<?php echo $depot_filter['page_size'] ?>" onkeypress="return depot_changePageSize(event)" /> 条记录
    <span id="page-link">
        <a href="#" onclick="depot_gotoPageFirst();return false;">第一页</a>
        <a href="#" onclick="depot_gotoPagePrev();return false;">上一页</a>
        <a href="#" onclick="depot_gotoPageNext();return false;">下一页</a>
        <a href="#" onclick="depot_gotoPageLast();return false;">最末页</a>
        跳至第
        <select id="depot_gotoPage" onchange="depot_gotoPage(this.value);">
            <?php echo create_pages($depot_filter['page'], $depot_filter['page_count']);?>
        </select>
        页
    </span>
