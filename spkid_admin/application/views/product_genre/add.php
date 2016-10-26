<?php include(APPPATH . 'views/common/header.php'); ?>

<div class="main">
    <div class="main_title"><span class="l"><span class="l">商品大类管理 >> 增加</span></span> <span class="r"><a href="product_genre/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('product_genre/proc_add', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
        <tr>
            <td class="item_title">名称:</td>
            <td class="item_input">
<input name="name" class="textbox require" id="name" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">代码:</td>
            <td class="item_input">
<input name="code" class="textbox require" id="code" value="" type="text"/>
            </td>
        </tr>
        <tr>
            <td class="item_title">虚拟产品:</td>
            <td class="item_input">
<?php print form_dropdown("virtual",$fields_source["virtual"],array(""),"data-am-selected");?>
            </td>
        </tr>
        <tr>
            <td class="item_title">是否快递:</td>
            <td class="item_input">
<?php print form_dropdown("delivery",$fields_source["delivery"],array(""),"data-am-selected");?>
            </td>
        </tr>
        <tr>
            <td class="item_title">产品名称:<br/>
<small> (名称后缀支持 日期控件<ins>#date</ins><br/> 隐藏字段<ins>#hide</ins>)</small>
</td>
            <td class="item_input">
<table>
                <?php foreach ($product_conf as $val): 
                if (empty($val)) continue;
                ?>
<tr><td class="item_title">
                <?=$val?>：
                </td><td class="item_input">
                <input type="hidden" name="product_field[]" value="<?=$val?>"/>
               <input name="product_val[]" class="textbox require" value="" type="text"/><br>
</td> </tr>
               <?php endforeach; ?>
                </table>
            </td>
        </tr>
        <tr>
            <td class="item_title">客户名称:</td>
            <td class="item_input">
<table>
                <?php foreach ($client_conf as $val): 
                if (empty($val)) continue;
                ?>
<tr><td class="item_title">
                <?=$val?>:
                </td><td class="item_input">
                <input type="hidden" name="client_field[]" value="<?=$val?>"/>
                <input name="client_val[]" class="textbox require" value="" type="text"/>
</td> </tr>
                <?php endforeach; ?>
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
