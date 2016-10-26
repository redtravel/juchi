<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'front_ad/index';
		function search(){
			listTable.filter['position_name'] = $.trim($('input[name=position_name]').val());
			listTable.filter['position_tag'] = $.trim($('input[name=position_tag]').val());
			listTable.filter['page_name'] = $.trim($('input[name=page_name]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">系统设置 >> 广告位置管理</span> <span class="r"><a href="front_ad/p_add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
              广告位置名称：
              <input type="text" name="position_name" id="position_name" />
              广告位置TAG：
              <input type="text" name="position_tag" id="position_tag" />
			  页面名称：
              <input type="text" name="page_name" id="page_name" />
              品牌：
              <select name="brand_id" id="brand_id">
			      <option value="">--请选择--</option>
                  <?php
                  foreach($all_brand as $item):
				  ?>
			      <option value="<?php echo $item->brand_id?>"><?php echo $item->brand_name?></option>
		      	  <?php
                  endforeach;
				  ?>
                </select>
              分类：
              <select name="category_id" id="category_id">
				  <option value="">--请选择--</option>
                  <?php
                  foreach($all_category as $item):
				  ?>
			      <option value="<?php echo $item->category_id?>"><?php echo $item->category_name?></option>
		      	  <?php
                  endforeach;
				  ?>
			    </select>
              <input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="11" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="145">广告位置名称</th>
			      <th width="180">广告位置TAG</th>
			      <th width="180">页面名称</th>
			      <th width="146">品牌</th>
			      <th width="137">分类</th>
			      <th width="98">宽度</th>
				  <th width="85">高度</th>
				  <th width="170">创建日期</th>
				  <th width="167">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->position_id; ?></td>
					<td><?php print $row->position_name; ?></td>
					<td><?php print $row->position_tag; ?></td>
					<td><?php print $row->page_name; ?></td>
				  	<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->category_name; ?></td>
					<td><?php print $row->ad_width; ?>px</td>
					<td><?php print $row->ad_height; ?>px</td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <?php if($perms['front_ad_po_edit'] == 1 || $perms['front_ad_po_view'] == 1):?>
                    <a class="edit" href="front_ad/p_edit/<?php echo $row->position_id;?>" title="编辑"></a>
                    <?php endif;if($perms['front_ad_po_edit'] == 1):?>
                    <a id="a_<?php print $row->position_id; ?>" class="del" href="javascript:void(0);" rel="front_ad/p_del/<?php print $row->position_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
					<?php if($perms['front_ad_view'] == 1 || $perms['front_ad_edit'] == 1):?>
                    <a href="front_ad/operate_index/<?php echo $row->position_id;?>" title="设置广告">设置广告</a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="11" class="bottomTd"> </td>
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