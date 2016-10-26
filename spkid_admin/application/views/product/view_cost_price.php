<div>
    <table class="dataTable" width="100%" >
	<tr>
	    <td width="50"> 批次号 </td>
	    <td width="50"> 供应商名称 </td>
	    <td width="50"> 合作方式 </td>
	    <td width="50"> 成本价 </td>
	    <td width="50"> 代销价格 </td>
	    <td width="50"> 浮动代销率 </td>
	    <td width="50"> 进项税率 </td>
	    <td width="50"> 销项税率 </td>
	</tr>
<?php foreach($list as $row): ?>
	<tr class="row">
	    <td algin="center"><?php print $row->batch_code; ?></td>
	    <td><?php print $row->provider_name; ?></td>
	    <td><?php print $row->cooperation_name; ?></td>
	    <td><?php print $row->cost_price; ?></td>
	    <td><?php print $row->consign_price; ?></td>
	    <td><?php print $row->consign_rate; ?></td>
	    <td><?php print $row->product_cess; ?></td>
	    <td><?php print $row->product_income_cess; ?></td>
	</tr>
<?php endforeach; ?>
    </table>
</div>