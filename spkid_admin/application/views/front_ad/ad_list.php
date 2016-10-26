<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>
	<div class="main">
		<div class="main_title"><span class="l">系统设置 >> 广告管理</span> <span class="r"><a href="front_ad/index" class="return r">返回列表</a><a href="front_ad/ad_add/<?php echo $position_id;?>" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="10" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="145">广告位置名称</th>
			      <th width="203">广告名称</th>
			      <th width="208">广告链接</th>
			      <th width="56">点击数</th>
			      <th width="175">开始时间</th>
				  <th width="201">结束时间</th>
				  <th width="52">启用</th>
				  <th width="88">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->ad_id; ?></td>
					<td><?php print $row->position_name; ?></td>
					<td><?php print $row->ad_name; ?></td>
				  	<td><?php print $row->ad_link; ?></td>
					<td><?php print $row->click_count; ?></td>
					<td><?php print $row->start_date; ?></td>
					<td><?php print $row->end_date; ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" /></td> 用样式显示 By Rock-->
                    <td><span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td>
                    <?php if($perms['front_ad_edit'] == 1 || $perms['front_ad_view'] == 1):?>
                    <a class="edit" href="front_ad/ad_edit/<?php echo $row->ad_id;?>/1" title="编辑"></a>
                    <?php endif;if($perms['front_ad_edit'] == 1):?>
                    <a id="a_<?php print $row->ad_id; ?>" class="del" href="javascript:void(0);" rel="front_ad/ad_del/<?php print $row->ad_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="10" class="bottomTd"> </td>
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