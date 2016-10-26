<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
    $(function(){
        if(<?=$shop_id?>>0){
            $("input[name=shop_sn]").attr("readonly",true);    
        }
        else{
            $("input[name=shop_sn]").attr("readonly",false);    
        }
    });
    
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('shop_name', '请填写店铺名称');
			validator.required('shop_sn', '请填写店铺sn');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
        <span class="l">店铺管理 >> <?=$shop_id==0?"新增":"修改"?> </span>
        <a href="shop/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open('shop/proc_add/'.$shop_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">店铺名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'shop_name',
                            'value'=>@$row->shop_name,'class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">店铺sn:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'shop_sn',
                            'value'=>@$row->shop_sn,'class'=> 'textbox require',
                            'style'=>'text-transform: uppercase;'
                            ));?>&nbsp;&nbsp;可由字母和数字组合</td>
			</tr>
			<tr>
				<td class="item_title">单商品生成订单:</td>
				<td class="item_input">
                    <label><?php print form_radio(array('name'=>'single_order', 'value'=>1,
                                'checked'=>($shop_id==0||@$row->single_order==0)?false:true)); ?>是</label>
					<label><?php print form_radio(array('name'=>'single_order', 'value'=>0,
                                'checked'=>($shop_id==0||@$row->single_order==0)?true:false)); ?>否</label>
				</td>
			</tr>
            <tr>
				<td class="item_title">支持货到付款:</td>
				<td class="item_input">
                    <label><?php print form_radio(array('name'=>'is_cod', 'value'=>1,
                                'checked'=>($shop_id==0||@$row->is_cod==1)?true:false)); ?>是</label>
					<label><?php print form_radio(array('name'=>'is_cod', 'value'=>0,
                                'checked'=>($shop_id==0||@$row->is_cod==1)?false:true)); ?>否</label>
				</td>
			</tr>
            <tr>
				<td class="item_title">供应商发货:</td>
				<td class="item_input">
                    <label><?php print form_radio(array('name'=>'shop_shipping', 'value'=>1,
                                'checked'=>($shop_id==0||@$row->shop_shipping==0)?false:true)); ?>是</label>
					<label><?php print form_radio(array('name'=>'shop_shipping', 'value'=>0,
                                'checked'=>($shop_id==0||@$row->shop_shipping==0)?true:false)); ?>否</label>
				</td>
			</tr>
            <tr>
				<td class="item_title">是否可用:</td>
				<td class="item_input">
                    <label><?php print form_radio(array('name'=>'shop_status', 'value'=>1,
                                'checked'=>($shop_id==0||@$row->shop_status==1)?true:false)); ?>是</label>
					<label><?php print form_radio(array('name'=>'shop_status', 'value'=>0,
                                'checked'=>($shop_id==0||@$row->shop_status==1)?false:true)); ?>否</label>
				</td>
			</tr>
		    <tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
