<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'register_code/index';
		function search(){
			listTable.filter['register_no'] = $.trim($('input[type=text][name=register_no]').val());
                        listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]').val());
                        listTable.filter['unit'] = $.trim($('input[type=text][name=unit]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<style type="text/css">

	</style>
	<div class="main">
		<div class="main_title"><span class="l">注册号</span><span class="r"><a href="register_code/add" class="add">新增</a></span></div>		
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			注册号：<input type="text" class="ts" name="register_no" id="register_no" value="" style="width:230px;" />
                        产品名称：<input type="text" class="ts" name="product_name" id="product_name" value="" style="width:230px;" />
                        生产单位：<input type="text" class="ts" name="unit" id="unit" value="" style="width:230px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="40px"><a href="javascript:listTable.sort('r.id', 'ASC'); ">编号<?php echo ($filter['register_id'] == 'r.id') ? $filter['sort_flag'] : '' ?></th>
					<th width="250px">注册号</th>
					<th>产品名称</th>
					<th>生产单位</th>
					<th>产品标准</th>
					<th width="300px;">产品性能结构及组成</th>
					<th>产品适用范围</th>
					<th>有效期</th>
					<th width="40px;">添加人</th>
					<th>添加时间</th>
					<th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><span><?php if(!empty($fields_source)&&isset($fields_source["id"])&&isset($fields_source["id"]["$row->id"]))echo $fields_source["id"]["$row->id"] ;else echo $row->id; ?></span></td>
					<td><?php print $row->register_no; ?><br/><?php print $row->field_value1; ?> 、<?php print $row->field_value2; ?></td>
					<td><span data-type="textarea" data-pk="<?php print $row->id; ?>" data-name="product_name" class="editable" data-title="产品名称" data-value="<?php print $row->product_name; ?>"><?php if(!empty($fields_source)&&isset($fields_source["product_name"])&&isset($fields_source["product_name"]["$row->product_name"]))echo $fields_source["product_name"]["$row->product_name"] ;else echo ($row->product_name)?$row->product_name:' 无 '; ?></span></td>
					<td><span data-type="textarea" data-pk="<?php print $row->id; ?>" data-name="unit" class="editable" data-title="生产单位" data-value="<?php print $row->unit; ?>"><?php if(!empty($fields_source)&&isset($fields_source["unit"])&&isset($fields_source["unit"]["$row->unit"]))echo $fields_source["unit"]["$row->unit"] ;else echo ($row->unit)?$row->unit:' 无 '; ?></span></td>
					<td><span data-type="textarea" data-pk="<?php print $row->id; ?>" data-name="standard" class="editable" data-title="产品标准" data-value="<?php print $row->standard; ?>"><?php if(!empty($fields_source)&&isset($fields_source["standard"])&&isset($fields_source["standard"]["$row->standard"]))echo $fields_source["standard"]["$row->standard"] ;else echo ($row->standard)?$row->standard:' 无 '; ?></span></td>
					<td><span data-type="textarea" data-pk="<?php print $row->id; ?>" data-name="property" class="editable" data-title="产品性能结构及组成" data-value="<?php print $row->property; ?>"><?php if(!empty($fields_source)&&isset($fields_source["property"])&&isset($fields_source["property"]["$row->property"]))echo $fields_source["property"]["$row->property"] ;else echo ($row->property)?$row->property:' 无 '; ?></span></td>
					<td><span data-type="textarea" data-pk="<?php print $row->id; ?>" data-name="scope" class="editable" data-title="产品适用范围" data-value="<?php print $row->scope; ?>"><?php if(!empty($fields_source)&&isset($fields_source["scope"])&&isset($fields_source["scope"]["$row->scope"]))echo $fields_source["scope"]["$row->scope"] ;else echo ($row->scope)?$row->scope:' 无 '; ?></span></td>
					<td><span data-viewformat="yyyy-mm-dd" data-type="date" data-pk="<?php print $row->id; ?>" data-name="valid_time" class="editable" data-title="有效期" data-value="<?php print $row->valid_time; ?>"><?php if(!empty($fields_source)&&isset($fields_source["valid_time"])&&isset($fields_source["valid_time"]["$row->valid_time"]))echo $fields_source["valid_time"]["$row->valid_time"] ;else echo ($row->valid_time)?$row->valid_time:' 无 '; ?></span></td>
					<td><?php print $row->admin_name; ?></td>
					<td><?php print date('Y-m-d',$row->add_admin_time); ?></td>
					<td>
						<?php if ($perm_fetch): ?>
							<a class="priv" href="register_code/grab/<?php print $row->id; ?>" title="抓取"></a>
						<?php endif ?>
						<a class="edit" href="register_code/edit/<?php print $row->id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="register_code/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
			<script>
				// jquery editable 
				function _editable(){
				$('.editable').editable({ url: '/register_code/editable', emptytext:'',
				        success: function(response, newValue) {
				            if(!response.success) return response.msg;
				            if( response.value != newValue ) return '操作失败';
				        }
				    });
				}
				listTable.func = _editable; // 分页加载后调用的函数名
				_editable();
			</script>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>