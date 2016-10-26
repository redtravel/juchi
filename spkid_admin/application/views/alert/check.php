<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<div class="main">
  <div class="main_title"><span class="l">系统设置 >> 检查结果明细</span>  <a href="data_alert/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">创建时间:</td>
				<td class="item_input">
                <?php echo $date_insert ?>
                </td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
                <?php echo $status == 1?'有错误':'无错误' ?>
				</td>
			</tr>
			<tr>
			  <td colspan="2"><?php echo $content ?></td>
		  </tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>