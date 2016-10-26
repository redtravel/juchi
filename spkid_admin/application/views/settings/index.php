<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	
	<div class="main">
		<div class="main_title"><span class="l">参数配置</span><span class="r"><a href="system_settings/generate" class="priv">参数生成</a>&nbsp;&nbsp;&nbsp;<a href="system_settings/add" class="add">新增</a></span></div>		
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<?php foreach($list as $row): ?>
				<?php print form_open_multipart('system_settings/proc_edit',array('onsubmit'=>'return check_form()'),array('id'=>$row->id,'config_code'=>$row->config_code,'storage_type'=>$row->storage_type));?>
					<tr class="row">
						<td class="item_title"><?=$row->config_name; ?>：</td>
						<td class="item_input">
							<?php 
							if($row->type == 1 )
								print form_input($row->config_code,$row->config_value,'class="textbox require" style="width:100px;" ');
							elseif($row->type == 2 )
								print form_dropdown($row->config_code, $row->options, $row->config_value);
							elseif($row->type == 3 )
								print form_textarea(array('name'=>$row->config_code,'rows'=>4,'cols'=>80), $row->config_value,($perm_edit?'':'disabled'));
							?>
							<?='<span class="am-badge am-badge-secondary am-radius">CODE：<mark>'.$row->config_code.'</mark></span>';?>
<small data-inputclass="edi_big_width" style="white-space:normal" data-pk="<?php print $row->id; ?>" data-name="comment" class="am-icon-edit editable" data-title="修改备注(<?=$row->config_name?>):" data-type="textarea" data-value="<?php print $row->comment; ?>">
<?=$row->comment;?> 
							
</small>
						</td>
						<td>
							<?php if ($perm_edit): ?>
								<?php print form_submit(array('class'=>'am-btn am-btn-primary','value'=>'提交'));?>
							<?php endif ?>
							
							<?php if ($perm_delete): ?>
							<a class="del" href="javascript:void(0)" rel="system_settings/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
						<?php endif ?>
						</td>
					</tr>
				<?php print form_close();?>
			<?php endforeach; ?>

			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>	

<script>
// jquery editable 
function _editable(){
    $('.editable').editable({ url: '/quick_edit/system_settings', emptytext:'',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
_editable();
</script>
<style>
.edi_big_width{ width:400px; }
</style>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
