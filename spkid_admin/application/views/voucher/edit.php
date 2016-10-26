<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/voucher.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript">
	//<![CDATA[
	listTable.url = 'voucher/search_product';
	var config = <?php print json_encode($config);?>;
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
		i=0;
        var i = Utils.request(location.href,'tab');
        if(!i) i = 0;
		$(btns[i]).click();
	});
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">现金券活动管理 >> 编辑 </span><a href="voucher/index" class="return r">返回列表</a></div>
	<div class="button_row">
        <ul>
         <li class="conf_btn" rel="1"><span>基础信息</span></li>
         <li class="conf_btn" rel="2"><span>发放列表</span></li>
        </ul>
        <div class="clear"></div>
	</div>
	<div class="blank5"></div>
	<?php print form_open_multipart('voucher/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('campaign_id'=>$campaign->campaign_id));?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="raw_title" colspan="4" style="text-align:left" >
					<?php print form_button('audit_campaign','审核','onclick="operate_campaign(\'audit\');" '.($perms['audit']?'':'disabled')); ?>
					<?php print form_button('btn_add_release', '添加发放', 'onclick="add_release();" '.($perms['release']?'':'disabled'));?>
					<?php print form_button('stop_campaign','停用','onclick="operate_campaign(\'stop\');" '.($perms['stop']?'':'disabled')); ?>

					<?php if($perms['stop']) print '停用理由：'.form_input('stop_reason',$campaign->stop_reason,'class="require textbox" size="60"')?>(停用时填写)
				</td>
			</tr>
			<tr>
				<td class="item_title">活动类型:</td>
				<td class="item_input" colspan="3"><?php print $config['name']; ?></td>
			</tr>
			<tr>
				<td class="item_title">活动名称:</td>
				<td class="item_input" colspan="3"><?php print form_input('campaign_name', $campaign->campaign_name, 'class="require textbox" '.($perms['edit']?'':'disabled'));?> </td>
			</tr>
			<tr>
				<td class="item_title">活动期间:</td>
				<td class="item_input" colspan="3">
				<?php print form_input('start_date', substr($campaign->start_date,0,10), 'class="require textbox" '.($perms['edit']?'':'disabled'));?>
				至
				<?php print form_input('end_date', substr($campaign->end_date,0,10), 'class="require textbox" '.($perms['edit']?'':'disabled'));?> 
				</td>
			</tr>
			<tr>
				<td class="item_title">排序:</td>
				<td class="item_input" colspan="3">
				<?php print form_input('sort_order', $campaign->sort_order, 'class="textbox" size=3 '.($perms['edit']?'':'disabled'));?> 
				</td>
			</tr>
			<tr>
				<td class="item_title">活动备注:</td>
				<td class="item_input" colspan="3">
					<textarea name="desc" cols="60" rows="4" <?php print $perms['edit']?'':'disabled'?>><?php print $campaign->desc; ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="item_title">添加时间:</td>
				<td class="item_input"><?php print $campaign->create_date; ?></td>
				<td class="item_title">添加人:</td>
				<td class="item_input">
				<?php print $campaign->create_admin ? $all_admin[$campaign->create_admin]->admin_name : ''; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">审核时间:</td>
				<td class="item_input"><?php print $campaign->campaign_status>0 ? $campaign->audit_date:'未审核 '; ?></td>
				<td class="item_title">审核人:</td>
				<td class="item_input"><?php print $campaign->audit_admin ? $all_admin[$campaign->audit_admin]->admin_name:''; ?></td>
			</tr>
			<tr>
				<td class="item_title">停用时间:</td>
				<td class="item_input"><?php print $campaign->campaign_status==2 ? $campaign->stop_date:'未停用 '; ?></td>
				<td class="item_title">停用人:</td>
				<td class="item_input"><?php print $campaign->stop_admin ? $all_admin[$campaign->stop_admin]->admin_name:''; ?></td>
			</tr>
			<?php if ($campaign->campaign_status==2): ?>
				<tr>
				<td class="item_title">停用理由:</td>
				<td class="item_input" colspan="3"><?php print $campaign->stop_reason?></td>
			</tr>
			<?php endif ?>
                        <tr>
				<td class="item_title" style="text-align:left" colspan="4">限定供应商:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="4">
					<?php foreach ($all_provider as $provider): ?>
					<div style="float:left; width:200px; text-align:left;">
						<label>
                                                    <?php 
                                                    //if (empty($campaign->provider)) $campaign->provider = array();
                                                    print form_radio('provider_ids', $provider->provider_id, $provider->provider_id==$campaign->provider, $perms['edit']?'':'disabled');?>
                                                    <?php print $provider->provider_name; ?>
                                                </label>
                                        </div>
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="4">限定品牌:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="4">
					<?php foreach ($all_brand as $brand): ?>
					<div style="float:left; width:200px; text-align:left;">
						<label>
						<?php print form_checkbox('brand_ids[]', $brand->brand_id, in_array($brand->brand_id, $campaign->brand), $perms['edit']?'':'disabled');?>
						<?php print $brand->brand_name; ?>
						</label>
                     </div>
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="4">限定分类:</td>
			</tr>
			<tr>
				<td class="item_input" colspan="4">
					<?php foreach ($all_category as $group): ?>
					<?php print "<div style='clear:both;'>【{$group->category_name}】</div>";?>
					<?php foreach ($group->sub_items as $v): ?>
						<div style="float:left; width:200px; text-align:left;">
						<label>
						<?php print form_checkbox('category_ids[]', $v->category_id, in_array($v->category_id, $campaign->category), $perms['edit']?'':'disabled');?>
						<?php print $v->category_name; ?>
						</label>
                     	</div>
					<?php endforeach;?>
					<div style="height:10px; clear:both;"></div>
					<?php endforeach ?>
				</td>
			</tr>
			<tr>
				<td class="item_title" style="text-align:left" colspan="4">
				限定商品:<font color="red">（注：限定商品后不能限定品牌和分类）</font>
				</td>
			</tr>
			<tr>
				<td class="item_input" colspan="4">
					<div id="product_list">
					<?php include 'product_list.php';?>
					</div>
				</td>
			</tr>
			<?php if($perms['edit']):?>
			<tr>
				<td class="item_title" style="text-align:left" colspan="4">
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
				<td class="item_input" colspan="4">
					<div id="listDiv">

					</div>
				</td>
			</tr>
			<tr>
				<td class="item_title">&nbsp;</td>
				<td class="item_input" colspan="3">
					<?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"');?>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>		
		</table>
	<?php print form_close();?>
	<table class="dataTable conf_tab" cellpadding=0 cellspacing=0 rel="2" style="margin-top:0">
		<tr>
			<td colspan=10 class="topTd"></td>
		</tr>
		<tr class="row">
			<th>发放ID</th>
			<th>现金券描述</th>
			<th>发放状态</th>
			<th>发放时间</th>
			<th>发放人</th>
			<th>现金券金额</th>
			<th>已发放数量</th>
			<th>最小订单金额</th>
			<th>有效期</th>
			<th>操作</th>
		</tr>
		<?php if (!$release_list): ?>
			<tr class="row">
				<td colspan="10">没有发放记录</td>
			</tr>
		<?php endif ?>
		<?php foreach($release_list as $release): ?>
		<tr class="row">
			<td><?php print $release->release_id?></td>
			<td><?php print $release->voucher_name?></td>
			<td><?php print $all_release_status[$release->release_status];?></td>
			<td><?php print $release->release_status?$release->audit_date:'';?></td>
			<td><?php print $release->audit_admin?$all_admin[$release->audit_admin]->admin_name:''?></td>
			<td><?php print $release->voucher_amount?></td>
			<td><?php print $release->voucher_count?></td>
			<td><?php print $release->min_order?></td>
			<td><?php print $release->expire_days?($release->expire_days.'天'):(substr($release->start_date,0,10).' 至 '.substr($release->end_date,0,10))?></td>
			<td>
			<a href="voucher/edit_release/<?php print $release->release_id?>">查看</a>
			<?php if ($release->release_status>0): ?>
				<a href="voucher/query/release_id/<?php print $release->release_id?>" target="_blank">报表</a>
				<a href="voucher/export/<?php print $release->release_id?>">导出</a>
			<?php else: ?>
			<a href="javascript:void(0)" rel="voucher/delete_release/<?php print $release->release_id?>" onclick="do_delete(this)">删除</a>
			<?php endif ?>
			</td>
		</tr>
		<?php endforeach;?>
		<tr>
			<td colspan=10 class="bottomTd"></td>
		</tr>	
	</table>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
