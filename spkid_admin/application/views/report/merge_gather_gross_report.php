<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		function checkForm(){
			var eles = document.forms['theForm'];
			if (eles['start_time'].value=='' || eles['end_time'].value==''){
				alert('错误：请输入报表期间!');
				return false;
			}
			if(eles['end_time'].value < eles['start_time'].value ){
				alert('错误：期间的结束时间早于或等于开始时间!');
				return false;
			}
		}
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 销售成本毛利明细表(财审口径)</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form method="post" action="report/merge_gather_gross_report" name="theForm"  onsubmit = "return checkForm()">
				<select name="category_id">
		    		<option  value="0">--商品类别--</option>
		    		<?php echo $cat_list ?>
			    </select>&nbsp;
				<select name="brand_id">
					<option value="0">--商品品牌--</option>
					<?php foreach($brand_list as $item): ?>
	                <option value='<?php echo $item->brand_id;?>' <?php if ($item->brand_id == $brand_id): ?>selected<?php endif; ?>><?php echo $item->brand_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<select name="coop_id" id="coop_id" >
					<option value="0">--合作方式--</option>
					<?php foreach($coop_list as $item): ?>
	                <option value='<?php echo $item->cooperation_id;?>' <?php if ($item->cooperation_id == $cooperation_id): ?>selected<?php endif; ?>><?php echo $item->cooperation_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<select name="provider_id" id="provider_id" >
					<option value="0">--选择供应商--</option>
					<?php foreach($provider_list as $item): ?>
	                <option value='<?php echo $item->provider_id;?>' <?php if ($item->provider_id == $provider_id): ?>selected<?php endif; ?>><?php echo $item->provider_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<span style="color: #FF0000;font: 12px verdana;">*</span>期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if ((isset($list) && !empty($list)) || (isset($count) && !empty($count))): ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="14" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th rowspan="2">日期</th>
					<th rowspan="2">单号</th>
					<th rowspan="2">大类</th>
					<th rowspan="2">中类</th>
					<th rowspan="2">款号</th>
					<th rowspan="2">货号</th>
					<th rowspan="2">颜色</th>
					<th rowspan="2">规格</th>
					<th colspan="10">合计</th>
				</tr>
				<tr>
					<th>销售<br />数量</th>
					<th>销售额</th>
					<th>折扣额</th>
					<th>净销售</th>
					<th>无税<br>销售额</th>
				    <th>成本<br>进价</th>
				    <th>税率</th>
				    <th>不含税<br>进价</th>
				    <th>销售<br>毛利</th>
				    <th>销售<br>毛利率</th>
				</tr>
				<?php foreach ($list as $bcat): ?>
				<?php if (isset($bcat['sub_cat_list']) && !empty($bcat['sub_cat_list'])): ?>
				<?php foreach ($bcat['sub_cat_list'] as $key =>$item): ?>
				<?php if (isset($item['order_product_list']) && !empty($item['order_product_list'])): ?>
				<?php foreach ($item['order_product_list'] as $gkey =>$gitem): ?>
				<tr>
					<td align="center"><?php echo $gitem['finance_date'];?></td>
					<td align="center"><?php echo $gitem['order_sn'];?></td>
					<td align="left"><?php echo ($gkey == 0)?$gitem['parent_name']:'';?></td>
					<td align="left"><?php echo $gitem['cat_name'];?></td>
					<td align="left"><?php echo $gitem['product_sn'];?></td>
					<td align="left"><?php echo $gitem['provider_productcode'];?></td>
					<td align="left"><?php echo $gitem['color_name'];?></td>
					<td align="left"><?php echo $gitem['goods_number'];?></td>
					<td align="left"><?php echo $gitem['product_num'];?></td>
					<td align="left"><?php echo $gitem['formated_merge_sale_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_merge_discount_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_merge_netsale_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_merge_norate_netsale_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_cost_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_fax_percent'];?></td>
					<td align="left"><?php echo $gitem['formated_nofaxcost_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_gross_amount'];?></td>
					<td align="left"><?php echo $gitem['formated_gross_percent'];?></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>

				<tr>
					<td align="center"></td>
					<td align="center"></td>
					<td align="left"><?php echo ($item['order_product_count'] == 0)?$item['parent_name']:'';?></td>
					<td align="left"><?php echo $item['cat_name'];?>小计</td>
					<td align="left"><?php echo $item['product_sn'];?></td>
					<td align="left"><?php echo $item['provider_productcode'];?></td>
					<td align="left"><?php echo $item['color_name'];?></td>
					<td align="left"><?php echo $item['size_name'];?></td>
					<td align="left"><?php echo $item['product_num'];?></td>
					<td align="left"><?php echo $item['formated_merge_sale_amount'];?></td>
					<td align="left"><?php echo $item['formated_merge_discount_amount'];?></td>
					<td align="left"><?php echo $item['formated_merge_netsale_amount'];?></td>
					<td align="left"><?php echo $item['formated_merge_norate_netsale_amount'];?></td>
					<td align="left"><?php echo $item['formated_cost_amount'];?></td>
					<td align="left"><?php echo $item['formated_fax_percent'];?></td>
					<td align="left"><?php echo $item['formated_nofaxcost_amount'];?></td>
					<td align="left"><?php echo $item['formated_gross_amount'];?></td>
					<td align="left"><?php echo $item['formated_gross_percent'];?></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<tr>
					<td align="center"></td>
					<td align="center"></td>
					<td align="left" colspan="2"><?php echo $bcat['cat_name'];?>合计</td>
					<td align="left"><?php echo $bcat['product_sn'];?></td>
					<td align="left"><?php echo $bcat['provider_productcode'];?></td>
					<td align="left"><?php echo $bcat['color_name'];?></td>
					<td align="left"><?php echo $bcat['size_name'];?></td>
					<td align="left"><?php echo $bcat['product_num'];?></td>
					<td align="left"><?php echo $bcat['formated_merge_sale_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_merge_discount_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_merge_netsale_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_merge_norate_netsale_amount'];?></td>

					<td align="left"><?php echo $bcat['formated_cost_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_fax_percent'];?></td>
					<td align="left"><?php echo $bcat['formated_nofaxcost_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_gross_amount'];?></td>
					<td align="left"><?php echo $bcat['formated_gross_percent'];?></td>
				</tr>
				<?php endforeach; ?>

				<tr>
					<td align="center"></td>
					<td align="center"></td>
					<td align="left" colspan="2"><?php echo $total['cat_name'];?>合计</td>
					<td align="left"><?php echo $total['product_sn'];?></td>
					<td align="left"><?php echo $total['provider_productcode'];?></td>
					<td align="left"><?php echo $total['color_name'];?></td>
					<td align="left"><?php echo $total['size_name'];?></td>
					<td align="left"><?php echo $total['product_num'];?></td>
					<td align="left"><?php echo $total['formated_merge_sale_amount'];?></td>
					<td align="left"><?php echo $total['formated_merge_discount_amount'];?></td>
					<td align="left"><?php echo $total['formated_merge_netsale_amount'];?></td>
					<td align="left"><?php echo $total['formated_merge_norate_netsale_amount'];?></td>

					<td align="left"><?php echo $total['formated_cost_amount'];?></td>
					<td align="left"><?php echo $total['formated_fax_percent'];?></td>
					<td align="left"><?php echo $total['formated_nofaxcost_amount'];?></td>
					<td align="left"><?php echo $total['formated_gross_amount'];?></td>
					<td align="left"><?php echo $total['formated_gross_percent'];?></td>
				</tr>

				<tr>
					<td colspan="14" class="bottomTd"> </td>
				</tr>
			</table><?php endif; ?>
	  		<div class="blank5"></div>
		  </div>
		</div>
	<?php include_once(APPPATH.'views/common/footer.php'); ?>
