<?php include(APPPATH . 'views/common/header.php'); ?>

<div class="main">
    <div class="main_title"><span class="l"><span class="l">页面SEO管理 >> 增加</span></span> <span class="r"><a href="page_seo/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('page_seo/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">代码:</td>
            <td class="item_input">
<input name="code" class="textbox require" id="code" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">名称:</td>
            <td class="item_input">
<input name="name" class="textbox require" id="name" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">标题:</td>
            <td class="item_input">
<input name="title" class="textbox require" id="title" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">关键字:</td>
            <td class="item_input">
<input name="keywords" class="textbox require" id="keywords" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">描述:</td>
            <td class="item_input">
<input name="description" class="textbox require" id="description" value="" type="text"/>
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