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
    <div class="main_title"><span class="l">盘点管理 >> 编辑 </span><a href="inventory/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('inventory/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('inventory_id'=>$row->inventory_id));?>
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan=2 class="topTd"></td>
            </tr>
            <tr>
                <td class="item_title">仓库:</td>
                <td class="item_input">
                    <select name='depot_id'>
                        <?php foreach ($depot_arr as $key => $value): ?>
                        <option value='<?=$key;?>'<?php if($row->depot_id == $key) print ' selected="TRUE"'; ?>><?=$value;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="item_title">编号:</td>
                <td class="item_input"><?php print form_input('inventory_sn',$row->inventory_sn,'class="textbox"'.' disabled'); ?></td>
            </tr>
            <tr>
                <td class="item_title">类型:</td>
                <td class="item_input">
                    <label><input type="radio" name="inventory_type" value="0" <?php if($row->inventory_type == 0): ?>checked="TRUE"<?php endif; ?> onchange="change_type();" />指定货架范围盘点</label>
                    <label><input type="radio" name="inventory_type" value="1" <?php if($row->inventory_type == 1): ?>checked="TRUE"<?php endif; ?> onchange="change_type();" />指定储位盘点</label>
                </td>
            </tr>
            <tr id="shelf" <?php if($row->inventory_type == 1): ?>style="display: none"<?php endif; ?>>
                <td class="item_title">货架范围:</td>
                <td class="item_input">
                    <?php print form_input('shelf_from',$row->shelf_from,'class="textbox"'); ?>
                    至
                    <?php print form_input('shelf_to',$row->shelf_to,'class="textbox"'); ?>
                </td>
            </tr>
            <tr id="location" <?php if($row->inventory_type == 0): ?>style="display: none"<?php endif; ?>>
                <td class="item_title">储位名称:</td>
                <td class="item_input"><?php print form_input('location_name',$location ? $location->location_name : '','class="textbox"'); ?></td>
            </tr>
            <tr>
                <td class="item_title">备注:</td>
                <td class="item_input"><textarea name="inventory_note" cols="40" rows="3"><?=$row->inventory_note;?></textarea></td>
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