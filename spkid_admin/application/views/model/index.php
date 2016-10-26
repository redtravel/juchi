<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		function img_tip()
		{
			$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
		}
		$(function(){
			img_tip();
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'model/index';
		listTable.func = img_tip;
		function search(){
			listTable.filter['model_name'] = $.trim($('input[type=text][name=model_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">模特列表</span><span class="r"><a href="model/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			模特名称：<input type="text" class="ts" name="model_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">编号</th>
					<th>模特名称</th>
					<th>模特图片</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->model_id; ?></td>
					<td><?php print $row->model_name; ?></td>
					<td>
						<?php print img_tip(PUBLIC_DATA_IMAGES, $row->model_image); ?>
					</td>
					<td>
						<a class="edit" href="model/edit/<?php print $row->model_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="model/delete/<?php print $row->model_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						
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