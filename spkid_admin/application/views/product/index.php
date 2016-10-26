<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/product_index.js"></script>
	<script type="text/javascript" src="public/js/cluetip.js"></script>
    <script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
	<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<style>
	    .sn_black{color:black}
	    .sn_red{color:red}
	    .sn_yellow{color:#f90}
	    .bold{font-weight:bold}
	    .state_row{
		background: none #F5F5F5;
		padding: 5px;
		margin: 10px 0;
		text-align: center;
		border: 1px solid #CCC;
	    }
	</style>
	<script type="text/javascript">
		//<![CDATA[
		//页面加载时计算颜色列表的宽度
		function resizeWidth() {
			var w=($('.main_title').width())/2-10;
			$('.goods_color_size').parent().width(w);
		}
		function img_tip()
		{
			$('td.img_tip_td').cluetip({showTitle:false,arrows: true,width:'240px',clickThrough:true});
			resizeWidth();
		}
		$(function(){
			img_tip();
			$(window).resize(resizeWidth);
		});

		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'product/index';
		listTable.func = img_tip;
		function search(){ 
			listTable.filter['product_id'] = $.trim($('input[type=text][name=product_id]').val());
			listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
			listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]').val());
			listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]').val());
			listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['style_id'] = $.trim($('select[name=style_id]').val());
			listTable.filter['product_sex'] = $.trim($('select[name=product_sex]').val());
			listTable.filter['season_id'] = $.trim($('select[name=season_id]').val());
			listTable.filter['product_status'] = $.trim($('select[name=product_status]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['is_pic'] = $.trim($('select[name=is_pic]').val());
			listTable.filter['batch_code'] = $.trim($('input[type=text][name=batch_code]').val());
			listTable.filter['medical1_id'] = $.trim($('select[name=medical1_id]').val());
			listTable.filter['medical2_id'] = $.trim($('select[name=medical2_id]').val());
			listTable.filter['is_on_sale'] = $.trim($('select[name=is_on_sale]').val());
            listTable.filter['source_id'] = $.trim($('select[name=source_id]').val());
			listTable.loadList();
		}
		function load_product_cost_panel(product)
		{
		    $.ajax({
			    url: '/product_api/get_cost_price',
			    data: {product_id:product, rnd : new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(result){
				if(result.error == 0)
				{
				    var content = result.content;
				    new $.dialog({ id:'thepanel',height:300,width:500,maxBtn:false, title:'产品成本信息',iconTitle:false,cover:true,html: content}).ShowDialog();
				}
			    }
			 });
		}
		
		function op_product_onoff(obj, product, field, val) {
		    $.ajax({
			    url: '/product/toggle_product/'+val,
			    data: {product_id:product, field:field,rnd : new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(result){
				if(result.error == 0)
				{
				    var content = result.content;
				    new $.dialog({ id:'thepanel',height:300,width:500,maxBtn:false, title:'产品成本信息',iconTitle:false,cover:true,html: content}).ShowDialog();
				}
			    }
			 });		    
		}
		
		jQuery.download = function(url, data, method){ 
		    if( url && data ){
		    var inputs = ''; 
		    jQuery.each(data, function(key,val){ 
		    inputs+='<input type="hidden" name="'+ key +'" value="'+ val +'" />'; 
		    }); 
		    jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>') 
		    .appendTo('body').submit().remove(); 
		    }; 
		};
	
                function op_product_onoff(obj, product, field, val) {
                    $.ajax({
			    url: '/product/toggle_product/'+val,
			    data: {id:product, field:field, rnd : new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(result) {
                                alert(result.err +' - '+ result.msg);
				if(result.err == 0)
				{
                                    var v_key = (val == 1) ? "下架" : "上架";
                                    var v_val = (val == 1) ? "上架" : "下架";
                                    $(obj).parents('tr').children().last().find('font:contains('+v_key+')').each(function(i){
                                        this.innerHTML = v_val;
                                        this.color = (val == 1) ? 'red' : 'gray';
                                    });
				}
			    }
		    });
                }
	
		function export_purcahse_order(){
		    var batch_code = $.trim($('input[name=batch_code]').val());
		    if(batch_code== null || batch_code ==""){
			alert("必须选择批次号");
			return false;
		    }
		    
		    $.download("product/export_purcahse_order",
			{ product_sn:$.trim($('input[type=text][name=product_sn]').val()), 
			product_name: $.trim($('input[type=text][name=product_name]').val()),
			provider_productcode: $.trim($('input[type=text][name=provider_productcode]').val()),
			category_id: $.trim($('select[name=category_id]').val()),
			brand_id: $.trim($('select[name=brand_id]').val()),
			style_id: $.trim($('select[name=style_id]').val()),
			product_sex: $.trim($('select[name=product_sex]').val()),
			season_id: $.trim($('select[name=season_id]').val()),
			product_status: $.trim($('select[name=product_status]').val()),
			provider_id: $.trim($('select[name=provider_id]').val()),
			is_pic: $.trim($('select[name=is_pic]').val()),
			batch_code:batch_code ,
			rnd : new Date().getTime()}
			);
		}
		
		//]]>
	</script>
	<div class="main">
        <div class="main_title">
        	<span class="l">商品管理 >> 商品列表</span>
        	<span class="r"><a class="add" href="product/add/">新增</a></span></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			ID：<input type="text" class="ts" name="product_id" value="" style="width:60px;" />
			名称：<input type="text" class="ts" name="product_name" value="" style="width:60px;" />
			款号：<input type="text" class="ts" name="product_sn" value="" style="width:60px;" />
			货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:60px;" />
                        <select name="source_id">
				<option value="0">平台类型</option>
				<?php foreach($all_source as $source) print "<option value='{$source->source_id}'>{$source->source_name}</option>"?>
			</select>
			<select name="category_id" data-am-selected="{searchBox: 1,maxHeight: 300}">
				<option value="">分类</option>
				<?php foreach($all_category as $category) print "<option value='{$category->category_id}'>{$category->level_space}{$category->cate_code}{$category->category_name}</option>"?>
			</select>
			<?php print form_dropdown('brand_id',get_pair($all_brand,'brand_id','brand_name', array(''=>'品牌')),'',' data-am-selected="{searchBox: 1,maxHeight: 300}"'); ?>
			<select name="product_status">
				<option value="">状态</option>
				<option value="is_best">展品</option>
				<option value="is_new">新品</option>
				<option value="is_hot">热销</option>
				<option value="is_promote">促销</option>
				<option value="is_gifts">赠品</option>
				<option value="is_stop">停止订货</option>
				<option value="is_audit_yes">已审核</option>
				<option value="is_audit_no">未审核</option>
				<option value="is_pic_yes">已拍摄</option>
				<option value="is_pic_no">未拍摄</option>
			</select>
			<?php print form_dropdown('provider_id',get_pair($all_provider,'provider_id','provider_code,provider_name', array(''=>'供应商')),'',' data-am-selected="{searchBox: 1,maxHeight: 300}"'); ?>
			批次号：<input type="text" class="ts" name="batch_code" />

			<?php print form_dropdown('medical1_id',get_pair($all_medical1,'field_id','field_value1', array(''=>'医疗类型'))); ?>
			<?php print form_dropdown('medical2_id',get_pair($all_medical2,'field_id','field_value1', array(''=>'医疗设备'))); ?>
			<select name="is_on_sale">
				<option value="">上/下 架</option>
				<option value="is_on_sale_yes">上架</option>
				<option value="is_on_sale_no">下架</option>
			</select>
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			<input type="button" class="am-btn am-btn-primary" value="批量审核" onclick="batch_audit()" />
			<input type="button" class="am-btn am-btn-primary" value="导出采购单模版" onclick="export_purcahse_order()" />
			</form>
		</div>
		<div class="blank5"></div>
		<div class="state_row">
				<font class="bold">提示："商品款号" 栏，正常销售——黑色；</font>
				<font class="bold" style="color:#f90;">下架后3天内包括第3天——黄色；</font>
				<font class="sn_red bold">下架后3天后——红色，不能下单。</font>
		</div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" width="100%" cellpadding=0 cellspacing=0>
				
				<tr class="row">
					<th width="50">
					<label><input type="checkbox" name="ck_check_all" onclick="check_all();"></label>
						<a href="javascript:listTable.sort('p.product_id', 'DESC'); ">ID<?php echo ($filter['sort_by'] == 'p.product_id') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="100">
						商品款号<br/>
						商品名称<br/>
						供应商货号<br/>
                        最后编辑时间
					</th>
					<th width="80">
						分类<br/>
						品牌<br/>
						季节<br/>
						性别
					</th>
					<th width="80">						
						<a href="javascript:listTable.sort('p.market_price', 'DESC'); ">市场价<?php echo ($filter['sort_by'] == 'p.market_price') ? $filter['sort_flag'] : '' ?></a><br/>
						<?php if($pro_cost_price):?><a href="javascript:listTable.sort('p.cost_price', 'DESC'); ">成本价<?php echo ($filter['sort_by'] == 'p.cost_price') ? $filter['sort_flag'] : '' ?></a><br/><?php endif; ?>
						<a href="javascript:listTable.sort('p.shop_price', 'DESC'); ">售价<?php echo ($filter['sort_by'] == 'p.shop_price') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="70">状态</th>

					<th width="50">库存</th>
					<th width="50">
						销售量<br/>
						访问量<br/>
					</th>
					<th width="40">
						<a href="javascript:listTable.sort('p.sort_order', 'ASC'); ">排序<?php echo ($filter['sort_by'] == 'p.sort_order') ? $filter['sort_flag'] : '' ?></a>
					</th>
					<th width="50">操作</th>
					<th>颜色尺码</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td>
					<label><input type="checkbox" name="product_id" value="<?php print $row->product_id; ?>" /><br/><?php print $row->product_id; ?><br/></label>
					<a href="liuyan/index.html?tag_id=<?php print $row->product_id; ?>" target="_blank">留言</a>
					</td>
					<td style="width:100px;overflow:hidden;" class="img_tip_td" rel="product_api/gallery_preview/<?php print $row->product_id; ?>/220">
					    <?php $sn_stype = "sn_red";
						 foreach ($row->cs_list as $sub_color){
						    foreach ($sub_color['sub_list'] as $sub){
							if($sub->is_on_sale){
							   $sn_stype = "sn_black" ;
							   break;
							}
						    }
						    if($sn_stype == "sn_black" )
							break;
						}
						
						if($sn_stype == "sn_red" && strtotime($row->promote_end_date) >0){
						    $tim = strtotime($row->promote_end_date) - strtotime(date("y-m-d h:i:s"));
						    if($tim>0 && $tim <=259200)
							$sn_stype = "sn_yellow" ;
						}
					    ?>
					    <span class="<?=$sn_stype?>" style="font-weight:bold;"><?php print $row->product_sn?></span>
						<br/>
						<a href="<?php print front_url("product-{$row->product_id}.html?is_preview=1"); ?>" target="_blank"><?php print $row->product_name;?></a><br/>				
						<?php print $row->provider_productcode?><br/>
						<?php //print $all_age[$row->min_month].'<span style="color:red;"> - </span>'.$all_age[$row->max_month]; ?><br/>
                        <?php print $row->update_time; ?>
                        <?php if(!empty($row->tmall_num_iid)):?>
                             <a style="color:blue;" href="http://detail.tmall.com/item.htm?&id=<?php print $row->tmall_num_iid;?>" target="_blank">Tmall</a>
                        <?php endif;?>
					</td>
					<td>
						<?php print $row->category_name; ?><br/>
						<?php print $row->brand_name; ?><br/>
						<?php print $row->season_name; ?><br/>
						<?php print $row->product_sex==1?'男':($row->product_sex==2?'女':($row->product_sex==3?'男女':'')); ?>
					</td>
					<td>
						<?php print $row->market_price?><br/>
                                                <?php if($pro_cost_price):?><a href="javascript:load_product_cost_panel(<?php print $row->product_id; ?>);">显示成本价</a><br/><?php endif; ?>
						<span style="color:#FF0000;"><?php print $row->shop_price?></span>
					</td>
					<td>						
						<?php print toggle_link('product/toggle','is_best',$row->product_id, $row->is_best,'<font color=red>展品</font>','<font color=gray>展品</font>');?>
						<?php print toggle_link('product/toggle','is_new',$row->product_id, $row->is_new,'<font color=red>新品</font>','<font color=gray>新品</font>');?><br/>
						<?php print toggle_link('product/toggle','is_hot',$row->product_id, $row->is_hot,'<font color=red>热销</font>','<font color=gray>热销</font>');?>
						<?php print toggle_link('product/toggle','is_promote',$row->product_id, $row->is_promote,'<font color=red>促销</font>','<font color=gray>促销</font>');?><br/>
						<?php print toggle_link('product/toggle','is_gifts',$row->product_id, $row->is_gifts,'<font color=red>赠品</font>','<font color=gray>赠品</font>');?>
					
						
					</td>					
					
					<td>
						实：<?php print $row->sub_gl;?><br/>
						虚：<?php print $row->sub_consign==-2?'无限':$row->sub_consign;?>
					</td>

					<td>
						<?php print $row->ps_real_num.'/'.$row->ps_num;?><br/>
						<?php print $row->pv_real_num.'/'.$row->pv_num;?>
					</td>
					
					<td>
						<?php print edit_link('product/edit_field', 'sort_order', $row->product_id, $row->sort_order);?>
					</td>
					<td>
						<a class="edit" href="product/edit/<?php print $row->product_id; ?>" title="编辑"></a>
						<?php if ($perm_delete): ?><br/>
							<a class="del" href="javascript:void(0)" rel="product/delete/<?php print $row->product_id; ?>" title="删除" onclick="do_delete(this)"></a><br/>
							审核<?php print toggle_link('product/toggle','is_audit',$row->product_id, $row->is_audit);?><br>
						<?php endif ?>
                                        <span style="cursor:pointer;color:red;" onclick="op_product_onoff(this, <?=$row->product_id?>, 'is_on_sale', 1);">上架</span>
<span class="yesForGif"></span>
<br>
                                        <span style="cursor:pointer;color:gray;" onclick="op_product_onoff(this, <?=$row->product_id?>, 'is_on_sale', 0);">下架</span>
<span class="noForGif"></span>
					</td>
					<td>
						<?php if ($row->cs_list): ?>
						<div style="overflow:auto;overflow-y:hidden;">
							<div class="goods_color_size">
								<div>
									<table  class="dataTable" width="100%" cellpadding=0 cellspacing=0>
										<tr>
											<?php foreach ($row->cs_list as $sub_color): ?>
												<td <?php print 'colspan="'.count($sub_color['sub_list']).'"';?>>
												    <?php print $sub_color['color_name']; ?>
												    [<a id="PIC_<?php print $row->product_id; ?>_<?php print $sub_color['color_id']; ?>" class="edit" 
													product_id="<?php print $row->product_id; ?>" color_id="<?php print $sub_color['color_id']; ?>" is_pic="<?php print $sub_color['is_pic']; ?>"
													href="javascript:void(0);" onclick="audit_pic('#'+this.id);" <?php if ($sub_color['is_pic']): ?> title="已拍摄">已拍摄 <?php else: ?> title="未拍摄">未拍摄 <?php endif ?></a>]
												</td>
											<?php endforeach ?>
										</tr>
										<tr>
											<?php foreach ($row->cs_list as $sub_color): ?>
												<?php foreach ($sub_color['sub_list'] as $sub): ?>
													<td class="product_td_width"><?php print $sub->size_name; ?></td>
												<?php endforeach ?>
											<?php endforeach ?>
										</tr>
										<tr>
											<?php foreach ($row->cs_list as $sub_color): ?>
												<?php foreach ($sub_color['sub_list'] as $sub): ?>
													<td style="white-space:nowrap;overflow:hidden;word-break:break-all;"><?php print $sub->provider_barcode; ?></td>
												<?php endforeach ?>
											<?php endforeach ?>
										</tr>
										<tr>
											<?php foreach ($row->cs_list as $sub_color): ?>
												<?php foreach ($sub_color['sub_list'] as $sub): ?>
													<td>
														[<?php print $sub->gl_num;?>]
														[<?php print edit_link('product/edit_field_sub', 'consign_num', $sub->sub_id, $sub->consign_num==-2?'不限':($sub->consign_num==-1?'不':$sub->consign_num));?>]
													</td>
												<?php endforeach ?>
											<?php endforeach ?>
										</tr>
										<tr>
											<?php foreach ($row->cs_list as $sub_color): ?>
												<?php foreach ($sub_color['sub_list'] as $sub): ?>
													<td>
														<?php print toggle_link('product/toggle_sub','is_on_sale',$sub->sub_id, $sub->is_on_sale,'<font color=red>上架</font>','<font color=gray>下架</font>');?>
													</td>
												<?php endforeach ?>
											<?php endforeach ?>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<?php else: ?>
							暂无颜色尺码
						<?php endif ?>
											
					</td>
				</tr>
				<?php endforeach; ?>
				
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
