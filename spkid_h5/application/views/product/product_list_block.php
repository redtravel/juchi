<script type="text/javascript">
$(function () {
	$('.pro_c li').hover(function () {
		$(this).addClass('sideRed');
		$(this).find('.fenxiangDiv').show();
	},function () {
		$(this).removeClass('sideRed');
		$(this).find('.fenxiangDiv').fadeOut(100);
	});
});
</script>
<div class="pro_c">
	<ul>
		<?php $i=1;foreach ($product_list as $p): ?>
		<li <?php if($i%3==0) print 'style="margin-right:0;"' ?>>
			<div class="img_xinpin"></div>
			<div class="fenxiangDiv" style="display:none;">
				<a href="/" class="icon_tengxun"></a>
				<a href="/" class="icon_sina"></a>
				<span>分享</span>
			</div>
			<a href="<?php print "product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank">
				<img class="lazy" width="318" height="318" data-original="<?php print img_url( $p->img_318_318,$p->product_id); ?>" src="<?php print static_style_url('img/common/loading_1.gif'); ?>" />
			</a>
			
			<dl>
				<dd>
					<div class="l"><?php print $p->brand_name ?></div>
					<div class="r" style="color:#666;">市场价:<font class="y_p">￥<?php print round($p->market_price,2); ?></font></div>
				</dd>
				<dd>
					<div class="l"><a href="<?php print "product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank"><?php print $p->product_name ?></a></div>
					<div class="r" style="margin-top:5px;">(5.7折)</div>
					<div class="priceNum r">￥<?php print round($p->product_price,2); ?></div>
				</dd>
			</dl>
		</li>
		<?php $i++;endforeach ?>
	</ul>
	<?php if (!$product_list): ?>
	<div class="error">
		<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100" rowspan="2"><img src="<?php print static_style_url('img/common/error_t.png'); ?>" width="56" height="55" /></td>
				<td width="400" height="40" class="f16b c_f60">亲爱的会员，您好！</td>
			</tr>
			<tr>
			<td class="f14" style="line-height:25px;">
				<font class="fred">您所查找的商品暂时缺货，请重新选购您所喜爱的商品！</font><br>
				<font class="c99">近期我们即将对该品类商品进行补货，请随时关注网站的更新及公告，祝您在爱童网购物愉快！</font>
			</td>
			</tr>
		</table>
	</div>
	<?php endif ?>
</div>