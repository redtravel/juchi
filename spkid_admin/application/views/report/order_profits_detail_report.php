<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/product_index.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
    <script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />

	<script type="text/javascript">
	    $(function(){
        	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=is_start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=is_end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});

		function checkForm(){
			var eles = document.forms['theForm'];
			// if (eles['start_time'].value=='' || eles['end_time'].value==''){
			// 	alert('错误：请输入报表期间!');
			// 	return false;
			// }
			if((eles['end_time'].value < eles['start_time'].value) || (eles['is_end_time'].value < eles['is_start_time'].value)){
				alert('错误：期间的结束时间早于或等于开始时间!');
				return false;
			}
		}
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 订单销售利润明细表（已完结，退货单为负利润）</span> </div>
    <div class="blank5"></div>
        <div class="search_row">
            <form method="post" action="report/order_profits_detail_report" name="theForm"  onsubmit = "return checkForm()">
                    分类：<select name="category_id" data-am-selected="{searchBox: 1,maxHeight: 300}">
                            <option value="">--分类--</option>
                            <?php                            
                            foreach($all_category as $category) {
                                $selected = "";
                                if ($category_id == $category->category_id) $selected = " selected";
                                print "<option value='{$category->category_id}'{$selected}>{$category->level_space}{$category->cate_code}{$category->category_name}</option>";
                            }
                            ?>
                            </select>
                &nbsp;
                    <?php print form_dropdown('brand_id', array(''=>'--品牌--')+get_pair($brand_list,'brand_id','brand_name'),array($brand_id), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    &nbsp;
                    
                    <?php print form_dropdown('provider_id', array(''=>'--供应商--')+get_pair($provider_list,'provider_id','provider_name'),array($provider_id), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    &nbsp;
                    <span style="color: #FF0000;font: 12px verdana;"></span>财审期间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
                    <span style="color: #FF0000;font: 12px verdana;"></span>完结期间：<input type="text" name="is_start_time" id="is_start_time" value="<?php echo $is_start_time;?>" /><input type="text" name="is_end_time" id="is_end_time" value="<?php echo $is_end_time;?>" />
                    订单号 <input type="text" name="order_sn" value="<?php echo $order_sn;?>" size="20" />
            商品款号 <input type="text" name="product_sn" value="<?php echo $product_sn;?>" size="15" />
    商品ID <input type="text" name="product_id" value="<?php echo $product_id;?>" size="15" />
            商品名称 <input type="text" name="keyword" value="<?php echo $keyword;?>" size="15" />
                    <input type="submit" class="am-btn am-btn-primary" value="搜索" />
                    <input type="submit" name="export" class="am-btn am-btn-primary" value="导出" />
            </form>
        </div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if ((isset($order_product) && !empty($order_product))): ?>
			<table cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="21" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>单号</th>
                                    <th>订单类型</th>
                                    <th>财审日期</th>
                                    <th>完结日期</th>
                                    <th>商品款号</th>
                                    <th>商品名称</th>
                                    <th>品牌</th>
                                    <th>供应商名称</th>
                                    <th>供应商条码</th>
                                    <th>规格</th>
                                    <th>数量</th>
                                    <th>含税销售单价</th>
                                    <th>含税成本单价</th>
                                    <th>大类</th>
                                    <th>中类</th>                                       
                                    <th>运营专员</th>
                                    <th>实际运费</th>
                                    <th>理论运费</th>
                                    <th>收货人</th>
                                    <th>手机号</th>
                                    <th>地址</th>
				</tr>
				<?php foreach ($order_product as $key => $op_val): ?>
                                    <tr>
                                        <td><?php print $op_val->trans_sn; ?></td>
                                        <td><?php print $op_val->genre_name; ?></td>
                                        <td><?php print $op_val->finance_check_date; ?></td>
                                        <td><?php print $op_val->is_ok_date; ?></td>
                                        <td><?php print $op_val->product_sn; ?></td>
                                        <td><?php print $op_val->product_name; ?></td>
                                        <td><?php print $op_val->brand_name; ?></td>
                                        <td><?php print $op_val->provider_name; ?></td>
                                        <td><?php print $op_val->provider_code; ?></td>
                                        <td><?php print $op_val->size_name; ?></td>
                                        <td><?php print ($op_val->product_number < 0) ? substr($op_val->product_number, 1) : '-'.$op_val->product_number; ?></td>
                                        <td><?php print $op_val->paid_price; ?></td>
                                        <td><?php print $op_val->cost_price; ?></td>
                                        <td><?php print $op_val->class_one; ?></td>
                                        <td><?php print $op_val->class_two; ?></td>                                               
                                        <td><?=$op_val->operator?></td>
                                        <td><?=($op_val->real_shipping_fee > 0) ? round($op_val->real_shipping_fee*$op_val->shop_price*abs($op_val->product_number)/$op_val->order_price, 2) : 0; ?></td>
                                        <td><?=($op_val->recheck_shipping_fee > 0) ? round($op_val->recheck_shipping_fee*$op_val->shop_price*abs($op_val->product_number)/$op_val->order_price, 2) : 0; ?></td>
                                        <td><?php print $op_val->consignee; ?></td>
                                        <td><?php print $op_val->mobile; ?></td>
                                        <td><?php print $op_val->address; ?></td>
                                    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="21" class="bottomTd"> </td>
				</tr>
			</table>
			<?php endif; ?>
  			<div class="blank5"></div>
	  	</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
