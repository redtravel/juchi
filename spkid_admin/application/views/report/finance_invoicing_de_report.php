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
    <div class="main_title"><span class="l">报表管理 >> 财务进销存明细表(财审口径)</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form method="post" action="report/finance_invoicing_de_report" name="theForm"  onsubmit = "return checkForm()">
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
				<select name="color_id" id="color_id" >
					<option value="0">--商品颜色--</option>
					<?php foreach($color_list as $item): ?>
	                <option value='<?php echo $item->color_id;?>' <?php if ($item->color_id == $color_id): ?>selected<?php endif; ?>><?php echo $item->color_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<select name="size_id" id="size_id" >
					<option value="0">--商品规格--</option>
					<?php foreach($size_list as $item): ?>
	                <option value='<?php echo $item->size_id;?>' <?php if ($item->size_id == $size_id): ?>selected<?php endif; ?>><?php echo $item->size_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<select name="coop_id" id="coop_id" >
					<option value="0">--合作方式--</option>
					<?php foreach($coop_list as $item): ?>
	                <option value='<?php echo $item->cooperation_id;?>' <?php if ($item->cooperation_id == $cooperation_id): ?>selected<?php endif; ?>><?php echo $item->cooperation_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<select name="depot_id" id="depot_id" >
					<option value="0">--选择仓库--</option>
					<?php foreach($depot_list as $item): ?>
	                <option value='<?php echo $item->depot_id;?>' <?php if ($item->depot_id == $depot_id): ?>selected<?php endif; ?>><?php echo $item->depot_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				<span style="color: #FF0000;font: 12px verdana;">*</span>期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />

     			商品款号 <input type="text" name="product_sn" value="<?php echo $product_sn;?>" size="15" />
                        商品ID <input type="text" name="product_id" value="<?php echo $product_id;?>" size="15" />
    			<!-- 关键字 -->
    			商品名称 <input type="text" name="keyword" value="<?php echo $keyword;?>" size="15" />

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
				    <th rowspan="2">品牌</th>
					<th rowspan="2">ERP大类</th>
					<th rowspan="2">ERP中类</th>
					<th rowspan="2">商品款号</th>
					<th rowspan="2">颜色</th>
					<th rowspan="2">尺码</th>
					<th rowspan="2">商品名称</th>
					<th rowspan="2">成本单价</th>
					<th rowspan="2">日期</th>
					<th rowspan="2">单号</th>
					<th colspan="2">期初</th>
					<th colspan="3">进</th>
					<th colspan="3">出</th>
					<th colspan="3">结存</th>
				</tr>
				<tr>
				  <th colspan="1">数量</th>
			      <th colspan="1">金额</th>
				  <th colspan="1">出入库类型</th>
			      <th colspan="1">数量</th>
			      <th colspan="1">金额</th>
			      <th colspan="1">出入库类型</th>
			      <th colspan="1">数量</th>
			      <th colspan="1">金额</th>
			      <th colspan="1">数量</th>
			      <th colspan="1">金额</th>
			      <th colspan="1">不含税金额</th>
				</tr>

				<?php foreach ($list as $result_key =>$arr_rs): ?>
				<tr>
					  <?php if($arr_rs['productfirst'] == 1): ?>
				  	  <td rowspan="<?php echo $arr_rs['rec_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['brand_name'];?></div></td>
				  	  <td rowspan="<?php echo $arr_rs['rec_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['pcatname'];?></div></td>
					  <td rowspan="<?php echo $arr_rs['rec_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['catname'];?></div></td>
			          <td rowspan="<?php echo $arr_rs['rec_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['product_sn'];?></div></td>
			          <?php endif; ?>
			          <td><div align="center"><?php echo $arr_rs['color_name'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['size_name'];?></div></td>
			          <?php if($arr_rs['productfirst'] == 1): ?>
			          <td rowspan="<?php echo $arr_rs['rec_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['product_name'];?></div></td>
			          <?php endif; ?>
			          <td><div align="center"><?php echo $arr_rs['cost_price'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['checktime'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['trans_sn'];?></div></td>
			          <?php if($arr_rs['kindfirst'] == 1): ?>
			          <td rowspan="<?php echo $arr_rs['rec_color_size_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['beforenum'];?></div></td>
			          <td rowspan="<?php echo $arr_rs['rec_color_size_count'];?>" valign="top"><div align="center"><?php echo $arr_rs['beforecount'];?></div></td>
			          <?php endif; ?>
			          <td><div align="center"><?php echo $arr_rs['product_number']>0?$arr_rs['typesrcipt']:'';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_number']>0?$arr_rs['product_number']:'&nbsp;';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_number']>0?$arr_rs['productcount']:'&nbsp;';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_number']<0?$arr_rs['typesrcipt']:'';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_number']<0?$arr_rs['product_number']:'&nbsp;';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_number']<0?$arr_rs['productcount']:'&nbsp;';?></div></td>
			          <?php if($arr_rs['kindfirst'] == 1): ?>
			          <td rowspan="<?php echo $arr_rs['rec_color_size_count'];?>" valign="bottom"><div align="center"><?php echo $arr_rs['afternum'];?></div></td>
			          <td rowspan="<?php echo $arr_rs['rec_color_size_count'];?>" valign="bottom"><div align="center"><?php echo $arr_rs['aftercount'];?></div></td>
			          <td rowspan="<?php echo $arr_rs['rec_color_size_count'];?>" valign="bottom"><div align="center"><?php echo $arr_rs['aftercesscount'];?></div></td>
			          <?php endif; ?>
			    </tr>
				<?php endforeach; ?>

				<tr><td colspan="10" style="font-weight:bold;"><div align="center">合计</div></td>
				<td><div align="center"><?php echo $count['total_before_num'];?></div></td>
				<td><div align="center"><?php echo $count['total_before_amount'];?></div></td>
				<td><div align="center">&nbsp;</div></td>
				<td><div align="center"><?php echo $count['numarr'][0];?></div></td>
				<td><div align="center"><?php echo $count['amountarr'][0];?></div></td>
				<td><div align="center">&nbsp;</div></td>
				<td><div align="center"><?php echo $count['numarr'][1];?></div></td>
				<td><div align="center"><?php echo $count['amountarr'][1];?></div></td>
				<td><div align="center"><?php echo $count['total_after_num'];?></div></td>
				<td><div align="center"><?php echo $count['total_after_amount'];?></div></td>
				<td><div align="center"><?php echo $count['total_after_cess_amount'];?></div></td>
				</tr>

			    <tr>
					<td colspan="14" class="bottomTd"> </td>
				</tr>
			</table><?php endif; ?>
  <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
