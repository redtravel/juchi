<?php include APPPATH.'views/common/rf_header.php'; ?>
    <div id="listDiv">
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 >
            <tr class="row">
                <th>请选择仓库</th>
            </tr>
            <?php foreach($all_depot as $row): ?>
            <tr class="row">
                <td>
                    <a href="/return_onshelf/scan/<?=$row->depot_id; ?>/<?=$type; ?>"><?=$row->depot_name; ?></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>