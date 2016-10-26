<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">商品售价导入结果</span> <span class="r"><a href="import" class="return r">返回</a></span></div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="4" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="100px">商品款号</th>
					<th>售价</th>
					<th>市场价</th>
					<th width="250px">状态</th>
				</tr>
				<?php foreach($error_records as $row): ?>
				<tr class="row">
					<td><?php print $row['product_sn']; ?></td>
                                        <td><?php print $row['shop_price']; ?></td>
					<td><?php print $row['market_price']; ?></td>
					<td><font color="red">失败</font> <?php print $row['msg']; ?></td>
				</tr>
				<?php endforeach; ?>
				<?php foreach($success_records as $row): ?>
				<tr class="row">
					<td><?php print $row['product_sn']; ?></td>
                                        <td><?php print $row['shop_price']; ?></td>
					<td><?php print $row['market_price']; ?></td>
					<td><font color="green">成功</font></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="4" class="bottomTd"></td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>