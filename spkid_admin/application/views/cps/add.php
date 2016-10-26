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
    <div class="main_title"><span class="l"><span class="l">CPS管理 >> 增加</span></span> <span class="r"><a href="cps/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('cps/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">SN:</td>
            <td class="item_input">
                <input name="cps_sn" class="textbox require" id="cps_sn" /></td>
        </tr>
        <tr>
            <td class="item_title">CPS名称:</td>
            <td class="item_input">
                <input name="cps_name" class="textbox require" id="cps_name" /></td>
        </tr>
        <tr>
            <td class="item_title">COOKIE有效时间:</td>
            <td class="item_input">
                <input name="cps_cookie_time" class="textbox require" id="cps_cookie_time" value="30" /></td>
        </tr>
        <tr>
            <td class="item_title">开始日期:</td>
            <td class="item_input">
                <input name="cps_start_time" class="textbox require" id="cps_start_time" value="2013-01-01" /></td>
        </tr>
        <tr>
            <td class="item_title">结束日期:</td>
            <td class="item_input">
                <input name="cps_shut_time" class="textbox require" id="cps_shut_time" value="2033-12-31" /></td>
        </tr>
        <tr>
            <td class="item_title">DATA:</td>
            <td class="item_input">
                <textarea id="cps_data" class="text require" rows="10" cols="50" name="cps_data">{}</textarea>
            </td>
        </tr>
        <tr>
            <td class="item_title">SCRIPT:</td>
            <td class="item_input">
                <textarea id="cps_rtn_script" class="text require" rows="30" cols="50" name="cps_rtn_script">{}</textarea>
            </td>
        </tr>
        <tr>
            <td class="item_title">状态：</td>
            <td class="item_input">        
                <input type="radio" value="0" id="cps_status" name="cps_status" checked="checked" /> 无效
                <input type="radio" value="1" id="cps_status" name="cps_status" /> 有效
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