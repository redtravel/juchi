<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'single_page/index';
		function search(){
			listTable.filter['is_use'] = $.trim($('select[name=is_use]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">系统设置 >> 单页专题管理</span> <span class="r"><a href="single_page/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
			  是否启用：
			    <select name="is_use" id="is_use">
			      <option value="">--请选择--</option>
			      <option value="2">启用</option>
			      <option value="1">未启用</option>
		      </select>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th>单页名称</th>
			      <th>启用</th>
				  <th width="162">创建日期</th>
				  <th width="137">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->single_id; ?></td>
					<td><?php print $row->page_name; ?></td>
				  <!--<td><img src="public/images/<?php /*echo $row->is_use == 0 ? 'no' : 'yes'*/?>.gif" /></td>样式显示 By Rock-->
                  <td><span class="<?php echo $row->is_use == 0 ? 'noForGif' : 'yesForGif'?>"></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <a class="edit" href="single_page/edit/<?php echo $row->single_id;?>" title="编辑"></a>
                    <?php if($perms['single_page_edit'] == 1):?>
                    <a id="a_<?php print $row->single_id; ?>" class="del" href="javascript:void(0);" rel="single_page/del/<?php print $row->single_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="6" class="bottomTd"> </td>
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