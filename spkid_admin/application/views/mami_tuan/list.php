<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>

	<script type="text/javascript">
	    /* 时间插件 */
		$(function(){
			$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		});
		
		/* 搜索 */
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'mami_tuan/index';
		function search(){
			listTable.filter['product_sn'] = $.trim($('input[name=product_sn]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.loadList();
		}
		//]]>
		
		/* 审核、反审核 */
		function post_confirm(tuan_id,type){
			if(!confirm('确定要操作吗？')){
				return false;
			}
			$.ajax({
			   type: "POST",
			   url: "mami_tuan/confirm",
			   dataType: "JSON",
			   data: {tuan_id:tuan_id,type:type,rnd:new Date().getTime()},
			   success: function(data){
					if(data.error == 1){
						alert(data.msg);
						return false;
					}else{
						/*
						if(type == 1){
							$('#onconfirm_'+tuan_id).hide();
						}else{
							$('#unconfirm_'+tuan_id).hide();
						}
						*/
						alert(data.msg);
						window.document.location.reload();
					}
			   }
			});
		}
		
		/* 上架、停止 */
		function post_sale(tuan_id,type){
			if(!confirm('确定要操作吗？')){
				return false;
			}
		    $.ajax({
			    url: '/mami_tuan/sale',
			    data: {tuan_id:tuan_id,type:type,rnd:new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(data){
					if(data.error == 1){
						alert(data.msg);
						return false;
					}else{
						/*
						if(type == 1){
							$('#onsale_'+tuan_id).hide();
						}else{
							$('#unsale_'+tuan_id).hide();
						}
						*/
						alert(data.msg);
						window.document.location.reload();
					}
			    }
		   });
		}
	</script>
	<div class="main">
		<div class="main_title"><span class="l">团购管理 >> 团购列表</span> <span class="r"><a href="mami_tuan/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	<div class="search_row">
	<form name="search" action="javascript:search(); ">
	    商品款号:<input type="text" name="product_sn" id="product_sn" />
	    开始日期：<input type="text" name="start_time" id="start_time" />
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
				  <th width="50">编号</th>
				  <th>排序</th>
				  <th>团购名称</th>
				  <th>商品款号</th>
				  <th>开始日期</th>
				  <th>结束日期</th>
				  <th>市场价格</th>
				  <th>团购价格</th>
				  <th>团购单位</th>
				  <th>剩余数量</th>
				  <th>状态</th>
				  <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->tuan_id; ?></td>
			    	<td align="center"><?php print $row->tuan_sort; ?></td>
			    	<td align="center"><?php print $row->tuan_name; ?></td>
			    	<td align="center"><?php print $row->product_sn; ?></td>
			    	<td align="center"><?php print $row->tuan_online_time; ?></td>
			    	<td align="center"><?php print $row->tuan_offline_time; ?></td>
					<td align="center"><?php print $row->market_price; ?></td>
					<td align="center"><?php print $row->tuan_price; ?></td>
					<td align="center"><?php print $row->tuan_unit; ?></td>
					<td align="center"><?php print $row->product_num; ?></td>
					<td id="td_html_<?php print $row->tuan_id; ?>"><?php print $this->status_list[$row->status]; ?><?php if($row->tuan_offline_time<$this->time) { print ' 已过期'; } elseif($row->is_promote == 1 && $row->is_onsale == 1) {print ' 已上架';}?></td>
					<td>
						<!-- 编辑、查看 -->
						<a class="edit" href="mami_tuan/edit/<?php print $row->tuan_id; ?>" title="编辑"></a>
						<!-- 预览 -->
						<a href="" target="_blank" title="预览">预览</a>
						
						<?php //if ($row->tuan_offline_time>=$this->time && $is_edit): ?>
						<!-- 客审、反客审 -->
						<?php if($row->status == 1 && $row->is_promote == 0){?>
						<input type="button" id="unconfirm_<?php print $row->tuan_id; ?>" onclick="return post_confirm(<?php print $row->tuan_id; ?>,0);" value="反审核">
						<?php }elseif($row->status == 0){?>
						<input type="button" id="onconfirm_<?php print $row->tuan_id; ?>" onclick="return post_confirm(<?php print $row->tuan_id; ?>,1);" value="审核">
						<?php }?>
						
						<!-- 上架、停止 -->
						<?php if($row->status != 2 && $row->status != 3):?>
						<input type="button" id="unsale_<?php print $row->tuan_id; ?>" onclick="post_sale(<?php print $row->tuan_id; ?>,0);" value="停止">
						<?php endif;?>
						<?php //if($row->status == 1 && $row->is_promote == 0):?>
						<!--<input type="button" id="onsale_<?php print $row->tuan_id; ?>" onclick="post_sale(<?php print $row->tuan_id; ?>,1);" value="上架"> -->
						<?php //endif;?>  
						<?php //endif;?>
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
		<div class="search_row">
			<span style="color:red"><strong>注意：</strong></span>
			<span style="color:red">1、上线必须是先审核商品；</span>
			<span style="color:red">2、编辑必须是未审核的商品；</span>
			<span style="color:red">3、反审核必须是未上线商品；</span>
			<span style="color:red">4、上线商品不能被删除。</span>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>