<!-- 总计 <?php echo $filter['record_count'] ?> 个记录 分为 <?php echo $filter['page_count'] ?> 页  当前第 <?php echo $filter['page'] ?> 页
<a href="#" onclick="filter_result(0,1);return false;">第一页</a>
<a href="#" onclick="filter_result(0,<?php echo $filter['page_count'] ?>);return false;">最末页</a> -->
<a class="preBtn" onclick="<?php if ($filter['page'] > 1) { ?>filter_result(0,<?php echo $filter['page'] - 1 ?>);return false; <?php } ?>">上一页</a>
<?php
if ($filter['page_count'] > 3) {
    echo "<a " . ($filter['page'] == 1 ? ' class="sel" ' : ' onclick="filter_result(0,1);return false;" ') . ">1</a>";
    if ($filter['page'] > 2) {
        echo "...";
        if ($filter['page'] + 1 < $filter['page_count']) {
            for ($i = $filter['page'] - 1; $i < $filter['page'] + 2 && $i < $filter['page_count']; $i++) {
                echo "<a " . ($filter['page'] == $i ? ' class="sel" ' : ' onclick="filter_result(0,' . $i . ');return false;" ') . ">" . $i . "</a>";
            }
        } else {
            for ($i = $filter['page'] - 2; $i < $filter['page'] + 3 && $i < $filter['page_count']; $i++) {
                echo "<a " . ($filter['page'] == $i ? ' class="sel" ' : ' onclick="filter_result(0,' . $i . ');return false;" ') . ">" . $i . "</a>";
            }
        }
    } else {
        for ($i = 2; $i < 4 && $i < $filter['page_count']; $i++) {
            echo "<a " . ($filter['page'] == $i ? ' class="sel" ' : ' onclick="filter_result(0,' . $i . ');return false;" ') . ">" . $i . "</a>";
        }
    }
    if ($filter['page'] + 1 < $filter['page_count']) {
        echo "...";
    }
    //echo "<a " . ($filter['page'] == $filter['page_count'] ? ' class="sel" ' : ' onclick="filter_result(0,' . $filter['page_count'] . ');return false;" ') . ">" . $filter['page_count'] . "</a>";
    ?>
    <a <?php if ($filter['page_count'] == $filter['page']) { ?> class="sel" <?php } else { ?> onclick="filter_result(0,<?php echo $filter['page_count'] ?>);return false;" <?php } ?>><?php echo $filter['page_count'] ?></a>
    <?php
} else {
    for ($i = 1; $i <= $filter['page_count']; $i++) {
        ?>
        <a <?php if ($i == $filter['page']) { ?> class="sel" <?php } else { ?> onclick="filter_result(0,<?php echo $i ?>);return false;" <?php } ?>><?php echo $i ?></a>
    <?php
    }
}
?>
<a class="nextBtn" onclick="<?php if ($filter['page'] < $filter['page_count']) { ?>filter_result(0,<?php echo $filter['page'] + 1 ?>);return false; <?php } ?>">下一页</a>