<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
    //<![CDATA[
    function check_form(){
        var validator = new Validator('mainForm');
        return validator.passed();
    }
    function change_type(){
        var inventory_type = $('input[type=radio][name=inventory_type]:checked').val();
        if (inventory_type === '0') {
            $('#shelf').show();
            $('#location').hide();
        } else if(inventory_type === '1') {
            $('#shelf').hide();
            $('#location').show();
        }
    }
    //]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">盘点管理 >> 新增 </span><a href="inventory/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('inventory/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan=2 class="topTd"></td>
            </tr>
            <tr>
                <td class="item_title">仓库:</td>
                <td class="item_input"><?php print form_dropdown('depot_id',$depot_arr);?></td>
            </tr>
            <tr>
                <td class="item_title">编号:</td>
                <td class="item_input"><?php print form_input('inventory_sn',$inventory_sn,'class="textbox"'.' readonly'); ?></td>
            </tr>
            <tr>
                <td class="item_title">类型:</td>
                <td class="item_input">
                    <label><input type="radio" name="inventory_type" value="0" checked="TRUE" onchange="change_type();" />指定货架范围盘点</label>
                    <label><input type="radio" name="inventory_type" value="1" onchange="change_type();" />指定储位盘点</label>
                </td>
            </tr>
            <tr id="shelf">
                <td class="item_title">货架范围:</td>
                <td class="item_input">
                    <?php print form_input(array('name' => 'shelf_from','class' => 'textbox')); ?>
                    至
                    <?php print form_input(array('name' => 'shelf_to','class' => 'textbox')); ?>
                </td>
            </tr>
            <tr id="location" style="display: none">
                <td class="item_title">储位名称:</td>
                <td class="item_input"><?php print form_input('location_name','','class="textbox"'); ?></td>
            </tr>
            <tr>
                <td class="item_title">备注:</td>
                <td class="item_input"><textarea name="inventory_note" cols="43" rows="3"></textarea></td>
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