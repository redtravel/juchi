<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
//textarea的鼠标触发事件
	$('.myAdeMsgBox textarea').attr('value','字数限制5-200个汉字');
	$('.myAdeMsgBox textarea').focus(function () {
		if ($(this).attr('value')=='字数限制5-200个汉字') {
			$(this).attr('value','');
			$(this).css({'color':'#000'});
		};
	});
	$('.myAdeMsgBox textarea').blur(function () {
		if ($(this).attr('value')=='') {
			$(this).attr('value','字数限制5-200个汉字');
			$(this).removeAttr('style');
		};
	});
</script>
<table border="0" align="center" cellpadding="0" cellspacing="0" style="margin:10px 15px; width:300px;">
 <tr>
		<td colspan="5" style="height:25px; overflow:hidden;">
			<h2><?=$user_name?>，请留下您购买商品的评价:</h2>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="left" valign="middle">
                    <textarea name="fl_text" rows="3" style="width:324px; height: 64px;"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="center" >
			<input type="hidden" name="product_id" value="<?php echo $product_id ?>">
			<a id="" class="btn_g_78 font14b" style="margin-top:10px;">确定</a>
			<font class="errorInfo red">评论内容不能为空!</font>
		</td>
	</tr>
</table>
