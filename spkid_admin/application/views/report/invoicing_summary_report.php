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
    <div class="main_title"><span class="l">报表管理 >> 财务进销存汇总表(仓库口径)</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form method="post" action="report/invoicing_summary_report" name="theForm"  onsubmit = "return checkForm()">
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
				<select name="depot_id" id="depot_id" >
					<option value="0">--选择仓库--</option>
					<?php foreach($depot_list as $item): ?>
	                <option value='<?php echo $item->depot_id;?>' <?php if ($item->depot_id == $depot_id): ?>selected<?php endif; ?>><?php echo $item->depot_name;?></option>";
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
					<th rowspan="2">品牌</th>
					<th rowspan="2">ERP大类</th>
					<th rowspan="2">ERP中类</th>
					<th colspan="1">期初</th>
					<th colspan="2">进</th>
					<th colspan="2">出</th>
					<th colspan="1">结存</th>
				</tr>
				<tr>
				 	<th>数量</th>
				  	<th>出入库类型</th>
			      	<th>数量</th>
			      	<th>出入库类型</th>
			      	<th>数量</th>
			      	<th>数量</th>
				</tr>
				<?php foreach ($list as $arr_data): ?>
				<?php if (isset($arr_data['out']) || isset($arr_data['in'])): ?>
				<?php if (isset($arr_data['in'])): ?>
				<?php foreach ($arr_data['in'] as $result_key =>$arr_rs): ?>
				<tr>
				  	  <td><div align="center"><?php echo $arr_rs['brand_name'];?></div></td>
				  	  <td><div align="center"><?php echo $arr_rs['pcatname'];?></div></td>
					  <td><div align="center"><?php echo $arr_rs['catname'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['before_product_number'];?></div></td>
			          <td><div align="center"><?php echo $result_key;?></div></td>
			          <td><div align="center"><?php echo $arr_rs['num'];?></div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center"><?php echo $arr_rs['after_product_number'];?></div></td>
			    </tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if (isset($arr_data['out'])): ?>
				<?php foreach ($arr_data['out'] as $result_key =>$arr_rs): ?>
				<tr>
				  	  <td><div align="center"><?php echo $arr_rs['brand_name'];?></div></td>
				  	  <td><div align="center"><?php echo $arr_rs['pcatname'];?></div></td>
					  <td><div align="center"><?php echo $arr_rs['catname'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['before_product_number'];?></div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center"><?php echo $result_key;?></div></td>
			          <td><div align="center"><?php echo $arr_rs['num'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['after_product_number'];?></div></td>
			    </tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php else: ?>
				<tr>
				  	  <td><div align="center"><?php echo $arr_data['brand_name'];?></div></td>
				  	  <td><div align="center"><?php echo $arr_data['pcatname'];?></div></td>
					  <td><div align="center"><?php echo $arr_data['catname'];?></div></td>
			          <td><div align="center"><?php echo $arr_data['before_product_number'];?></div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center"><?php echo $arr_data['after_product_number'];?></div></td>
			    </tr>
				<?php endif; ?>
				<?php endforeach; ?>
				<?php if ((isset($count) && !empty($count))): ?>
				<?php if (isset($count['total_serial']) && isset($count['total_serial']['in'])): ?>
				<?php foreach ($count['total_serial']['in'] as $cat_key =>$arr_cat): ?>
				<tr>
					<td colspan="3"><div align="center" style="font-weight:bold;">合计</div></td>
					<td><div align="center"><?php echo $count['total_before_num'];?></div></td>
					<td><div align="center"><?php echo $cat_key;?></div></td>
					<td><div align="center"><?php echo $arr_cat['num'];?></div></td>
					<td><div align="center">&nbsp;</div></td>
					<td><div align="center">&nbsp;</div></td>
					<td><div align="center"><?php echo $count['total_after_num'];?></div></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if (isset($count['total_serial']) && isset($count['total_serial']['out'])): ?>
				<?php foreach ($count['total_serial']['out'] as $cat_key =>$arr_cat): ?>
				<tr>
					<td colspan="3"><div align="center" style="font-weight:bold;">合计</div></td>
					<td><div align="center"><?php echo $count['total_before_num'];?></div></td>
				    <td><div align="center">&nbsp;</div></td>
					<td><div align="center">&nbsp;</div></td>
					<td><div align="center"><?php echo $cat_key;?></div></td>
					<td><div align="center"><?php echo $arr_cat['num'];?></div></td>
					<td><div align="center"><?php echo $count['total_after_num'];?></div></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<tr>
				  	  <td colspan="3"><div align="center" style="font-weight:bold;">合计</div></td>
			          <td><div align="center"><?php echo $count['total_before_num'];?></div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center">&nbsp;</div></td>
			          <td><div align="center"><?php echo $count['total_after_num'];?></div></td>
			    </tr>
				<?php endif; ?>
			    <tr>
					<td colspan="14" class="bottomTd"> </td>
				</tr>
			</table><?php endif; ?>
  <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
