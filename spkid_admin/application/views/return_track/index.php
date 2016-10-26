<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		$(function(){
                    $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
                    $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
                });

		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'return_track/index';
		function search(){
                    listTable.filter['apply_id'] = $.trim($('input[name=apply_id]').val());
                    listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
                    listTable.filter['track_return_sn'] = $.trim($('input[name=track_return_sn]').val());
                    listTable.filter['searchType'] = $('input[name=searchType]:checked').val();
                    listTable.loadList();
		}

		function swap_more_search(){
	        var more_search = document.getElementById('more_search');

	        if (more_search.style.display == ''){
	            more_search.style.display='none';
	        }else{
	            more_search.style.display='';
	        }
	    }
		//]]>
	</script>
<div class="main">
		<div class="main_title">
                    <span class="l">退货单管理 >> 天猫退单跟踪</span>
		</div>
        <div class="blank5"></div>
        <div class="search_row">
                    <form name="search" action="javascript:search(); ">
			退货申请ID：<input type="text" class="ts" name="apply_id" id="apply_id" value="" style="width:100px;" />
			系统订单号：<input type="text" class="ts" name="order_sn" id="order_sn" value="" style="width:100px;" />
			天猫退单号：<input type="text" class="ts" name="track_return_sn" id="track_return_sn" value="" style="width:100px;" />
                        <input type="radio" name="searchType" value="1" style="vertical-align: middle;">待天猫申请退货列表
                        <input type="radio" name="searchType" value="2" style="vertical-align: middle;">退单待收货列表
                        <input type="radio" name="searchType" value="3" style="vertical-align: middle;">退单待返款列表
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
                    </form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
                                    <th>退货申请ID</th>
                                    <th>申请退货时间</th>
                                    <th>退货运单号</th>
                                    <th>系统订单号</th>
                                    <th>天猫订单号</th>
                                    <th>天猫退单号</th>
                                    <th>退单创建时间</th>
                                    <th width="100">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
                                <tr class="row">
                                    <td align="center"><a href ="apply_return/info/<?php print $row->apply_id; ?>" target="_blank"><?php print $row->apply_id; ?></a></td>
                                    <td><?php print $row->apply_time; ?></td>
                                    <td><?php print $row->invoice_no; ?></td>
                                    <td align="center"><a href ="order/info/<?php print $row->order_id; ?>" target="_blank"><?php print $row->order_sn; ?></a></td>
                                    <td><?php print $row->track_order_sn; ?></td>
                                    <td><?php print $row->track_return_sn; ?></td>
                                    <td><?php print $row->track_create_time; ?></td>
                                    <td>
                                        <a href="return_track/edit/<?php print $row->invoice_no; ?>">编辑</a>
                                        <?php if(empty($row->track_shipping_sn)):?>
                                        | <a href="order_return/add/<?php print $row->order_id; ?>/<?php print $row->apply_id; ?>">收货</a>
                                        <?php endif;?>
                                    </td>
                                </tr>
				<?php endforeach; ?>
                                
                                <tr>
					<td colspan="8" class="bottomTd"> </td>
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
