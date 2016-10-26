<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/product.js"></script>
<script type="text/javascript">
$(function(){
			$('input[type=text][name=package_name]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
			$('input[type=text][name=desc_waterproof]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('product_name', '请填写课程名称');
		validator.isNonNegative('shop_price', '请填写本店售价', true);
		validator.required('unit_name', '请填写单位');
		validator.isPrice('market_price', '请填写市场价', true);
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">课程管理 >> 新增</span> <a href="course/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('course/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
            <tr>
				<td class="item_title">平台类型:</td>
                <td class="item_input">
                    <select name="source_id">
                        <option value="0">请选择</option>
                        <?php foreach($all_source as $source) print "<option value='{$source->source_id}'>{$source->source_name}</option>"?>
                    </select>
                </td>
                <td class="item_title">运营专员:</td>
                <td class="item_input">
                    <?php print form_dropdown('operator', array(''=>'请选择')+get_pair($all_admin,'realname','realname'),array(''), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                </td>
			</tr>
			<tr>
				<td class="item_title">后台品名:</td>
                <td class="item_input" colspan="2">
                    <?php print form_input(array('name'=> 'product_name_alias','class'=> 'textbox require', 'style'=>'width:600px;'));?>
                </td>
			</tr>
			<tr>
				<td class="item_title" width="100px;">课程名称</td>
				<td class="item_input"><?php print form_input(array('name'=> 'product_name','class'=> 'textbox require'));?></td>
				<td class="item_title">课程角标:</td>
				<td class="item_input">
					<label><?php print form_checkbox('is_best', 1, FALSE)?>清仓</label>
					<label><?php print form_checkbox('is_new', 1, FALSE)?>新品</label>
					<label><?php print form_checkbox('is_hot', 1, FALSE)?>热销</label>
					<label><?php print form_checkbox('is_offcode', 1, FALSE)?>促销</label>
					<label><?php print form_checkbox('is_gifts', 1, FALSE)?>赠品</label>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="product_sn_label">课程编号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'product_sn','class'=> 'textbox','placeholder'=>'留空可自动生成'));?></td>
				<td class="item_title" id="price_show_label">是否询价:</td>
				<td class="item_input">
					<select name="price_show">
                        <option value="0">请选择</option>
                        <option value="1">询价/仅展示</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="category_id_label">课程分类:</td>
				<td class="item_input">
					<?php print form_product_category('category_id', $all_category,'','data-am-selected');?>
				</td>
				<td class="item_title" id="unit_name_label">单位:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'unit_name','class'=>'textbox require','size'=>"3",'value'=>''));?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="shop_id_label">店铺:</td>
				<td class="item_input">
					<?php print form_dropdown('shop_id', array('0'=>'请选择')+get_pair($all_provider_coop,'provider_id','provider_name'),array(''),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title">商品大类:</td>
				<td class="item_input">课程产品 </td>
			</tr>
			<tr>
				<td class="item_title">本站价:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'shop_price','class'=>'textbox require'));?>
				</td>
				<td class="item_title">市场价:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'market_price','class'=>'textbox require'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">关键字:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'keywords','class'=>'textbox'))?>
				</td>
				<td class="item_title">排序号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sort_order','class'=> 'textbox', 'size'=>3));?></td>
			</tr>
            <tr>
				<td class="item_title" id="content_source_label">内容来源:</td>
                <td class="item_input">
                   <?php print form_input(array('name'=>'content_source','class'=>'textbox'))?>
                </td>
			</tr>
			<tr>
				<td class="item_title">销售数量:</td>
                <td class="item_input">
					<?php print form_input(array('name'=>'ps_num','class'=>'textbox'))?>
				</td>
				<td class="item_title">访问数量:</td>
                <td class="item_input">
                   <?php print form_input(array('name'=>'pv_num','class'=>'textbox'))?>
                </td>
			</tr>
			<tr>
				<td class="item_title" colspan="4" style="text-align: center">附加详细信息</td>
			</tr>
			 <tr>
				<td class="item_title">讲课老师:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'subhead','class'=>'textbox'))?>subhead
				</td>
				<td class="item_title">开课开始时间:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'package_name','class'=> 'textbox'));?>package_name</td>
			</tr>
			<tr>
				<td class="item_title">开课城市:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_material','class'=>'textbox'))?>desc_material
				</td>
				<td class="item_title">开课结束时间:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_waterproof','class'=> 'textbox'));?>desc_waterproof</td>
			</tr>
			<tr>
				<td class="item_title">开课地址:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_crowd','class'=>'textbox'))?>desc_crowd
				</td>
				<td class="item_title">客服名:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_expected_shipping_date','class'=> 'textbox'));?>desc_expected_shipping_date</td>
			</tr>
			<tr>
				<td class="item_title">手机号:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_composition','class'=>'textbox'))?>desc_composition
				</td>
				<td class="item_title">促销信息:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_dimensions','class'=> 'textbox'));?>desc_dimensions</td>
			</tr>			
           
			<tr>
				<td class="item_title" id="product_desc_label">课程详情(原始数据):</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc') ?>
				</td>
			</tr>
			<!-- 课程详情 -->
			
			<tr class="course_detail_edit">
				<td class="item_title">老师介绍:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail5") ?>
				</td>
			</tr>
			<tr class="course_detail_edit">
				<td class="item_title">课程详情:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail1") ?>
				</td>
			</tr>

			<tr class="course_detail_edit">
				<td class="item_title">案例展示:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail2") ?>
				</td>
			</tr>
			<tr class="course_detail_edit">
				<td class="item_title">培训负责人:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail3") ?>
				</td>
			</tr>
			<tr class="course_detail_edit">
				<td class="item_title">付款信息:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail4") ?>
				</td>
			</tr>	
			<tr>
				<td class="item_title" id="product_desc_detail_label">备注:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc_detail') ?>
				</td>
			</tr>		
			<!-- ends 课程详情 -->	
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
