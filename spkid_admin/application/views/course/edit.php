<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/product.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$(':input[name=package_name]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$(':input[name=desc_waterproof]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		var btns = $(".conf_btn");
		var tabs = $(".conf_tab");
		$.each(btns, function(i, btn){
			$(btn).bind("click",function(){
				$(tabs).hide();
				if(i == 2){
				    show_product_type(<?=$row->product_id?>);
				}
				$(tabs[i]).show();
				$(btns).removeClass("currentbtn");
				$(this).addClass("currentbtn");
			});
			$(btn).bind("focus",function(){$(this).blur();});
		});
		i=0;
        var i = Utils.request(location.href,'tab');
        if(!i) i = 0;
		$(btns[i]).click();
		<?php foreach($all_gallery_sub as $color_id=>$gallery_sub) print "ajax_form({$color_id});";?>
	});
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('product_name', '请填写课程名称');			
		validator.required('unit_name', '请填写单位');
		validator.isNonNegative('shop_price', '请填写本店售价', true);
		validator.isPrice('market_price', '请填写市场价', true);
		return validator.passed();
	}
	listTable.filter.product_id = <?php print $row->product_id; ?>;
	listTable.url = 'product_api/link_search';
	function search(){
		var container = $('form[name=search]');
        listTable.filter['product_id2'] = $.trim($('input[type=text][name=product_id2]', container).val());
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]', container).val());
		listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]', container).val());
		listTable.loadList();
	}
	function show_product_type(product_id){
		$.ajax({
		    url: 'product_type_link/pre_set_type',
		    data: {product_id:product_id, rnd : new Date().getTime()},
		    dataType: 'json',
		    type: 'POST',
		    success: function(result){
				if(result.error == 0)
				{
				    var content = result.content;
				    var div = $("div[class=conf_tab][rel=3]");
				    div.html(content);
				    div.append("<input type='am-btn am-btn-primary' style='text-align: center' class='am-btn am-btn-primary' value='确认' onclick='set_type()'/>");
				    div.find("#type_link_title").hide();
				}
		    }
		});
	}
	
	function set_type(){
	    var product_id = $("#product_id").val();
	    var chk_value =[];    
	    $('input[name="type_ids"]:checked').each(function(){
	     	chk_value.push($(this).val());    
	    });
	    $.ajax({
		    url: 'product_type_link/set_type',
		    data: {product_id : product_id ,type_ids : chk_value, rnd : new Date().getTime()},
		    dataType: 'json',
		    type: 'POST',
		    success: function(){
				window.location.reload();
		    }
	    });
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">课程管理 >> 编辑</span> <a href="course/index" class="return r"></a></div>
	<div class="blank5"></div>
	<div class="button_row">
        <ul>
	        <li class="conf_btn" rel="1"><span>基础信息</span></li>
	        <li class="conf_btn" rel="2"><span>颜色规格</span></li>
		 	<li class="conf_btn" rel="3"><span>前台分类</span></li>
	        <li class="conf_btn" rel="4"><span>关联商品</span></li>
        </ul>
        <div class="clear"></div>
	</div>
	<div class="blank5"></div>
	<?php print form_open_multipart('course/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('product_id'=>$row->product_id));?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
            <tr>
				<td class="item_title">平台类型:</td>
                <td class="item_input">
                    <select name="source_id">
                        <option value="0">请选择</option>
                        <?php foreach($all_source as $source) print "<option value='{$source->source_id}' ".($row->source_id == $source->source_id ? 'selected' : '').">{$source->source_name}</option>"?>
                    </select>
                </td>
                <td class="item_title">运营专员:</td>
                <td class="item_input">
                    <?php print form_dropdown('operator', array(''=>'请选择')+get_pair($all_admin,'realname','realname'),array($row->operator), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                </td>
			</tr>
			<tr>
				<td class="item_title">后台品名:</td>
                <td class="item_input" colspan="2">
                    <?php print form_input(array('name'=> 'product_name_alias','class'=> 'textbox require', 'value'=>$row->product_name_alias,'style'=>'width:600px;'));?>
                </td>
			</tr>
			<tr>
				<td class="item_title" width="100px">课程名称</td>
				<td class="item_input"><?php print form_input(array('name'=> 'product_name','class'=> 'textbox require', 'value'=>$row->product_name,'style'=>'width:600px;'));?></td>
				<td class="item_title">课程角标:</td>
				<td class="item_input">
					<label><?php print form_checkbox('is_best', 1, (bool)$row->is_best)?>展品</label>
					<label><?php print form_checkbox('is_new', 1, (bool)$row->is_new)?>新品</label>
					<label><?php print form_checkbox('is_hot', 1, (bool)$row->is_hot)?>热销</label>
					<label><?php print form_checkbox('is_offcode', 1, (bool)$row->is_offcode)?>促销</label>
					<label><?php print form_checkbox('is_gifts', 1, (bool)$row->is_gifts)?>赠品</label>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="product_sn_label">课程编号:</td>
				<td class="item_input"><?php print form_input('product_sn',$row->product_sn,"class='textbox require' ".($row->is_audit?'disabled':''));?></td>
				<td class="item_title" id="price_show_label">是否询价:</td>
				<td class="item_input">
					<select name="price_show">
                        <option value="0">请选择</option>
                        <option value="1" <?php if($row->price_show == 1) echo 'selected';?>>询价/仅展示</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="category_id_label">课程分类:</td>
				<td class="item_input">
					<?php print form_product_category('category_id', $all_category, $row->category_id, (" data-am-selected='{searchBox: 1,maxHeight: 300}'"));?>
				</td>
				<td class="item_title" id="unit_name_label">单位:</td>
				<td class="item_input">
                    <?php print form_input('unit_name',$row->unit_name,"class='textbox require' size='3' ");?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="shop_id_label">店铺:</td>
				<td class="item_input">
					<?php print form_dropdown('shop_id', array('0'=>'请选择')+get_pair($all_provider_coop,'provider_id','provider_name'),array($row->shop_id),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
				<td class="item_title">商品大类:</td>
				<td class="item_input">课程产品 </td>
			</tr>
			<tr>
				<td class="item_title">本站价:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'shop_price','class'=>'textbox require','value'=>$row->shop_price));?>
				</td>
				<td class="item_title">市场价:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'market_price','class'=>'textbox require','value'=>$row->market_price));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">关键字:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'keywords','class'=>'textbox','size'=>80,'value'=>$row->keywords))?>
				</td>
				<td class="item_title">排序号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sort_order','class'=> 'textbox', 'size'=>3,'value'=>$row->sort_order));?></td>
			</tr>
            <tr>
				<td class="item_title" id="content_source_label">内容来源:</td>
                <td class="item_input">
                   <?php print form_input(array('name'=>'content_source','class'=>'textbox', 'value'=>$row->content_source))?>
                </td>
			</tr>
			<tr>
				<td class="item_title">销售数量:</td>
                <td class="item_input">
					<?php print form_input(array('name'=>'ps_num','class'=>'textbox','value'=>$row->ps_num))?>
                    实售:<?php echo $row->ps_real_num;?>
				</td>
				<td class="item_title">访问数量:</td>
                <td class="item_input">
                   <?php print form_input(array('name'=>'pv_num','class'=>'textbox','value'=>$row->pv_num))?>
                   实访:<?php echo $row->pv_real_num;?>
                </td>
			</tr>

			<tr>
				<td class="item_title" colspan="4" style="text-align: center">商品附加详细信息</td>
			</tr>
			<tr>
				<td class="item_title">讲课老师:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'subhead','class'=>'textbox','value'=>$row->subhead))?>
				</td>

				<td class="item_title">开课开始时间:</td>
				<td class="item_input">
					<?php print form_input(array('name'=> 'package_name','class'=> 'textbox','value'=>$row->package_name));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">开课城市:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_material','class'=>'textbox','value'=>$row->desc_material))?>
				</td>
				<td class="item_title">开课结束时间:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_waterproof','class'=> 'textbox','value'=>$row->desc_waterproof));?></td>
			</tr>			
			<tr>
				<td class="item_title">开课地址:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_crowd','class'=>'textbox','value'=>$row->desc_crowd))?>
				</td>
				<td class="item_title">客服名:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_expected_shipping_date','class'=> 'textbox','value'=>$row->desc_expected_shipping_date));?></td>
			</tr>
			<tr>
				<td class="item_title">手机号:</td>
				<td class="item_input">
					<?php print form_input(array('name'=>'desc_composition','class'=>'textbox','value'=>$row->desc_composition))?>
				</td>
				<td class="item_title">促销信息:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'desc_dimensions','class'=> 'textbox','value'=>$row->desc_dimensions));?></td>
			</tr>			
			<tr>
				<td class="item_title">课程详情(原始数据):</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc',$row->product_desc) ?>
				</td>
			</tr>
			<!-- 课程详情 -->	
			
			<tr class="course_detail_edit">
				<td class="item_title">老师介绍:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail5",$row->detail5) ?>
				</td>
			</tr>	
			<tr class="course_detail_edit">
				<td class="item_title">课程详情:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail1",$row->detail1) ?>
				</td>
			</tr>

			<tr class="course_detail_edit">
				<td class="item_title">案例展示:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail2",$row->detail2) ?>
				</td>
			</tr>

			<tr class="course_detail_edit">
				<td class="item_title">培训负责人:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail3",$row->detail3) ?>
				</td>
			</tr>

			<tr class="course_detail_edit">
				<td class="item_title">付款信息:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor("detail4",$row->detail4) ?>
				</td>
			</tr>
			<tr>
				<td class="item_title" id="product_desc_detail_label">备注:</td>
				<td class="item_input" colspan=3>
					<?php print $this->ckeditor->editor('product_desc_detail',$row->product_desc_detail) ?>
				</td>
			</tr>	
			<!-- ends 课程详情 -->	
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
		</table>
		<?php print form_close();?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="2">
			<tr>
				<td class="topTd"></td>
			</tr>
			<tr>
				<td class="search_row" style="border-width:0 0 1px 0;">
					颜色组：<?php print form_dropdown('cs_color_group_id',get_pair($all_color_group,'group_id','group_name',array('0'=>'所有颜色组')),'','onchange="cs_group_id_change();"')?>
					颜色：<?php print form_dropdown('cs_color_id',get_pair($all_color,'color_id','color_name'))?>
					<input type="button"  value="添加"  class="am-btn am-btn-secondary" onclick="add_color();">
				</td>
			</tr>
			<tr>
				<td class='item_input' id="cs_list">
					<?php foreach($all_gallery_sub as $color_id => $gallery_sub):?>						
						<table class="access_list" id="cs_color_<?php print $color_id?>" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td colspan="2" class="access_left" style="text-align:center">
										<strong><?php print $gallery_sub['color_info']['color_name']?></strong><a href="javascript:remove_color(<?php print $color_id?>);">[删]</a>
										&nbsp;排序：<?php print edit_link('product_api/sort_sub', $row->product_id, $color_id, $gallery_sub['color_info']['sort_order']);?>
									</td>
								</tr>
								<tr>
									<td class="access_left" style="text-align:left">
										规格：
										<?php print form_dropdown('cs_size_src', get_pair($all_size,'size_id','size_sn,size_name'),'',' data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
										<input type="button" class="am-btn am-btn-secondary" value="添加"  onclick="add_size(<?php print $color_id?>);">
										<br>供应商条码：<input type="text" id="provider_barcode" />
									</td>
									<td class="cs_table_2">
										<?php foreach($gallery_sub['size_list'] as $sub):?>
										<div id="cs_size_<?php print $sub->size_id;?>" style="border: 1px solid #C8DFA7;display: inline-block;margin: 3px;padding: 2px;text-align: center;width: 220px;">
											规格：<?php print $sub->size_name;?> &nbsp;&nbsp; [<a href="javascript:remove_size(<?php print $color_id?>,<?php print $sub->size_id?>);">删</a>]<br>
											供应商条码：<?php print $sub->provider_barcode;?>
										</div>
										<?php endforeach;?>
									</td>
								</tr>
								<tr>
									<td class="access_left" style="text-align:left">
										<form id="cs_upload_<?php print $color_id?>" action="product_api/add_gallery" method="POST" enctype="multipart/form-data" encoding="multipart/form-data">
										<label><input type="radio" name="cs_upload_image_type" value="default">默认</label><br/>
										<label><input type="radio" name="cs_upload_image_type" value="part">局部</label><br/>						   
                                        <input type="file" name="cs_upload_image"><font style="color:#ff0000;">只能上传.jpg格式的图片</font>
										<input type="hidden" name="cs_upload_color_id" value="<?php print $color_id?>"><br>
										<input type="hidden" name="cs_upload_product_id" value="<?php print $row->product_id?>"><br>
										<input type="submit" name="mysubmit" value="上传" class="am-btn am-btn-secondary"></form>
									</td>
									<td class="cs_table_4">
										<?php foreach($gallery_sub['gallery_list'] as $gallery):?>
										<div class="cs_image_<?php print $gallery->image_type?> cs_image_<?php print $gallery->image_id?>" style="display:inline-block;float:left;margin:5px;width:100px;">
											<span><?php if($gallery->image_type=='default') print '默认图'; elseif($gallery->image_type=='tonal') print '色片图'; else print '局部图';?></span>&nbsp;
											<a href="javascript:remove_gallery(<?php print $gallery->image_id?>)" style="clear:both;">[删]</a><br>
											<img src="public/data/images/<?php print $gallery->img_url?>.85x85.jpg" ><br>
											<?php print edit_link('product_api/edit_gallery', 'img_desc', $gallery->image_id, $gallery->img_desc?$gallery->img_desc:'无描述');?>
											<br>
											排序：<?php print edit_link('product_api/edit_gallery', 'sort_order', $gallery->image_id, $gallery->sort_order);?><br>
										</div>
										<?php endforeach;?>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="span5"></div>
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table> 
		<div class="conf_tab" rel="3"></div>
		<div class="conf_tab" rel="4">
			<div id="link_list">
				<?php include('link_list.php');?>
			</div>
			<div class="blank5"></div>
			<div class="search_row">
				<form name="search" action="javascript:search(); ">
	                课程ID：<input type="text" class="ts" name="product_id2" value="" style="width:100px;" />
					课程款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
					课程名称：<input type="text" class="ts" name="product_name" value="" style="width:100px;" />
					供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
					<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
				</form>
			</div>
			<div class="blank5"></div>
			<div id="listDiv"></div>
			<div class="blank5"></div>
		</div>
		
</div>
<?php include(APPPATH.'views/common/footer.php');?>
