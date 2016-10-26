<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
	<script type="text/javascript" src="public/js/region.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript">
		//<![CDATA[
		function img_tip()
		{
			$('span.pro_tip').cluetip({showTitle:false,arrows: true,width:'650px'});
			$(':input[name=add_start]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=add_ends]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=pay_start]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=pay_ends]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=shipping_start]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=shipping_end]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
			$(':input[name=inv_export_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		}
		$(function(){
			img_tip();
		});
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'order_course/index';
		listTable.func = img_tip;
		function search(){
			listTable.filter['order_sn'] = $.trim($(':input[name=order_sn]').val());
			listTable.filter['user_name'] = $.trim($(':input[name=user_name]').val());
			listTable.filter['consignee'] = $.trim($(':input[name=consignee]').val());
			listTable.filter['product_sn'] = $.trim($(':input[name=product_sn]').val());
			listTable.filter['lock_admin'] = $.trim($(':input[name=lock_admin]').val());
			listTable.filter['source_id'] = $.trim($(':input[name=source_id]').val());
			listTable.filter['pay_id'] = $.trim($(':input[name=pay_id]').val());
			listTable.filter['shipping_id'] = $.trim($(':input[name=shipping_id]').val());
			listTable.filter['order_status'] = $.trim($(':input[name=order_status2]').val());
			listTable.filter['pay_status'] = $.trim($(':input[name=pay_status2]').val());
			listTable.filter['shipping_status'] = $.trim($(':input[name=shipping_status2]').val());
			listTable.filter['is_ok'] = $.trim($(':input[name=is_ok]').val());
			listTable.filter['category_id'] = $.trim($(':input[name=category_id]').val());
			listTable.filter['provider_id'] = $.trim($(':input[name=provider_id]').val());
			listTable.filter['country'] = $.trim($(':input[name=country]').val());
			listTable.filter['province'] = $.trim($(':input[name=province]').val());
			listTable.filter['city'] = $.trim($(':input[name=city]').val());
			listTable.filter['district'] = $.trim($(':input[name=district]').val());
			listTable.filter['add_start'] = $.trim($(':input[name=add_start]').val());
			listTable.filter['add_end'] = $.trim($(':input[name=add_ends]').val());
			listTable.filter['pay_start'] = $.trim($(':input[name=pay_start]').val());
			listTable.filter['pay_end'] = $.trim($(':input[name=pay_ends]').val());
			listTable.filter['shipping_start'] = $.trim($(':input[name=shipping_start]').val());
			listTable.filter['shipping_end'] = $.trim($(':input[name=shipping_end]').val());
            listTable.filter['odd']=$(':checkbox:checked[name=odd]').length;
            listTable.filter['pick']=$(':checkbox:checked[name=pick]').length;
			listTable.filter['consign']=$(':checkbox:checked[name=consign]').length;
			listTable.filter['tel'] = $.trim($(':input[name=tel]').val());
			listTable.filter['mobile'] = $.trim($(':input[name=mobile]').val());
			listTable.filter['payment_status'] = $.trim($(':input[name=payment_status]').val());
			listTable.loadList();
		}
		function switch_advanced_search () {
			if($('#advanced_search_div').css('display')=='none'){
				$('#advanced_search_div').css('display','');
			}else{
				$('#advanced_search_div').css('display','none');
			}
		}
		
		function export_inv(){
		    var inv_export_date = $('input[type=text][name=inv_export_date]').val();
		    if(inv_export_date == '' ){
				alert('请选择导出发票的日期');
				return false;
		    }
		    window.open("order_course_api/export_inv?inv_export_date="+inv_export_date);
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">课程订单列表</span><span class="r"><a href="order_course/add" class="add">新增</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			订单号：<input type="text" class="ts" name="order_sn" value="" style="width:100px;" />
			购货人手机/Email：<input type="text" class="ts" name="user_name" value="" style="width:100px;" />
			收货人：<input type="text" class="ts" name="consignee" value="" style="width:60px;" />	
			<?php print form_dropdown('source_id',array(''=>'订单来源')+get_pair($source_list,'source_id','source_name')); ?>
			<?php print form_dropdown('pay_id',array(''=>'支付方式')+get_pair($pay_list,'pay_id','pay_name')); ?>
			<?php print form_dropdown('payment_status',array(''=>'付款状态','-1'=>'未付款','1'=>'已付款')); ?>
			<?php print form_dropdown('shipping_id',array(''=>'配送方式')+get_pair($shipping_list,'shipping_id','shipping_name')); ?>
			<?php print form_dropdown('order_status2',array('0'=>'订单状态','-1'=>'未客审','1'=>'已客审','4'=>'作废','5'=>'拒收')); ?>
			<?php print form_dropdown('pay_status2',array('0'=>'财审状态','-1'=>'未财审','1'=>'已财审')); ?>
			<?php print form_dropdown('shipping_status2',array('0'=>'配送状态','-1'=>'未发货','1'=>'已发货')); ?>
			<?php print form_dropdown('is_ok',array('0'=>'完结状态','-1'=>'未完结','1'=>'已完结')); ?>
			课程编号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
			
			<a href="javascript:void(0);" onclick="switch_advanced_search();" style="color:red;">高级</a>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			<div id="advanced_search_div" style="display:none;">
            <label><input type="checkbox" name="odd" value="1" />问题单</label>
            <label><input type="checkbox" name="pick" value="1" />拣货中</label>
			<label><input type="checkbox" name="consign" value="1" />虚库销售</label>
			<a href="javascript:;" onclick="export_inv();return false;">导出当日发票记录</a>
			发票查询：<?php print form_input('inv_export_date','','style="width:100px;"readonly="readonly"') ?>
				锁定人：<input type="text" class="ts" name="lock_admin" value="" style="width:100px;" />
				<select name="category_id">
					<option value="">分类</option>
					<?php foreach ($category_list as $cat): ?>
						<option value="<?php print $cat->category_id; ?>"><?php print "{$cat->level_space} {$cat->category_name}" ?></option>
					<?php endforeach ?>
				</select>
				<?php print form_dropdown('country',array(''=>'国家')+get_pair($country_list,'region_id','region_name'),'','id="selCountries" onChange="region.changed(this, \'selProvinces\')"'); ?>
				<?php print form_dropdown('province',array(''=>'省'),'','id="selProvinces" onChange="region.changed(this, \'selCities\')"'); ?>
				<?php print form_dropdown('city',array(''=>'市'),'','id="selCities" onChange="region.changed(this, \'selDistricts\')"'); ?>
				<?php print form_dropdown('district',array(''=>'区'),'','id="selDistricts"'); ?>
				收货人电话号码：<?php print form_input('tel','','style="width:100px;"') ?>
				收货人手机号：<?php print form_input('mobile','','style="width:100px;"') ?>
				<br/>
				下单时间：
				<?php print form_input('add_start','','style="width:100px;"') ?> - 
				<?php print form_input('add_ends','','style="width:100px;"') ?>
				财审时间：
				<?php print form_input('pay_start','','style="width:100px;"') ?> - 
				<?php print form_input('pay_ends','','style="width:100px;"') ?>
				发货时间：
				<?php print form_input('shipping_start','','style="width:100px;"') ?> - 
				<?php print form_input('shipping_end','','style="width:100px;"') ?>
			</div>
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="150px">
						<a href="javascript:listTable.sort('o.order_id', 'DES'); ">订单号<?php echo ($filter['sort_by'] == 'o.order_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="150px">购货人</th>
					<th>收货人</th>
					<th width="80px">商品金额</th>
					<th width="80px">待付金额</th>
					<th width="180px">订单状态</th>
					<th width="100px">锁定</th>
					<th width="50px">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td style="text-align:left;">
					<span class="pro_tip">
					    <?php if($row->consign)print "<span style='color:red'>[虚]</span>"; print $row->order_sn; ?>
					</span>	
					<br/>					
						<?php print "{$row->pay_name} - {$row->shipping_name}"; ?><br/>
						<?php print substr($row->create_date,0,16); ?>
					</td>
					<td style="text-align:left;">
						<?php print $row->user_name; ?><br/>
						注册于<?php print substr($row->reg_date,0,10);?>
					</td>
					<td style="text-align:left;">
						<?php print $row->consignee?>
						<?php if ($row->tel || $row->mobile): ?>
							[<?php print "<i>{$row->tel}</i> <i>{$row->mobile}</i>";?>]
						<?php endif ?><br/>
						<?php print $row->address?>
					</td>
					<td>
						<?php print $row->order_price ?>
					</td>
					<td>
						<?php print fix_price($row->order_price+$row->shipping_fee-$row->paid_price); ?>
					</td>
					<td>
						<?php print implode('&nbsp;',format_order_status($row,TRUE)); ?><br/>
						[<?php print $row->source_name ?>]
						<?php if ($row->has_return): ?>
							<a style="color:red;" href="order_return/index/order_sn/<?php print $row->order_sn ?>">有退货</a>
						<?php endif ?>
						<?php if ($row->has_change): ?>
							<a style="color:red;" href="order_change/index/order_sn/<?php print $row->order_sn ?>">有换货</a>
						<?php endif ?>
					</td>
					<td>
                        <?php print $row->lock_admin?("<span class=\"lockForGif\"></span>".$row->lock_name):'' ?>
					</td>					
					<td>
						<a class="edit" href="order_course/info/<?php print $row->order_id; ?>" title="编辑"></a>
						<?php if ($row->order_status==1 && $row->shipping_status==1 && $row->pay_status == 1): ?>				
							<br/><a href="order_return/add/<?php print $row->order_id; ?>">退货</a>
						<?php endif ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="8" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
