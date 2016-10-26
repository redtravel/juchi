<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		// show image tip
		function img_tip()
		{
			$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
		}
		$(function(){
			img_tip();
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'size/image_index';
		listTable.func = img_tip;
		function search(){
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
			listTable.filter['sex'] = $.trim($('select[name=sex]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">规格详情列表</span><span class="r"><a href="size/image_add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			<select name="category_id">
				<option value="0">所有分类</option>
				<?php 
				foreach ($all_category as $key => $value) {
					echo "<option value='{$value->category_id}'>{$value->level_space} {$value->category_name}</option>";
				}
				;?>
			</select>
			<select name="brand_id">
				<option value="0">所有品牌</option>
				<?php 
				foreach ($all_brand as $key => $value) {
					echo "<option value='{$value->brand_id}'>{$value->brand_name}</option>";
				}
				;?>
			</select>
			<?php print form_dropdown('sex',array(0=>'所有性别','1'=>'男','2'=>'女'))?>
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
						<a href="javascript:listTable.sort('s.size_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 's.size_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th>品牌</th>
					<th>分类</th>
					<th>性别</th>
					<th>详情图</th>
					<th>规格表</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->size_image_id; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->category_name; ?></td>
					<td><?php if($row->sex==1) print '男'; elseif($row->sex==2) print '女'; ?></td>
					<td>
						<?php print img_tip(PUBLIC_DATA_IMAGES,$row->image_url);?>
					</td>
					<td>
                                                <?php if ($row->size_table != null && $row->size_table != ''): ?>
                                                        <a href="size/size_table_show/<?php print $row->size_image_id; ?>">查看</a>
                                                        <a href="size/size_table_delete/<?php print $row->size_image_id; ?>">删除</a>
                                                <?php endif; ?>
					</td>
					<td>
						<a class="edit" href="size/image_edit/<?php print $row->size_image_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="size/image_delete/<?php print $row->size_image_id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						<a href="size/size_table_edit/<?php print $row->size_image_id; ?>" title="编辑规格表">规格表</a>
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