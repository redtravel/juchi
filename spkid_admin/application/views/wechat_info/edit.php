<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=wechat_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
    $('input[type=text][name=register_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l"><span class="l">活动微信用户管理 >> 增加/编辑</span></span> <span class="r"><a href="wechat_info/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('wechat_info/proc_edit', array('name' => 'mainForm', 'onsubmit' => 'return check_form()'),array('wechat_id' => $row->wechat_id)); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">微信openid:</td>
            <td class="item_input">
<input name="wechat_openid" class="textbox require" id="wechat_openid" value="<?=$row->wechat_openid;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">昵称:</td>
            <td class="item_input">
<input name="wechat_nickname" class="textbox require" id="wechat_nickname" value="<?=$row->wechat_nickname;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">性别:</td>
            <td class="item_input">
<input name="wechat_sex" class="textbox require" id="wechat_sex" value="<?=$row->wechat_sex;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">市:</td>
            <td class="item_input">
<input name="wechat_city" class="textbox require" id="wechat_city" value="<?=$row->wechat_city;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">省:</td>
            <td class="item_input">
<input name="wechat_province" class="textbox require" id="wechat_province" value="<?=$row->wechat_province;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">国家:</td>
            <td class="item_input">
<input name="wechat_country" class="textbox require" id="wechat_country" value="<?=$row->wechat_country;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">头像:</td>
            <td class="item_input">
<input name="wechat_headimgurl" class="textbox require" id="wechat_headimgurl" value="<?=$row->wechat_headimgurl;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">活动编号:</td>
            <td class="item_input">
<input name="tuan_id" class="textbox require" id="tuan_id" value="<?=$row->tuan_id;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">授权时间:</td>
            <td class="item_input">
<input name="wechat_date" class="textbox require" id="wechat_date" value="<?php print date('Y-m-d', strtotime($row->wechat_date));?>" type="text"/> <input name="wechat_day" class="textbox require"  id="wechat_day" value="<?php print date('H:i:s', strtotime($row->wechat_date));?>" type="text" />
            </td>
        </tr>
        <tr>
            <td class="item_title">报名姓名:</td>
            <td class="item_input">
<input name="register_name" class="textbox require" id="register_name" value="<?=$row->register_name;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">联系方式:</td>
            <td class="item_input">
<input name="register_mobile" class="textbox require" id="register_mobile" value="<?=$row->register_mobile;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">购买数量:</td>
            <td class="item_input">
<input name="register_num" class="textbox require" id="register_num" value="<?=$row->register_num;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">报名日期:</td>
            <td class="item_input">
<input name="register_date" class="textbox require" id="register_date" value="<?php print date('Y-m-d', strtotime($row->register_date));?>" type="text"/><input name="register_day" class="textbox require"  id="register_day" value="<?php print date('H:i:s', strtotime($row->register_date));?>" type="text" />
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