<?php if($full_page): ?>
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
		<div class="main_title"><span class="l">友情链接列表</span><span class="r"><a href="friend/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
            名称：
              <input type="text" name="link_name" id="link_name" />
              地址：
			    <input type="text" name="link_url" id="link_url" />
		      <input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="180">名称</th>
			      <th width="270">地址</th>
			      <th width="141">logo</th>
				  <th width="157">排序号</th>
				  <th width="140">创建人</th>
				  <th width="163">创建日期</th>
				  <th width="77">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->link_id; ?></td>
					<td><?php print $row->link_name; ?></td>
					<td><?php print $row->link_url; ?></td>
					<td><?php if(!empty($row->link_logo)):?><img height="20" width="20" src="<?php print PUBLIC_DATA_IMAGES . $row->link_logo; ?>" /><?php endif;?></td>
					<td><?php print $row->sort_order; ?></td>
					<td><?php echo empty($row->create_admin) ? '' : $all_admin[$row->create_admin]->admin_name; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <?php if($perms['friendlink_edit'] == 1):?>
                    <a href="friend/edit/<?php print $row->link_id; ?>" title="编辑" class="edit"></a>
                    <a class="del" href="javascript:void(0);" rel="friend/delete/<?php print $row->link_id; ?>" title="删除" onclick="do_delete(this)"></a>
					<?php endif;?>    
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="9" class="bottomTd"> </td>
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