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
    <div class="main_title"><span class="l">活动专题管理 >> 编辑 </span><a href="subject/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('subject/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('subject_id'=>$row->subject_id));?>
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan=2 class="topTd"></td>
            </tr>
            <tr>
                <td class="item_title">有效期范围:</td>
                <td class="item_input">
                    <?php print form_input('start_date',$row->start_date,'class="textbox require" '.($perm_edit?'':'disabled'));?>
                    至
                    <?php print form_input('end_date',$row->end_date,'class="textbox require" '.($perm_edit?'':'disabled'));?>
                </td>
            </tr>
            <tr>
                <td class="item_title">生成文件名:</td>
                <td class="item_input">
                    http://域名/zhuanti/<?php print form_input('page_file',$row->page_file,'class="textbox require" '.($perm_edit?'':'disabled'));?>.html
                </td>
            </tr>
            <tr>
                <td class="item_title">标题（title）:</td>
                <td class="item_input"><?php print form_input('subject_title',$row->subject_title,'class="textbox require" '.($perm_edit?'':'disabled'));?></td>
            </tr>
            <tr>
                <td class="item_title">关键字（keywords）:</td>
                <td class="item_input"><?php print form_input('subject_keyword',$row->subject_keyword,'class="textbox" '.($perm_edit?'':'disabled'));?></td>
            </tr>
            <tr>
                <td class="item_title">简介（description）:</td>
                <td class="item_input"><textarea name="page_desc" id="brand_info" cols="80" rows="3"><?php print($row->page_desc);?></textarea></td>
            </tr>

            <?php if ($perm_edit): ?>
                <tr>
                    <td class="item_title"></td>
                    <td class="item_input">
                        <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    </td>
                </tr>
            <?php endif ?>

            <tr>
                <td colspan=2 class="bottomTd"></td>
            </tr>
        </table>
    <?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
