<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('input[type=text][name=end_date]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:''});
	});
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
	listTable.filter.page = '<?php echo $filter['page']; ?>';
	listTable.url = 'import/history';
	function search(){
		listTable.filter['create_admin'] = $.trim($('input[type=text][name=create_admin]').val());
		listTable.filter['start_date'] = $.trim($('select[name=start_date]').val());
		listTable.filter['end_date'] = $.trim($('select[name=end_date]').val());
		listTable.loadList();
	}
	
	function batch_conform(imp_id){
	    if(!confirm('确定审核？'))return;
		$.ajax({
		   type: "POST",
		   url: "import/batch_conform/"+imp_id,
		   data: { rnd : new Date().getTime()},
		   dataType: "json",
		   success: function(msg){
			alert(msg.msg);
			if(msg.err == 0){
			    search();
			}
		   }
		});
	}
	jQuery.download = function(url, data, method){ 
		    if( url && data ){
		    var inputs = ''; 
		    jQuery.each(data, function(key,val){ 
		    inputs+='<input type="hidden" name="'+ key +'" value="'+ val +'" />'; 
		    }); 
		    jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>') 
		    .appendTo('body').submit().remove(); 
		    }; 
		};
	function export_color_size($list_id){
	    $.download("import/download_color_size_template/"+$list_id,{rnd : new Date().getTime()});
	}
	function export_product_sub($list_id){
	    $.download("import/download_product_sub_template/"+$list_id,{rnd : new Date().getTime()});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">历次导入列表</span></div>
	<div class="blank5"></div>
	<div class="search_row">
	    <form name="search" action="javascript:search(); ">
	    导入人：<?php print form_dropdown('create_admin', get_pair($all_import_admin,'create_admin','admin_name'));?>
	    开始时间：<input type="text" name="start_date" class="ts" value="" style="width:100px;">
	    结束时间：<input type="text" name="end_date" class="ts" value="" style="width:100px;">
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
				<th width="50px">编号</th>
				<th>导入人</th>
				<th>导入时间</th>
				<th>状态</th>
				<th>操作</th>
				<th>审核</th>
			</tr>
			<?php foreach($list as $row): ?>
			<tr class="row">
				<td><?php print $row->id; ?></td>
				<td><?php echo  $show_import_admin[$row->create_admin]->admin_name?></td>
				<td><?php echo  $row->create_date?></td>
				<td><?php if($row->status == '02'){echo '执行中';} elseif($row->status == '03'){echo '执行失败';}else{echo "执行成功";} ?></td>
				<td>
				    <?php if($row->status == '06'):?>
				    【<a href="javascript:export_color_size(<?php print $row->id; ?>)">下载颜色规格模版</a>】
				    【<a href="javascript:export_product_sub(<?php print $row->id; ?>)">下载次要信息模版</a>】
				    <?php endif;?></td>
				<td width="50px" align="center">
				    <?php if(!empty($row->confirm_admin)):
					echo  $show_import_admin[$row->confirm_admin]->admin_name ."/".$row->confirm_date;
				    else:
					if($row->status == '06'):echo "<a href='javascript:batch_conform( ".$row->id .");'>统一审核</a>";endif;
				    endif;?>
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
<?php if($full_page): ?>		
	</div>
</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>