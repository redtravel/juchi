<?php foreach ($list as $l): ?>
<div class="comment_item">
	<b class="youke_type"></b>
	<div class="comment_item_r">
		<h2>
			<em>评论时间: <?php print $l->comment_date; ?></em>
			<strong><?php print mask_str($l->user_name?$l->user_name:$l->admin_user_name,3,0,'***'); ?></strong>
			<span><?php print $l->rank_name?$l->rank_name:'普通会员' ?></span>
		</h2>
		<div class="comment_desc">
			<dl>
				<dt>商品点评：</dt>
				<dd><?php print $l->comment_content ?></dd>
			</dl>
			<dl>
				<dt>尺码感受：</dt>
				<dd>
					身高<?php print intval($l->height);?>CM，
					体重<?php print $l->weight;?>KG，
					购买 <?php print $l->size_name;?>
					<?php if($l->suitable==1) print '偏小';elseif($l->suitable==2) print '正好';elseif($l->suitable==3) print '偏大';?>
				</dd>
			</dl>
		</div>
		<?php if ($l->reply_content): ?>
		<!--客服回复：-->
		<h6 class="single_line"></h6>
		<div class="comment_reply">
			<h2>
				<em>回复时间: <?php print $l->reply_date; ?></em>
				<strong><?php print SITE_NAME;?>管理员</strong>
				<span>管理员</span>
			</h2>
			<p class="replyp"><?php print $l->reply_content ?></p>
		</div>
		<?php endif ?>
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
<?php // endif ?>

<?php if (!$list): ?>
暂时没有针对此商品的点评。
<?php endif ?>