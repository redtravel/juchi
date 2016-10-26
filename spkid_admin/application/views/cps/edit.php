<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
    //<![CDATA[
    function check_form(){
        var validator = new Validator('mainForm');
        validator.required('cps_sn', '请填写SN');
        validator.required('cps_name', '请填写CPS名称');
        validator.isInt('cps_cookie_time', '请正确填写COOKIE有效时间');
        return validator.passed();
    }

    //]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">CPS管理 >> 编辑</span> <span class="r"><a href="cps/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('cps/proc_edit/' . $cps->cps_id, array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">SN:</td>
            <td class="item_input">
                <input name="cps_sn" class="textbox require" id="cps_sn" value="<?php echo $cps->cps_sn ?>" /></td>
        </tr>
        <tr>
            <td class="item_title">CPS名称:</td>
            <td class="item_input">
                <input name="cps_name" class="textbox require" id="cps_name" value="<?php echo $cps->cps_name ?>" /></td>
        </tr>
        <tr>
            <td class="item_title">COOKIE有效时间:</td>
            <td class="item_input">
                <input name="cps_cookie_time" class="textbox require" id="cps_cookie_time" value="<?php echo $cps->cps_cookie_time ?>" /></td>
        </tr>
        <tr>
            <td class="item_title">开始日期:</td>
            <td class="item_input">
                <input name="cps_start_time" class="textbox require" id="cps_start_time" value="<?php echo $cps->cps_start_time ?>" /></td>
        </tr>
        <tr>
            <td class="item_title">结束日期:</td>
            <td class="item_input">
                <input name="cps_shut_time" class="textbox require" id="cps_shut_time" value="<?php echo $cps->cps_shut_time ?>" /></td>
        </tr>
        <tr>
            <td class="item_title">DATA:</td>
            <td class="item_input">
                <textarea id="cps_data" class="text require" rows="10" cols="50" name="cps_data"><?php echo $cps->cps_data ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="item_title">SCRIPT:</td>
            <td class="item_input">
                <textarea id="cps_rtn_script" class="text require" rows="30" cols="50" name="cps_rtn_script"><?php echo $cps->cps_rtn_script ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="item_title">状态：</td>
            <td class="item_input">        
                <input type="radio" value="0" id="cps_status" name="cps_status" <?php echo $cps->cps_status == 0 ? 'checked="checked"' : ''; ?> /> 无效
                <input type="radio" value="1" id="cps_status" name="cps_status" <?php echo $cps->cps_status == 1 ? 'checked="checked"' : ''; ?> /> 有效
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