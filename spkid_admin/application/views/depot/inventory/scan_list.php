<?php include APPPATH.'views/common/rf_header.php'; ?>
    <div id="listDiv">
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 >
            <tr class="row">
                <th>盘点编号列表</th>
            </tr>
            <?php foreach($list as $row): ?>
            <tr class="row">
                <td>
                    <a href="/inventory/scanning/<?=$row->inventory_id; ?>">
                        <?=$row->inventory_sn; ?>
                        <?php if(!empty($row->inventory_note)): ?>
                        (<?=$row->inventory_note; ?>)
                        <?php endif; ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>