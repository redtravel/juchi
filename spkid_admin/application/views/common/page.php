    <span id="totalRecords"><?php echo $filter['record_count'] ?></span> 条记录
    <span id="pageCurrent"><?php echo $filter['page'] ?></span> / <span id="totalPages"><?php echo $filter['page_count'] ?></span> 页
    每页 <input type='text' size='3' id='pageSize' value="<?php echo $filter['page_size'] ?>" onkeypress="return listTable.changePageSize(event)" /> 条记录
    <span id="page-link">
        <a href="javascript:listTable.gotoPageFirst()">第一页</a>
        <a href="javascript:listTable.gotoPagePrev()">上一页</a>
        <a href="javascript:listTable.gotoPageNext()">下一页</a>
        <a href="javascript:listTable.gotoPageLast()">最末页</a>
        跳至第
        <select id="gotoPage" onchange="listTable.gotoPage(this.value)">
            <?php echo create_pages($filter['page'], $filter['page_count']);?>
        </select>
        页
    </span>
