<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">商品次要信息导入结果</span> <span class="r"><a href="import" class="return r">返回</a></span></div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
                                    <td colspan="11" class="topTd"> </td>
				</tr>
				<tr class="row">
                                    <th width="80px">商品款号</th>
                                    <th width="80px">供应商货号</th>
                                    <th width="50px">商品名称</th>
                                    <th width="30px">副标题</th>
                                    <th width="20px">重量</th>
                                    <th width="30px">计量单位</th>
                                    <th width="80px">三级分类编码</th>
                                    <th width="80px">供应商名称</th>
                                    <th width="30px">包装名称</th>
                                    <th width="30px">包装方式</th>
                                    <th width="50px">状态</th>
				</tr>
				<?php foreach($error_records as $row): ?>
				<tr class="row">
                                    <td><?php print $row['product_sn']; ?></td>
                                    <td><?php print $row['provider_productcode']; ?></td>
                                    <td><?php print $row['product_name']; ?></td>
                                    <td><?php print $row['subhead']; ?></td>
                                    <td><?php print $row['product_weight']; ?></td>
                                    <td><?php print $row['unit_name']; ?></td>
                                    <td><?php print $row['type_code']; ?></td>
                                    <td><?php print $row['provider_name']; ?></td>
                                    <td><?php print $row['package_name']; ?></td>
                                    <td><?php print $row['pack_method']; ?></td>
                                    <td><font color="red">失败</font> <?php print $row['msg']; ?></td>
				</tr>
				<?php endforeach; ?>
				<?php foreach($success_records as $row): ?>
				<tr class="row">
                                    <td><?php print $row['product_sn']; ?></td>
                                    <td><?php print $row['provider_productcode']; ?></td>
                                    <td><?php print $row['product_name']; ?></td>
                                    <td><?php print $row['subhead']; ?></td>
                                    <td><?php print $row['product_weight']; ?></td>
                                    <td><?php print $row['unit_name']; ?></td>
                                    <td><?php print $row['type_code']; ?></td>
                                    <td><?php print $row['provider_name']; ?></td>
                                    <td><?php print $row['package_name']; ?></td>
                                    <td><?php print $row['pack_method']; ?></td>
                                    <td><font color="green">成功</font></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>