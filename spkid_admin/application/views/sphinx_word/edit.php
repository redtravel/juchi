<?php include(APPPATH . 'views/common/header.php'); ?>

<div class="main">
    <div class="main_title"><span class="l"><span class="l">产品搜索分词管理 >> 增加/编辑</span></span> <span class="r"><a href="sphinx_word/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('sphinx_word/proc_edit/<?php echo $row->id?>', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">分词:</td>
            <td class="item_input">
<input name="name" class="textbox require" id="name" value="<?=$row->name;?>" type="text"/>
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