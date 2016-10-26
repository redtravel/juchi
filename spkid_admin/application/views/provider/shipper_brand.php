<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<style type="text/css">
.provider_brand{border: 1px solid #C8DFA7;display: inline-block;margin: 3px;padding: 2px;text-align: center;width: 180px;}
</style>
<script type="text/javascript">
	//<![CDATA[
	var provider_id =<?php echo $row->provider_id; ?>;
	
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
	listTable.filter.page = '<?php echo $filter['page']; ?>';
	listTable.url = 'shipper_brand/index/<?php echo $filter['provider_id']; ?>/<?php echo $filter['parent_id']; ?>';
	function search(){
		listTable.filter['brand_id'] = $.trim($('input[type=text][name=brand_id]').val());
		listTable.filter['brand_name'] = $.trim($('input[type=text][name=brand_name]').val());
		listTable.filter['brand_initial'] = $.trim($('input[type=text][name=brand_initial]').val());
		listTable.filter['skip_set'] = $.trim($('input[type=checkbox][name=skip_set]:checked').val());
		listTable.loadList();
	}
	function set_brand(brand_id){
	     $.ajax({
	            url: '/shipper_brand/add_brand',
	            data: {is_ajax:1,rnd : new Date().getTime(),brand_id:brand_id,provider_id:provider_id},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err == 0)
	                {
			    alert("添加完成");
                            window.location.reload(listTable.url);
	                }
	            }
	        });
	}
	
	function remove_brand(brand_id){
	    if(!confirm('确定删除此发货商关联的品牌？'))return;
	    $.ajax({
	            url: '/shipper_brand/remove_brand',
	            data: {is_ajax:1,rnd : new Date().getTime(),brand_id:brand_id,provider_id:provider_id},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err == 0)
	                {
			    alert("删除成功");
                            window.location.reload(listTable.url);
	                }
	            }
	        });
	}
        
        function update_commission(id) {
            var commission = $('input[name=commission_'+id+']').val();
            $.ajax({
	            url: '/shipper_brand/update_commission',
	            data: {id:id, commission:commission},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err === 0) {
			    alert("扣点修改成功");
                            window.location.reload(listTable.url);
	                }
	            }
	        });
        }
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">发货商管理 >> 分配品牌 </span><a href="provider/scm_index/<?php print $parent->provider_id;?>" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<!--<div class="search_row">-->
	    <table class="dataTable">
		<tr class="row">
		    <td>发货商代码:<?php print $row->provider_code;?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		    <td>发货商名称:<?php print $row->provider_name;?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		    <td>状态:<?php print $row->is_use?"启用":"禁用" ; ?></td>
		</tr>
		<tr>
		    <td colspan="3">发货商关联品牌列表：<br />
                        <table class="dataTable" cellpadding=0 cellspacing=0>
                            <tr class="row">
                                    <th width="50px">编号</th>
                                    <th>品牌名称</th>
                                    <th width="300px;">扣点</th>
                                    <th width="220px;">操作</th>
                            </tr>
                            <?php foreach($brand_list as $brand): ?>
                            <tr class="row">
                                    <td><?php print $brand->brand_id; ?></td>
                                    <td><?php print '['.$brand->brand_initial.'] '.$brand->brand_name; ?></td>
                                    <td><input type="text" name="commission_<?php print $brand->id; ?>" value="<?php print $brand->commission;?>" /></td>
                                    <td>
                                        <a class="del" href="javascript:remove_brand(<?php print $brand->brand_id; ?>);">删除</a>
                                        <a class="add" href="javascript:update_commission(<?php print $brand->id; ?>);" title="添加">修改扣点</a>
                                        <a target="_blank" href="shipper_brand/commission_history/<?php print $brand->id; ?>">查看扣点历史</a>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
		    </td>
		</tr>
	    </table>
	<!--</div>-->
	<br />
	<div class="search_row">
		<form name="search" action="javascript:search(); ">
		品牌编号：<input type="text" class="ts" name="brand_id" style="width:100px;" />
		品牌名称：<input type="text" class="ts" name="brand_name" style="width:100px;" />
		品牌首字母：<input type="text" class="ts" name="brand_initial" style="width:100px;" />
		<label><input type="checkbox" name="skip_set" value="true"/>过滤已设置</label>
		<input type="submit" class="am-btn am-btn-primary" value="搜索"/>
		</form>
	</div>
	<div class="blank5"></div>
	<div id="listDiv">
<?php endif; ?>	    
	    <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
		    <tr>
			    <td colspan="5" class="topTd"> </td>
		    </tr>
		    <tr class="row">
			    <th width="50px">
				    <a href="javascript:listTable.sort('b.brand_id', 'ASC'); ">编号<?php echo ($filter['sort_by'] == 'b.brand_id') ? $filter['sort_flag'] : '' ?></a>
			    </th>
			    <th>品牌名称</th>
			    <th><a href="javascript:listTable.sort('b.sort_order', 'ASC'); ">排序号<?php echo ($filter['sort_by'] == 'b.sort_order') ? $filter['sort_flag'] : '' ?></a></th>
			    <th>启用</th>
			    <th width="120px;">操作</th>
		    </tr>
		    <?php foreach($list as $row): ?>
		    <tr class="row">
			    <td><?php print $row->brand_id; ?></td>
			    <td><?php print '['.$row->brand_initial.'] '.$row->brand_name; ?></td>
			    <td><?php print $row->sort_order;?></td>
			    <td width="50px" align="center"><?php print $row->is_use?"是":"否";?></td>
			    <td><a class="add" href="javascript:set_brand(<?php print $row->brand_id; ?>);" title="添加">添加</a></td>
		    </tr>
		    <?php endforeach; ?>
		    <tr>
			    <th colspan="5" class="bottomTd"></th>
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