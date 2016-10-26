<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
//		$(function(){
//                    $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
//                    $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
//                });
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'order_track/index';
		function search(){
                    listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
                    listTable.filter['order_status'] = $.trim($(':input[name=order_status]').val());
                    listTable.filter['track_order_sn'] = $.trim($('input[name=track_order_sn]').val());
                    listTable.filter['track_shipping_sn'] = $.trim($('input[name=track_shipping_sn]').val());
                    listTable.filter['searchType'] = $('input[name=searchType]:checked').val();
                    listTable.loadList();
		}
		//]]>
	</script>
        <div class="main">
		<div class="main_title">
                    <span class="l">订单管理 >> 天猫订单跟踪</span>
                    <?php if (check_perm('order_track_edit')): ?>
                    <span class="r"><a href="order_track/send" class="add" target="_blank">订单发货</a></span>
                    <?php endif; ?>
		</div>
                <div class="blank5"></div>
                <div class="search_row">
                    <form name="search" action="javascript:search(); ">
			系统订单号：<input type="text" class="ts" name="order_sn" value="" style="width:100px;" />
			系统订单状态：<?php print form_dropdown('order_status',$order_status);?>
			天猫订单号：<input type="text" class="ts" name="track_order_sn" value="" style="width:100px;" />
                        <input type="radio" name="searchType" value="1" style="vertical-align: middle;">待天猫下单列表
                        <input type="radio" name="searchType" value="2" style="vertical-align: middle;">待天猫发货列表
                        <input type="radio" name="searchType" value="3" style="vertical-align: middle;">订单待收货/转发列表
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
                                    <th width="150">系统订单号</th>
                                    <th>系统下单时间</th>
                                    <th>系统订单状态</th>
                                    <th>天猫订单号</th>
                                    <th>天猫物流单号</th>
                                    <th>创建时间</th>
                                    <th width="100">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
                                <tr class="row">
                                    <td align="center"><a href ="order/info/<?php print $row->order_id; ?>" target="_blank"><?php print $row->order_sn; ?></a></td>
                                    <td><?php print $row->create_date; ?></td>
                                    <td><?php print $order_status[$row->order_status]; ?></td>
                                    <td><?php print $row->track_order_sn; ?></td>
                                    <td><?php print $row->track_shipping_sn; ?></td>
                                    <td><?php print $row->track_create_time; ?></td>
                                    <td>
                                        <?php if($row->order_status == 0 || $row->order_status == 1) :?>
                                        <a href="order_track/edit/<?php print $row->order_sn; ?>">编辑</a>
                                        <?php endif; ?>
                                        <?php if(!empty($row->track_shipping_sn)) :?>
                                        | <a href="order_track/send/<?php print $row->order_sn; ?>">发货</a>
                                        <?php endif; ?>
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
