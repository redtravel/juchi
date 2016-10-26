<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="../../../public/js/listtable.js"></script>
    <script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){  
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
                $('input[type=text][name=e_start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=e_end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		function checkForm(){
                    var eles = document.forms['theForm'];
                    if (eles['end_time'].value==''){
                            alert('错误：请输入统计节点!');
                            return false;
                    }
		}
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 库存明细表</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
		<form method="post" action="report/inventory_details_report" name="theForm"  onsubmit = "return checkForm()">	
                    
                    <select name="brand_id" data-am-selected="{searchBox: 1,maxHeight: 300}">
					<option value="">--商品品牌--</option>
					<?php foreach($brand_list as $item) print "<option value='{$item->brand_id}'>{$item->brand_initial} {$item->brand_name}</option>";?>
				</select>
                    
                    &nbsp;
                    <span style="color: #FF0000;font: 12px verdana;">*</span>统计节点：<input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
                    商品款号 <input type="text" name="product_sn" value="<?php echo $product_sn;?>" size="15" />
                    商品编码 <input type="text" name="sku" value="<?php echo $sku;?>" size="15" />
                    条码 <input type="text" name="provider_barcode" value="<?php echo $provider_barcode;?>" size="15" />
                    商品名称 <input type="text" name="keyword" value="<?php echo $keyword;?>" size="15" />
                    <select name="is_expire_date">
			<option value="0">是否有有效期</option>
			<option value="1"<?php if($is_expire_date == 1){echo ' selected';} ?>>无</option>
                        <option value="2"<?php if($is_expire_date == 2){echo ' selected';} ?>>有</option>
                    </select>
                    有效期：<input type="text" name="e_start_time" id="e_start_time" value="<?php echo $e_start_time;?>" /><input type="text" name="e_end_time" id="e_end_time" value="<?php echo $e_end_time;?>" />
                    <select name="actual_stock">
			<option value="0">实际库存</option>
                        <option value="1"<?php if($actual_stock == 1){echo ' selected';} ?>>无</option>
                        <option value="2"<?php if($actual_stock == 2){echo ' selected';} ?>>有</option>
                    </select>
                    <select name="avail_stock">
			<option value="0">可售库存</option>
			<option value="1"<?php if($avail_stock == 1){echo ' selected';} ?>>无</option>
                        <option value="2"<?php if($avail_stock == 2){echo ' selected';} ?>>有</option>
                    </select>
                    <select name="order_stock">
			<option value="0">订单占用库存</option>
			<option value="1"<?php if($order_stock == 1){echo ' selected';} ?>>无</option>
                        <option value="2"<?php if($order_stock == 2){echo ' selected';} ?>>有</option>
                    </select>
                    
		    <input type="submit" name="search" class="am-btn am-btn-primary" value="搜索" />
                    <input type="submit" name="export" class="am-btn am-btn-primary" value="导出" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if (isset($list) && !empty($list)): ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
                                    <td colspan="18" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>品牌</th>
                                    <th>后台商品名称</th>
                                    <th>货号</th>
                                    <th>商品编码</th>
                                    <th>商品款号</th>
                                    <th>条形码</th>
                                    <th>后台分类</th>
                                    <th>规格型号描述</th>
                                    <th>库号</th>
                                    <th>储位</th>
                                    <th>实库库存</th>
                                    <th>可售库存</th>
                                    <th>订单占用</th>
                                    <th>成本价</th>
                                    <th>库存金额</th>
                                    <th>入库时间</th>
                                    <th>有无有效期</th>
                                    <th>过期时间</th>
				</tr>

				<?php foreach ($list as $k =>$rs): ?>
				<tr>
                                    <td><?=$rs->brand_name?></td>
                                    <td><?=$rs->product_name?></td>
                                    <td><?=$rs->provider_productcode?></td>
                                    <td><?=$rs->product_sn?>_<?=$rs->size_sn?></td>
                                    <td><?=$rs->product_sn?></td>
                                    <td><?=$rs->provider_barcode?></td>
                                    <td><?=$rs->category_name?></td>
                                    <td><?=$rs->size_name?></td>
                                    <td><?=$rs->depot_name?></td>
                                    <td><?=$rs->location_name?></td>
                                    <td><?=$rs->real_num?></td>
                                    <td><?=$rs->real_num - abs($rs->order_num)?></td>
                                    <td><?=abs($rs->order_num)?></td>
                                    <td><?=($rs->real_num - abs($rs->order_num)) > 0 ? round($rs->s_cost_price/($rs->real_num - abs($rs->order_num)), 2) : 0;?></td>
                                    <td><?=$rs->s_cost_price?></td>
                                    <td><?=$rs->rk_time?></td>
                                    <td><?=($rs->expire_date != '0000-00-00') ? '有' : '否';?></td>
                                    <td><?=$rs->expire_date?></td>                                    
                                </tr>
				<?php endforeach; ?>
                                <tr>
                                    <td colspan="18" class="bottomTd"> </td>
				</tr>
			</table>
                        <?php endif; ?>
                <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
