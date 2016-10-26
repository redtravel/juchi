<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/package.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		var btns = $(".conf_btn");
		var tabs = $(".conf_tab");
		$.each(btns, function(i, btn){
			$(btn).bind("click",function(){
				$(tabs).hide();
				$(tabs[i]).show();
				$(btns).removeClass("currentbtn");
				$(this).addClass("currentbtn");
			});
			$(btn).bind("focus",function(){$(this).blur();});
		});
		var i = Utils.request(location.href,'tab');
		if(!i) i = 0;
		$(btns[i]).click();
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
		$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
	});
	
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('pag_dis_name', '请填写礼包名称');
		validator.required('start_date', '请填写开始时间');
		validator.required('end_date', '请填写结束时间');
		return validator.passed();
	}
	listTable.filter.pag_dis_id = <?php print $package->pag_dis_id; ?>;
	listTable.url = 'package_discount/product_search';
	function main_search(){
		$('.package_main').attr('id','listDiv');
		$('.package_discount').removeAttr('id');
		var container = $('form[name=main_search]');
		listTable.filter['dis_pro_type'] = $.trim($('input[type=hidden][name=dis_pro_type]', container).val());
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]', container).val());
		listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]', container).val());
		listTable.loadList();
	}
	function discount_search(){
		$('.package_discount').attr('id','listDiv');
		$('.package_main').removeAttr('id');
		var container = $('form[name=discount_search]');
		listTable.filter['dis_pro_type'] = $.trim($('input[type=hidden][name=dis_pro_type]', container).val());
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]', container).val());
		listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]', container).val());
		listTable.loadList();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">礼包管理 >> 编辑 </span><a href="package_discount/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<div class="button_row">
        <ul>
            <li class="conf_btn" rel="1"><span>基础信息</span></li>
            <li class="conf_btn" rel="2"><span>主商品</span></li>
            <li class="conf_btn" rel="3"><span>折扣商品</span></li>
        </ul>
	</div>

	<div class="blank5"></div>

	<?php print form_open_multipart('package_discount/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('pag_dis_id'=>$package->pag_dis_id));?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" colspan="4" style="text-align:left;">
					<?php print form_button('check_btn','点击启用','onclick="check_discount_package();" '.($perms['check']?'':'disabled')); ?>
					<?php print form_button('over_btn','点击停用','onclick="over_discount_package();" '.($perms['over']?'':'disabled')); ?>
					<?php print $perms['over']?form_input('over_note',$package->over_note,'class="textbox require" size="60"'):'';?>
				</td>
				
			</tr>
			<tr>
				<td class="item_title">礼包类型</td>
				<td colspan="3" class="item_input"><?php print $all_type[$package->pag_dis_type];?></td>
			</tr>
			<tr>
				<td class="item_title">礼包名称</td>
				<td colspan="3" class="item_input"><?php print form_input('pag_dis_name', $package->pag_dis_name, 'class="textbox require"');?></td>
			</tr>
			<tr>
				<td class="item_title">购买数量</td>
				<td colspan="3" class="item_input">
					<?php print form_input('pag_dis_max_num', $package->pag_dis_max_num, 'class="textbox require"');?>
					(礼包主产品对应折扣商品购买数量比例)
				</td>
			</tr>
			<tr>
				<td class="item_title">开始时间</td>
				<td colspan="3" class="item_input">
					<?php print form_input('start_date', substr($package->start_time,0,10), 'class="textbox require" '.($perms['edit']?'':'disabled'));?>
					<?php print form_input('start_time', substr($package->start_time,11), 'class="textbox require" '.($perms['edit']?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">结束时间</td>
				<td colspan="3" class="item_input">
					<?php print form_input('end_date', substr($package->end_time,0,10), 'class="textbox require" '.($perms['edit']?'':'disabled'));?>
					<?php print form_input('end_time', substr($package->end_time,11), 'class="textbox require" '.($perms['edit']?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包图片</td>
				<td class="item_input" colspan=3>
					<?php print form_upload('pag_dis_image', '', ''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->pag_dis_image);?>
					<label><?php if ($package->pag_dis_image) print form_checkbox('delete_pag_dis_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">首页图片</td>
				<td class="item_input" colspan=3>
					<?php print form_upload('pag_dis_homepage_image','',''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->pag_dis_homepage_image);?>
					<label><?php if ($package->pag_dis_homepage_image) print form_checkbox('delete_pag_dis_homepage_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">分享图片</td>
				<td class="item_input" colspan=3>
					<?php print form_upload('pag_dis_wechat_image','',''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->pag_dis_wechat_image);?>
					<label><?php if ($package->pag_dis_wechat_image) print form_checkbox('delete_pag_dis_wechat_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包状态</td>
				<td colspan="3" class="item_input"><?php print $all_status[$package->pag_dis_status];?></td>
			</tr>
			<tr>
				<td class="item_title">添加时间</td>
				<td class="item_input"><?php print $package->create_date; ?></td>
				<td class="item_title">添加人</td>
				<td class="item_input"><?php print $package->create_admin_name; ?></td>
			</tr>
			<tr>
				<td class="item_title">启用时间</td>
				<td class="item_input">
					<?php print $package->pag_dis_status>0?$package->check_date:'未启用'; ?>
				</td>
				<td class="item_title">启用人</td>
				<td class="item_input"><?php print $package->check_admin_name; ?></td>
			</tr>
			<tr>
				<td class="item_title">停用时间</td>
				<td class="item_input">
					<?php print $package->pag_dis_status>1?$package->over_date:'未停用'; ?>
				</td>
				<td class="item_title">停用人</td>
				<td class="item_input"><?php print $package->over_admin_name; ?></td>
			</tr>
			<?php if($package->pag_dis_status==2):?>
			<tr>
				<td class="item_title">停用理由</td>
				<td colspan="3" class="item_input"><?php print $package->over_note;?></td>
			</tr>
			<?php endif;?>
			
			<tr>
				<td class="item_title">排序</td>
				<td colspan="3" class="item_input">
					<?php print form_input('sort_order', $package->sort_order, 'class="textbox" size=3 '.($perms['edit']?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包简介</td>
				<td colspan="3" class="item_input">
					<?php print form_textarea(array('name'=>'pag_dis_desc','cols'=>120,'rows'=>8), $package->pag_dis_desc,($perms['edit']?'':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3>
					<?php print form_submit('mysubmit','提交',' '.($perms['edit']?'class="am-btn am-btn-primary"':'disabled'));?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
		<?php print form_close();?>
		
		<div class="conf_tab" rel="2">
			<div id="main_list">
			<?php include('main_product_list.php');?>
			</div>
			<div class="blank5"></div>
			<?php if($perms['edit']): ?>
			<div class="search_row">
				<form name="main_search" action="javascript:main_search(); ">
				<input type="hidden" class="ts" name="dis_pro_type" value="0" style="width:100px;" />
				商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
				商品名称：<input type="text" class="ts" name="product_name" value="" style="width:100px;" />
				供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
				<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
				</form>
			</div>
			<div class="package_main">
			</div>
			<?php endif;?>
			<div class="blank5"></div>
		</div>

		<div class="conf_tab" rel="3">
			<div id="product_list">
			<?php include('product_list.php');?>
			</div>
			<div class="blank5"></div>
			<?php if($perms['edit']): ?>
			<div class="search_row">
				<form name="discount_search" action="javascript:discount_search(); ">
				<input type="hidden" class="ts" name="dis_pro_type" value="1" style="width:100px;" />
				商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
				商品名称：<input type="text" class="ts" name="product_name" value="" style="width:100px;" />
				供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
				<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
				</form>
			</div>
			<div class="package_discount">
			</div>
			<?php endif;?>
			<div class="blank5"></div>
		</div>
		
</div>
<?php include(APPPATH.'views/common/footer.php');?>
