<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<div class="main">
    <div class="main_title"><span class="l">前端配置 >> 文章分类列表</span> <span class="r"><a href="article/cat_add" class="add">新增</a></span></div>
    <div class="blank5"></div>
		<div id="listDiv">
			<table width="1170"  cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="50">ID</th>
			      <th>分类名称</th>
			      <th width="150">关键字</th>
			      <th width="50">排序</th>
				  <th width="169">是否使用</th>
				  <th width="150">创建时间</th>
				  <th width="80">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td><?php print $row->cat_id; ?></td>
					<td style="text-align:left;"><?php print $row->level_space.$row->cat_name; ?></td>
					<td><?php print $row->keywords; ?></td>
					<td>
						<?php print edit_link('article/edit_cat_field','sort_order',$row->cat_id,$row->sort_order); ?>
					</td>
					<!--<td><img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" /></td>用样式显示 ByRock-->
                    <td><span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>" ></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    	
						<a href="article/cat_edit/<?php print $row->cat_id; ?>" title="编辑" class="edit"></a> 
                        <?php if($perms['art_cat_edit'] == 1):?>
			        	<a class="del" href="javascript:void(0);" rel="article/cat_del/<?php print $row->cat_id; ?>" title="删除" onclick="do_delete(this)"></a>
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
