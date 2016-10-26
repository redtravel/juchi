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
    <div class="main_title"><span class="l"><span class="l">微信转发配置管理 >> 增加/编辑</span></span> <span class="r"><a href="weixin_settings/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('weixin_settings/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">memcache key:</td>
            <td class="item_input">
<input name="key_code" class="textbox require" id="key_code" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">微信转发标题:</td>
            <td class="item_input">
<input name="title" class="textbox require" id="title" value="" type="text" style="width: 450px;"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">微信转发描述:</td>
            <td class="item_input">
<textarea name="describe" id="describe" cols="80" rows="4" class="require"></textarea>
            </td>
        </tr>
        <tr>
            <td class="item_title">微信转发图片:</td>
            <td class="item_input">
<input name="file_url" class="textbox require" id="file_url" value="" type="file"/>
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