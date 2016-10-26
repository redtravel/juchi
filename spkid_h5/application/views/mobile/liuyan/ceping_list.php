<?php  foreach ($list as $l): ?>
<div class="qa_item">
    <b class="rank_type_<?=$l->rank_id?>"></b>
	<div class="comment_item_r">
		<h2>
			<em>咨询时间: <?php print $l->comment_date;?></em>
			<strong><?php print ($l->user_name||$l->admin_user_name)?mask_str($l->user_name?$l->user_name:$l->admin_user_name,3,0,'***'):'游客'; ?></strong>
			<span><?php print $l->rank_name?$l->rank_name:'' ?></span>
		</h2>
		<p class="qa_desc"><?php print $l->comment_content; ?></p>
		<?php if($l->reply_content):?>
		<h6 class="single_line"></h6>
		<div class="comment_reply">
			<h2>
				<em>回复时间: <?php print $l->reply_date;?></em>
				<strong><?php print SITE_NAME;?>管理员 </strong>
				<span>管理员</span>
			</h2>
			<p class="replyp"><?php print $l->reply_content;?></p>
		</div>
		<?php endif;?>
	</div>
</div>
<?php endforeach ?>

<?php // if ($list): ?>
<!--<div class="dianping_page">
	<a href="javascript:void(0)" class="preBtn">上一页</a>
	<a href="javascript:void(0)">1</a>
	<a href="javascript:void(0)" class="sel">2</a>
	<a href="javascript:void(0)">3</a>
	<a style="border:0">...</a>
	<a href="javascript:void(0)">30</a>
	<a href="javascript:void(0)" class="nextBtn">下一页</a>
</div>-->

<?php if (!$list): ?>
	暂时没有关于此商品的咨询。
<?php endif ?>