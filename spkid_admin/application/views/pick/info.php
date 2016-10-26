<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	var sn='';
	var invoice_no='';
	var pick_sn='<?php print $pick->pick_sn?>';
	
	function tick(odd_sn){
		if($('#float_advice').dialog('isOpen')){
			odd_sn = $(':hidden[name=odd_sn]').val();
			var odd_advice=$.trim($(':input[name=odd_advice]').val());
			if(odd_advice==''){
				alert('请填写意见');
				return false;
			}
			$.ajax({
				url:'pick/tick',
				data:{odd_sn:odd_sn,pick_sn:pick_sn,odd_advice:odd_advice,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.msg) alert(result.msg);
					if(result.err) return false;
					var tr=$('tr[sn='+odd_sn+']');
					tr.remove();
					if(sn==odd_sn){
						sn='';
						invoice_no='';
					}
					$(':input[name=odd_advice]').val('');
					$('#float_advice').dialog('close')
				}
			});

		}else{
			$(':hidden[name=odd_sn]').val(odd_sn);
			$('#float_advice').dialog('open')
		}
	}
	
	$(function(){
		$('#scan_input').focus();
		$('#float_advice').dialog({autoOpen:false,width:300,modal:true,resizable:false,title:'标记问题单：请填写意见'});
	});
		
	//]]>
</script>
	<div class="main">
		<div class="main_title"><span class="l">拣货单详情【<?php print $pick->pick_sn;?>】</span><span class="r"><a href="pick" class="add">拣货单列表</a></span></div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr>
					<th>订单、换货单号</th>
					<th>快递单号</th>
                                        <th>订单状态</th>
					<th>操作</th>
				</tr>
				<?php foreach($pick_info as $row):?>
				<tr sn="<?php print $row->sn;?>">
					<td class="item_title" width="150">
						<?php if($pick->type=='change'):?>
						<a href="order_change/edit/<?php print $row->change_id;?>" target="_blank"><?php print $row->change_sn;?></a>
						<?php else:?>
						<input type="hidden" name="order_ids[]" value="<?php print $row->order_id;?>" />
						<a href="order/info/<?php print $row->order_id;?>" target="_blank"><?php print $row->order_sn;?></a>
						<?php endif;?>
					</td>
					<td><?php print $row->invoice_no;?></td>
                                        <td><?php print implode('&nbsp;',format_order_status($row,false,true)); ?></td>
					<td>
						<?php if($row->shipping_status==0):?><a href="javascript:void()" onclick="tick('<?php print $row->sn;?>')">标记为问题单</a><?php endif;?>
					</td>
				</tr>
				<?php endforeach;?>
				
				<tr>
					<td colspan="6" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<div id="float_advice" style="display:none;">
	<input type="hidden" name="odd_sn" value="" />
	<textarea name="odd_advice" class="log"></textarea><br/>
	<input type="button" name="btn_advice" value="提交" onclick="tick('')" />
</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>