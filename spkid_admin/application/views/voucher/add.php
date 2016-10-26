<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/voucher.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.url = 'voucher/search_product';
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('campaign_name', '请填写活动名称');
		validator.reg('start_date',/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/,'请填写开始日期');
		validator.reg('end_date',/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/,'请填写结束日期');
                validator.required('provider_ids', '请选择供应商');
		return validator.passed();
	}
	
	$(function(){
		$(':input[name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$(':input[name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
	});
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">现金券活动管理 >> 新增 </span><a href="voucher/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('voucher/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">活动类型:</td>
				<td class="item_input"><?php print form_dropdown('campaign_type', get_pair($voucher_config, 'code', 'name'),'');?></td>
			</tr>
			<tr>
				<td class="item_title">活动名称:</td>
				<td class="item_input"><?php print form_input('campaign_name', '', 'class="require textbox"');?> </td>
			</tr>
			<tr>
				<td class="item_title">活动期间:</td>
				<td class="item_input">
				<?php print form_input('start_date', '', 'class="require textbox"');?> 
				至
				<?php print form_input('end_date', '', 'class="require textbox"');?> 
				</td>
			</tr>
			
			<tr>
				<td class="item_title">排序:</td>
				<td class="item_input">
				<?php print form_input('sort_order', '', 'class="textbox" size=3');?> 
				</td>
			</tr>
			<tr>
				<td class="item_title">活动备注:</td>
				<td class="item_input">
					<input type="text" name="desc" size="60" class="textbox"></textarea>
				</td>
			</tr>
                        <tr>
				<td class="item_title" style="text-align:left" colspan="2">限定供应商:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="2">
					<?php foreach ($all_provider as $provider): ?>
					<div style="float:left; width:200px; text-align:left;">
						<label><input type="radio" name="provider_ids" value="<?php print $provider->provider_id; ?>"><?php print $provider->provider_name; ?></label>
                                        </div>
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="2">限定品牌:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="2">
					<?php foreach ($all_brand as $brand): ?>
					<div style="float:left; width:200px; text-align:left;">
						<label><input type="checkbox" name="brand_ids[]" value="<?php print $brand->brand_id; ?>"><?php print $brand->brand_name; ?></label>
                     </div>
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="2">限定分类:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="2">
					<?php foreach ($all_category as $group): ?>
					<?php print "<div style='clear:both;'>【{$group->category_name}】</div>";?>
					<?php foreach ($group->sub_items as $v): ?>
						<div style="float:left; width:200px; text-align:left;">
						<label><input type="checkbox" name="category_ids[]" value="<?php print $v->category_id; ?>"><?php print $v->category_name; ?></label>
                     	</div>
					<?php endforeach;?>
					<div style="height:10px; clear:both;"></div>
					<?php endforeach ?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="2">
				限定商品:<font color="red">（注：限定商品后不能限定品牌和分类）</font>
				</td>
			</tr>
			<tr>
				<td class="item_input" colspan="2">
					<div id="product_list">
					</div>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="2">
					<div class="search_row">
							商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
							<?php print form_product_category('category_id', $all_category,'', '',array(''=>'商品分类'));?>
							<?php print form_dropdown('brand_id', array(''=>'商品品牌')+get_pair($all_brand,'brand_id','brand_name'));?>
							价格区间:
							<input type="text" class="ts" name="min_price" value="" style="width:100px;" />
							-
							<input type="text" class="ts" name="max_price" value="" style="width:100px;" />
							<input type="button" class="am-btn am-btn-secondary" value="搜索" onclick="search_product();" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="item_input" colspan="2">
					<div id="listDiv">

					</div>
				</td>
			</tr>
			<tr>
				<td class="item_title">&nbsp;</td>
				<td class="item_input">
					<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"');?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>		
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>