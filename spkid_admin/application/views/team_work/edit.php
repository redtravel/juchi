<?php include(APPPATH . 'views/common/header.php'); ?>

<div class="main">
    <div class="main_title"><span class="l"><span class="l">合作咨询管理 >> 增加/编辑</span></span> <span class="r"><a href="team_work/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('team_work/proc_edit/'.$row->team_id, array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">合作属性:</td>
            <td class="item_input">
                <select name="team_type" id="team_type">
                    <?php foreach ($team_type as $key => $val) { ?>
                        <option value="<?php echo $key;?>" <?php if($row->team_type == $key) echo 'selected = "selected"';?>><?php echo $team_type[$key];?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="item_title">企业名称:</td>
            <td class="item_input">
<input name="team_company" class="textbox require" id="team_company" value="<?=$row->team_company;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">联系人名字:</td>
            <td class="item_input">
<input name="team_name" class="textbox require" id="team_name" value="<?=$row->team_name;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">联系电话:</td>
            <td class="item_input">
<input name="team_tel" class="textbox require" id="team_tel" value="<?=$row->team_tel;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">联系邮箱:</td>
            <td class="item_input">
<input name="team_email" class="textbox require" id="team_email" value="<?=$row->team_email;?>" type="text"/>
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