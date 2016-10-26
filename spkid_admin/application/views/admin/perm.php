<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript">
	//<![CDATA[
	function toggle_perm (action_code) {
		var admin_id = $(':hidden[name=admin_id]').val();
		$.ajax({
			url:'admin/toggle_perm',
			data:{action_code:action_code,admin_id:admin_id, rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if (result.msg) {alert(result.msg)};
				if (result.err) {return false;};
				$('#perm_'+action_code).removeClass('status_on');
				if(result.status) $('#perm_'+action_code).addClass('status_on');
			}
		});
	}
	function toggle_group (action_code) {
		var admin_id = $(':hidden[name=admin_id]').val();
		$.ajax({
			url:'admin/toggle_group',
			data:{action_code:action_code,admin_id:admin_id, rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if (result.msg) {alert(result.msg)};
				if (result.err) {return false;};
				$('#perm_group_'+action_code+' .perm').removeClass('status_on');
				if(result.status) $('#perm_group_'+action_code+' .perm').addClass('status_on');
			}
		});
	}

	function toggle_supper() {
		var admin_id = $(':hidden[name=admin_id]').val();
		$.ajax({
			url:'admin/toggle_supper',
			data:{admin_id:admin_id, rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if (result.msg) {alert(result.msg)};
				if (result.err) {return false;};
				$('.perm').removeClass('status_on');
				if (result.status) {
					$('button[name=supper_perm]').html('当前超级权限').css('color','red');
				}else{
					$('button[name=supper_perm]').html('当前普通权限').css('color','black');
				};
				
			}
		});
	}
	//]]>
</script>
<style type="text/css">
.perm_group{margin:3px 5px; height:22px; white-space:nowrap; float:left; border:solid 1px #7F9DB9; background:url(public/style/img/block_bg.png) repeat-x;}
.perm_group span{ height:22px; line-height:22px; display:inline-block; padding:0 5px; white-space:nowrap;}	
.perm_title{background:url(public/style/img/bf_bg.png) repeat-x; cursor:pointer;}
.perm{cursor:pointer; color:#656565; }
.status_on{color: #F00;}
</style>
<div class="main">
	<div class="main_title"><span class="l">管理员管理 >> 权限设置</span> <a href="admin" class="return r">返回列表</a></div>
	<div class="blank5"></div>
		<?php print form_open('admin/proc_perm', '', array('admin_id'=>$admin->admin_id));?>
		<table class="form" cellpadding=0, cellspacing=0>

			<tr>
				<td class="item_title" width="10%">管理员帐号</td>
				<td width="10%" class="item_input"><?php print $admin->admin_name; ?></td>
				<td class="item_title" width="10%">状态</td>
				<td width="70%" class="item_input">
				<?php print $admin->user_status == 1 ? '可用' : '停用'; ?>
				<?php print form_button('supper_perm', $admin->action_list=='-1'?'当前超级权限':'当前普通权限','onclick="toggle_supper();" style="'.($admin->action_list=='-1'?'color:red':'').'"')?>
			
				</td>
			</tr>
			<?php foreach ($all_action as $group): ?>
				<tr>
				<td class="item_title" width="100px"><?php print $group->action_name?></td>
				<td class="item_input" colspan="3" >
                <?php foreach ($group->sub_items as $item): ?>
						<div class="perm_group" id="perm_group_<?php print $item->action_code?>">
							<span class="perm_title" onclick="toggle_group('<?php print $item->action_code?>')"><?php print $item->action_name?></span>				
							<?php foreach ($item->sub_items as  $perm): ?>
								<span id="perm_<?php print $perm->action_code?>" onclick="toggle_perm('<?php print $perm->action_code?>')" class="perm <?php print preg_match(','.$perm->action_code.',', $admin_action) ? 'status_on':''?>" onclick="toggle_perm('<?php print $perm->action_code?>')">
								<?php print $perm->action_name?>
								</span>
							<?php endforeach ?>
						</div>
					<?php endforeach ?>
				</td>
			</tr>
			<?php endforeach ?>
			
		</table>
		<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>