<?php include(APPPATH.'views/common/header.php'); ?>
<div class="main">
        <div class="main_title"><span class="l">订单管理 >> 订单来源列表</span> <span class="r"><a href="order_source/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="56">ID</th>
			      <th width="259">订单来源CODE</th>
			      <th width="366">订单来源名称</th>
			      <th width="86">是否使用</th>
				  <th width="246">创建日期</th>
				  <th width="157">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->source_id; ?></td>
					<td><?php print $row->source_code; ?></td>
					<td><?php print $row->source_name; ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" alt="" /></td>用样式显示  By Rock-->
                    <td><span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>" ></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    	<?php if($perms['order_source_edit'] == 1):?>
                    	<a href="order_source/edit/<?php print $row->source_id; ?>" title="编辑" class="edit"></a>
						<a href="order_source/delete/<?php print $row->source_id; ?>" title="删除" onclick="return confirm('确定删除？')" class="del"></a>
                    	<?php endif;?>
                    </td>
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
