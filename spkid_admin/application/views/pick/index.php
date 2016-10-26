<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript">
		//<![CDATA[
$(function(){
      $('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
      $('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
      bind_print();
});

function bind_print(){
    $('a[tag=print_flag]').click(function(obj){set_print($(obj.target));});
}
function set_print(obj){
  var pick_sn = obj.attr("pick_sn");
  var flag = obj.attr("class")=="icon_print"?0:1;
  $.post("/pick/set_print_flag",{pick_sn:pick_sn,flag:flag},function(data){
	      data = jQuery.parseJSON(data);
	      if(data.err==1){
		  alert(data.msg);
		  return;
	      }
	      if(data.flag == 1){
		  $(obj).attr("title","已打印").attr("class","icon_print");
	      }else{
		  $(obj).attr("title","未打印").attr("class","icon_printoff");
	      }
       });
}
listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
listTable.filter.page = '<?php echo $filter['page']; ?>';
listTable.url = 'pick/index';

function search(){
      listTable.filter['pick_sn'] = $.trim($('input[type=text][name=pick_sn]').val());
      listTable.filter['order_sn'] = $.trim($('input[type=text][name=order_sn]').val());
      listTable.filter['start_date'] = $.trim($('input[type=text][name=start_date]').val());
      listTable.filter['end_date'] = $.trim($('input[type=text][name=end_date]').val());
      listTable.filter['over'] = $.trim($('select[name=over]').val());
      listTable.filter['pick'] = $.trim($('select[name=pick]').val());
      listTable.filter['is_print'] = $.trim($('input[type=checkbox][name=is_print]:checked').val());
      listTable.loadList();
}
		
		//]]>
</script>
	<div class="main">
		<div class="main_title">
			<span class="l">拣货单列表</span>
			<span class="r">
			    <a href="pick/scan_pick" class="add" target="_blank">扫描拣货</a>
			    <a href="pick/print_main" class="add">运单销售单打印</a>
			    <a href="pick/overview" class="add">新增拣货单</a>
			</span>
		</div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			拣货单号：<input type="text" class="ts" name="pick_sn" value="" style="width:100px;" />
			订换货单号：<input type="text" class="ts" name="order_sn" value="" style="width:100px;" />
			导出时间：<input type="text" class="ts" name="start_date" value="" style="width:100px;" />
			- <input type="text" class="ts" name="end_date" value="" style="width:100px;" />
			<select name="pick">
				<option value="-1">拣货状态</option>
				<option value="0">未拣</option>
				<option value="2">已拣</option>
			</select>
			<select name="over">
				<option value="-1">复核状态</option>
				<option value="0">未复核</option>
				<option value="1">已复核</option>
			</select>
			<label>已打印:<input type="checkbox" name="is_print" value="1"/></label>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
                        <input type="button" name="" value="批量打印装箱单" class="am-btn am-btn-primary" onclick="print_picks();"/>
			</form>
                    
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="13" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="120px"><input type="checkbox" id="pick_ids" value="1"/>拣货单号</th>
					<th>类型</th>
					<th>配送方式</th>
					<th>创建人</th>
					<th>拣货状态</th>
					<th>拣货人</th>
					<th>复核人</th>
					<th>总单数</th>
					<th>复核单数</th>
					<th>打印</th>
					<th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td style="width: 150px;"><input type="checkbox" name="pick_id" value="<?php print $row->pick_id; ?>"/><a href="pick/info/<?php print $row->pick_sn; ?>">
					<?php print $row->pick_sn; ?></a></td>
					<td><?php print $this->pick_type[$row->type]; ?></td>
					<td><?php print $row->shipping_name; ?></td>
					<td><?php print $row->admin_name.'/'.$row->create_date; ?></td>
					<td><?php print $row->pick_status; ?></td>
					<td><?php if(!empty($row->pick_user)) print $row->pick_user.'/'.$row->pick_date; ?></td>
					<td><?php if(!empty($row->qc_user))print $row->qc_user.'/'.$row->qc_date; ?></td>
					<td><?php print $row->total_num; ?></td>
					<td><?php print $row->over_num; ?></td>
					<td>
						<a href="pick/print_pick/<?php print $row->pick_sn; ?>" target="_blank" class="icon_jian" title="打印拣货单" ></a>
						<a href="pick/print_order/<?php print $row->pick_sn; ?>" target="_blank" class="icon_xiang" title="打印包裹装箱单" ></a>
						<a href="pick/print_main/<?php print $row->pick_sn; ?>" target="_blank" class="icon_yun" title="打印运单" ></a>
					</td>
					<td>
					    <a tag="print_flag" href="javascript:void(0);" 
					       pick_sn="<?php print $row->pick_sn; ?>" 
		<?php if($row->is_print): ?> class="icon_print" title="已打印"  <?php else: ?>class="icon_printoff" title="未打印"<?php endif;?>></a>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="13" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<script type="text/javascript">
$(document).on('click', '#pick_ids', function (e) {
    var v_checked = $(this).prop('checked');
    $("input[name=pick_id]").each(function(i, obj){
        if (v_checked){
            $(obj).prop('checked', true);
        } else {
            $(obj).removeAttr('checked');
        }
    });
});
function print_picks(){
    var ids = new Array();
    $("input[name=pick_id]:checked").each(function(i, obj){
        ids[i] = $(obj).val();
    });
    var ids2 = ids.join('-');
    if (ids2 == '') return;
    window.location.href='/pick/print_orders/'+ids2;
}
</script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
