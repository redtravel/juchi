<?php include(APPPATH . 'views/common/header.php'); ?>
<style>
.ui-sortable-helper {
    display: table;
}
</style>
<div class="main">
<form class="am-form am-form-horizontal" action="/generate_code/do_proc" id="form" method="post" name="form">
  <fieldset>
    <legend>自动生成代码</legend>
 
  <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">上级菜单</label>
    <div class="am-u-sm-10"> <label class="am-form-label am-icon-edit"><span data-type="select" data-title="选择上级菜单" id="parent_id_name"> 
        <?php foreach( $top_menus AS $menu ): ?>
            <?php if($parent_id == $menu->action_id) echo $menu->menu_name;?>
        <?php endforeach; ?> </span></label>
        <input type="hidden" name="parent_id" id="parent_id" class="am-form-field" value="<?=$parent_id?>">
    </div>
  </div>

  <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">关键字code</label>
    <div class="am-u-sm-10"> <label class="am-form-label"><?=$code;?> </label><input type="hidden" name="code" id="code" class="am-form-field" value="<?=$code?>">
    </div>
  </div>

  <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">关键字名称</label>
    <div class="am-u-sm-10"> <label class="am-form-label"><?=$name;?> </label><input type="hidden" name="name" id="name" class="am-form-field" value="<?=$name?>">
    </div>
  </div>
  
  <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">数据库表名：</label>
    <div class="am-u-sm-10"> <label class="am-form-label"><?=$table;?></label> <input type="hidden" id="table" class="am-form-field" name="table" value="<?=$table?>">
    </div>
  </div>

    <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">默认生成文件：</label>
       <label class="am-checkbox-inline">
       <input type="checkbox" name="gen_model" value="1" checked> model
       </label>
       <label class="am-checkbox-inline">
       <input type="checkbox" name="gen_controller" value="1" checked> controller
       </label>
    </div>

  <div class="am-form-group">
    <label for="row_deletable" class="am-u-sm-2 am-form-label">列表行是否可删除</label>
    <div class="am-u-sm-10">

      <label class="am-radio-inline">
        <input type="radio"  value="1" name="row_deletable" checked> 可删
      </label>
      <label class="am-radio-inline">
        <input type="radio" value="0" name="row_deletable"> 不可删
      </label>

    </div>
  </div>
<hr>
<!-- 表字段列表 -->
<table class="am-table am-table-bordered am-table-striped am-table-hover">
<thead>
        <tr>
            <th>[排序]字段</th>
            <th width=200>字段名称</th>
            <th>数据源</th>
            <th>操作</th>
        </tr>
</thead>
<tbody id="field_table">
<?php foreach ( $fields AS $field ){ ?>
        <tr>
        <td><span class="am-icon-sort"><?=$field->name;?> <input type="hidden" value='<?=$field->name;?>' name="using_fields[]"/> </span><br/>
        <span class="am-badge am-badge-primary am-radius"><?=$field->type;?><?php if($field->max_length){ ?>(<?=$field->max_length;?>)<?php } ?></span>
<span class="am-badge am-badge-secondary am-radius">default:<? if(is_null($field->default)) echo 'NULL';else echo $field->default; ?></span>
<?php if ( $field->primary_key == '1' ):?>
<input type="hidden" value='<?=$field->name;?>' name="pk"/>
<span class="am-badge am-badge-warning am-radius">主键</span>
<?php endif; ?>

</td>
        <td> <input name="<?php echo $field->name;?>_label" type="text" class="am-form-field am-radius" placeholder="字段名称" value="<?php echo $field->comment;?>"/> </td>
        <td>
<input type="text" value='' name="<?php echo $field->name;?>_source_settings" class="am-form-field am-radius" placeholder="参数配置的变量名/函数名，需要是数组"/>
</td>
<td>
    <div class="am-form-group">
    <?php foreach( $op_names AS $op ): ?>
       <label class="am-checkbox-inline">
       <input type="checkbox" name="<?=$op['type']?>[]" value="<?=$field->name;?>" 
           <?php if ($field->primary_key=='1' && isset($op['pk_disable']))echo ' disabled';?>
           <?php if(!isset($op['pk_disable'])) foreach( $op['defaults'] AS $op_default) echo $op_default;?>
       > <?=$op['name'];?>
       </label>
    <?php endforeach; ?>
    </div>
</td>
        </tr>
<?php }?>
</tbody>
</table>
  <button onclick="check_table();" type="button" class="am-btn am-btn-primary am-btn-block">下一步</button> 
  
  </fieldset>
</form>
</div>
<script>
<!--
function check_table(){
    $.ajax({
        type: "POST",
            url: "generate_code/check_table",
            data: "table="+$('#table').val(),
            dataType: "json",
            success: function(result){
                if(result.success){$('#form').submit();}
                else{
            alert(result.msg);        
                }
            }

    });
}
$(function(){

    // table sortable 
$('#field_table').sortable({
    placeholder: "ui-state-highlight"
});
// editable
    $('#parent_id_name').editable( {
        value:<?echo $parent_id;?>,
            source:[
<?php foreach ( $top_menus AS $menu ):?>
                { text:'<? echo $menu->action_name;?>', value:'<?php echo $menu->action_id;?>' },
<?php endforeach; ?>
            ]
    }).on('save',function(e, params){
        $('#parent_id').val( params.newValue );
    });
});
//-->
</script>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
