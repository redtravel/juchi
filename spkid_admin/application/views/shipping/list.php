<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>

	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'friend/index';
		function search(){
			listTable.filter['link_name'] = $.trim($('input[name=link_name]').val());
			listTable.filter['link_url'] = $.trim($('input[name=link_url]').val());
			listTable.loadList();
		}
		//]]>
	</script>
<div class="main">
		<div class="main_title"><span class="l">订单管理 >> 配送方式列表</span> <span class="r"><a href="shipping/add_shipping_info" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="124">编码</th>
			      <th width="135">名称</th>
			      <th width="492">描述</th>
				  <th width="45">启用</th>
				  <th width="127">创建日期</th>
				  <th width="128">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->shipping_id; ?></td>
					<td><?php print $row->shipping_code; ?></td>
					<td><?php print $row->shipping_name; ?></td>
					<td><?php print $row->shipping_desc; ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" /></td>用样式修改 ByRock-->
					<td><span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    	
                    	<a href="shipping/edit_shipping_info/<?php print $row->shipping_id; ?>" title="编辑" class="edit"></a>
                        <?php if($perms['shipping_edit'] == 1):?>
                    	<a onclick="return confirm('确定删除？')" href="shipping/delete_shipping_info/<?php print $row->shipping_id; ?>" title="删除" class="del"></a>
						<?php endif;?>
						<a href="shipping/operate/<?php print $row->shipping_id; ?>" title="设置区域" >设置区域</a></td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="9" class="bottomTd"> </td>
				</tr>
			</table>
<div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
