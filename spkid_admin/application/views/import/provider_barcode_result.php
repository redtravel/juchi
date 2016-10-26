<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">修改条形码导入结果</span> <span class="r"><a href="import" class="return r">返回</a></span></div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			    <thead>
				<tr class="row">
					<th colspan="7" class="topTd"> </th>
				</tr>
				<tr class="row">
					<th width="100px">商品款号</th>
					<th>颜色名称</th>
					<th>规格名称</th>
					<th>商品条形码(老)</th>
                                        <th>商品条形码(新)</th>
					<th width="250px">状态</th>
				</tr>
			    </thead>
			    <tbody>
				<?php foreach($error_records as $row): ?>
				<tr class="row">
					<td><?php print $row['product_sn']; ?></td>
					<td><?php print $row['color_name']; ?></td>
					<td><?php print $row['size_name']; ?></td>
                                        <td><?php print $row['provider_barcode_old']; ?></td>
                                        <td><?php print $row['provider_barcode_new']; ?></td>
					<td><font color="red">失败</font> <?php print $row['msg']; ?></td>
				</tr>
				<?php endforeach; ?>
				<?php foreach($success_records as $row): ?>
				<tr class="row">
					<td><?php print $row['product_sn']; ?></td>
					<td><?php print $row['color_name']; ?></td>
					<td><?php print $row['size_name']; ?></td>
                                        <td><?php print $row['provider_barcode_old']; ?></td>
                                        <td><?php print $row['provider_barcode_new']; ?></td>
					<td><font color="green">成功</font></td>
				</tr>
				<?php endforeach; ?>
			    </tbody>
			    <tfoot>
				<tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			     </tfoot>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>