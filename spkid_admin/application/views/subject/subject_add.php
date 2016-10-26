<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
    //<![CDATA[
    function check_form(){
        var validator = new Validator('mainForm');
        validator.required('start_date', '请填写活动开始时间！');
        validator.required('end_date', '请填写活动结束时间！');
        validator.required('page_file', '请填写生成文件名称！');
        validator.required('subject_title', '请填写活动标题！');
        if (validator.passed()) {
            var start_date = $('input[name=start_date]').val();
            var end_date = $('input[name=end_date]').val();

            var start = new Date(Date.parse(start_date.replace(/-/g,"/")));
            var end = new Date(Date.parse(end_date.replace(/-/g,"/")));
            var now = new Date();
            if (end <= start) {
                alert('-结束时间应大于开始时间！');
                return false;
            } else if (end <= now) {
                alert('-结束时间应大于当前时间！');
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    $(function(){
        $(':input[name=start_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
        $(':input[name=end_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
    });
    //]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">活动专题管理 >> 新增 </span><a href="subject/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('subject/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan=2 class="topTd"></td>
            </tr>
            <tr>
                <td class="item_title">有效期范围:</td>
                <td class="item_input">
                <?php print form_input('start_date', '', 'class="require textbox"');?> 
                至
                <?php print form_input('end_date', '', 'class="require textbox"');?> 
                </td>
            </tr>
            <tr>
                <td class="item_title">生成文件名:</td>
                <td class="item_input">
                    http://域名/zhuanti/<?php print form_input(array('name'=> 'page_file','class'=> 'textbox require'));?>.html
                </td>
            </tr>
            <tr>
                <td class="item_title">标题（title）:</td>
                <td class="item_input"><?php print form_input(array('name'=> 'subject_title','class'=> 'textbox require'));?></td>
            </tr>
            <tr>
                <td class="item_title">关键字（keywords）:</td>
                <td class="item_input"><?php print form_input(array('name' => 'subject_keyword','class' => 'textbox')); ?></td>
            </tr>
            <tr>
                <td class="item_title">简介（description）:</td>
                <td class="item_input"><textarea name="page_desc" id="brand_info" cols="80" rows="3"></textarea></td>
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
