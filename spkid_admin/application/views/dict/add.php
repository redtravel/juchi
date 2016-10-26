<?php include(APPPATH . 'views/common/header.php'); ?>

<div class="main">
    <div class="main_title"><span class="l"><span class="l">数据字典管理 >> 增加</span></span> <span class="r"><a href="dict/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('dict/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">类型:</td>
            <td class="item_input">
<?php print form_dropdown("dict_id",$fields_source["dict_id"],array(""),"data-am-selected");?>
            </td>
        </tr>
        <tr>
            <td class="item_title">自定义ID:</td>
            <td class="item_input">
<input name="field_id" class="textbox require" id="field_id" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">数值1:</td>
            <td class="item_input">
<input name="field_value1" class="textbox require" id="field_value1" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">数值2:</td>
            <td class="item_input">
<input name="field_value2" class="textbox require" id="field_value2" value="" type="text"/>
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