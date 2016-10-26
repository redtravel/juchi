<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<div class="main">
    <div class="main_title"><span class="l">活动专题管理 >> 模块编辑 >> 自定义内容 </span><a href="subject/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    
    <div class="search_row">
        <?php print form_open_multipart('subject/proc_edit_module',array('name'=>'mainForm'));?>
            <input type="hidden" name="module_id" value="<?php print($row->module_id);?>" />
            <input type="hidden" name="module_type" value="<?php print($row->module_type);?>" />
            
            标题：<input type="text" class="ts" name="module_title" value="<?=$row->module_title?>" style="width:150px;" />
            位置：<select name="module_location">
                <option value="t" <?php if($row->module_location == 't'): ?> selected="true" <?php endif; ?>>头</option>
                <option value='l' <?php if($row->module_location == 'l'): ?> selected="true" <?php endif; ?>>左</option>
                <option value='r' <?php if($row->module_location == 'r'): ?> selected="true" <?php endif; ?>>右</option>
                <option value='b' <?php if($row->module_location == 'b'): ?> selected="true" <?php endif; ?>>底</option>
            </select>
            排序值：<input type="text" class="ts" name="sort_order" value="<?=$row->sort_order?>" style="width:70px;" />
            <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'保存'));?>
            <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'重置'));?>
            
            <br/>内容：<?php print $this->ckeditor->editor('module_text', $row->module_text); ?>
        <?php print form_close();?>
    </div>
    <div class="blank5"></div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>