<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('input[type=text][name=comment_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

});
//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l"><span class="l">团购活动评论管理 >> 增加/编辑</span></span> <span class="r"><a href="mami_tuan_comment/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('mami_tuan_comment/proc_edit', array('name' => 'mainForm', 'onsubmit' => 'return check_form()'),array('comment_id'=>$row->comment_id)); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">微信用户编号:</td>
            <td class="item_input">
<input name="wechat_id" class="textbox require" id="wechat_id" value="<?=$row->wechat_id;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">活动编号:</td>
            <td class="item_input">
<input name="tuan_id" class="textbox require" id="tuan_id" value="<?=$row->tuan_id;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">评论内容:</td>
            <td class="item_input">
<input name="comment_content" class="textbox require" id="comment_content" value="<?=$row->comment_content;?>" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">评论时间:</td>
            <td class="item_input">
<input name="comment_date" class="textbox require" id="comment_date" value="<?=$row->comment_date;?>" type="text"/>
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