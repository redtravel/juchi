<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		function redirect(url){
		if (!/*@cc_on!@*/0) {
			window.open(url,'_blank');        
		    } else {
			var a = document.createElement('a');            
			a.href = url;            
			a.target = '_blank';            
			document.body.appendChild(a);            
			a.click();
		    }
		}
		$(function(){
		      $("#print_provider_barcode").click(function(){
			  redirect("purchase_box/pruchase_provider_barcode/<?php print $purchase_info->purchase_code; ?>");
		      });
		      $("#print_provider_barcode_scaned").click(function(){
			  redirect("purchase_box/pruchase_provider_barcode_scaned/<?php print $purchase_info->purchase_code; ?>");
		      });
		});
	</script>
	<div class="main">
		<div class="main_title">
                    <span class="l">采购管理 &gt;&gt; 采购单商品</span> &nbsp;单号：<?php print $purchase_info->purchase_code; ?>
                    <span class="r">
                        <input id="print_provider_barcode_scaned" type="button" class="am-btn am-btn-secondary" value="打印已收货条码" class="button10"/>
                        <input id="print_provider_barcode" type="button" class="am-btn am-btn-secondary" value="打印需收货条码" class="button10"/>
                        [ <a href="/purchase/index">返回列表 </a>]
                    </span>
                </div>
		<div class="produce">
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/purchase/edit/<?php print $purchase_info->purchase_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/purchase/edit_product/<?php print $purchase_info->purchase_id; ?>'"><span>采购单商品</span></li>
	     </ul>

		<div class="pc base">
		<div id="goodsDiv">
			<table class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="13" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="150px">商品款号</th>
					<th>商品名称</th>
                                        <th>成本价</th>
					<th>供应商货号</th>
					<th>条码</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>颜色</th>
					<th>尺码</th>
                                        <th>过期日期</th>
                                        <th>生产批号</th>
					<th>预采购数量</th>
					<th>已收货数量</th>
					<th width="60px;">上架数量</th>
				</tr>
				<?php foreach($goods_list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?></td>
					<td><?php print $row->b_product_name; ?></td>
                                        <td><?php print $row->cost_price; ?></td>
					<td><?php print $row->provider_productcode; ?></td>
					<td><?php print $row->provider_barcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name."[".$row->provider_code."]"; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
                                        <td><?php print ($row->expire_date == '0000-00-00' || $row->expire_date == '0000-00-00 00:00:00' || $row->expire_date == '')?'无':$row->expire_date; ?></td>
                                        <td><?php print $row->production_batch; ?></td>
                                        <td><?php print $row->product_number; ?></td>					
					<td><?php print $row->product_finished_number; ?></td>
					<td><?php print intval($row->finish_product_number); ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="13" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div></div></div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>