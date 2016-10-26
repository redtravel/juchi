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
		listTable.url = 'brand/index';
		listTable.func = img_tip;
		function search(){
			listTable.filter['brand_name'] = $.trim($('input[type=text][name=brand_name]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">品牌列表</span><span class="r"><a href="brand/add" class="add">新增</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			品牌名称：<input type="text" class="ts" name="brand_name" value="" style="width:100px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="50px">
						<a href="javascript:listTable.sort('b.brand_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'b.brand_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>品牌名称</th>
					<th>LOGO</th>
					<th>BANNER</th>
					<th>
						<a href="javascript:listTable.sort('b.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'b.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>启用</th>
					<th>产地</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->brand_id; ?></td>
					<td><?php print '['.$row->brand_initial.'] '.$row->brand_name; ?></td>
					<td>
						<img src="<?php print PUBLIC_DATA_IMAGES . $row->brand_logo;?>" width="80" />
					</td>
					<td>
						<?php if ($row->brand_banner): ?>
							<img src="<?php print PUBLIC_DATA_IMAGES . $row->brand_banner;?>" width="150" />	
						<?php else: ?>
							未上传
						<?php endif ?>
											
					</td>
					<td>
						<?php print edit_link('brand/edit_field', 'sort_order', $row->brand_id, $row->sort_order);?>
					</td>
					<td width="50px" align="center">
						<?php print toggle_link('brand/toggle','is_use',$row->brand_id, $row->is_use);?>
					</td>
					<td>
						<img src="<?php print PUBLIC_DATA_IMAGES . $row->flag_url;?>" alt="<?php print $row->flag_name;?>" />
					</td>
					<td>
						<a class="edit" href="brand/edit/<?php print $row->brand_id; ?>" title="编辑"></a>
						<?php if($perm_delete):?>
						<a class="del" href="javascript:void(0);" rel="brand/delete/<?php print $row->brand_id; ?>" title="删除" onclick="do_delete(this)"></a>
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