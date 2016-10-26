<?php include(APPPATH . 'views/common/header.php'); ?>
<div class="main">
<form class="am-form am-form-horizontal" action="/generate_code/step.html" id="form" method="post" onsubmit="return check_table();">
  <fieldset>
    <legend>自动生成代码</legend>

  <div class="am-form-group">
    <label class="am-u-sm-2 am-form-label">上级菜单</label>
    <div class="am-u-sm-10">
        <select name="parent_id" data-am-selected="{maxHeight: 200}">
        <?php foreach( $top_menus AS $menu ): ?>
        <option value="<?=$menu->action_id;?>"><?=$menu->menu_name;?></option>
        <?php endforeach; ?>
        </select>
    </div>
  </div>
  <div class="am-form-group">
    <label for="code" class="am-u-sm-2 am-form-label">关键字code</label>
    <div class="am-u-sm-10">
      <input type="text" name="code" id="code" class="am-form-field" placeholder="会据此生成文件名，目录名等">
    </div>
  </div>

  <div class="am-form-group">
    <label for="name" class="am-u-sm-2 am-form-label">关键字名称</label>
    <div class="am-u-sm-10">
      <input type="text" id="name" name="name" class="am-form-field" placeholder="会据此生成权限名称，菜单名称">
    </div>
  </div>
  
  <div class="am-form-group">
    <label for="table" class="am-u-sm-2 am-form-label">数据库表名：</label>
    <div class="am-u-sm-10">
      <input type="text" id="table" name="table" class="am-form-field" placeholder="当前DB下的表名">
<span class="am-badge am-badge-warning am-radius">提示：</span> 仅对ty_开头的表有效
    </div>
  </div>
  <button type="submit" class="am-btn am-btn-primary am-btn-block">下一步</button> 
  
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
                if(result.success){return true;}
                else{
                    alert(result.msg);return false;       
                }
            }

    });
}
//-->
</script>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
