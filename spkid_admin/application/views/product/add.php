<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/product.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('product_name', '请填写商品名称');
        validator.required('product_name_alias', '请填写后台品名');
		validator.selected('provider_id', '请选择供应商名称');
		validator.selected('brand_id', '请选择品牌');
		validator.selected('register_id', '请填写注册证号');
		validator.required('unit_name', '请填写计量单位');
        validator.required('operator', '请填写运营专员');
		validator.isNonNegative('shop_price', '请填写本店售价', true);
		validator.isPrice('market_price', '请填写市场价', true);
		return validator.passed();                        
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">商品管理 >> 新增</span> <a href="product/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('product/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
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
                <td class="item_input" colspan="3">
                    <?php print form_input(array('name'=> 'product_name_alias','class'=> 'textbox require', 'style'=>'width:600px;'));?>
                </td>
			</tr>
			<tr>
				<td class="item_title" width="100px;">商品名称</td>
				<td class="item_input"><?php print form_input(array('name'=> 'product_name','class'=> 'textbox require', 'style'=>'width:600px;'));?></td>
				<td class="item_title">商品角标:</td>
				<td class="item_input">
					<label><?php print form_checkbox('is_best', 1, FALSE)?>清仓</label>
					<label><?php print form_checkbox('is_new', 1, FALSE)?>新品</label>
					<label><?php print form_checkbox('is_hot', 1, FALSE)?>热销</label>
					<label><?php print form_checkbox('is_offcode', 1, FALSE)?>促销</label>
					<label><?php print form_checkbox('is_gifts', 1, FALSE)?>赠品</label>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="product_sn_label">商品款号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'product_sn','class'=> 'textbox','placeholder'=>'留空可自动生成'));?></td>
				<td class="item_title" id="is_stop_label">商品状态:</td>
				<td class="item_input">
					<label><?php print form_checkbox('is_stop', 1, FALSE)?>停止订货</label>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="provider_id_label">供应商:</td>
				<td class="item_input">
					<?php print form_dropdown('provider_id', array('0'=>'请选择')+get_pair($all_provider,'provider_id','provider_name'),array(''),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title" id="provider_productcode_label">供应商货号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_productcode','class'=> 'textbox'));?></td>
			</tr>
			<tr>
				<td class="item_title" id="brand_id_label">品牌:</td>
				<td class="item_input">
					<?php print form_dropdown('brand_id', array('0'=>'请选择')+get_pair($all_brand,'brand_id','brand_name'),array(''),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title" id="category_id_label">分类:</td>
				<td class="item_input">
					<?php print form_product_category('category_id', $all_category,'','data-am-selected');?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="register_id_label">注册证号:</td>
				<td class="item_input">
					<?php print form_dropdown('register_id', array('0'=>'请选择')+get_pair($all_register,'id','register_no'),array(''),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title" id="unit_name_label">计量单位:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'unit_name','class'=>'textbox require','size'=>3, 'value'=>''));?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="shop_id_label">店铺:</td>
				<td class="item_input">
					<?php print form_dropdown('shop_id', array('0'=>'请选择')+get_pair($all_provider_coop,'provider_id','provider_name'),array(''),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title" id="genre_id_label">商品大类:</td>
				<td class="item_input">
					<?php print form_dropdown('genre_id', get_pair($all_genre,'id','name'),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="flag_id_label">产地:</td>
				<td class="item_input">
					<?php print form_dropdown('flag_id', get_pair($all_flag,'flag_id','flag_name'));?>
				</td>
				<td class="item_title" id="product_weight_label">商品重量:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'product_weight','class'=>'textbox','size'=>3));?> 克
				</td>
			</tr>
			<tr>
				<td class="item_title" id="size_image_label">规格详情图</td>
				<td class="item_input">
					<?php print form_upload('size_image');?>
				</td>
				<td class="item_title" id="price_show_label">是否询价:</td>
				<td class="item_input">
					<select name="price_show">
                        <option value="0">请选择</option>
                        <option value="1">询价/仅展示</option>
                    </select>
				</td>
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
				<td class="item_title" id="limit_num_label">限购数量:</td>
                <td class="item_input">
					<?php print form_input(array('name'=>'limit_num','class'=>'textbox'))?>
				</td>
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
				<td class="item_title" id="subhead_label">副标题:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'subhead','class'=>'textbox'))?>
				</td>
				<td class="item_title" id="package_name_label">包装名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'package_name','class'=> 'textbox'));?></td>
			</tr>
            <tr>
				<td class="item_title" id="pack_method_label">包装方式:</td>
                <td class="item_input" colspan="3">
					<?php print form_input(array('name'=>'pack_method','class'=>'textbox'))?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="product_desc_label">商品描述:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc') ?>
				</td>
			</tr>
			<!-- 产品详情 -->	
			<tr class="product_detail_edit">
				<td class="item_title">产品图文详情:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail1") ?>
				</td>
			</tr>
			<tr class="product_detail_edit">
				<td class="item_title">产品测试视频:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail2") ?>
				</td>
			</tr>			
			<!-- ends 产品详情 -->	
			<tr>
				<td class="item_title" id="product_desc_detail_label">商品细节展示:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc_detail') ?>
				</td>
			</tr>
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
