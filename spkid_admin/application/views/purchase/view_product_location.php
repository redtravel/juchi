<div>
    <table class="dataTable" width="100%" >
	<tr>
	    <td width="150"> 仓库名称 </td>
	    <td width="150"> 储位名称 </td>
        <td width="100"> 批次</td>
        <td width="100"> 库存 </td>
        <td width="100"> 生产批号 </td>
        <td width="100"> 有效期 </td>
	</tr>
<?php foreach($list as $row): ?>
	<tr class="row">
	    <td algin="center"><?php print $row->depot_name; ?></td>
	    <td><?php print $row->location_name; ?></td>
            <td><?php print $row->batch_code; ?></td>
            <td><?php print $row->num; ?></td>
            <td><?php print $row->production_batch; ?></td>
            <td><?php print ($row->expire_date == '0000-00-00' || $row->expire_date == '0000-00-00 00:00:00' || $row->expire_date == '')?'无':$row->expire_date; ?></td>

	</tr>
<?php endforeach; ?>
    </table>
</div>
