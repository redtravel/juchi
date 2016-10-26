<span id="totalRecords"><?php echo $filter['record_count'] ?></span>条
<span id="pageCurrent"><?php echo $filter['page'] ?></span>/<span id="totalPages"><?php echo $filter['page_count'] ?></span>页
<span id="page-link">
    <a href="javascript:load_history(<?php echo $filter['issue_id'] ?>,0)">|&lt;&lt;</a>
    <a href="javascript:load_history(<?php echo $filter['issue_id'] ?>,<?php echo max($filter['page']-1,0) ?>)">上一页</a>
    第
    <select id="gotoPage" onchange="load_history(<?php echo $filter['issue_id'] ?>,$(this).val())">
        <?php echo create_pages($filter['page'], $filter['page_count']);?>
    </select>
    页
    <a href="javascript:load_history(<?php echo $filter['issue_id'] ?>,<?php echo $filter['page']+1 ?>)">下一页</a>
    <a href="javascript:load_history(<?php echo $filter['issue_id'] ?>,<?php echo $filter['page_count'] ?>)">&gt;&gt;|</a>
    
</span>