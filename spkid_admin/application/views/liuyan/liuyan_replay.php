<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript">
	function audit(comment_id){
		if(!confirm('确认审核！'))return;
		$.ajax({
		   type: "POST",
		   url: "liuyan/audit",
		   dataType:'json',
		   data: "comment_id="+comment_id,
		   success: function(i){
			 if(i.iformation == 1){
			 	alert('记录不存在');
				return;
			 }
			 if(i.iformation == 2){
			 	alert('审核成功');
				$('input[name=author_button]').val('已审核');
				$('td#audit_d').html(i.audit);
				$('input[name=author_button]').attr('disabled','disabled');
				return;
			 }
			 if(i.iformation == 3){
			 	alert('记录已经被审核');
				$('input[name=author_button]').val('已审核');
				$('input[name=author_button]').attr('disabled','disabled');
				return;
			 }
			 
		   }
		});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">留言管理 >> 回复</span> <a href="liuyan/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('liuyan/proc_replay/'.$comment_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=3 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">评论类型</td>
			  <td colspan="2" class="item_input" style="color:red"><?php if($arr['comment_type'] == 1){echo '咨询';}elseif($arr['comment_type'] == 2){echo '评价';}?></td>
		  </tr>
			<tr>
				<td width="96" class="item_title">关联类型:</td>
				<td width="122" class="item_input"><?php if($arr['tag_type'] == 1){echo '商品';}elseif($arr['tag_type'] == 2){echo '礼包';}?></td>
				<td width="400" class="item_input">
                <?php if($perms['liuyan_aurep'] == 1):?>
                <?php if($arr['is_audit'] == 0 && $arr['is_del'] == 0):?>
				  <input type="button" name="author_button" onclick="return audit(<?php echo $arr['comment_id'];?>)" id="author_button" value="审核" />
			    <?php else:?>
				  <input type="button" name="author_button" disabled="disabled" id="author_button" value="已审核" />
                <?php endif;?>
                <?php endif?>
                </td>
			</tr>
			<tr>
				<td class="item_title">对应商品:</td>
				<td colspan="2" class="item_input"><?php echo $arr['tag_name']?></td>
			</tr>
			<tr>
			  <td class="item_title">用户名</td>
			  <td colspan="2" class="item_input"><?php echo $arr['user_name'] != '' ? $arr['user_name'] : '匿名';?><?php echo empty($arr['create_admin']) ? '' : '<span style="color:red">*</span>';?></td>
		  </tr>
			<tr>
			  <td class="item_title">评论内容</td>
			  <td colspan="2" class="item_input"><textarea disabled="disabled" cols="60" rows="5"><?php echo $arr['comment_content'];?></textarea></td>
		  </tr>
          
          <?php if($arr['comment_type'] == 2):?>
			<tr>
			  <td class="item_title">身高(cm)</td>
			  <td colspan="2" class="item_input"><?php echo $arr['height'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">体重(kg)</td>
			  <td colspan="2" class="item_input"><?php echo $arr['weight'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">尺码</td>
			  <td colspan="2" class="item_input"><?php echo $arr['size_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">尺码感受</td>
			  <td colspan="2" class="item_input"><?php if($arr['suitable'] == 1){echo '偏小';}elseif($arr['suitable'] == 2){echo '正好';}else{echo '偏大';};?></td>
		  </tr>
          <?php endif;?>
          
          
			<tr>
			  <td class="item_title">评论时间</td>
			  <td colspan="2" class="item_input"><?php echo $arr['comment_date'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">用户IP</td>
			  <td colspan="2" class="item_input"><?php echo $arr['comment_ip'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">审核人</td>
			  <td colspan="2" class="item_input" id="audit_d"><?php echo $arr['audit_admin_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">回复人</td>
			  <td colspan="2" class="item_input"><?php echo $arr['replay_admin_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">回复内容</td>
			  <td colspan="2" class="item_input"><textarea <?php echo $perms['liuyan_edit'] == 1 ? '' : 'disabled="disabled"';?>   name="reply_content" cols="60" rows="5"><?php echo $arr['reply_content'];?></textarea></td>
		  </tr>
			<tr>
			  <td class="item_title">回复时间</td>
			  <td colspan="2" class="item_input"><?php echo $arr['reply_date'] != '0000-00-00 00:00:00' ? $arr['reply_date'] : '';?></td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td colspan="2" class="item_input">
                <?php if($perms['liuyan_edit'] == 1):?>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    <?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan=3 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>