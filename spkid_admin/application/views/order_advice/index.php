<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //	<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'order_advice';
        //]]>
    </script>
	<div class="main">
		<div class="main_title"><span class="l">所有意见</span></div>
		<div class="search_row">
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="5" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th>时间</th>
					<th>类型</th>
					<th>订单/退单</th>
					<th>内容</th>
					<th>操作人</th>
				</tr>
				<?php foreach($list as $advice): ?>
				<tr class="row">
					<td width="14%"><?php print $advice->advice_date; ?></td>
                    <td width="12%"><span style="display:inline-block;width:15px;height:15px;background-color:<?php print $advice->type_color ?>;">&nbsp;</span>
                    <?php print $advice->type_name; ?></td>
                    <td width="12%">
                        <?php if($advice->is_return == 1): ?>
                            <a href="/order/info/<?php print $advice->order_id; ?>"><?php print $advice->order_sn; ?></a>
                        <?php endif; ?>
                        <?php if($advice->is_return == 2): ?>
                            <a href="/order_return/edit/<?php print $advice->order_id; ?>"><?php print $advice->order_sn; ?></a>
                        <?php endif; ?>
                        <?php if($advice->is_return == 3): ?>
                            <?php print $advice->order_sn; ?>
                        <?php endif; ?>
                    </td>
                    <td width="50%"><?php print $advice->advice_content; ?></td>
                    <td width="12%"><?php print $advice->admin_name; ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="5" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
