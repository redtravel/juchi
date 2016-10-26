<?php include(APPPATH.'views/common/header.php');?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/validator.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        function check_form(){
            var validator = new Validator('mainForm');
            //validator.isNullOption('invoice_no', '运单号不能为空',true);
            return validator.passed();
        }
        function selectShipping(obj,old_id,old_no){
            var shipping_id = $(obj).val();
            if(old_id == shipping_id){
                $('#invoice_no').val(old_no);
            }else{
                $('#invoice_no').val('');
            }
        }
        //]]>
    </script>
<div class="main">
	<div class="main_title"><span class="l">订单快递信息 >> 编辑</span> <a href="pick/scan_shipping_list" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('pick/scan_shipping_edit_save',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd">
                    <input name="order_id" value="<?php print $row->order_id; ?>" type="hidden"/>
                </td>
			</tr>
			<tr>
                <td class="item_title">订单号:</td>
                <td class="item_input">
                    <?php print $row->order_sn; ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">快递公司:</td>
                <td class="item_input">
                    <select name="shipping_id" onchange="selectShipping(this,'<?php print $row->shipping_id; ?>','<?php print $row->invoice_no; ?>');">
                        <?php foreach ($shipping_list as $shipping): ?>
                            <option value="<?php print $shipping->shipping_id; ?>" <?php print $row->shipping_id==$shipping->shipping_id?'selected="selected"':''; ?>><?php print $shipping->shipping_name; ?></option>
                        <?php endforeach;?>
                    </select>
                </td>
			</tr>
			<tr>
                <td class="item_title">运单号:</td>
                <td class="item_input">
                    <input id="invoice_no" name="invoice_no" value="<?php print $row->invoice_no; ?>"/>
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