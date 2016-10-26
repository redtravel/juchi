<?php include(APPPATH.'views/common/header.php'); ?>
<div class="main">
<div class="main_title"><span class="l">订单管理 >> 建议类型列表</span> <span class="r"><a href="order_advice_type/add" class="add">新增</a></span></div>
<div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="52">ID</th>
			      <th width="165">建议类型颜色</th>
			      <th width="258">建议类型名称</th>
			      <th width="236">建议类型CODE</th>
			      <th width="82">是否使用</th>
				  <th width="227">创建日期</th>
				  <th width="150">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->type_id; ?></td>
					<td><div style="height:15px; width:15px; background-color:<?php echo $row->type_color?>"></div></td>
					<td><?php print $row->type_name; ?></td>
					<td><?php print $row->type_code; ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" alt="" /></td>用样式显示 By Rock-->
                    <td><span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <?php if($perms['suggestion_edit'] == 1):?>
                    <a href="order_advice_type/edit/<?php print $row->type_id; ?>" title="编辑" class="edit"></a>
					<a href="order_advice_type/delete/<?php print $row->type_id; ?>" title="删除" onclick="return confirm('确定删除？')" class="del"></a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="8" class="bottomTd"> </td>
				</tr>
			</table>
<div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
