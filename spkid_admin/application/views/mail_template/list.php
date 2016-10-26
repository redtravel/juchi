<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>

	<script type="text/javascript">

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'mail_template/index';
		function search(){
			listTable.filter['is_html'] = $.trim($('select[name=is_html]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">系统设置 >> 邮件短信模板管理</span> <span class="r"><a href="mail_template/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
			  是否是HTML：
<select name="is_html" id="is_html">
			      <option value="">--请选择--</option>
			      <option value="2">是HTML</option>
			      <option value="1">不是HTML</option>
		      </select>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th>模板TAG</th>
			      <th>模板名称</th>
			      <th>模板标题</th>
			      <th>优先级</th>
			      <th>是否是HTML</th>
				  <th width="162">创建日期</th>
				  <th width="137">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->template_id; ?></td>
					<td><?php print $row->template_code; ?></td>
					<td><?php print $row->template_name; ?></td>
					<td><?php print $row->template_subject; ?></td>
					<td><?php print $row->template_priority; ?></td>
				    <!--<td><img src="public/images/<?php /*echo $row->is_html == 0 ? 'no' : 'yes'*/?>.gif" /></td> 用样式修改 By Rock-->
                    <td><span class="<?php echo $row->is_html == 0 ? 'noForGif' : 'yesForGif'?>"></span></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <a class="edit" href="mail_template/edit/<?php echo $row->template_id;?>" title="编辑"></a>
                    <?php if($perms['mail_template_edit'] == 1):?>
                    <a id="a_<?php print $row->template_id; ?>" class="del" href="javascript:void(0);" rel="mail_template/del/<?php print $row->template_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="9" class="bottomTd"> </td>
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