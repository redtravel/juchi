<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/order.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'inventory_warning/view_warning_list';
	function search () {
                listTable.filter['provider_id'] = $.trim($(':input[name=provider_id]').val());
                listTable.filter['purchase_batch'] = $.trim($(':input[name=purchase_batch]').val());
                listTable.filter['product_sn'] = $.trim($(':input[name=product_sn]').val());
                listTable.loadList();
	}
        
        function get_provider_batch(dom) {
		$("#purchase_batch option").remove();
                var url = '/inventory_query/get_inventory_batch/'+dom.value;
		$.get(url,function(result){
			$.each($.parseJSON(result), function(key, value) {
		        var htmlStr = '<option value="'+key+'">'+value+'</option>';
		        $("#purchase_batch").append(htmlStr);
		    });
		});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">仓库管理 >> 库存预警条目 </span>
            <span class="r">[ <a href="/inventory_warning/index">返回列表 </a>]</span>
            <span class="r"><a href="inventory_warning/add" class="add">新增</a></span>
        </div>
	<div class="search_row">
		<form name="search" action="javascript:search(); ">
		供应商：
                <select id="provider_id" name="provider_id" onchange="get_provider_batch(this);" >
                        <?php foreach ($provider_list as $key => $val): ?>
                            <option value="<?php print $key; ?>"><?php print $val; ?></option>
                        <?php endforeach; ?>
                </select>
                批次号：
                <select id="purchase_batch" name="purchase_batch">
                    <?php foreach ($batch_list as $key => $val): ?>
                        <option value="<?php print $key; ?>"><?php print $val; ?></option>
                    <?php endforeach; ?>
                </select>
                具体款号：<?php print form_input('product_sn'); ?>
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
        	</form>
	</div>
	<div id="listDiv" style="margin-top:5px;">
<?php endif; ?>
            <table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
                <tr class="row">
                    <th>预警ID</th>
                    <th width="300px">预警类型</th>
                    <th width="300px">预警值</th>
                    <th>最小预警库存数</th>
                    <th>预警状态</th>
                    <th>操作</th>
                </tr>
                <?php if(!$list):?>
                <tr class="row">
                    <td colspan="6">无记录</td>
                </tr>
                <?php endif; ?>
                <?php foreach($list as $warning): ?>
                <tr class="row p_<?php print $warning->id?>">
                    <td><?php print $warning->id; ?></td>
                    <td><?php if ($warning->warn_type==1) print '商品款号'; elseif ($warning->warn_type==2) print '指定批次'; ?></td>
                    <td><?php if ($warning->warn_type==1) print $warning->product_name; elseif ($warning->warn_type==2) print $warning->batch_name; ?></td>
                    <td><?php print $warning->min_number; ?></td>
                    <td><?php if ($warning->warn_status==1) print '可用'; elseif ($warning->warn_status==2) print '结束'; ?></td>
                    <td>
                    	<?php if($perm_edit && $warning->warn_status==1):?>
                            <a href="inventory_warning/edit_warning_info/<?php print $warning->id; ?>" title="编辑" class="edit"></a>
                        <?php endif; ?>
                        <?php if($perm_delete && $warning->warn_status==1):?>
                            <a onclick="return confirm('确定删除？')" href="inventory_warning/delete_warning_info/<?php print $warning->id; ?>" title="删除" class="del"></a>
                        <?php endif; ?>
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
<?php include(APPPATH.'views/common/footer.php');?>
<?php endif; ?>
