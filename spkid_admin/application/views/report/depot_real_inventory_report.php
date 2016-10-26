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
    <div class="main_title"><span class="l">报表管理 >> 实际库存报表</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form method="post" action="report/depot_real_inventory_report" name="theForm"  onsubmit = "return checkForm()">
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
				<select name="depot_id" id="depot_id" >
					<option value="0">--选择仓库--</option>
					<?php foreach($depot_list as $item): ?>
	                <option value='<?php echo $item->depot_id;?>' <?php if ($item->depot_id == $depot_id): ?>selected<?php endif; ?>><?php echo $item->depot_name;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;
				储位 <input type="text" name="location_name" value="<?php echo $location_name;?>" size="15" />&nbsp;</ br>
				<select name="onsale_id" id="onsale_id" >
					<option value="-1">--上下架--</option>
					<?php foreach($onsale_list as $key=>$item): ?>
	                <option value='<?php echo $key;?>' <?php if ($key == $onsale_id): ?>selected<?php endif; ?>><?php echo $item;?></option>";
	                <?php endforeach; ?>
				</select>&nbsp;

     			商品款号 <input type="text" name="product_sn" value="<?php echo $product_sn;?>" size="15" />(多个款号请用,分隔)
    			<!-- 关键字 -->
                        <span style="color: #FF0000;font: 12px verdana;">*</span>期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />

			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if (isset($list) && !empty($list)): ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="13" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th>品牌</th>
					<th>ERP大类</th>
					<th>ERP中类</th>
					<th>商品款号</th>
					<th>供应商货号</th>
					<th>季节</th>
					<th>男女</th>
					<th>上下架</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>商品名称</th>
					<th>数量</th>
					<th>储位</th>
				</tr>
				<?php foreach ($list as $result_key =>$arr_rs): ?>
				<tr>
				  	  <td><div align="center"><?php echo $arr_rs['brand_name'];?></div></td>
				  	  <td><div align="center"><?php echo $arr_rs['pcatname'];?></div></td>
					  <td><div align="center"><?php echo $arr_rs['catname'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_sn'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['provider_productcode'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['season_name'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_sex']==1?'男':'女';?></div></td>
			          <td><div align="center"><?php echo empty($arr_rs['is_onsale'])?'上架':'下架';?></div></td>
			          <td><div align="center"><?php echo $arr_rs['color_name'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['size_name'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['product_name'];?></div></td>
			          <td><div align="center"><?php echo $arr_rs['real_number'];?></div></td>
			          <td><div align="center">
			          <table border="1" cellpadding="0" cellspacing="0">
			            <?php foreach ($arr_rs['packet'] as $packet_item): ?>
						<tr>
						<td width="60" align="center"><?php echo $packet_item['depot'];?></td>
						<td width="80" align="center"><?php echo $packet_item['location'];?></td>
						<td width="40" align="center"><?php echo $packet_item['num'];?></td>
						</tr>
						<?php endforeach; ?>
						</table>
			          </div></td>
			    </tr>
				<?php endforeach; ?>
				<tr><td colspan="11" style="font-weight:bold;"><div align="center">合计</div></td>
				<td><div align="center"><?php echo $total;?></div></td>
				<td><div align="center">&nbsp;</div></td>
				</tr>

			    <tr>
					<td colspan="14" class="bottomTd"> </td>
				</tr>
			</table><?php endif; ?>
  <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
