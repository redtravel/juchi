<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/brand.js"></script>
<link type="text/css" href="public/style/jui/datepicker.css" rel="stylesheet" />
<link type="text/css" href="public/style/jui/theme.css" rel="stylesheet" />
<script type="text/javascript" src="public/js/jui/core.min.js"></script>
<script type="text/javascript" src="public/js/jui/datepicker.min.js"></script>
<script type="text/javascript" src="public/js/campaign.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
	$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

	});
	$(function(){
		show_flag();
	});
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('campaign_name', '请填写活动名称');
		validator.required('limit_price', '请填写最小金额');
		validator.selected('tag_id', '请选择赠送商品');
		validator.required('start_time', '请填写开始时间');
		validator.required('end_time', '请填写结束时间');
		return validator.passed();
	}

	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">活动管理 >> 新增满减 </span><a href="campaign/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('campaign/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="100">活动名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'campaign_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title" width="100">活动类型:</td>
				<td class="item_input"><?php print form_dropdown('campaign_type',$campaign_types['minus']);?></td>
			</tr>
			<tr>
				<td class="item_title">最小金额:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'limit_price','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">减金额:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'tag_id','class'=> 'textbox require'));?>金额为正值</td>
			</tr>
			<tr>
				<td class="item_title">选择品牌:</td>
				<td class="item_input">                
				<input name="brand" type="text" id="brand" value="" size="20" onblur="return sel(this.value,'brand','brand_id');"  />
				<select name="brand_id" id="brand_id">
				    <option value="0">--请选择--</option>
				    </select>可输入品牌名称搜索
				</td>
			</tr>
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input">
					<input type="text" name="start_time" id="start_time" />0点
				</td>
			</tr>
			<tr>
			  <td class="item_title">结束时间:</td>
			  <td class="item_input"><input type="text" name="end_time" id="end_time" />0点</td>
			</tr>
			<tr>
			  <td class="item_title">单品列表:<br/>《单品满减》时有用</td>
			  <td class="item_input"><?php print form_textarea(array('id'=>'product_sns','name'=>'product_sns','rows'=>5,'cols'=>80));?>
					<?php //print form_button(array('name'=>'check_id','class'=>'button','content'=>'验证商品ID','onclick'=>"javascript:check_product_valid('product_id')"));?>
					<?php print form_button(array('name'=>'check_code','class'=>'button','content'=>'验证商品编号','onclick'=>"javascript:check_product_valid('product_sn')"));?>验证结果：<span id="check_result"></span>
					</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?>禁用</label>
                    <label><?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?>启用</label>
					
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'button','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
