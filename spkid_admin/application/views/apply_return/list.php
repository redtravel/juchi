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
		listTable.url = 'apply_return/index';
		function search(){
                    listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
                    listTable.filter['apply_id'] = $.trim($('input[name=apply_id]').val());
                    listTable.filter['user_name'] = $.trim($('input[name=user_name]').val());
                    listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
                    listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
                    listTable.filter['order_type'] = $.trim($('select[name=order_type]').val());
                    listTable.filter['provider_status'] = $.trim($('select[name=provider_status]').val());
                    listTable.filter['invoice_no'] = $.trim($('input[name=invoice_no]').val());
                    listTable.filter['apply_status'] = $.trim($('select[name=apply_status]').val());
                    listTable.loadList();
		}
                
                //取消退单
                function cancel_apply(apply_id){
                        var val = {};
                        var cancel_reason = window.prompt("请输入取消理由",""); 
                        if (cancel_reason) {
                            val['apply_id'] = apply_id;
                            val['cancel_reason'] = cancel_reason;
                            $.ajax({
                                url: '/apply_return/remove',
                                data: {apply_id:apply_id,cancel_reason:cancel_reason},
                                dataType: 'json',
                                type: 'POST',
                                success: function(result){
                                    if(result == 0)
                                    {
                                            alert('取消成功！');
                                            window.location = '/apply_return/index';
                                    }
                                }
                            });
                        }
                }
		//]]>
	</script>
        <div class="main">
		<div class="main_title"><span class="l">申请退货单管理 >> 申请退货单列表</span>
		</div>
        <div class="blank5"></div>
        <div class="search_row">
                <form name="search" action="javascript:search(); ">
                订单编号：<input type="text" class="ts" name="order_sn" id="order_sn" value="" style="width:100px;" />
                退货单编号：<input type="text" class="ts" name="apply_id" id="return_sn" value="" style="width:100px;" />
                退货人：<input type="text" class="ts" name="user_name" id="user_name" value="" style="width:100px;" />
                申请时间: <input type="text" name="start_time" id="start_time" />~<input type="text" name="end_time" id="end_time" />
                订单类型：<select name="order_type" id="order_type">
                            <option value="-1">订单类型</option>
                            <option value="0">普通订单</option>
                            <option value="1">第三方订单</option>
                         </select>
                供应商审核：<select name="provider_status" id="provider_status">
                                <option value="-1">供应商审核</option>
                                <option value="0">未审核</option>
                                <option value="1">已审核</option>
                           </select>
                运单号：<input type="text" class="ts" name="invoice_no" id="invoice_no" value="" style="width:100px;" />
                申请单状态：<select name="apply_status" id="apply_status">
                                <option value="-1">申请单状态</option>
                                <option value="0">待处理</option>
                                <option value="1">处理中</option>
                                <option value="2">已处理</option>
                                <option value="3">已取消</option>
                                <option value="4">原单拒收</option>
                           </select>
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
                        <th>申请编号</th>
                        <th>物流公司</th>
                        <th>运单号</th>
                        <th>订单号</th>
                        <th>退货人</th>
                        <th>退货件数</th>
                        <th>申请时间</th>
                        <th>操作</th>
                    </tr>
                    <?php foreach($list as $row): ?>
                    <tr class="row">
                        <td align="center"><?php print $row['apply_id']; ?></td>
                        <td><?php print $row['shipping_name']; ?></td>
                        <td><?php print $row['invoice_no']; ?></td>
                        <td><?php print $row['order_sn']; ?></td>
                        <td><?php print $row['sent_user_name']; ?></td>
                        <td><?php print $row['product_number']; ?></td>
                        <td><?php print $row['apply_time']; ?></td>
                        <td>
                            <a href="apply_return/info/<?php print $row['apply_id']; ?>" title="查看">查看</a>&nbsp;&nbsp;
                            <?php if ($row['is_process']) : ?>
                            <a href="order_return/add/<?php print $row['order_id']; ?>/<?php print $row['apply_id']; ?>" target="_blank">退货</a>&nbsp;&nbsp;
                            <?php endif; ?>
                            <?php if ($row['apply_status'] == 0) : ?>
                            <span style="color:green">待处理</span>&nbsp;&nbsp;
                            <span><a href="javascript:void(0);" onclick="cancel_apply(<?php print $row['apply_id']; ?>)">取消</a></span>&nbsp;&nbsp;
                            <?php elseif ($row['apply_status'] == 1) : ?>
                            <span style="color:green">处理中</span>
                            <?php elseif ($row['apply_status'] == 2) : ?>
                            <span style="color:red">已处理</span>
                            <?php elseif ($row['apply_status'] == 3) : ?>
                            <span style="color:red">已取消</span>
                            <?php elseif ($row['apply_status'] == 4) : ?>
                            <span><a href="order/info/<?php print $row['order_id']; ?>" style="color:red" target="_blank">原单拒收</a></span>
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
