<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
		<script type="text/javascript">
		//<![CDATA[
		  $(function(){
			$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$('input[type=text][name=create_start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$('input[type=text][name=create_end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		  });
		function selectRadio(name,endDate,startDate){
            $('input[type=text][name='+name+'start_date]').val(startDate);
            $('input[type=text][name='+name+'end_date]').val(endDate);
        }
		listTable.url = 'pick/scan_shipping_list';
	
		function search(){
			listTable.filter['invoice_sn'] = $.trim($('input[type=text][name=invoice_sn]').val());
			listTable.filter['order_sn'] = $.trim($('input[type=text][name=order_sn]').val());
			listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
			listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
			listTable.filter['start_time'] = $.trim($('#start_time').val());
			listTable.filter['end_time'] = $.trim($('#end_time').val());
            //订单创建时间BABY-583
			listTable.filter['create_start_date'] = $.trim($('input[type=text][name=create_start_date]').val());
			listTable.filter['create_end_date'] = $.trim($('input[type=text][name=create_end_date]').val());
			listTable.filter['create_start_time'] = $.trim($('#create_start_time').val());
			listTable.filter['create_end_time'] = $.trim($('#create_end_time').val());
            //订单创建时间BABY-583 end
			listTable.filter['shipping_status'] = $.trim($('select[name=shipping_status]').val());
			listTable.filter['shipping_id'] = $.trim($('select[name=shipping_id]').val());
			listTable.loadList();
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">发货列表</span><span class="r"><a href="pick/scan_shipping" class="add" target="_blank">扫描发货</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form action="" method="post">
			订单号：<input type="text" class="ts" name="order_sn" value="" style="width:100px;" />
			运单号：<input type="text" class="ts" name="invoice_sn" value="" style="width:100px;" />
            <select name="shipping_status">
                <option value="-1">发货状态</option>
                <option value="0">未进入拣货单</option>
                <option value="1">进入拣货单未拣货</option>
                <option value="2">已拣货未复核</option>
                <option value="3">已复核未发运</option>
                <option value="4">已发运</option>
            </select>
            <select name="shipping_id">
            <option value="-1">快递公司</option>
            <?php foreach ($shipping_list as $row) : ?>
            <option value="<?php print $row->shipping_id; ?>"><?php print $row->shipping_name; ?></option>
            <?php endforeach ?>
            </select>
            <br>
			发货时间：<input type="text" class="ts" name="start_date" value="" style="width:100px;" /><input type="text" class="ts" name="start_time" id="start_time" style="width:80px;" value="00:00:00" />
			- <input type="text" class="ts" name="end_date" value="" style="width:100px;" /><input type="text" class="ts" name="end_time" id="end_time" style="width:80px;" value="23:59:59" />
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>');">今天
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))); ?>');">昨天
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y"))); ?>');">近3天
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y"))); ?>');">近1周
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-13,date("Y"))); ?>');">近2周
            <input type="radio" name="shipping_date_radio" onclick="selectRadio('','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-20,date("Y"))); ?>');">近3周
            <br>
            订单创建时间：
            <input type="text" class="ts" name="create_start_date" value="" style="width:100px;" />
            <input type="text" class="ts" name="create_start_time" id="create_start_time" style="width:80px;" value="00:00:00" />
			- 
            <input type="text" class="ts" name="create_end_date" value="" style="width:100px;" />
            <input type="text" class="ts" name="create_end_time" id="create_end_time" style="width:80px;" value="23:59:59" />
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>');">今天
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))); ?>');">昨天
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y"))); ?>');">近3天
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y"))); ?>');">近1周
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-13,date("Y"))); ?>');">近2周
            <input type="radio" name="create_date_radio" onclick="selectRadio('create_','<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d",mktime(0,0,0,date("m"),date("d")-20,date("Y"))); ?>');">近3周
			<br>
            <input type="button" class="am-btn am-btn-primary" value="搜索" onclick="javascript:search();" />
            <input type="submit" name="export" class="am-btn am-btn-primary" value="导出"/>
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; if ($list) : ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="14" class="topTd"> </td>
                </tr>
                <tr class="row">
                    <th width="120px">订单号</th>
                    <th>订单时间</th>
                    <th>运单号</th>
                    <th>发货状态</th>
                    <th>发货时间</th>
                    <th>快递公司</th>
                    <th>送货地址</th>
                    <th>配送地址</th>
                    <th>收货人</th>
                    <th>包裹金额</th>
                    <th>付款方式</th>
                    <th>待收货款金额</th>
                    <th>订单理论重量</th>
                    <th>订单实际重量</th>
                    <?php if($perm_edit): ?>
                    <th>操作</th>
                    <?php endif; ?>
		</tr>
                <?php foreach($list as $row): ?>
                <tr class="row">
                    <td><a target="_blank" href="order/info/<?php print $row->order_id; ?>"><?php print $row->order_sn; ?></a></td>
                    <td><?php print $row->create_date; ?></td>
                    <td><?php print $row->invoice_no; ?></td>
                    <td><?php print ($row->shipping_status) ? '已发货' : '未发货'; ?></td>
                    <td><?php print $row->shipping_date; ?></td>
                    <td><?php print $row->shipping_name; ?></td>
                    <td><?php print $row->province." ".$row->city." ".$row->district; ?></td>
                    <td><?php print $row->address; ?></td>
                    <td><?php print $row->consignee; ?></td>
                    <td><?php print $row->order_amount; ?></td>
                    <td><?php print $row->pay_name; ?></td>
                    <td><?php print $row->paid_money; ?></td>
                    <td><?php print $row->order_weight_unreal; ?></td>
                    <td><?php print $row->recheck_weight_unreal; ?></td>
                    <?php if($perm_edit): ?>
                    <td>
                        <a class="edit" href="pick/scan_shipping_edit/<?php echo  $row->order_id; ?>" title="编辑快递信息"></a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="14" class="bottomTd"> </td>
                </tr>
            </table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
            </div>
          <?php endif; ?>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
