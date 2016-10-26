<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
<div class="main">
    <div class="main_title"><span class="l">报表管理 >> <td><?php print $report_type; ?>列表</span> </div>
    <div class="blank5"></div>
		<div id="listDiv">
			<table width="1170"  cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="4" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="10%">ID</th>
				  <th width="20%">报表编码</th>
				  <th width="55%">报表名称</th>
			      <th>操作</th>
				</tr>
				<?php $i = 0; foreach($list as $row): $i++; ?>
				<?php if (isset($perms[$row->action_code]) && $perms[$row->action_code] == 1): ?>
			    <tr class="row">
			    	<td><?php print $i; ?></td>
			    	<td><?php print $row->action_code; ?></td>
			    	<td><?php print $row->action_name; ?></td>
					<td><a href="report/<?php print $row->action_code; ?>" title="生成报表" class="edit"></a></td>
			    </tr>
			    <?php endif; ?>
				<?php endforeach; ?>
			    <tr>
					<td colspan="4" class="bottomTd"> </td>
				</tr>
			</table>
    <div class="blank5"></div>
</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
