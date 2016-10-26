<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
	$(function(){
	    $('.inputEdit').bind('dblclick',function () {
		var text=parseInt($(this).text());
		var input='<input type="text" value="'+text+'" style="width:30px;text-align:center" box_id="'+$(this).attr("box_id")+'" box_sub_id="'+$(this).attr("box_sub_id")+'"/>';
		$(this).html(input);
		re_bind();
		$(this).children('input').focus();
	    });
	});
	function re_bind(){
	    $('span.inputEdit > input').bind('blur',function () {
		    var p = $(this).parent();
		    var val = $(this).val();
		    if(!/^\d+$/.test(val)){
			alert('只能输入正整数和0');
			return;
		    }
		    var val=parseInt(val);
		   
		    //AJAX
		    var box_id = $(this).attr("box_id");
		    var box_sub_id = $(this).attr("box_sub_id");
		    $.ajax({
			url: '/purchase_box/proc_edit_purchase_num/',
			data: {is_ajax:1,rnd : new Date().getTime(),box_id :box_id ,box_sub_id :box_sub_id,box_sub_val:val},
			dataType: 'json',
			type: 'POST',
			success: function(result){
			    if(result.err == 0)
			    {
				p.html(val);
			    }
			    if(result.err == 2)
			    {
				p.html(result.val);
			    }
			    alert(result.msg);
			}
		    });
	    });
	  }

	 function cancel_purchase_scan(purchase_code){
	    if(!confirm('确定取消此次采购的所有收货扫描？'))return;
	    $.ajax({
	            url: '/purchase_box/cancel_purchase_scan/'+purchase_code,
	            data: {is_ajax:1,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err == 0)
	                {
			    location.href=location.href;
	                }
	            }
	        });
	}
	function cancel_purchase_box_scan(purchase_box_id){
	if(!confirm('确定取消此箱子的收货扫描？'))return;
	    $.ajax({
	            url: '/purchase_box/cancel_purchase_box_scan/'+purchase_box_id,
	            data: {is_ajax:1,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.err == 0)
	                {
			    location.href=location.href;
	                }
	            }
	        });
	}
	
	function proc_box_prodcut_statistics(purchase_box_id){
	    $.ajax({
	            url: '/purchase_box/proc_box_prodcut_statistics/'+purchase_box_id,
	            data: {is_ajax:1,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
			alert(result.msg);
	            }
	        });
	}
    </script>
    <div class="main">
	    <div class="main_title"><span class="l">扫描收货 &gt;&gt; 扫描记录</span></div>
	    <div class="blank5"></div>
	    <div class="search_row">
            <table>
                <tr>
                    <td align="right">采购单编号:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_code?>
			<?php if(check_perm('cancel_purchase_scan') ):?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" class="button10" onclick="cancel_purchase_scan('<?=$purchase->purchase_code?>');" value="取消此采购单所有收货" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			    <? endif;?>
		    </td>
		     <td align="right">供应商:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->provider_name?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">预计收货数量:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">已收货数量:</td>
                    <td>&nbsp;&nbsp;<?=$purchase->purchase_finished_number?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right">箱子数量:</td>
                    <td>&nbsp;&nbsp;<?=count($box_list)?></td>
                </tr>
            </table>
	    </div>
	    <div id="listDiv">
		    <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			    <tr>
				<td colspan="14" class="topTd"> </td>
			    </tr>
			    <tr class="row">
			      <th>条码</th>
			      <th>货号</th>
			      <th>商品名称</th>
			      <th>品牌</th>
			      <th>款号</th>
			      <th>颜色</th>
			      <th>规格</th>
			      <th>生产批号</th>
			      <th>有效期</th>
                              <th>外观验收</th>
                              <th>检查数量</th>
			      <th>收货数量</th>
			      <th>上架数量</th>
			      <th width="120px;">操作</th>
			    </tr>
			    <?php foreach($box_list as $row): ?>
			    <tr>
				<td colspan="13">
				    箱号【<?=$row->box_code?>】
				    收货总件数【<?=$row->product_number?>】
				    上架总件数【<?=$row->product_shelve_num?>】
				    收货人【<?=$row->scan_name?>】
				    上架人【<?=$row->shelve_name?>】
                                    到货日期【<?=$row->delivery_date?>】
				</td>
				<td rowspan="<?=count($row->box_sub_list)+1?>">
				    <?php if(check_perm('cancel_purchase_box_scan')):?><input type="button" class="am-btn am-btn-primary" onclick="cancel_purchase_box_scan(<?=$row->box_id?>)" value="取消此箱收货" style="margin: 2px;"/><? endif;?>
				    <?php if(check_perm('purchse_edit_p_num')):?><input type="button" class="am-btn am-btn-primary" onclick="proc_box_prodcut_statistics(<?=$row->box_id?>)" value="审核对应入库单" style="margin: 2px;"/><? endif;?>
				</td>
			    </tr>
			    <?php foreach($row->box_sub_list as $sub): ?>
			    <tr class="row" <?php if(intval($sub->product_number) != intval($sub->over_num)) echo 'style="background-color:#f3ff9e"';?>>
				<td><?=$sub->provider_barcode?></td>
				<td><?=$sub->provider_productcode?></td>
				<td><?=$sub->product_name?></td>
				<td><?=$sub->brand_name?></td>
				<td><?=$sub->product_sn?></td>
				<td><?=$sub->color_name?></td>
				<td><?=$sub->size_name?></td>
				<td><?=$sub->production_batch?></td>
				<td><?php print ($sub->v_expire_date == '0000-00-00' || $sub->v_expire_date == '0000-00-00 00:00:00' || $sub->v_expire_date == '')?'无':$sub->v_expire_date; ?></td>
                                <td><?=$sub->oqc?></td>
                                <td><?=$sub->check_num?></td>
				<td><span <?php if(check_perm('purchse_edit_p_num') && intval($sub->product_number) != intval($sub->over_num)): ?> box_id="<?=$row->box_id?>" box_sub_id="<?=$sub->box_sub_id?>" class='inputEdit'<?php endif;?>>
				    <?=intval($sub->product_number)?>
				    </span>
				</td>
				<td><?=intval($sub->over_num)?></td>
			    </tr>
			    <?php endforeach; ?>
			    <?php endforeach; ?>
			    <tr>
				<td colspan="14" class="bottomTd"> </td>
			    </tr>
		    </table>
		    <div class="blank5"></div>
	    </div>
    </div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
