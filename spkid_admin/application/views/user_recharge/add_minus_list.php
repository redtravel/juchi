<?php include(APPPATH.'views/common/header.php'); ?>
<div class="main_title"><span class="l"><?php if($flag==1)echo "批量减款";else echo "批量充值";?> </span>
<form method="post" action="recharge/index?flag=<?php echo $flag;?>" name="theForm" enctype="multipart/form-data">
    <div>
        <br/>
        <b>账户文件：</b>
        <input type="file" name="unload_file" value=""/>
        <input type="hidden" name="act" value="upload"/>
        <input type="submit" class="am-btn am-btn-primary" value="提交" style='margin-left:30px;' />
    </div>
    <div>
        <label><b>文件模板：</b></label>
        <span>
           模板文件内容中，部分地方有严格的要求：第三列式登录账号，第四列是要操作的金额。 
           待上传文件，只支持CSV格式文件。可以从<a href="/public/import/_template/accounts.csv" target="_blank"><font color="red">此处下载</font></a>。
        </span>
    </div>
    <!--<input type="hidden" name="act" value="display" />--> 
    
</form>
<?php if($unavail_accounts):?>
<h4><font color='red'> 账号不可登陆或者账号余额不足：</font></h4>
<div class="list-div" id="listDiv">
<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
    <thead>
        <tr><th>用户ID</th>
            <?php foreach($table_heads as $th): ?>
            <th><?php echo $th;?></th>
           <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        
      <?php foreach($unavail_accounts as $as): ?>
      <tr>
          <?php foreach($as as $a): ?>
          <td><?php echo $a;?></td>
          <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
        
    </tbody>
</table>
</div>
<?php endif;?>
<?php if($avail_accounts):?>
<h4>可登陆账号：</h4>
<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
    <tbody>
        <thead>
        <tr><th>用户ID</th>
           <?php foreach($table_heads as $row): ?>
            <th><?php echo $row;?></th>
           <?php endforeach; ?>
        </tr>
        </thead>
        
      <?php foreach($avail_accounts as $row): ?>
      <tr>
          <?php foreach($row as $a): ?>
          <td><?php echo $a?></td>
          <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
        
    </tbody>
</table>
<?php if($minus_button):?>
<form name="do_form" action="recharge/minus" method="post" id="do_form">
    <input type="hidden" name="avail_accounts_encoded" value="<?php echo $avail_accounts_encoded;?>" />
    <input type="hidden" name="accounts_minus" value="<?php echo $accounts_minus;?>" />
    <input type="hidden" name="table_heads_encoded" value="<?php  echo $table_heads_encoded;?>" />
    <input type="hidden" name="user_ids" value="<?php echo $user_ids;?>" />
    <?php if($unavail_accounts):?><input type="hidden" name="unavail_accounts_encoded" value="<?php echo $unavail_accounts_encoded;?>" /><?php endif;?>
    <input type="hidden" name="act" value="minus" />
    <input type="hidden" name="flag" value="<?php echo $flag;?>" />
    <?php if($flag==1&&$show_button):?><input type="button" name="abc" value="减款" class='am-btn am-btn-primary' id="minus"/><?php endif;?>
    <?php if($flag==2&&$show_button):?><input type="button" name="abc" value="充值" class='am-btn am-btn-primary' id="minus" /><?php endif;?>
</form>
<?php endif;?>
<?php endif;?>
<script>
$('#minus').click(function(){
    $('#minus').hide()
    $('form[name="do_form"]').submit()
})
</script>

<?php if($archives):?>
        <table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
        <tr>
            <th>时间</th><th>操作人</th><th>成功文档</th><th>失败文档</th>
        </tr>
        <?php foreach($archives as $archive): ?>
        
        <tr><?php foreach($archive as $key=>$an): ?>
                <?php //if($key!=2):?>
                <td><?php echo $an;?></td>
                <?php //endif;?>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
       </table>
    <?php endif;?>
