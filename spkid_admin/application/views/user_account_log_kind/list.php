<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>
<div class="main">
		<div class="main_title"><span class="l">会员管理 >> 变动类型列表</span> <span class="r"><a href="user_account_log_kind/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="117">变动CODE</th>
			      <th width="564">变动名称</th>
			      <th width="173">是否使用</th>
			      <th width="198">创建时间</th>
			      <th width="118">操作</th>
				</tr>
				
                
                <?php foreach($arr as $item):?>
			    <tr class="row">
			    	<td align="center"><?php echo $item->change_code?></td>
					<td><?php echo $item->change_name?></td>
					<!--<td><img src="public/images/<?php /*echo $item->is_use == 0 ? 'no' : 'yes'*/?>.gif" /></td>样式显示 By Rock-->
                    <td><span class="<?php echo $item->is_use == 0 ? 'noForGif' : 'yesForGif'?>"></span></td>
					<td><?php echo $item->create_date?></td>
					<td>
                    	
                    	
						<a href="user_account_log_kind/edit/<?php echo $item->change_code?>" title="编辑" class="edit"></a>
						<?php if($perms['uaccount_k_edit'] == 1):?>
                        <?php if(!in_array($item->change_code,$log_arr)):?>
                        <a class="del" href="javascript:void(0);" rel="user_account_log_kind/del/<?php print $item->change_code?>" title="删除" onclick="do_delete(this)"></a>
                        <?php endif;?>
                        <?php endif;?>
			      </td>
			    </tr>
				<?php endforeach;?>
                
                
                
			    <tr>
					<td colspan="6" class="bottomTd"> </td>
				</tr>
			</table>
    <div class="blank5"></div>

</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
