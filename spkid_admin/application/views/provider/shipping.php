<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
        return true;
		var validator = new Validator('mainForm');
		return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">供应商管理 >> 运费配置 </span><a href="provider/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('provider/proc_shipping',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('provider_id'=>$row->provider_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
                        <tr>
				<td class="item_title">当前供应商:</td>
				<td class="item_input"><?php print $row->provider_name;?></td>
			</tr>	
            <?php foreach($all_region as $region):?>
            <tr>
				<td class="item_title"><?php print $region->region_name;?>:</td>
				<td class="item_input">
                    运费<?php print form_input('shipping_fee_'.$region->region_id,$region->shipping_fee,'size=5 class="textbox require" '.($perm_edit?'':'disabled'));?>元，
                    满<?php print form_input('free_price_'.$region->region_id,$region->free_price,'size=5 class="textbox require" '.($perm_edit?'':'disabled'));?>免邮
                </td>
			</tr>	
            <?php endforeach;?>
			
			<?php if ($perm_edit): ?>
				<tr>
					<td class="item_title"></td>
					<td class="item_input">
						<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
					</td>
				</tr>
			<?php endif ?>
			
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
