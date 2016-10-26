<?php include(APPPATH.'views/common/header.php');?>
<div class="main">
      <div class="main_title"><span class="l">充值管理 >> 查看 </span><a href="user_recharge/index" class="return r">返回列表</a></div>
<div class="blank5"></div>
  <table class="form" cellpadding=0 cellspacing=0>
<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">用户名:</td>
				<td class="item_input">
                <?php echo $user_arr->user_name;?></td>
			</tr>
			<tr>
				<td class="item_title">充值金额:</td>
				<td class="item_input"><?php echo $check->amount;?></td>
			</tr>
			<tr>
			  <td class="item_title">支付方式:</td>
			  <td class="item_input">
			    <?php echo empty($check->pay_id) ? '' : $pay_arr[$check->pay_id]->pay_name;?>
              </td>
		  </tr>
			<tr>
				<td class="item_title">管理员备注:</td>
				<td class="item_input">
                <textarea name="admin_note" disabled="disabled" cols="60" rows="4"><?php echo $check->admin_note;?></textarea>
					
				</td>
			</tr>
			<tr>
			  <td class="item_title">用户备注:</td>
			  <td class="item_input">
		      <textarea name="user_note" disabled="disabled" cols="60" rows="4"><?php echo $check->user_note;?></textarea></td>
		  </tr>
			<tr>
			  <td class="item_title">审核：</td>
			  <td class="item_input">
               <input name="is_audit" disabled="disabled" type="radio" value="0" <?php echo $check->is_audit == 0 ? 'checked="checked"' : '';?>/>否
			   <input type="radio" disabled="disabled" name="is_audit" value="1" <?php echo $check->is_audit == 1 ? 'checked="checked"' : '';?> />是
			       
	          </td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<input name="" value="返回" onclick="javascript:history.go(-1);" type="button" />
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
</div>
<?php include(APPPATH.'views/common/footer.php');?>