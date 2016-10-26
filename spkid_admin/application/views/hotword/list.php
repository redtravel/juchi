<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>

	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'hotword/index';
		function search(){
			listTable.filter['hotword_name'] = $.trim($('input[name=hotword_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
<div class="main">
		<div class="main_title"><span class="l">系统设置 >> 热门关键字管理</span><span class="r"><a href="hotword/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
            热门关键字名称：
              <input type="text" name="hotword_name" id="hotword_name" />

		      <input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="180">热门关键字名称</th>
			      <th width="270">热门关键字URL</th>
			      <th width="141">排序</th>
				  <th width="157">点击量</th>
				  <th width="157">类别</th>
				  <th width="163">创建日期</th>
				  <th width="77">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->hotword_id; ?></td>
					<td><?php print $row->hotword_name; ?></td>
					<td><?php print $row->hotword_url; ?></td>
					<td><?php print $row->sort_order; ?></td>
					<td><?php print $row->click_count; ?></td>
					<td><?php print ( $row->hotword_type == 1 ) ? "课程" : "商品"; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
	                    <?php if($perms['hotword_edit'] == 1):?>
	                    <a href="hotword/edit/<?php print $row->hotword_id; ?>" title="编辑" class="edit"></a>
	                    <a class="del" href="javascript:void(0);" rel="hotword/delete/<?php print $row->hotword_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif;?>    
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="8" class="bottomTd"> </td>
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