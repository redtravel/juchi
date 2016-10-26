<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/jquery.scrollTo.js"></script>
	<script type="text/javascript" src="public/js/My97DatePicker/WdatePicker.js"></script>
	<script type="text/javascript">
    	
		//<![CDATA[
		//listTable.filter.page_count = '<?php //echo $filter['page_count']; ?>';
		//listTable.filter.page = '<?php //echo $filter['page']; ?>';
		listTable.url = '/order_routing';
		function search() {
			listTable.filter['source_id'] = $.trim($('select[name=source_id]').val());
			listTable.filter['shipping_id'] = $.trim($('select[name=shipping_id]').val());
			listTable.filter['pay_id'] = $.trim($('select[name=pay_id]').val());
			listTable.loadList();
		}
		
		function add(){
	        var routing_id = $('input[name=a_routing_id]').val();
	        var source_id = parseInt($('select[name=a_source_id]').val());
	        if(isNaN(source_id)||source_id<1){
	            alert('请选择订单来源!');
	            return;
	        }
	        var shipping_id = parseInt($('select[name=a_shipping_id]').val());
	        if(isNaN(shipping_id)||shipping_id<1){
	            alert('请选择配送方式!');
	            return;
	        }
	        var pay_id = parseInt($('select[name=a_pay_id]').val());
	        if(isNaN(pay_id)||pay_id<1){
	            alert('请选择支付方式!');
	            return;
	        }
	        var show_type = parseInt($('select[name=a_show_type]').val());
	        if(isNaN(show_type)||show_type<1){
	            alert('请选择显示方式!');
	            return;
	        }
	        var routing = $.trim($('select[name=a_routing]').val());
	        if(routing!='F' && routing!='S'){
	            alert('请选择订单流程!');return false;
	        }
	        var data = {
				routing_id : routing_id,
				source_id : source_id,
				shipping_id:shipping_id,
				pay_id:pay_id,
				show_type:show_type,
				routing:routing,
				rnd : new Date().getTime()
			};
			$.post('/order_routing/save', data, function(result) {
				result = eval('(' + result + ')');
				if(result.msg) {
					alert(result.msg);
				}
				if(result.error!=0){
	                return false;
	            }
	            if(result.routing_id>0){
	                init_form();
	            }
	            search();
			});
	    }
	    
	    function del(routing_id){
	        if(!confirm('确定删除？')){
	            return false;
	        }
	        $.post('/order_routing/del', {routing_id:routing_id}, function(result) {
	        	result = eval('(' + result + ')');
	        	if(result.msg!=''){
	                alert(result.msg);
	            }
	            if(result.error!=0){
	                return false;
	            }
	            search();
	        });
	    }
	    
	    function edit(routing_id,source_id,shipping_id,pay_id,show_type,routing){
	        $('input[type=hidden][name=a_routing_id]').val(routing_id);
	        $('select[name=a_source_id]').val(source_id).attr('disabled',true);
	        $('select[name=a_shipping_id]').val(shipping_id).attr('disabled',true);
	        $('select[name=a_pay_id]').val(pay_id).attr('disabled',true);
	        $('select[name=a_show_type]').val(show_type);
	        $('select[name=a_routing]').val(routing).attr('disabled',true);
	        $('input[type=button][name=add]').val('更新');
	        $.scrollTo('div#routing_edit_div',800);
	    }
	    
	    function init_form(){
	        $('input[type=hidden][name=a_routing_id]').val('');
	        $('select[name=a_source_id]').val(0).attr('disabled',false);
	        $('select[name=a_shipping_id]').val(0).attr('disabled',false);
	        $('select[name=a_pay_id]').val(0).attr('disabled',false);
	        $('select[name=a_show_type]').val(0);
	        $('select[name=a_routing]').val('').attr('disabled',false);
	        $('input[type=button][name=add]').val('添加');
	    }
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">订单流程配置</span>
		</div>

		<div class="blank5"></div>
		
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
				<?php print form_dropdown('source_id',get_pair($all_source,'source_id','source_name', array(''=>'订单来源'))); ?>
				<?php print form_dropdown('shipping_id',get_pair($all_shipping,'shipping_id','shipping_name', array(''=>'配送方式'))); ?>
				<?php print form_dropdown('pay_id',get_pair($all_payment,'pay_id','pay_name', array(''=>'支付方式'))); ?>
				<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		
		<div class="blank5"></div>
		<div style="height:5px;"></div>
		
		<div id="routing_edit_div" class="search_row">
		    <input type="hidden" value="" name="a_routing_id">
			<?php print form_dropdown('a_source_id',get_pair($all_source,'source_id','source_name', array(''=>'订单来源'))); ?>
			<?php print form_dropdown('a_shipping_id',get_pair($all_shipping,'shipping_id','shipping_name', array(''=>'配送方式'))); ?>
			<?php print form_dropdown('a_pay_id',get_pair($all_payment,'pay_id','pay_name', array(''=>'支付方式'))); ?>
			<?php print form_dropdown('a_show_type',$all_show_type); ?>
			<?php print form_dropdown('a_routing',$all_routing); ?>
		    <input type="button" class="am-btn am-btn-primary" onclick="add();" value="添加" name="add">
		</div>
		
		<div class="blank5"></div>
		
		<div id="listDiv">
<?php endif; ?>
			
			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="5" class="topTd"> </td>
				</tr>
				<tr class="row">
	                <th>订单来源</th>
	                <th>配送方式</th>
	                <th>支付方式</th>
	                <th>显示方式</th>
	                <th>操作流程</th>
	                <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<?php foreach($row['shipping_list'] as $shipping_key => $shipping): ?>
				<?php foreach($shipping['pay_list'] as $pay_key => $pay): ?>
				<tr class="row">
					<?php if($shipping_key==0 && $pay_key==0): ?>
					<td align="center" rowspan="<?php print $row['span_count']; ?>"><?php print $row['source_name']; ?></td>
					<?php endif; ?>
					<?php if($pay_key==0): ?>
					<td align="center" rowspan="<?php print $shipping['span_count']; ?>"><?php print $shipping['shipping_name']; ?></td>
					<?php endif; ?>
					<td align="center"><?php print $pay->pay_name; ?></td>
					<td align="center"><?php print $all_show_type[$pay->show_type]; ?></td>
					<td align="center"><?php print $all_routing_show[$pay->routing]; ?></td>
					<td align="center">
						<a onclick="del(<?php print $pay->routing_id; ?>)" href="javascript:void(0);">删除</a>
						<a onclick="edit(<?php print $pay->routing_id; ?>,<?php print $pay->source_id; ?>,<?php print $pay->shipping_id; ?>,<?php print $pay->pay_id; ?>,<?php print $pay->show_type; ?>,'<?php print $pay->routing; ?>');return false;" href="javascript:void(0);">修改</a>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<tr>
					<td colspan="5" class="bottomTd"> </td>
				</tr>
			</table>
			
			<div class="blank5"></div>
			
<?php if($full_page): ?>
		</div>
		
		<div class="blank5"></div>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
