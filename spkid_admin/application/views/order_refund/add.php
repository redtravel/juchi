<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript">
function check_form(){
    var v_msg = '';
    $(".require").each(function(i, obj){
        var input_val = $(obj).val();
        if (input_val == ''){
            var title = $(obj).parent().siblings('td').html().replace(':', '');
            v_msg += title + '不能为空！\n';            
        }
    });
    if (v_msg != ''){
        alert(v_msg);
        return false;
    }
    return true;
}
</script>
<div class="main">
    <div class="main_title"><span class="l"><span class="l">订单退款管理 >> 增加/编辑</span></span> <span class="r"><a href="order_refund/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('order_refund/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">订单号:</td>
            <td class="item_input">
<input name="order_sn" class="textbox require" id="order_sn" value="<?=$order_sn?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">退款类型:</td>
            <td class="item_input">
<?php print form_dropdown("r_type",$fields_source["r_type"],array(""),"data-am-selected");?>
            </td>
        </tr>
        <tr>
            <td class="item_title">退款金额:</td>
            <td class="item_input">
<input name="amount" class="textbox require" id="amount" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">备注:</td>
            <td class="item_input">
                <textarea name="remark" id="remark" cols="80" rows="3"></textarea>
            </td>
        </tr>


        <tr>
            <td class="item_title"></td>
            <td class="item_input">
                <?php print form_submit(array('name' => 'mysubmit', 'class' => 'am-btn am-btn-primary', 'value' => '提交')); ?>
            </td>
        </tr>
        <tr>
            <td colspan=2 class="bottomTd"></td>
        </tr>
    </table>
    <?php print form_close(); ?>
</div>
<?php include(APPPATH . 'views/common/footer.php'); ?>