<?php include(APPPATH.'views/common/header.php');?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/validator.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        function check_form(){
            var validator = new Validator('mainForm');
            return validator.passed();
        }
        function select_shipping(obj){
            if($(obj).val()==='其他'){
                var shipping_name = window.prompt("请输入一个新的快递公司名称：", "");
                if (shipping_name!=null && shipping_name!==""){
                    $(obj).append('<option value="'+shipping_name+'">'+shipping_name+'</option>');
                    $(obj).val(shipping_name);;
                }
            }
        }
        //]]>
    </script>
<div class="main">
	<div class="main_title"><span class="l">用户运费管理 >> <?php echo $perm_edit?'编辑':'查看'; ?></span> <a href="order_user_shipping_fee/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('order_user_shipping_fee/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"><input name="return_id" value="<?php print $row->return_id; ?>" type="hidden"/></td>
			</tr>
			<tr>
                <td class="item_title">退货单号:</td>
                <td class="item_input">
                    <?php print $row->return_sn; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">订单号:</td>
                <td class="item_input">
                    <?php print $row->order_sn; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">退货时间:</td>
                <td class="item_input">
                    <?php print $row->create_date; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">运费金额:</td>
                <td class="item_input">
                        <input name="user_shipping_fee" value="<?php print $row->user_shipping_fee; ?>" <?php echo $perm_edit&&$uncheck?'':'readonly'; ?>/>
                </td>
			</tr>
			<tr>
                <td class="item_title">快递公司:</td>
                <td class="item_input">
                    <select name ="shipping_name" onchange="select_shipping(this);">
                        <option>顺丰速递</option>
                        <option>申通速递</option>
                        <option>圆通速递</option>
                        <option>中通快递</option>
                        <?php foreach($shipping_name_list as $shipping_name): ?>
                            <option value="<?php echo $shipping_name->shipping_name?>" <?php echo $shipping_name->shipping_name==$row->shipping_name?'selected':''; ?> ><?php echo $shipping_name->shipping_name?></option>
                        <?php endforeach; ?>
                        <option>其他</option>
                    </select>
                </td>
			</tr>
			<tr>
                <td class="item_title">财审人:</td>
                <td class="item_input">
                    <?php print $row->admin_name; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">财审时间:</td>
                <td class="item_input">
                    <?php print $row->finance_date; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title"></td>
                <td class="item_input">
                    <?php if($perm_edit&&$uncheck): ?>
                        <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    <?php endif; ?>
                </td>
			</tr>
			<tr>
                <td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>