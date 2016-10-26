<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
            var validator = new Validator('mainForm');
            validator.required('order_sn', '请务删除系统订单号');
            validator.required('track_return_sn', '请填写平台订单号');
            return validator.passed();
	}

	//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">天猫退单管理 >> 编辑</span>  <a href="return_track/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('return_track/proc_edit/'.$order->order_sn,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan=2 class="topTd"></td>
            </tr>
            <tr>
                <td class="item_title">系统订单号:</td>
                <td class="item_input">
                    <input name="order_sn" class="textbox require" value="<?php print $order->order_sn;?>" disabled="disabled" />
                    <input name="apply_id" type="hidden" value="<?php print $apply_return['apply_id'];?>" />
                    <input name="invoice_no" type="hidden" value="<?php print $apply_return['invoice_no'];?>" />
                    <a href ="order/info/<?php print $order->order_id; ?>" target="_blank">查看</a>
                </td>
            </tr>
            <tr>
                <td class="item_title">天猫退单号:</td>
                <td class="item_input"><input name="track_return_sn" value="<?php echo empty($return_track) ? '' : $return_track->track_return_sn;?>" class="textbox require" /></td>
            </tr>
            <tr>
                <td class="item_title">天猫退货物流公司:</td>
                <td class="item_input"><input name="track_shipping_name" value="<?php echo empty($return_track) ? '' : $return_track->track_shipping_name;?>" class="textbox" /></td>
            </tr>
            <tr>
                <td class="item_title">天猫退货物流单号:</td>
                <td class="item_input"><input name="track_shipping_sn" value="<?php echo empty($return_track) ? '' : $return_track->track_shipping_sn;?>" class="textbox" /></td>
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