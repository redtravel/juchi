<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/package.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<!--<script type="text/javascript" src="public/js/jquery.form.js"></script> -->
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
		validator.required('package_name', '请填写礼包名称');
		validator.required('start_date', '请填写开始时间');
		validator.required('end_date', '请填写结束时间');
		return validator.passed();
	}
	listTable.filter.package_id = <?php print $package->package_id; ?>;
	listTable.url = 'package/product_search';
	function search(){
		var container = $('form[name=search]');
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]', container).val());
		listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]', container).val());
		listTable.filter['style_id'] = $('select[name=style_id]', container).val();
		listTable.filter['season_id'] = $('select[name=season_id]', container).val();
		listTable.filter['product_sex'] = $('select[name=product_sex]', container).val();
		listTable.loadList();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">礼包管理 >> 编辑 </span><a href="package/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<div class="button_row">
        <ul>
            <li class="conf_btn" rel="1"><span>基础信息</span></li>
            <li class="conf_btn" rel="2"><span>礼包区域</span></li>
            <li class="conf_btn" rel="3"><span>礼包商品</span></li>
        </ul>
	</div>

	<div class="blank5"></div>

	<?php print form_open_multipart('package/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('package_id'=>$package->package_id));?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" colspan="4" style="text-align:left;">
					<?php print form_button('check_btn','点击启用','onclick="check_package();" '.($perms['check']?'':'disabled')); ?>
					<?php print form_button('over_btn','点击停用','onclick="over_package();" '.($perms['over']?'':'disabled')); ?>
					<?php print $perms['over']?form_input('over_note',$package->over_note,'class="textbox require" size="60"'):'';?>
				</td>
				
			</tr>
			<tr>
				<td class="item_title">礼包类型</td>
				<td colspan="3" class="item_input"><?php print $all_type[$package->package_type];?></td>
			</tr>
			<tr>
				<td class="item_title">礼包名称</td>
				<td colspan="3" class="item_input"><?php print form_input('package_name', $package->package_name, 'class="textbox require"');?></td>
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
					<?php print form_upload('package_image', '', ''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->package_image);?>
					<label><?php if ($package->package_image) print form_checkbox('delete_package_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">首页图片</td>
				<td class="item_input" colspan=3>
					<?php print form_upload('package_homepage_image','',''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->package_homepage_image);?>
					<label><?php if ($package->package_homepage_image) print form_checkbox('delete_package_homepage_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">分享图片</td>
				<td class="item_input" colspan=3>
					<?php print form_upload('package_wechat_image','',''.($perms['edit']?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES,$package->package_wechat_image);?>
					<label><?php if ($package->package_wechat_image) print form_checkbox('delete_package_wechat_image',1, FALSE, ''.($perms['edit']?'':'disabled')) . '删除原图'?></label>
				</td>
			</tr>
			<tr>
				<td class="item_title">礼包设置</td>
				<td colspan="3" class="item_input">
					<label><?php print form_checkbox('is_liuyan', '1', $package->is_liuyan, ''.($perms['edit']?'':'disabled'));?>启用留言功能</label>
					<label><?php print form_checkbox('is_empty', '1', $package->is_empty, ''.($perms['edit']?'':'disabled'));?>商品售空后仍在列表中显示</label>
					<label><?php print form_checkbox('is_recommend', '1', $package->is_recommend, ''.($perms['edit']?'':'disabled'));?>推荐</label>
				</td>
			</tr>
			<tr>
				<td class="item_title">价格配置</td>
				<td colspan="3" class="item_input">
					<div class="package_config_item_0">
						商品数量<?php print form_input('goods_number[]', $package->package_goods_number,'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						价格<?php print form_input('goods_price[]', $package->package_amount,'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						原价<?php print form_input('shop_price[]', $package->own_price,'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						市场价<?php print form_input('market_price[]', $package->market_price,'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						<?php if($package->package_type==1 && $perms['config']):?>
						<span class="op" style="cursor:pointer;" onclick="add_config();">[+]</span>
						<?php endif;?>
					</div>
					<?php foreach($package->package_other_config as $config):?>
					<div class="package_config_item">
						商品数量<?php print form_input('goods_number[]', $config[0],'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						价格<?php print form_input('goods_price[]', $config[1],'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						原价<?php print form_input('shop_price[]', $config[2],'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						市场价<?php print form_input('market_price[]', $config[3],'class="textbox require" size="3" '.($perms['config']?'':'disabled'));?>
						<?php if($package->package_type==1):?>
						<span class="op" style="cursor:pointer;" onclick="remove_config(this);">[-]</span>
						<?php endif;?>
					</div>
					<?php endforeach; ?>
				</td>
			</tr>

			<tr>
				<td class="item_title">礼包状态</td>
				<td colspan="3" class="item_input"><?php print $all_status[$package->package_status];?></td>
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
					<?php print $package->package_status>0?$package->check_date:'未启用'; ?>
				</td>
				<td class="item_title">启用人</td>
				<td class="item_input"><?php print $package->check_admin_name; ?></td>
			</tr>
			<tr>
				<td class="item_title">停用时间</td>
				<td class="item_input">
					<?php print $package->package_status>1?$package->over_date:'未停用'; ?>
				</td>
				<td class="item_title">停用人</td>
				<td class="item_input"><?php print $package->over_admin_name; ?></td>
			</tr>
			<?php if($package->package_status==2):?>
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
					<?php print form_textarea(array('name'=>'package_desc','cols'=>120,'rows'=>8), $package->package_desc,($perms['edit']?'':'disabled'));?>
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
			<div id="area_list">
			<?php include('area_list.php');?>
			</div>
			<div class="blank5"></div>
			<?php if($perms['edit']): ?>
			<div id="area_form">
			<?php include 'form_add_area.php';?>
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
				<form name="search" action="javascript:search(); ">
				商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
				商品名称：<input type="text" class="ts" name="product_name" value="" style="width:100px;" />
				供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
				<?php print form_dropdown('style_id', get_pair($all_style,'style_id','style_name',array(''=>'风格')));?>
				<?php print form_dropdown('season_id', get_pair($all_season,'season_id','season_name',array(''=>'季节')));?>
				<select name="product_sex"><option value="">性别</option><option value="1">男款</option><option value="2">女款</option><option value="3">男女款</option></select>
				<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
				</form>
			</div>
			<div id="listDiv">
			</div>
			<?php endif;?>
			<div class="blank5"></div>
		</div>
		
</div>
<?php include(APPPATH.'views/common/footer.php');?>
