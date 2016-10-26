<?php include(APPPATH.'views/common/header.php');?>
<div class="main">
	<div class="main_title"><span class="l">留言管理 >> 查看</span> <a href="liuyan/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td width="96" class="item_title">关联类型:</td>
				<td class="item_input"><?php if($arr['tag_type'] == 1){echo '商品';}elseif($arr['tag_type'] == 2){echo '礼包';}?></td>
			</tr>
			<tr>
				<td class="item_title">对应商品:</td>
				<td class="item_input"><?php echo $arr['tag_name']?></td>
			</tr>
			<tr>
			  <td class="item_title">评论类型</td>
			  <td class="item_input"><?php if($arr['comment_type'] == 1){echo '咨询';}elseif($arr['comment_type'] == 2){echo '评价';}?></td>
		  </tr>
			<tr>
			  <td class="item_title">用户名</td>
			  <td class="item_input"><?php echo $arr['user_name'] == '' ? '匿名' : $arr['user_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">评论内容</td>
			  <td class="item_input"><textarea disabled="disabled" cols="60" rows="5"><?php echo $arr['comment_content'];?></textarea></td>
		  </tr>
			<tr>
			  <td class="item_title">身高(cm)</td>
			  <td class="item_input"><?php echo $arr['height'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">体重(kg)</td>
			  <td class="item_input"><?php echo $arr['weight'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">尺码</td>
			  <td class="item_input"><?php echo $arr['size_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">尺码感受</td>
			  <td class="item_input"><?php if($arr['suitable'] == 1){echo '偏小';}elseif($arr['suitable'] == 2){echo '正好';}else{echo '偏大';};?></td>
		  </tr>
			<tr>
			  <td class="item_title">评论时间</td>
			  <td class="item_input"><?php echo $arr['comment_date'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">用户IP</td>
			  <td class="item_input"><?php echo $arr['comment_ip'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">审核人</td>
			  <td class="item_input"><?php echo $arr['audit_admin_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">回复人</td>
			  <td class="item_input"><?php echo $arr['replay_admin_name'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">回复内容</td>
			  <td class="item_input"><?php print $this->ckeditor->editor('reply_content',$arr['reply_content']);?></td>
		  </tr>
			<tr>
			  <td class="item_title">回复时间</td>
			  <td class="item_input"><?php echo $arr['reply_date'];?></td>
		  </tr>
			<tr>
			  <td class="item_title">是否逻辑删除</td>
			  <td class="item_input"><?php if($arr['is_del'] == 1){echo '是';}elseif($arr['is_del'] == 0){echo '否';}?></td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
</div>
<?php include(APPPATH.'views/common/footer.php');?>