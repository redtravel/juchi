<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'article/article_index';
		function search(){
			listTable.filter['cat_id'] = $.trim($('select[name=cat_id]').val());
			listTable.filter['author'] = $.trim($('select[name=author]').val());
			listTable.filter['is_use'] = $.trim($('select[name=is_use]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}
		
		//]]>
	</script>
	<div class="main">
    <div class="main_title"><span class="l">前端配置 >> 文章列表</span> <span class="r"><a href="article/article_add" class="add">新增</a></span></div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
			<select name="cat_id">
				<option value="">分类</option>
				<?php foreach($all_cat as $cat) print "<option value='{$cat->cat_id}'>{$cat->level_space}{$cat->cat_name}</option>"?>
			</select>
			  作者：
			  <input type="text" name="author" id="author" />
<select name="is_use" id="is_use">
	    <option value="">--是否使用择--</option>
			    <option value="1">使用</option>
			    <option value="2">不使用</option>
			  </select>
			  创建时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="100">文章分类</th>
			      <th width="245">标题</th>
			      <th width="61">作者</th>
				  <th width="65">关键字</th>
				  <th width="69">排序号</th>
				  <th width="60">使用</th>
				  <th width="122">外链</th>
				  <th width="123">来源</th>
				  <th width="185">创建时间</th>
			      <th width="80">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->article_id; ?></td>
					<td style="text-align:left;">&nbsp;<?php print $row->cat_name; ?></td>
					<td style="text-align:left;">&nbsp;<?php print $row->title; ?></td>
					<td>&nbsp;<?php print $row->author; ?></td>
					<td>&nbsp;<?php print $row->keywords; ?></td>
					<td>
					<?php print edit_link('article/edit_field','sort_order',$row->article_id,$row->sort_order); ?>
					</td>
					<!--<td>&nbsp;<img src="public/images/<?php /*echo $row->is_use == 1 ? 'yes' : 'no'*/?>.gif" /></td>用样式显示 ByRock-->
                    <td>&nbsp;<span class="<?php echo $row->is_use == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td>&nbsp;<?php print $row->url; ?></td>
					<td>&nbsp;<?php print $row->source; ?></td>
					<td>&nbsp;<?php print $row->create_date; ?></td>
					<td>
                    	
						<a href="article/article_edit/<?php print $row->article_id; ?>" title="编辑" class="edit"></a> 
                        <?php if($perms['art_edit'] == 1):?>
                        <a class="del" href="javascript:void(0);" rel="article/article_del/<?php print $row->article_id; ?>" title="删除" onclick="do_delete(this)"></a>
			        	<?php endif;?>
			      </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="12" class="bottomTd"> </td>
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