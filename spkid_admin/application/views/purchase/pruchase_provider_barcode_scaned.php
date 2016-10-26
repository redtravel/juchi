<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script>
	$(function(){
	    $(':checkbox[name=check_all_box]').prop('checked',true);
	    $(':checkbox[flg=product_id]').prop('checked',true);
	    $(':checkbox[name=check_all_box]').prop(function(){check_all();});
	});
	function check_all() {
	    if($(':checkbox[name=check_all_box]').prop('checked'))
		    $(':checkbox[flg=product_id]').prop('checked',true);
	    else
		    $(':checkbox[flg=product_id]').prop('checked',false);
	}
	function _print(){
	    $("#action_form").attr("action","purchase_box/print_provider_barcode");
	    $("#action_form").submit();
	}
	
	function _export(){
	    $("#action_form").attr("action","purchase_box/export_provider_barcode");
	    $("#action_form").submit();
	}
    </script>
    <div class="main">
	    <div class="main_title"><span class="l">采购管理 &gt;&gt; 打印已收货条码</span></div>
	    <div class="blank5"></div>
	    <div class="search_row">
            <table style="width:60%">
                <tr>
                    <td align="left">采购单编号:&nbsp;&nbsp;<?=$purchase->purchase_code?></td>
                </tr>
            </table>
	    </div>
	    <div id="listDiv">
		<form id="action_form" action="purchase_box/print_provider_barcode" method="POST" target="_blank">
		    <input name="purchase_code"  type="hidden" value="<?=$purchase->purchase_code?>"/>
		    <input name="type"  type="hidden" value="scaned"/>
		    <br />
		    <input type="button" class="am-btn am-btn-primary" value="提交打印" onclick ="_print();"/>
		    <input type="button" class="am-btn am-btn-primary"  value="导出Excel" onclick="_export();"/>
		    <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
			    <tr>
                                <td colspan="7" class="topTd"> </td>
			    </tr>
			    <tr class="row">
			      <th><input name="check_all_box" type="checkbox"/></th>
			       <th>序号</th>
			      <th>款号</th>
			      <th>名称</th>
			      <th>货号</th>
			      <th>颜色</th>
			      <th>规格</th>
			      <th>条码</th>
			      <th>打印条码数</th>
			    </tr>
			    <?php $history = "";$index = 0; foreach($list as $sub): ?>
			    <tr class="row">
				<td><input type="checkbox" flg="product_id" name="product_id[]" value="<?=$sub->sub_id?>"/></td>
				<td>
				    <?php if(empty($history)){
					    $history =$sub->product_sn;
					    $index+=1;
					}elseif ($history != $sub->product_sn) {
					    $history =$sub->product_sn;
					    $index+=1;
					}?>
				    <span><?=$index?></span>
				    <input type="hidden" name="in_<?=$sub->sub_id?>" value="<?=$index?>"/>
				</td>
				<td><?=$sub->product_sn?></td>
				<td><?=$sub->product_name?></td>
				<td><?=$sub->provider_productcode?></td>
				<td><?=$sub->color_name?></td>
				<td><?=$sub->size_name?></td>
				<td><?=$sub->provider_barcode?></td>
				<td>1<input name="p_<?=$sub->sub_id?>" type="hidden" value="1"/></td>
			    </tr>
			    <?php endforeach; ?>
			    <tr>
				<td colspan="7" class="bottomTd"> </td>
			    </tr>
		    </table>
		    <input type="button" class="am-btn am-btn-primary" value="提交打印" onclick="_print();"/>
		    <input type="button" class="am-btn am-btn-primary"  value="导出Excel" onclick="_export();"/>
		    </form>
		    <div class="blank5"></div>
	    </div>
    </div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
