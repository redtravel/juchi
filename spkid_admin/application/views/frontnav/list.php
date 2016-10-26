<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
<div class="main">
		<div class="main_title">
		    <span class="l">系统设置 >> 导航管理</span>
		    <span class="r">
			<!--<a href="frontnav/static_header" class="add">生成头部</a>-->
			<a href="frontnav/add" class="add">新增</a>
		    </span>
		</div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="50">ID</th>
				  <th>导航名称</th>
				  <th>包含分类</th>
				  <th>链接地址</th>
				  <th>广告链接</th>
				  <th>排序</th>
				  <th>操作</th>
				</tr>
				<?php foreach($nav_list as $nav): ?>
			    <tr class="row">
			      <td><?php print $nav->nav_id; ?></td>
			      <td><?php print $nav->nav_name; ?></td>
			      <td>
			      <?php 
			      	if($nav->category_ids){
			      		$category_ids = explode(',',$nav->category_ids);
			      		foreach ($category_ids as $cat_id) print (isset($all_category[$cat_id])?$all_category[$cat_id]->type_name:'').'&nbsp;&nbsp;';
			      	}
			      ?>
			      </td>
			      <td><?php print str_replace('[front]',FRONT_HOST,$nav->nav_url); ?></td>
			      <td><?php print str_replace('[front]',FRONT_HOST,$nav->nav_ad_url); ?></td>
			      <td><?php print edit_link('frontnav/edit_field','sort_order',$nav->nav_id,$nav->sort_order); ?></td>
				  <td>
				  	<?php if ($can_edit): ?>
				  	<a href="frontnav/edit/<?php print $nav->nav_id; ?>" title="编辑" class="edit"></a>
                    <a class="del" href="javascript:void(0);" rel="frontnav/delete/<?php print $nav->nav_id; ?>" title="删除" onclick="do_delete(this)"></a>
				  	<?php endif ?>
                    
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
